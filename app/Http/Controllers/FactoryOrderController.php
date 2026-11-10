<?php

namespace App\Http\Controllers;

use App\Models\BagTransaction;
use App\Models\BeamEvent;
use App\Models\Customer;
use App\Models\FabricStock;
use App\Models\Invoice;
use App\Models\LoomAssignment;
use App\Models\PakkiParchi;
use App\Models\PartaRecord;
use App\Models\Production;
use App\Models\SalesContract;
use App\Models\SizingBeam;
use App\Models\ThaanRoll;
use App\Models\WorkOrder;
use App\Models\YarnMovement;
use App\Models\YarnQuality;
use App\Services\LedgerPostingService;
use App\Services\PartaService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FactoryOrderController extends Controller
{
    public function index(): View
    {
        return view('factory.orders', [
            'fabrics' => FabricStock::orderBy('quality_name')->get(),
            'looms' => config('loomflow.looms'),
            'customers' => Customer::where('status', 'active')->orderBy('name')->get(),
            'partas' => PartaRecord::with('fabricStock')->latest('id')->get()->unique('fabric_stock_id'),
            'orders' => WorkOrder::with(['fabricStock', 'contract', 'parta', 'customer'])->latest('id')->paginate(30),
            'activeOrders' => WorkOrder::with('fabricStock')->whereIn('status', ['active', 'fulfilled'])->latest('id')->get(),
            'existingContracts' => SalesContract::with('customer')->whereNotIn('id', WorkOrder::whereNotNull('sales_contract_id')->pluck('sales_contract_id'))->orderBy('contract_no')->get(),
            'assignments' => LoomAssignment::with(['order.fabricStock'])->whereNull('end_date')->orderBy('loom_no')->get(),
            'beams' => SizingBeam::with('workOrder.contract')->whereNotIn('status', ['exhausted', 'returned_to_customer'])->orderBy('beam_no')->get(),
            'yarnBalances' => YarnMovement::selectRaw('work_order_id, component, owner_type, SUM(CASE WHEN movement_type IN (\'receipt\',\'return\',\'adjustment_in\') THEN weight_kg ELSE -weight_kg END) as balance_kg')->groupBy('work_order_id', 'component', 'owner_type')->get(),
            'recentBeams' => BeamEvent::with('beam')->latest('id')->limit(15)->get(),
        ]);
    }

    public function parta(Request $request, PartaService $parta): RedirectResponse
    {
        $data = $request->validate([
            'fabric_stock_id' => 'required|exists:fabric_stocks,id',
            'read' => 'required|numeric|gt:0', 'pick' => 'required|numeric|gt:0',
            'width_inches' => 'required|numeric|gt:0',
            'warp_count' => 'required|numeric|gt:0', 'weft_count' => 'required|numeric|gt:0',
            'warp_rate_per_lb' => 'required|numeric|min:0', 'weft_rate_per_lb' => 'required|numeric|min:0',
            'conversion_rate_per_pick' => 'required|numeric|min:0',
        ]);
        DB::transaction(fn () => $parta->save(FabricStock::findOrFail($data['fabric_stock_id']), $data));

        return back()->with('success', 'Parta saved with weight and cost per metre. Existing order costing remains unchanged.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_no' => 'required|string|max:100|unique:work_orders,order_no',
            'type' => 'required|in:own,job',
            'fabric_stock_id' => 'required|exists:fabric_stocks,id',
            'target_meters' => 'nullable|numeric|gt:0',
            'customer_id' => 'nullable|required_if:type,job|exists:customers,id',
            'existing_contract_id' => 'nullable|exists:sales_contracts,id',
            'contract_no' => 'nullable|string|max:100|unique:sales_contracts,contract_no',
            'rate_per_pick' => 'nullable|numeric|min:0',
            'warp_supply' => 'required|in:customer,factory,mixed',
            'weft_supply' => 'required|in:customer,factory,mixed',
            'start_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ]);
        DB::transaction(function () use ($data): void {
            $fabric = FabricStock::findOrFail($data['fabric_stock_id']);
            $parta = PartaRecord::where('fabric_stock_id', $fabric->id)->latest('version')->first();
            if (! $parta) {
                throw ValidationException::withMessages(['fabric_stock_id' => 'Save the quality Parta before opening an order.']);
            }
            $contract = null;
            $party = null;
            $target = $data['target_meters'] ?? null;
            $rate = (float) ($data['rate_per_pick'] ?? $parta->conversion_rate_per_pick);
            if ($data['type'] === 'job') {
                if (! empty($data['existing_contract_id'])) {
                    $contract = SalesContract::whereKey($data['existing_contract_id'])->lockForUpdate()->firstOrFail();
                    if (WorkOrder::where('sales_contract_id', $contract->id)->exists()) {
                        throw ValidationException::withMessages(['existing_contract_id' => 'This contract already has a work order.']);
                    }
                    if ($contract->contract_type !== 'Conversion') {
                        throw ValidationException::withMessages(['existing_contract_id' => 'Only conversion/job-work contracts can be linked here.']);
                    }
                    if ((int) $contract->fabric_stock_id !== (int) $fabric->id) {
                        throw ValidationException::withMessages(['existing_contract_id' => 'The contract quality does not match.']);
                    }
                    if ($contract->customer_id && (int) $contract->customer_id !== (int) $data['customer_id']) {
                        throw ValidationException::withMessages(['customer_id' => 'Choose the customer linked to this contract.']);
                    }
                    $customer = Customer::findOrFail($data['customer_id']);
                    $contract->customer_id ??= $customer->id;
                    $contract->buyer_name = $customer->name;
                    $contract->buyer_phone = $customer->phone;
                    $contract->save();
                    $party = $customer->name;
                    $target = (float) $contract->total_meters;
                    $rate = (float) $contract->conversion_rate;
                } else {
                    if (blank($data['contract_no'] ?? null) || ! $target) {
                        throw ValidationException::withMessages(['contract_no' => 'Job work requires a contract number and target quantity.']);
                    }
                    $customer = Customer::findOrFail($data['customer_id']);
                    $party = $customer->name;
                    $contract = SalesContract::create([
                        'contract_no' => $data['contract_no'], 'customer_id' => $customer->id,
                        'buyer_name' => $party, 'buyer_phone' => $customer->phone,
                        'fabric_stock_id' => $fabric->id, 'fabric_quality' => $fabric->quality_name,
                        'contract_type' => 'Conversion', 'read' => $parta->read, 'pick' => $parta->pick,
                        'warp_count' => $parta->warp_count, 'weft_count' => $parta->weft_count,
                        'width' => $parta->width_inches,
                        'yarn_warp_rate' => $parta->warp_rate_per_lb, 'yarn_weft_rate' => $parta->weft_rate_per_lb,
                        'conversion_rate' => $rate,
                        'rate_per_meter' => round((float) $parta->pick * $rate, 2),
                        'total_meters' => $target, 'total_amount' => round($target * (float) $parta->pick * $rate, 2),
                        'remaining_meters' => $target, 'delivered_meters' => 0,
                        'accepted_meters' => 0, 'status' => 'active', 'start_date' => $data['start_date'],
                        'sale_tax_rate' => 0, 'sales_tax_amount' => 0, 'tax_enabled' => false,
                    ]);
                }
            }
            WorkOrder::create([
                'order_no' => $data['order_no'], 'type' => $data['type'],
                'sales_contract_id' => $contract?->id, 'fabric_stock_id' => $fabric->id,
                'customer_id' => $contract?->customer_id, 'parta_record_id' => $parta->id, 'party_name' => $party,
                'target_meters' => $target, 'rate_per_pick' => $rate,
                'warp_supply' => $data['type'] === 'own' ? 'factory' : $data['warp_supply'],
                'weft_supply' => $data['type'] === 'own' ? 'factory' : $data['weft_supply'],
                'start_date' => $data['start_date'], 'notes' => $data['notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Order created. Allocate one or more looms to begin tracking production.');
    }

    public function assign(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'loom_no' => ['required', 'string', Rule::in(config('loomflow.looms'))],
            'start_date' => 'required|date',
            'change_reason' => 'nullable|string|max:500',
        ]);
        DB::transaction(function () use ($data): void {
            $order = WorkOrder::whereKey($data['work_order_id'])->lockForUpdate()->firstOrFail();
            if ($order->status !== 'active') {
                throw ValidationException::withMessages(['work_order_id' => 'Only active orders can be allocated to looms.']);
            }
            if ($data['start_date'] < $order->start_date) {
                throw ValidationException::withMessages(['start_date' => 'Allocation cannot start before the order.']);
            }
            $mountedBeam = SizingBeam::where('assigned_loom_no', $data['loom_no'])
                ->where('status', 'mounted_on_loom')->lockForUpdate()->first();
            if ($mountedBeam && (int) ($mountedBeam->work_order_id ?? 0) !== (int) $order->id) {
                throw ValidationException::withMessages([
                    'loom_no' => 'This loom still has beam '.$mountedBeam->beam_no.' from another order. Unmount it, return it to the customer, or settle and reassign it before assigning new work.',
                ]);
            }
            $current = LoomAssignment::where('loom_no', $data['loom_no'])->whereNull('end_date')->lockForUpdate()->first();
            if ($current) {
                if ((int) $current->work_order_id === (int) $order->id) {
                    throw ValidationException::withMessages(['loom_no' => 'This loom is already assigned to that order.']);
                }
                if ($data['start_date'] <= $current->start_date) {
                    throw ValidationException::withMessages(['start_date' => 'A loom cannot switch contracts on its existing assignment date.']);
                }
                $lastProduction = Production::where('loom_no', $data['loom_no'])->where('work_order_id', $current->work_order_id)->max('production_date');
                if ($lastProduction && $data['start_date'] <= substr((string) $lastProduction, 0, 10)) {
                    throw ValidationException::withMessages(['start_date' => 'Use a later date than the previous contract production.']);
                }
                $current->end_date = Carbon::parse($data['start_date'])->subDay()->toDateString();
                $current->change_reason = $data['change_reason'] ?? 'Order switched';
                $current->save();
            }
            LoomAssignment::create([
                'work_order_id' => $order->id, 'loom_no' => $data['loom_no'],
                'start_date' => $data['start_date'], 'change_reason' => $data['change_reason'] ?? null,
            ]);
        });

        return back()->with('success', 'Loom allocation saved. Checking automatically uses the correct order and quality.');
    }

    public function yarn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id', 'component' => 'required|in:warp,weft',
            'owner_type' => 'required|in:customer,factory',
            'movement_type' => 'required|in:receipt,issue,return,adjustment_in,adjustment_out',
            'weight_kg' => 'required|numeric|gt:0', 'bags' => 'nullable|numeric|min:0',
            'yarn_count' => 'nullable|string|max:80', 'lot_no' => 'nullable|string|max:100',
            'reference_no' => 'nullable|string|max:100', 'movement_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'destination_type' => 'nullable|in:factory_store,sizing,loom',
            'destination_name' => 'nullable|string|max:150',
        ]);
        DB::transaction(function () use ($data): void {
            $order = WorkOrder::findOrFail($data['work_order_id']);
            $supply = $data['component'] === 'warp' ? $order->warp_supply : $order->weft_supply;
            if ($data['owner_type'] === 'factory' && in_array($data['movement_type'], ['issue', 'return'], true)) {
                throw ValidationException::withMessages([
                    'movement_type' => 'Record factory yarn issues and returns in Bags. Link the bag transaction to this order there so kg and stock stay synchronized.',
                ]);
            }
            if ($order->type === 'own' && $data['owner_type'] === 'customer') {
                throw ValidationException::withMessages(['owner_type' => 'Own production uses factory yarn.']);
            }
            if ($supply !== 'mixed' && $supply !== $data['owner_type']) {
                throw ValidationException::withMessages(['owner_type' => 'This order says the '.$data['component'].' yarn is supplied by '.$supply.'.']);
            }
            $movements = YarnMovement::where('work_order_id', $order->id)->where('component', $data['component'])
                ->where('owner_type', $data['owner_type'])->lockForUpdate()->get();
            $balance = $movements->sum(fn ($m): float => in_array($m->movement_type, ['receipt', 'return', 'adjustment_in'], true) ? (float) $m->weight_kg : -(float) $m->weight_kg);
            if (in_array($data['movement_type'], ['issue', 'adjustment_out'], true) && $balance + 0.0001 < (float) $data['weight_kg']) {
                throw ValidationException::withMessages(['weight_kg' => 'Not enough recorded yarn for this owner and component.']);
            }
            YarnMovement::create($data);
            if ($data['component'] === 'warp' && $data['owner_type'] === 'customer'
                && ($data['destination_type'] ?? 'factory_store') === 'sizing'
                && in_array($data['movement_type'], ['receipt', 'issue'], true)) {
                if ((float) ($data['bags'] ?? 0) <= 0 || blank($data['yarn_count'] ?? null)
                    || ! YarnQuality::where('name', $data['yarn_count'])->exists()) {
                    throw ValidationException::withMessages(['yarn_count' => 'For customer warp sent to sizing, enter the bag quantity and choose an existing Yarn Quality name exactly.']);
                }
                BagTransaction::create([
                    'transaction_type' => 'customer_sizing_receipt',
                    'reference_no' => $data['reference_no'] ?? null,
                    'quality' => $data['yarn_count'], 'quantity_bags' => $data['bags'],
                    'source' => $order->party_name ?? 'Customer',
                    'destination' => $data['destination_name'] ?? 'Sizing Contractor',
                    'party_name' => $order->party_name,
                    'transaction_date' => $data['movement_date'],
                    'notes' => $data['notes'] ?? null,
                    'owner_type' => 'customer', 'work_order_id' => $order->id,
                    'net_weight_kg' => $data['weight_kg'],
                ]);
            }
        });

        return back()->with('success', 'Yarn movement recorded in the order material khata.');
    }

    public function beam(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $data = $request->validate([
            'sizing_beam_id' => 'required|exists:sizing_beams,id',
            'action' => 'required|in:mount,unmount,settle_reassign,return_customer',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'loom_no' => ['nullable', 'string', Rule::in(config('loomflow.looms'))],
            'event_date' => 'required|date',
            'initial_net_weight_kg' => 'nullable|numeric|gt:0',
            'remaining_weight_kg' => 'nullable|numeric|gt:0',
            'agreed_settlement' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        DB::transaction(function () use ($data, $ledgerPostingService): void {
            $beam = SizingBeam::whereKey($data['sizing_beam_id'])->lockForUpdate()->firstOrFail();
            if ((float) $beam->remaining_length_meters <= 0 || $beam->status === 'returned_to_customer') {
                throw ValidationException::withMessages(['sizing_beam_id' => 'An exhausted beam cannot be transferred.']);
            }
            $fromOrder = $beam->work_order_id;
            $fromLoom = $beam->assigned_loom_no;
            $toOrder = $data['work_order_id'] ?? $fromOrder;
            $toLoom = $data['loom_no'] ?? null;
            $predicted = null;
            $weight = null;
            $settlement = null;
            $oldOrder = null;
            if (isset($data['initial_net_weight_kg'])) {
                $beam->initial_net_weight_kg = $data['initial_net_weight_kg'];
            }
            if ($data['action'] === 'unmount') {
                $toOrder = $fromOrder;
                $toLoom = null;
                $beam->assigned_loom_no = null;
                $beam->status = 'ready_in_store';
            } elseif ($data['action'] === 'return_customer') {
                $returningOrder = $fromOrder ? WorkOrder::findOrFail($fromOrder) : null;
                if (! $returningOrder || $returningOrder->type !== 'job' || ! in_array($returningOrder->warp_supply, ['customer', 'mixed'], true)) {
                    throw ValidationException::withMessages(['action' => 'Only a customer-supplied job-work beam can be returned to a customer.']);
                }
                $toOrder = null;
                $toLoom = null;
                $beam->assigned_loom_no = null;
                $beam->work_order_id = null;
                $beam->status = 'returned_to_customer';
            } else {
                if (! $toOrder || ! $toLoom) {
                    throw ValidationException::withMessages(['loom_no' => 'Choose the destination order and loom.']);
                }
                $targetOrder = WorkOrder::findOrFail($toOrder);
                $allocation = LoomAssignment::where('work_order_id', $targetOrder->id)->where('loom_no', $toLoom)
                    ->where('start_date', '<=', $data['event_date'])->where(function ($q) use ($data): void {
                        $q->whereNull('end_date')->orWhere('end_date', '>=', $data['event_date']);
                    })->first();
                if (! $allocation) {
                    throw ValidationException::withMessages(['loom_no' => 'Allocate this loom to the order for the selected date first.']);
                }
                if ($beam->fabric_stock_id && (int) $beam->fabric_stock_id !== (int) $targetOrder->fabric_stock_id) {
                    throw ValidationException::withMessages(['work_order_id' => 'Beam and order must have the same fabric quality.']);
                }
                if ($fromOrder && (int) $fromOrder !== (int) $toOrder && $data['action'] !== 'settle_reassign') {
                    throw ValidationException::withMessages(['action' => 'A beam stays with its original contract. Use Settle & Reassign.']);
                }
                if ($data['action'] === 'settle_reassign') {
                    if (! $fromOrder || (int) $fromOrder === (int) $toOrder) {
                        throw ValidationException::withMessages(['work_order_id' => 'Choose a different contract to settle and reassign.']);
                    }
                    $oldOrder = WorkOrder::with(['contract', 'parta'])->findOrFail($fromOrder);
                    if ($oldOrder->type !== 'job' || $oldOrder->warp_supply === 'factory') {
                        throw ValidationException::withMessages(['work_order_id' => 'Customer yarn settlement is only available for customer-supplied warp.']);
                    }
                    $weight = isset($data['remaining_weight_kg']) ? (float) $data['remaining_weight_kg'] :
                        ((float) $beam->total_length_meters > 0 && $beam->initial_net_weight_kg ?
                            (float) $beam->initial_net_weight_kg * (float) $beam->remaining_length_meters / (float) $beam->total_length_meters : null);
                    $rate = (float) ($oldOrder->contract?->yarn_warp_rate ?? $oldOrder->parta?->warp_rate_per_lb ?? 0);
                    if ($weight !== null && $rate > 0) {
                        $predicted = round($weight * 2.2046226218 * $rate, 2);
                    }
                    if (! isset($data['agreed_settlement']) && $predicted === null) {
                        throw ValidationException::withMessages(['agreed_settlement' => 'Enter the agreed settlement amount, or provide beam weight and a contract yarn rate to calculate it.']);
                    }
                    $settlement = round((float) ($data['agreed_settlement'] ?? $predicted), 2);
                }
                $occupied = SizingBeam::where('assigned_loom_no', $toLoom)->where('status', 'mounted_on_loom')->whereKeyNot($beam->id)->exists();
                if ($occupied) {
                    throw ValidationException::withMessages(['loom_no' => 'Destination loom already has another mounted beam.']);
                }
                $beam->assigned_loom_no = $toLoom;
                $beam->mounted_date = $data['event_date'];
                $beam->work_order_id = $toOrder;
                $beam->fabric_stock_id = $targetOrder->fabric_stock_id;
                $beam->status = 'mounted_on_loom';
            }
            $beam->save();
            $beamEvent = BeamEvent::create([
                'sizing_beam_id' => $beam->id, 'from_work_order_id' => $fromOrder,
                'to_work_order_id' => $toOrder, 'from_loom' => $fromLoom, 'to_loom' => $toLoom,
                'action' => $data['action'], 'remaining_length_meters' => $beam->remaining_length_meters,
                'estimated_weight_kg' => $weight, 'predicted_settlement' => $predicted,
                'agreed_settlement' => $data['action'] === 'settle_reassign' ? ($data['agreed_settlement'] ?? $predicted) : null,
                'event_date' => $data['event_date'], 'notes' => $data['notes'] ?? null,
            ]);
            if ($settlement !== null && $settlement > 0 && $oldOrder !== null) {
                $this->postCustomerSettlement(
                    $ledgerPostingService,
                    'beam_settlement',
                    $beamEvent->id,
                    $oldOrder->party_name,
                    $data['event_date'],
                    'BEAM-'.$beamEvent->id,
                    'Customer yarn settlement for beam '.$beam->beam_no,
                    $settlement,
                    $data['notes'] ?? null,
                    $oldOrder->customer_id,
                );
            }
        });

        return back()->with('success', 'Beam action saved with its remaining length and settlement history.');
    }

    /**
     * Post a customer credit and matching factory settlement debit.
     */
    private function postCustomerSettlement(
        LedgerPostingService $ledgerPostingService,
        string $sourceType,
        int $sourceId,
        string $customerName,
        string $date,
        string $voucher,
        string $description,
        float $amount,
        ?string $notes,
        ?int $customerId,
    ): void {
        $ledgerPostingService->post($sourceType, $sourceId, [
            [
                'role' => 'customer', 'customer_id' => $customerId, 'party_name' => $customerName, 'party_type' => 'fabric_buyer',
                'transaction_date' => $date, 'voucher_no' => $voucher, 'description' => $description,
                'debit' => 0, 'credit' => $amount, 'notes' => $notes,
            ],
            [
                'role' => 'settlement_expense', 'party_name' => 'Customer Material Settlements', 'party_type' => 'expense',
                'transaction_date' => $date, 'voucher_no' => $voucher, 'description' => $description,
                'debit' => $amount, 'credit' => 0, 'notes' => $notes,
            ],
        ]);
    }

    public function close(Request $request, WorkOrder $order): RedirectResponse
    {
        $data = $request->validate([
            'closed_on' => 'required|date', 'closure_reason' => 'nullable|string|max:1000',
        ]);
        DB::transaction(function () use ($order, $data): void {
            $order = WorkOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->status === 'closed') {
                throw ValidationException::withMessages(['closed_on' => 'This order is already closed.']);
            }
            if ($order->type === 'job' && $order->target_meters !== null && (float) $order->accepted_meters + 0.01 < (float) $order->target_meters && blank($data['closure_reason'] ?? null)) {
                throw ValidationException::withMessages(['closure_reason' => 'Enter a reason when closing below the agreed accepted quantity.']);
            }
            if (ThaanRoll::where('work_order_id', $order->id)->whereIn('status', ['returned_hold', 'returned_ready'])->exists()) {
                throw ValidationException::withMessages(['closed_on' => 'Decide what to do with all returned Thaans before closing.']);
            }
            $customerYarnBalances = YarnMovement::where('work_order_id', $order->id)
                ->where('owner_type', 'customer')
                ->selectRaw('component, SUM(CASE WHEN movement_type IN (?, ?, ?) THEN weight_kg ELSE -weight_kg END) as balance_kg', ['receipt', 'return', 'adjustment_in'])
                ->groupBy('component')->get()
                ->filter(fn ($movement): bool => (float) $movement->balance_kg > 0.001);
            if ($customerYarnBalances->isNotEmpty()) {
                throw ValidationException::withMessages(['closed_on' => 'Customer yarn is still in factory custody. Record its return or settlement in the yarn khata before closing.']);
            }
            $activeOrderLooms = LoomAssignment::where('work_order_id', $order->id)->whereNull('end_date')->pluck('loom_no');
            $remainingBeams = SizingBeam::where('remaining_length_meters', '>', 0)
                ->where(function ($query) use ($order, $activeOrderLooms): void {
                    $query->where('work_order_id', $order->id)
                        ->orWhere(function ($mountedQuery) use ($activeOrderLooms): void {
                            $mountedQuery->where('status', 'mounted_on_loom')->whereIn('assigned_loom_no', $activeOrderLooms);
                        });
                })
                ->where(function ($query) use ($order): void {
                    $query->where('status', 'mounted_on_loom');
                    if ($order->type === 'job' && in_array($order->warp_supply, ['customer', 'mixed'], true)) {
                        $query->orWhere('status', 'ready_in_store');
                    }
                })->exists();
            if ($remainingBeams) {
                throw ValidationException::withMessages(['closed_on' => 'A beam is still mounted or customer-owned under this order. Unmount it and return it or settle and reassign it before closing.']);
            }
            foreach (LoomAssignment::where('work_order_id', $order->id)->whereNull('end_date')->lockForUpdate()->get() as $assignment) {
                if ($data['closed_on'] < $assignment->start_date) {
                    throw ValidationException::withMessages(['closed_on' => 'Closing date cannot precede a loom allocation.']);
                }
                $assignment->end_date = $data['closed_on'];
                $assignment->save();
            }
            $order->update(['status' => 'closed', 'closed_on' => $data['closed_on'], 'closure_reason' => $data['closure_reason'] ?? null]);
            if ($order->contract) {
                $order->contract->update(['status' => 'completed']);
            }
        });

        return back()->with('success', 'Order closed; all earlier production and settlement history is preserved.');
    }

    public function closing(WorkOrder $order): View
    {
        $order->load(['fabricStock', 'parta', 'contract', 'customer']);
        $kachis = $order->kachis()->with('pakki')->get();
        $pakkiRows = PakkiParchi::with('invoice.allocations')
            ->whereHas('kachi', fn ($query) => $query->where('work_order_id', $order->id))
            ->get();
        $invoiceIds = $pakkiRows->pluck('invoice_id')->filter()->unique();
        $invoices = Invoice::with('allocations')->whereIn('id', $invoiceIds)->get();
        $unpaidInvoices = $invoices->map(fn (Invoice $invoice): array => [
            'invoice' => $invoice,
            'paid' => (float) $invoice->allocations->sum('amount'),
            'due' => max(0, (float) $invoice->total - (float) $invoice->allocations->sum('amount')),
        ]);
        $yarnBalances = YarnMovement::where('work_order_id', $order->id)
            ->selectRaw('component, owner_type, SUM(CASE WHEN movement_type IN (?, ?, ?) THEN weight_kg ELSE -weight_kg END) as balance_kg', ['receipt', 'return', 'adjustment_in'])
            ->groupBy('component', 'owner_type')->orderBy('component')->get();
        $activeOrderLooms = LoomAssignment::where('work_order_id', $order->id)->whereNull('end_date')->pluck('loom_no');
        $beams = SizingBeam::where(function ($query) use ($order, $activeOrderLooms): void {
            $query->where('work_order_id', $order->id)
                ->orWhere(function ($mountedQuery) use ($activeOrderLooms): void {
                    $mountedQuery->where('status', 'mounted_on_loom')->whereIn('assigned_loom_no', $activeOrderLooms);
                });
        })
            ->where('remaining_length_meters', '>', 0)
            ->where(function ($query) use ($order): void {
                $query->where('status', 'mounted_on_loom');
                if ($order->type === 'job' && in_array($order->warp_supply, ['customer', 'mixed'], true)) {
                    $query->orWhere('status', 'ready_in_store');
                }
            })->orderBy('beam_no')->get();
        $returnedThaans = ThaanRoll::where('work_order_id', $order->id)
            ->whereIn('status', ['returned_hold', 'returned_ready'])->orderBy('roll_no')->get();
        $summary = [
            'provisional_meters' => (float) Production::where('work_order_id', $order->id)->sum('net_meters_produced'),
            'checked_meters' => (float) ThaanRoll::where('work_order_id', $order->id)->sum('length_meters'),
            'dispatched_meters' => (float) $kachis->sum('dispatched_meters'),
            'accepted_meters' => (float) $pakkiRows->sum('accepted_meters'),
            'rejected_meters' => (float) $pakkiRows->sum('rejected_meters'),
            'billed_amount' => (float) $pakkiRows->sum('bill_amount'),
            'unpaid_amount' => (float) $unpaidInvoices->sum('due'),
        ];
        $customerYarnOutstanding = $yarnBalances->contains(fn ($movement): bool => $movement->owner_type === 'customer' && (float) $movement->balance_kg > 0.001);
        $beamOutstanding = $beams->isNotEmpty();
        $canClose = $order->status !== 'closed' && ! $customerYarnOutstanding && ! $beamOutstanding && $returnedThaans->isEmpty();

        return view('factory.closing', compact(
            'order', 'summary', 'unpaidInvoices', 'yarnBalances', 'beams', 'returnedThaans', 'canClose'
        ));
    }
}
