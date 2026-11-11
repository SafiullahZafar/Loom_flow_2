<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FabricStock;
use App\Models\KachiParchi;
use App\Models\PartaRecord;
use App\Models\Sale;
use App\Models\SalesContract;
use App\Models\ThaanRoll;
use App\Models\WorkOrder;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(): View
    {
        $sales = Sale::with('contract')->latest('sale_date')->latest('id')->limit(5)->get();
        $contracts = SalesContract::latest('start_date')->latest('id')->limit(5)->get();
        $page = 'dashboard';

        return $this->renderPage($page, $sales, $contracts);
    }

    public function slips(): View
    {
        return $this->renderPage(
            'slips',
            Sale::with('contract')->latest('sale_date')->latest('id')->paginate(15),
            collect()
        );
    }

    public function contracts(): View
    {
        return $this->renderPage(
            'contracts',
            collect(),
            SalesContract::latest('start_date')->latest('id')->paginate(15)
        );
    }

    /**
     * @param  Collection<int, Sale>|LengthAwarePaginator  $sales
     * @param  Collection<int, SalesContract>|LengthAwarePaginator  $contracts
     */
    private function renderPage(
        string $page,
        Collection|LengthAwarePaginator $sales,
        Collection|LengthAwarePaginator $contracts
    ): View {
        $customers = collect();
        $fabrics = collect();
        $partas = collect();
        $contractOrderNumbers = collect();
        $jobOrders = collect();
        $rolls = collect();
        $kachis = collect();
        $pendingKachis = collect();
        $rejectedKachis = collect();
        if (in_array($page, ['dashboard', 'contracts'], true)) {
            $customers = Customer::where('status', 'active')->orderBy('name')->get();
            $contractOrderNumbers = WorkOrder::whereNotNull('sales_contract_id')
                ->pluck('order_no', 'sales_contract_id');
        }

        if ($page === 'contracts') {
            $fabrics = FabricStock::orderBy('quality_name')->get();
            $partas = PartaRecord::whereIn('fabric_stock_id', $fabrics->modelKeys())
                ->orderByDesc('version')->get()->unique('fabric_stock_id')->keyBy('fabric_stock_id');
        }

        if ($page === 'dashboard') {
            $jobOrders = WorkOrder::with(['fabricStock', 'parta', 'customer', 'contract'])
                ->where('type', 'job')->whereIn('status', ['active', 'fulfilled'])->orderBy('order_no')->get();
            $rolls = ThaanRoll::with('order')->whereIn('status', ['ready', 'returned_ready', 'returned_hold', 'dispatched'])->orderByDesc('id')->get();
            $kachis = KachiParchi::with(['order.fabricStock', 'order.customer', 'items.roll', 'pakki', 'returns'])->latest('id')->paginate(30);
            $pendingKachis = KachiParchi::with('order')->whereDoesntHave('pakki')->orderByDesc('id')->get();
            $rejectedKachis = KachiParchi::with(['pakki', 'items.roll'])
                ->whereHas('pakki', fn ($query) => $query->where('rejected_meters', '>', 0))->orderByDesc('id')->get();
        }

        // Metrics
        $totalSalesCount = Sale::count();
        $totalRevenue = Sale::sum('net_total');
        $totalMetersSold = Sale::sum('meters_sold');
        $contractSalesRevenue = Sale::where('sale_type', 'contract')->sum('net_total');
        $openMarketRevenue = Sale::where('sale_type', 'open_market')->sum('net_total');

        $activeContractsCount = SalesContract::where('status', 'active')->count();
        $totalContractMetersAgreed = SalesContract::sum('total_meters');
        $totalContractMetersDelivered = SalesContract::sum('delivered_meters');

        return view('sales.index', compact(
            'page',
            'sales',
            'contracts',
            'customers',
            'fabrics',
            'partas',
            'contractOrderNumbers',
            'jobOrders',
            'rolls',
            'kachis',
            'pendingKachis',
            'rejectedKachis',
            'totalSalesCount',
            'totalRevenue',
            'totalMetersSold',
            'contractSalesRevenue',
            'openMarketRevenue',
            'activeContractsCount',
            'totalContractMetersAgreed',
            'totalContractMetersDelivered'
        ));
    }

    public function storeSale(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'slip_no' => 'required|string|max:100|unique:sales,slip_no',
            'sale_type' => 'required|in:open_market,contract',
            'sales_contract_id' => 'nullable|required_if:sale_type,contract|exists:sales_contracts,id',
            'fabric_stock_id' => 'required|exists:fabric_stocks,id',
            'customer_id' => 'required|exists:customers,id',
            'meters_sold' => 'required|numeric|min:1',
            'rolls_sold' => 'required|integer|min:1',
            'rate_per_meter' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_type' => 'required|in:cash,credit,bank_transfer',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'sale_date' => 'required|date',
            'vehicle_no' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $sale = DB::transaction(function () use ($validated, $ledgerPostingService): Sale|false {
            $fabric = FabricStock::whereKey($validated['fabric_stock_id'])->lockForUpdate()->firstOrFail();
            $customer = Customer::whereKey($validated['customer_id'])->lockForUpdate()->firstOrFail();
            $contract = null;

            if ($validated['sale_type'] === 'contract') {
                if (WorkOrder::where('sales_contract_id', $validated['sales_contract_id'])->exists()) {
                    return false; // Managed job-work contracts dispatch only through Kachi and bill on Pakki.
                }
                $contract = SalesContract::whereKey($validated['sales_contract_id'])->lockForUpdate()->firstOrFail();
                if ($contract->customer_id && (int) $contract->customer_id !== (int) $customer->id) {
                    throw ValidationException::withMessages(['customer_id' => 'Choose the customer linked to the selected contract.']);
                }
                if ((int) $contract->fabric_stock_id !== (int) $fabric->id) {
                    return false;
                }

                if ($contract->remaining_meters < $validated['meters_sold']) {
                    return false;
                }

                if ($fabric->current_stock_meters < $validated['meters_sold']
                    || $fabric->committed_contract_meters < $validated['meters_sold']) {
                    return false;
                }

                $contract->customer_id = $customer->id;
                $contract->buyer_name = $customer->name;
                $contract->buyer_phone = $customer->phone;
                $validated['buyer_name'] = $customer->name;
                $validated['buyer_phone'] = $customer->phone;
                $validated['rate_per_meter'] = $contract->rate_per_meter;
                $contract->delivered_meters += $validated['meters_sold'];
                $contract->remaining_meters -= $validated['meters_sold'];
                if ($contract->remaining_meters <= 0) {
                    $contract->status = 'completed';
                }
                $contract->save();

                $fabric->committed_contract_meters -= $validated['meters_sold'];
            } else {
                $trackedReady = (float) ThaanRoll::where('fabric_stock_id', $fabric->id)
                    ->whereIn('status', ['ready', 'returned_ready'])->sum('length_meters');
                $legacyAvailable = max(0, (float) $fabric->current_stock_meters - $trackedReady);
                if ($legacyAvailable + 0.001 < (float) $validated['meters_sold'] || $fabric->available_open_meters < $validated['meters_sold']) {
                    return false;
                }

                $validated['sales_contract_id'] = null;
                $validated['buyer_name'] = $customer->name;
                $validated['buyer_phone'] = $customer->phone;
                $fabric->available_open_meters -= $validated['meters_sold'];
            }

            $validated['customer_id'] = $customer->id;
            $validated['fabric_quality'] = $fabric->quality_name;
            $validated['subtotal'] = $validated['meters_sold'] * $validated['rate_per_meter'];
            $validated['discount'] = $validated['discount'] ?? 0;
            $validated['net_total'] = max(0, $validated['subtotal'] - $validated['discount']);
            $validated['cost_per_meter'] = (float) $fabric->unit_cost_meter;
            $validated['cost_of_goods_sold'] = round((float) $validated['meters_sold'] * $validated['cost_per_meter'], 2);

            $fabric->current_stock_meters -= $validated['meters_sold'];
            $fabric->total_rolls_in_stock = max(0, $fabric->total_rolls_in_stock - $validated['rolls_sold']);
            $fabric->save();

            $sale = Sale::create($validated);
            $isPaid = $sale->payment_status === 'paid';
            $costOfGoodsSold = (float) $sale->cost_of_goods_sold;
            $ledgerLines = [
                [
                    'role' => 'buyer',
                    'party_name' => $sale->buyer_name,
                    'customer_id' => $sale->customer_id,
                    'party_type' => 'fabric_buyer',
                    'transaction_date' => $sale->sale_date,
                    'voucher_no' => $sale->slip_no,
                    'description' => "Fabric Sale: {$sale->meters_sold}m {$sale->fabric_quality} ({$sale->sale_type})",
                    'debit' => $isPaid ? 0 : $sale->net_total,
                    'credit' => 0,
                    'notes' => "Payment Type: {$sale->payment_type}, Status: {$sale->payment_status}",
                ],
                ...($isPaid ? [[
                    'role' => 'cash',
                    'party_name' => 'Cash / Bank Clearing',
                    'party_type' => 'cash_bank',
                    'transaction_date' => $sale->sale_date,
                    'voucher_no' => $sale->slip_no,
                    'description' => "Payment received for fabric sale {$sale->slip_no}",
                    'debit' => $sale->net_total,
                    'credit' => 0,
                    'notes' => "Payment Type: {$sale->payment_type}",
                ]] : []),
                [
                    'role' => 'revenue',
                    'party_name' => 'Fabric Sales Revenue',
                    'party_type' => 'income',
                    'transaction_date' => $sale->sale_date,
                    'voucher_no' => $sale->slip_no,
                    'description' => "Fabric Sale: {$sale->meters_sold}m {$sale->fabric_quality} ({$sale->sale_type})",
                    'debit' => 0,
                    'credit' => $sale->net_total,
                    'notes' => "Payment Type: {$sale->payment_type}, Status: {$sale->payment_status}",
                ],
            ];
            if ($costOfGoodsSold > 0) {
                $ledgerLines[] = [
                    'role' => 'cost_of_goods_sold',
                    'party_name' => 'Cost of Goods Sold',
                    'party_type' => 'expense',
                    'transaction_date' => $sale->sale_date,
                    'voucher_no' => $sale->slip_no,
                    'description' => 'Cost of fabric sold: '.$sale->fabric_quality,
                    'debit' => $costOfGoodsSold,
                    'credit' => 0,
                    'notes' => null,
                ];
                $ledgerLines[] = [
                    'role' => 'inventory',
                    'party_name' => 'Finished Fabric Inventory',
                    'party_type' => 'inventory',
                    'transaction_date' => $sale->sale_date,
                    'voucher_no' => $sale->slip_no,
                    'description' => 'Finished fabric inventory released to sale: '.$sale->fabric_quality,
                    'debit' => 0,
                    'credit' => $costOfGoodsSold,
                    'notes' => null,
                ];
            }
            $ledgerPostingService->post('sale', $sale->id, $ledgerLines);

            return $sale;
        });

        if (! $sale) {
            return back()->withInput()->with('error', 'The selected fabric quality has insufficient unreserved stock, or it does not match the selected contract.');
        }

        return redirect()->route('sales.index')
            ->with('success', "Sale Slip {$sale->slip_no} generated successfully! Total: Rs. ".number_format($sale->net_total, 2));
    }

    public function storeContract(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contract_no' => 'required|string|max:100|unique:sales_contracts,contract_no',
            'party_contract_no' => 'nullable|string|max:80',
            'contract_type' => 'required|in:Conversion,Grey Sale',
            'loom_type' => 'nullable|string|max:40',
            'buyer_code' => 'nullable|string|max:40',
            'customer_id' => 'required|exists:customers,id',
            'buyer_gst' => 'nullable|string|max:60',
            'fabric_stock_id' => 'required|exists:fabric_stocks,id',
            'quality_code' => 'nullable|string|max:40',
            'contract_quality' => 'nullable|string|max:120',
            'contract_quality_width' => 'nullable|numeric|gt:0|max:100000',
            'panna' => 'nullable|numeric|gt:0|max:100000',
            'total_meters' => 'required|numeric|min:100',
            'read' => 'required|numeric|gt:0|max:100000',
            'pick' => 'required|numeric|gt:0|max:100000',
            'warp_count' => 'required|numeric|gt:0|max:100000',
            'weft_count' => 'required|numeric|gt:0|max:100000',
            'width' => 'required|numeric|gt:0|max:100000',
            'yarn_warp_rate' => 'required|numeric|min:0|max:100000000',
            'yarn_weft_rate' => 'required|numeric|min:0|max:100000000',
            'conversion_rate' => 'required|numeric|min:0|max:100000000',
            'broker_code' => 'nullable|string|max:40',
            'broker_name' => 'nullable|string|max:120',
            'broker_commission_per_meter' => 'nullable|numeric|min:0|max:100000000',
            'kp_percentage' => 'nullable|numeric|between:0,100',
            'kp_days' => 'nullable|integer|between:0,3650',
            'pp_percentage' => 'nullable|numeric|between:0,100',
            'pp_days' => 'nullable|integer|between:0,3650',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'delivery_instructions' => 'nullable|string|max:2000',
            'payment_instructions' => 'nullable|string|max:2000',
            'quality_instructions' => 'nullable|string|max:2000',
            'other_instructions' => 'nullable|string|max:2000',
            'tax_enabled' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric|between:0,1',
            'notes' => 'nullable|string|max:2000',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $validated['buyer_name'] = $customer->name;
        $validated['buyer_phone'] = $customer->phone;
        $fabric = FabricStock::findOrFail($validated['fabric_stock_id']);
        $validated['fabric_quality'] = $fabric->quality_name;
        $validated['tax_enabled'] = (bool) ($validated['tax_enabled'] ?? false);
        $calculatedValues = SalesContract::calculateContractValues($validated);
        $validated = array_merge($validated, $calculatedValues);
        $validated['delivered_meters'] = 0;
        $validated['remaining_meters'] = $validated['total_meters'];
        $validated['status'] = 'active';
        $validated['contract_type'] = $validated['contract_type'] ?? 'Conversion';
        // Tax is an opt-in, user-entered estimate (never an automatic 17%).
        unset($validated['tax_rate']);
        $validated['loom_type'] = $validated['loom_type'] ?? null;
        $validated['broker_commission_per_meter'] = $validated['broker_commission_per_meter'] ?? 0;
        $validated['kp_percentage'] = $validated['kp_percentage'] ?? 0;
        $validated['kp_days'] = $validated['kp_days'] ?? 0;
        $validated['pp_percentage'] = $validated['pp_percentage'] ?? 0;
        $validated['pp_days'] = $validated['pp_days'] ?? 0;
        $validated['panna'] = $validated['panna'] ?? 1;

        $contract = DB::transaction(function () use ($validated): SalesContract {
            $fabric = FabricStock::whereKey($validated['fabric_stock_id'])->lockForUpdate()->firstOrFail();
            $contract = SalesContract::create($validated);
            $activeContractMeters = SalesContract::where('fabric_stock_id', $fabric->id)
                ->where('status', 'active')->where('contract_type', 'Grey Sale')
                ->sum('remaining_meters');
            $fabric->committed_contract_meters = min(
                (float) $fabric->current_stock_meters,
                (float) $activeContractMeters
            );
            $customerStock = (float) ThaanRoll::where('fabric_stock_id', $fabric->id)
                ->where('owner_type', 'customer')->whereIn('status', ['ready', 'returned_ready'])->sum('length_meters');
            $fabric->available_open_meters = max(
                0,
                $fabric->current_stock_meters - $customerStock - $fabric->committed_contract_meters
            );
            $fabric->save();

            return $contract;
        });

        return redirect()->route('sales.contracts')
            ->with('success', "Sales Contract {$contract->contract_no} for {$contract->buyer_name} ({$contract->total_meters}m) registered successfully!");
    }
}
