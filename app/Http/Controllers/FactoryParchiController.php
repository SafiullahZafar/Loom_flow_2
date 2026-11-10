<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FabricStock;
use App\Models\Invoice;
use App\Models\KachiParchi;
use App\Models\KachiParchiItem;
use App\Models\PakkiParchi;
use App\Models\Sale;
use App\Models\ThaanDisposition;
use App\Models\ThaanReturn;
use App\Models\ThaanRoll;
use App\Models\WorkOrder;
use App\Services\FactoryStockService;
use App\Services\LedgerPostingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FactoryParchiController extends Controller
{
    public function index(): View
    {
        return view('factory.dispatch', [
            'jobOrders' => WorkOrder::with(['fabricStock', 'parta'])->where('type', 'job')->whereIn('status', ['active', 'fulfilled'])->orderBy('order_no')->get(),
            'allJobOrders' => WorkOrder::where('type', 'job')->orderBy('order_no')->get(),
            'rolls' => ThaanRoll::with('order')->whereIn('status', ['ready', 'returned_ready', 'returned_hold', 'dispatched'])->orderByDesc('id')->get(),
            'kachis' => KachiParchi::with(['order.fabricStock', 'items.roll', 'pakki', 'returns'])->latest('id')->paginate(30),
            'pakkis' => PakkiParchi::with('kachi.order')->latest('id')->limit(20)->get(),
            'returns' => ThaanReturn::with('roll')->latest('id')->limit(25)->get(),
            'pendingKachis' => KachiParchi::with('order')->whereDoesntHave('pakki')->orderByDesc('id')->get(),
            'rejectedKachis' => KachiParchi::with(['pakki', 'items.roll'])->whereHas('pakki', fn ($q) => $q->where('rejected_meters', '>', 0))->orderByDesc('id')->get(),
        ]);
    }

    public function dispatch(Request $request, FactoryStockService $stock): RedirectResponse
    {
        $data = $request->validate([
            'parchi_no' => 'required|string|max:100|unique:kachi_parchis,parchi_no',
            'work_order_id' => 'required|exists:work_orders,id',
            'dispatch_date' => 'required|date',
            'roll_ids' => 'required|array|min:1|max:200',
            'roll_ids.*' => 'required|integer|distinct|exists:thaan_rolls,id',
            'vehicle_no' => 'nullable|string|max:100', 'notes' => 'nullable|string|max:1000',
        ]);
        DB::transaction(function () use ($data, $stock): void {
            $order = WorkOrder::with('fabricStock')->whereKey($data['work_order_id'])->lockForUpdate()->firstOrFail();
            if ($order->type !== 'job' || ! in_array($order->status, ['active', 'fulfilled'], true)) {
                throw ValidationException::withMessages(['work_order_id' => 'Choose an open job-work order.']);
            }
            $rolls = ThaanRoll::whereIn('id', $data['roll_ids'])->lockForUpdate()->get();
            if ($rolls->count() !== count($data['roll_ids']) || $rolls->contains(fn ($r): bool => (int) $r->work_order_id !== (int) $order->id || ! in_array($r->status, ['ready', 'returned_ready'], true))) {
                throw ValidationException::withMessages(['roll_ids' => 'Select only available checked Thaans for this order.']);
            }
            $total = round((float) $rolls->sum('length_meters'), 2);
            $kachi = KachiParchi::create([
                'parchi_no' => $data['parchi_no'], 'work_order_id' => $order->id,
                'dispatch_date' => $data['dispatch_date'], 'dispatched_meters' => $total,
                'thaan_count' => $rolls->count(), 'vehicle_no' => $data['vehicle_no'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
            foreach ($rolls as $roll) {
                KachiParchiItem::create(['kachi_parchi_id' => $kachi->id, 'thaan_roll_id' => $roll->id, 'length_meters' => $roll->length_meters]);
                $roll->update(['status' => 'dispatched']);
            }
            $stock->move($order->fabricStock, -$total, -$rolls->count(), $data['dispatch_date'], 'Kachi '.$kachi->parchi_no);
            if ($order->contract) {
                $order->contract->increment('delivered_meters', $total);
            }
        });

        return back()->with('success', 'Kachi Parchi saved with individual Thaans and total dispatched metres. No weaving bill is created until Pakki Parchi.');
    }

    public function pakki(Request $request, LedgerPostingService $posting): RedirectResponse
    {
        $data = $request->validate([
            'parchi_no' => 'required|string|max:100|unique:pakki_parchis,parchi_no',
            'kachi_parchi_id' => 'required|exists:kachi_parchis,id|unique:pakki_parchis,kachi_parchi_id',
            'received_date' => 'required|date',
            'accepted_thaans' => 'required|integer|min:0',
            'accepted_meters' => 'required|numeric|min:0',
            'rejected_meters' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        DB::transaction(function () use ($data, $posting): void {
            $kachi = KachiParchi::with('order.parta')->whereKey($data['kachi_parchi_id'])->lockForUpdate()->firstOrFail();
            if ($kachi->pakki()->exists()) {
                throw ValidationException::withMessages(['kachi_parchi_id' => 'This Kachi already has a Pakki Parchi.']);
            }
            $accepted = round((float) $data['accepted_meters'], 2);
            $rejected = round((float) $data['rejected_meters'], 2);
            if (abs($accepted + $rejected - (float) $kachi->dispatched_meters) > 0.011 || (int) $data['accepted_thaans'] > $kachi->thaan_count) {
                throw ValidationException::withMessages(['accepted_meters' => 'Accepted + rejected metres must equal Kachi metres; accepted Thaan count cannot exceed dispatched pieces.']);
            }
            if (($accepted === 0.0 && (int) $data['accepted_thaans'] !== 0) || ($accepted > 0 && (int) $data['accepted_thaans'] === 0)) {
                throw ValidationException::withMessages(['accepted_thaans' => 'Accepted piece count must match whether accepted fabric exists.']);
            }
            $order = WorkOrder::whereKey($kachi->work_order_id)->lockForUpdate()->firstOrFail();
            $unitRate = round((float) ($order->parta?->pick ?? $order->fabricStock?->pick ?? 0) * (float) $order->rate_per_pick, 2);
            $bill = round($accepted * $unitRate, 2);
            $invoice = null;
            if ($accepted > 0) {
                $customer = Customer::whereRaw('LOWER(name) = ?', [mb_strtolower(trim((string) $order->party_name))])->first();
                $customer ??= Customer::create(['name' => trim((string) $order->party_name), 'status' => 'active']);
                $invoice = Invoice::create([
                    'invoice_no' => 'JW-'.$data['parchi_no'], 'customer_id' => $customer->id,
                    'sales_contract_id' => $order->sales_contract_id, 'sale_id' => null,
                    'invoice_date' => $data['received_date'], 'subtotal' => $bill, 'discount' => 0,
                    'total' => $bill, 'status' => 'issued', 'notes' => 'Weaving mazdoori on Pakki Parchi '.$data['parchi_no'],
                ]);
                $invoice->items()->create([
                    'description' => 'Weaving '.$order->order_no.' / '.$data['parchi_no'].' ('.$order->fabricStock?->quality_name.')',
                    'quantity' => $accepted, 'unit' => 'Meter', 'rate' => round($unitRate, 2), 'amount' => $bill,
                ]);
                if ($bill > 0) {
                    $posting->post('invoice', $invoice->id, [
                        ['role' => 'customer', 'party_name' => $customer->name, 'party_type' => 'fabric_buyer', 'transaction_date' => $data['received_date'], 'voucher_no' => $invoice->invoice_no, 'description' => 'Pakki accepted weaving bill', 'debit' => $bill, 'credit' => 0, 'notes' => null],
                        ['role' => 'revenue', 'party_name' => 'Job Work Revenue', 'party_type' => 'income', 'transaction_date' => $data['received_date'], 'voucher_no' => $invoice->invoice_no, 'description' => 'Pakki accepted weaving bill', 'debit' => 0, 'credit' => $bill, 'notes' => null],
                    ]);
                }
            }
            PakkiParchi::create([
                'parchi_no' => $data['parchi_no'], 'kachi_parchi_id' => $kachi->id,
                'invoice_id' => $invoice?->id, 'received_date' => $data['received_date'],
                'accepted_thaans' => $data['accepted_thaans'], 'accepted_meters' => $accepted,
                'rejected_meters' => $rejected, 'bill_amount' => $bill, 'notes' => $data['notes'] ?? null,
            ]);
            $order->accepted_meters = round((float) $order->accepted_meters + $accepted, 2);
            if ($order->target_meters && (float) $order->accepted_meters >= (float) $order->target_meters) {
                $order->status = 'fulfilled';
            }
            $order->save();
            if ($order->contract) {
                $order->contract->accepted_meters = $order->accepted_meters;
                $order->contract->remaining_meters = max(0, (float) $order->contract->total_meters - (float) $order->accepted_meters);
                $order->contract->save();
            }
            if ($rejected <= 0.001) {
                ThaanRoll::whereIn('id', $kachi->items()->pluck('thaan_roll_id'))->where('status', 'dispatched')->update(['status' => 'accepted']);
            }
        });

        return back()->with('success', 'Pakki recorded. Weaving bill uses accepted metres only, not dispatched metres.');
    }

    public function returnThaan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kachi_parchi_id' => 'required|exists:kachi_parchis,id',
            'roll_ids' => 'required|array|min:1|max:200',
            'roll_ids.*' => 'required|integer|distinct|exists:thaan_rolls,id',
            'returned_on' => 'required|date',
        ]);
        DB::transaction(function () use ($data): void {
            $kachi = KachiParchi::with(['pakki', 'items'])->whereKey($data['kachi_parchi_id'])->lockForUpdate()->firstOrFail();
            if (! $kachi->pakki) {
                throw ValidationException::withMessages(['kachi_parchi_id' => 'Record the Pakki Parchi before entering rejected returns.']);
            }
            $validIds = $kachi->items->pluck('thaan_roll_id')->all();
            $rolls = ThaanRoll::whereIn('id', $data['roll_ids'])->lockForUpdate()->get();
            if ($rolls->count() !== count($data['roll_ids']) || $rolls->contains(fn ($r): bool => ! in_array($r->id, $validIds) || $r->status !== 'dispatched')) {
                throw ValidationException::withMessages(['roll_ids' => 'Select marked, dispatched Thaans from this Kachi only.']);
            }
            $already = (float) $kachi->returns()->with('roll')->get()->sum(fn ($r): float => (float) $r->roll?->length_meters);
            if ($already + (float) $rolls->sum('length_meters') > (float) $kachi->pakki->rejected_meters + 0.011) {
                throw ValidationException::withMessages(['roll_ids' => 'Returned full-Thaan metres exceed the Pakki rejection quantity.']);
            }
            foreach ($rolls as $roll) {
                ThaanReturn::create(['kachi_parchi_id' => $kachi->id, 'thaan_roll_id' => $roll->id, 'returned_on' => $data['returned_on']]);
                $roll->update(['status' => 'returned_hold']);
            }
            $returned = round($already + (float) $rolls->sum('length_meters'), 2);
            if (abs($returned - (float) $kachi->pakki->rejected_meters) < 0.011) {
                $acceptedPieces = $kachi->thaan_count - $kachi->returns()->count();
                if ($acceptedPieces !== (int) $kachi->pakki->accepted_thaans) {
                    throw ValidationException::withMessages(['roll_ids' => 'The number of returned Thaans does not match the accepted Thaan count on Pakki.']);
                }
                ThaanRoll::whereIn('id', $validIds)->where('status', 'dispatched')->update(['status' => 'accepted']);
            }
        });

        return back()->with('success', 'Returned rejected Thaans identified using original markings. They remain on hold until you decide their use.');
    }

    public function disposition(Request $request, FactoryStockService $stock, LedgerPostingService $posting): RedirectResponse
    {
        $data = $request->validate([
            'thaan_roll_id' => 'required|exists:thaan_rolls,id',
            'action' => 'required|in:resend_a,return_customer,settle_keep,sell_approved,carry_forward',
            'to_work_order_id' => 'nullable|exists:work_orders,id',
            'settlement_amount' => 'nullable|numeric|min:0',
            'approval_reference' => 'nullable|string|max:150',
            'action_date' => 'required|date', 'notes' => 'nullable|string|max:1000',
        ]);
        DB::transaction(function () use ($data, $stock, $posting): void {
            $roll = ThaanRoll::with('order.fabricStock')->whereKey($data['thaan_roll_id'])->lockForUpdate()->firstOrFail();
            if ($roll->status !== 'returned_hold') {
                throw ValidationException::withMessages(['thaan_roll_id' => 'Only rejected returned Thaans on hold may be disposed.']);
            }
            $oldOrder = $roll->order;
            $action = $data['action'];
            $newOrder = null;
            if ($action === 'carry_forward') {
                $newOrder = WorkOrder::findOrFail($data['to_work_order_id'] ?? 0);
                if ($newOrder->type !== 'job' || $newOrder->status !== 'active' || $newOrder->party_name !== $oldOrder->party_name || (int) $newOrder->fabric_stock_id !== (int) $oldOrder->fabric_stock_id || $newOrder->id === $oldOrder->id) {
                    throw ValidationException::withMessages(['to_work_order_id' => 'Carry forward only to another active contract for the same party and quality.']);
                }
            }
            if (in_array($action, ['settle_keep', 'sell_approved'], true) && ! isset($data['settlement_amount'])) {
                throw ValidationException::withMessages(['settlement_amount' => 'Enter the settlement agreed with the customer.']);
            }
            if ($action === 'sell_approved' && blank($data['approval_reference'] ?? null)) {
                throw ValidationException::withMessages(['approval_reference' => 'Customer approval reference is required before external sale.']);
            }
            $isStock = in_array($action, ['resend_a', 'settle_keep', 'sell_approved', 'carry_forward'], true);
            if ($isStock) {
                $roll->status = $action === 'resend_a' || $action === 'carry_forward' ? 'returned_ready' : 'ready';
                if (in_array($action, ['settle_keep', 'sell_approved'], true)) {
                    $roll->owner_type = 'factory';
                }
                if ($newOrder) {
                    $roll->work_order_id = $newOrder->id;
                }
                $roll->save();
                $stock->move($oldOrder->fabricStock, (float) $roll->length_meters, 1, $data['action_date'], 'Disposition '.$action.' '.$roll->roll_no);
            } else {
                $roll->update(['status' => 'disposed']);
            }
            $disposition = ThaanDisposition::create([
                'thaan_roll_id' => $roll->id, 'from_work_order_id' => $oldOrder->id,
                'to_work_order_id' => $newOrder?->id, 'action' => $action,
                'settlement_amount' => $data['settlement_amount'] ?? null,
                'approval_reference' => $data['approval_reference'] ?? null,
                'action_date' => $data['action_date'], 'notes' => $data['notes'] ?? null,
            ]);
            if (in_array($action, ['settle_keep', 'sell_approved'], true) && (float) $disposition->settlement_amount > 0) {
                $amount = round((float) $disposition->settlement_amount, 2);
                $description = $action === 'settle_keep'
                    ? 'Rejected fabric settlement retained by factory: '.$roll->roll_no
                    : 'Rejected fabric settlement approved for resale: '.$roll->roll_no;
                $voucher = 'THAAN-SETTLEMENT-'.$disposition->id;
                $posting->post('thaan_disposition_settlement', $disposition->id, [
                    [
                        'role' => 'customer', 'party_name' => $oldOrder->party_name, 'party_type' => 'fabric_buyer',
                        'transaction_date' => $data['action_date'], 'voucher_no' => $voucher, 'description' => $description,
                        'debit' => 0, 'credit' => $amount, 'notes' => $data['notes'] ?? null,
                    ],
                    [
                        'role' => 'settlement_expense', 'party_name' => 'Customer Material Settlements', 'party_type' => 'expense',
                        'transaction_date' => $data['action_date'], 'voucher_no' => $voucher, 'description' => $description,
                        'debit' => $amount, 'credit' => 0, 'notes' => $data['notes'] ?? null,
                    ],
                ]);
            }
        });

        return back()->with('success', 'Rejected fabric disposition saved with its original Thaan marking and history.');
    }

    public function printKachi(KachiParchi $kachi): View
    {
        $kachi->load(['order.fabricStock', 'items.roll']);

        return view('factory.print', ['title' => 'Kachi Parchi', 'kachi' => $kachi]);
    }

    public function printPakki(PakkiParchi $pakki): View
    {
        $pakki->load(['kachi.order.fabricStock', 'kachi.items.roll']);

        return view('factory.print', ['title' => 'Pakki Parchi', 'kachi' => $pakki->kachi, 'pakki' => $pakki]);
    }

    public function ownSale(Request $request, FactoryStockService $stock, LedgerPostingService $posting): RedirectResponse
    {
        $data = $request->validate([
            'slip_no' => 'required|string|max:100|unique:sales,slip_no',
            'roll_ids' => 'required|array|min:1|max:200', 'roll_ids.*' => 'required|integer|distinct|exists:thaan_rolls,id',
            'buyer_name' => 'required|string|max:150', 'rate_per_meter' => 'required|numeric|min:0',
            'sale_date' => 'required|date', 'notes' => 'nullable|string|max:1000',
        ]);
        DB::transaction(function () use ($data, $stock, $posting): void {
            $rolls = ThaanRoll::with('order')->whereIn('id', $data['roll_ids'])->lockForUpdate()->get();
            if ($rolls->count() !== count($data['roll_ids']) || $rolls->isEmpty() || $rolls->contains(fn ($r): bool => $r->status !== 'ready' || $r->owner_type !== 'factory')) {
                throw ValidationException::withMessages(['roll_ids' => 'Choose available factory-owned A-category Thaans only.']);
            }
            if ($rolls->pluck('fabric_stock_id')->unique()->count() !== 1) {
                throw ValidationException::withMessages(['roll_ids' => 'One sale slip must contain one fabric quality.']);
            }
            $fabric = FabricStock::findOrFail($rolls->first()->fabric_stock_id);
            $total = round((float) $rolls->sum('length_meters'), 2);
            $amount = round($total * (float) $data['rate_per_meter'], 2);
            $sale = Sale::create([
                'slip_no' => $data['slip_no'], 'sale_type' => 'open_market',
                'fabric_stock_id' => $fabric->id, 'buyer_name' => trim($data['buyer_name']),
                'fabric_quality' => $fabric->quality_name, 'meters_sold' => $total,
                'rolls_sold' => $rolls->count(), 'rate_per_meter' => $data['rate_per_meter'],
                'subtotal' => $amount, 'discount' => 0, 'net_total' => $amount,
                'payment_type' => 'credit', 'payment_status' => 'unpaid',
                'sale_date' => $data['sale_date'], 'notes' => $data['notes'] ?? null,
            ]);
            foreach ($rolls as $roll) {
                $roll->update(['status' => 'dispatched']);
            }
            $stock->move($fabric, -$total, -$rolls->count(), $data['sale_date'], 'Own fabric sale '.$sale->slip_no);
            $lines = [
                ['role' => 'buyer', 'party_name' => $sale->buyer_name, 'party_type' => 'fabric_buyer', 'transaction_date' => $sale->sale_date, 'voucher_no' => $sale->slip_no, 'description' => 'Own-fabric sale', 'debit' => $amount, 'credit' => 0, 'notes' => null],
                ['role' => 'revenue', 'party_name' => 'Fabric Sales Revenue', 'party_type' => 'income', 'transaction_date' => $sale->sale_date, 'voucher_no' => $sale->slip_no, 'description' => 'Own-fabric sale', 'debit' => 0, 'credit' => $amount, 'notes' => null],
            ];
            $cost = round($total * (float) $fabric->unit_cost_meter, 2);
            if ($cost > 0) {
                $lines[] = ['role' => 'cost_of_goods_sold', 'party_name' => 'Cost of Goods Sold', 'party_type' => 'expense', 'transaction_date' => $sale->sale_date, 'voucher_no' => $sale->slip_no, 'description' => 'Own-fabric cost', 'debit' => $cost, 'credit' => 0, 'notes' => null];
                $lines[] = ['role' => 'inventory', 'party_name' => 'Finished Fabric Inventory', 'party_type' => 'inventory', 'transaction_date' => $sale->sale_date, 'voucher_no' => $sale->slip_no, 'description' => 'Own-fabric cost released', 'debit' => 0, 'credit' => $cost, 'notes' => null];
            }
            $posting->post('sale', $sale->id, $lines);
        });

        return back()->with('success', 'Own-fabric sale saved. Create its invoice in the existing Invoices module if required.');
    }
}
