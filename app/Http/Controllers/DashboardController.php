<?php

namespace App\Http\Controllers;

use App\Models\BagPurchase;
use App\Models\KachiParchi;
use App\Models\WorkOrder;
use App\Models\LoomAssignment;
use App\Models\ThaanRoll;
use App\Models\BagTransaction;
use App\Models\Expense;
use App\Models\FabricStock;
use App\Models\FabricStockMovement;
use App\Models\Part;
use App\Models\Production;
use App\Models\Sale;
use App\Models\SalesContract;
use App\Models\SizingBeam;
use App\Models\SizingSlip;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Bag Stock summary
        $totalPurchasedBags = BagPurchase::sum('total_bags');

        $bagsToFactory = BagPurchase::sum('bags_to_factory');
        $bagsIssuedToSizing = BagTransaction::where('transaction_type', 'issue_to_sizing')->sum('quantity_bags');
        $bagsReturnedFromSizing = BagTransaction::where('transaction_type', 'return_from_sizing')->sum('quantity_bags');
        $factoryBagStock = max(0, $bagsToFactory - $bagsIssuedToSizing + $bagsReturnedFromSizing);

        $bagsToSizingDirect = BagPurchase::sum('bags_to_sizing');
        $bagsUsedInSizing = SizingSlip::sum('bags_used');
        $sizingBagsInHand = max(0, ($bagsToSizingDirect + $bagsIssuedToSizing) - ($bagsUsedInSizing + $bagsReturnedFromSizing));

        // 2. Sizing & Beams summary
        $activeBeamsCount = SizingBeam::where('status', 'mounted_on_loom')->count();
        $readyBeamsCount = SizingBeam::where('status', 'ready_in_store')->count();
        $totalBeamMetersRemaining = SizingBeam::where('status', 'mounted_on_loom')->sum('remaining_length_meters');

        // 3. Parts summary
        $totalPartsStock = Part::sum('stock_quantity');
        $lowStockPartsCount = Part::whereColumn('stock_quantity', '<=', 'min_alert_threshold')->count();

        // 4. Fabric Stock summary
        $totalFabricStockMeters = FabricStock::sum('current_stock_meters');
        $openFabricMeters = FabricStock::sum('available_open_meters');
        $committedFabricMeters = FabricStock::sum('committed_contract_meters');

        // 5. Sales & Contracts
        $activeContractsCount = SalesContract::where('status', 'active')->count();
        $totalSalesAmount = Sale::sum('net_total');
        $monthExpenseTotal = Expense::whereBetween('expense_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->sum('amount');

        // 6. Production & Efficiency
        $todayProductionMeters = Production::whereDate('production_date', today())->sum('net_meters_produced');
        $totalProductionMeters = Production::sum('net_meters_produced');
        $avgEfficiency = Production::avg('efficiency_percentage') ?? 0;
        $totalLeakageMeters = Production::sum('leakage_waste_meters');

        $runningLooms = SizingBeam::with('fabricStock')
            ->where('status', 'mounted_on_loom')->orderBy('assigned_loom_no')->get();
        $activeLoomAssignments = LoomAssignment::with('order.fabricStock')->whereNull('end_date')->orderBy('loom_no')->get();
        $activeContractRows = SalesContract::with('fabricStock')->where('status', 'active')->orderBy('end_date')->get();
        $contractDispatchedMeters = KachiParchi::join('work_orders', 'work_orders.id', '=', 'kachi_parchis.work_order_id')
            ->whereNotNull('work_orders.sales_contract_id')
            ->selectRaw('work_orders.sales_contract_id, SUM(kachi_parchis.dispatched_meters) as dispatched_meters')
            ->groupBy('work_orders.sales_contract_id')->pluck('dispatched_meters', 'sales_contract_id');
        $contractProductionMeters = Production::join('work_orders', 'work_orders.id', '=', 'productions.work_order_id')
            ->whereNotNull('work_orders.sales_contract_id')
            ->selectRaw('work_orders.sales_contract_id, SUM(productions.net_meters_produced) as produced_meters')
            ->groupBy('work_orders.sales_contract_id')->pluck('produced_meters', 'sales_contract_id');
        $jobWorkBalanceMeters = (float) ThaanRoll::where('owner_type', 'customer')
            ->whereIn('status', ['ready', 'returned_ready'])->sum('length_meters');
        $todayCheckedMeters = (float) ThaanRoll::whereDate('checked_on', today())->sum('length_meters');
        $totalAcceptedMeters = (float) WorkOrder::where('type', 'job')->sum('accepted_meters');

        // Pending Pakki dispatches awaiting customer acceptance
        $pendingPakkis = KachiParchi::with('order.fabricStock')->whereDoesntHave('pakki')->latest('id')->take(4)->get();
        $pendingPakkiCount = KachiParchi::whereDoesntHave('pakki')->count();
        $pendingPakkiMeters = (float) KachiParchi::whereDoesntHave('pakki')->sum('dispatched_meters');

        // Recent items for live feed
        $recentPurchases = BagPurchase::latest()->take(3)->get();
        $recentProductions = Production::latest()->take(3)->get();
        $recentSales = Sale::latest()->take(3)->get();

        return view('dashboard.hub', compact(
            'totalPurchasedBags',
            'factoryBagStock',
            'sizingBagsInHand',
            'activeBeamsCount',
            'readyBeamsCount',
            'totalBeamMetersRemaining',
            'totalPartsStock',
            'lowStockPartsCount',
            'totalFabricStockMeters',
            'openFabricMeters',
            'committedFabricMeters',
            'activeContractsCount',
            'totalSalesAmount',
            'monthExpenseTotal',
            'todayProductionMeters',
            'totalProductionMeters',
            'avgEfficiency',
            'totalLeakageMeters',
            'runningLooms',
            'activeLoomAssignments',
            'todayCheckedMeters',
            'totalAcceptedMeters',
            'activeContractRows',
            'contractDispatchedMeters',
            'contractProductionMeters',
            'jobWorkBalanceMeters',
            'pendingPakkis',
            'pendingPakkiCount',
            'pendingPakkiMeters',
            'recentPurchases',
            'recentProductions',
            'recentSales'
        ));
    }
}
