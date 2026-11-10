<?php

namespace App\Http\Controllers;

use App\Models\FabricStock;
use App\Models\FabricStockMovement;
use App\Models\LoomAssignment;
use App\Models\Production;
use App\Models\SizingBeam;
use App\Models\WorkOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductionController extends Controller
{
    public function index(): View
    {
        $productions = Production::with('beam')->latest('production_date')->latest('id')->limit(5)->get();

        return $this->renderPage('dashboard', $productions);
    }

    public function loomSummary(): View
    {
        return $this->renderPage('loom-summary', collect());
    }

    public function leakage(): View
    {
        return $this->renderPage('leakage', collect());
    }

    public function history(Request $request): View
    {
        $query = Production::with('beam')->latest('production_date')->latest('id');

        if ($request->filled('loom_no')) {
            $query->where('loom_no', $request->loom_no);
        }

        if ($request->filled('date')) {
            $query->whereDate('production_date', $request->date);
        }

        return $this->renderPage('history', $query->paginate(15)->withQueryString());
    }

    /**
     * @param  Collection<int, Production>|LengthAwarePaginator  $productions
     */
    private function renderPage(string $page, Collection|LengthAwarePaginator $productions): View
    {

        // Active Beams mounted or ready
        $activeBeams = SizingBeam::withSum('productions', 'net_meters_produced')
            ->with('sizingSlip')
            ->where('status', 'mounted_on_loom')
            ->get();
        $mountedBeams = SizingBeam::with('sizingSlip', 'fabricStock')
            ->where('status', 'mounted_on_loom')
            ->get()
            ->keyBy('assigned_loom_no');
        $beamLeakageRows = SizingBeam::with('sizingSlip')
            ->with('productions:id,sizing_beam_id,loom_no')
            ->withSum('productions', 'net_meters_produced')
            ->withSum('productions', 'beam_meters_consumed')
            ->orderByDesc('id')
            ->get();
        $sizingSlipProductionRows = $page === 'leakage'
            ? DB::table('sizing_slips')
                ->leftJoin('sizing_beams', 'sizing_beams.sizing_slip_id', '=', 'sizing_slips.id')
                ->leftJoin('productions', 'productions.sizing_beam_id', '=', 'sizing_beams.id')
                ->select([
                    'sizing_slips.id',
                    'sizing_slips.slip_no',
                    'sizing_slips.yarn_quality',
                    'sizing_slips.total_beams',
                ])
                ->selectRaw('COUNT(DISTINCT CASE WHEN productions.id IS NOT NULL THEN sizing_beams.id END) as used_beam_count')
                ->selectRaw('COUNT(DISTINCT productions.loom_no) as loom_count')
                ->selectRaw('MAX(sizing_slips.total_length_meters * sizing_slips.width_multiplier) as expected_total_meters')
                ->selectRaw('COALESCE(SUM(productions.expected_fabric_meters), 0) as expected_consumed_meters')
                ->selectRaw('COALESCE(SUM(productions.net_meters_produced), 0) as actual_woven_meters')
                ->selectRaw('COALESCE(SUM(productions.leakage_waste_meters), 0) as recorded_leakage_meters')
                ->groupBy('sizing_slips.id', 'sizing_slips.slip_no', 'sizing_slips.yarn_quality', 'sizing_slips.total_beams')
                ->orderByDesc('sizing_slips.id')
                ->get()
            : collect();
        $fabricQualities = FabricStock::orderBy('quality_name')->get();
        $looms = config('loomflow.looms');
        $loomQualityIds = Production::query()
            ->whereIn('loom_no', $looms)
            ->whereNotNull('fabric_stock_id')
            ->orderByDesc('production_date')
            ->orderByDesc('id')
            ->get(['loom_no', 'fabric_stock_id'])
            ->unique('loom_no')
            ->mapWithKeys(fn (Production $production): array => [$production->loom_no => $production->fabric_stock_id]);
        foreach ($mountedBeams as $loomNo => $beam) {
            if ($beam->fabric_stock_id) {
                $loomQualityIds[$loomNo] = $beam->fabric_stock_id;
            }
        }
        $expectedTotal = (float) $sizingSlipProductionRows->sum('expected_total_meters');
        $actualTotal = (float) $sizingSlipProductionRows->sum('actual_woven_meters');
        $gapTotal = max(0, $expectedTotal - $actualTotal);
        $finalLeakageTotal = (float) $beamLeakageRows
            ->where('status', 'exhausted')
            ->sum(fn (SizingBeam $beam): float => max(
                0,
                ((float) $beam->total_length_meters * (float) ($beam->sizingSlip?->width_multiplier ?? 1))
                    - (float) ($beam->productions_sum_net_meters_produced ?? 0)
            ));

        // Aggregate statistics
        $totalFabricProduced = Production::sum('net_meters_produced');
        $totalBeamConsumed = Production::sum('beam_meters_consumed');
        $totalLeakageMeters = Production::sum('leakage_waste_meters');
        $totalExpectedFabricMeters = Production::sum('expected_fabric_meters');
        $overallLeakagePercentage = $totalExpectedFabricMeters > 0 ? round(($totalLeakageMeters / $totalExpectedFabricMeters) * 100, 2) : 0;
        $avgEfficiency = round(Production::avg('efficiency_percentage') ?? 87.5, 1);

        // Loom-wise performance summary
        $loomWiseStats = Production::selectRaw('loom_no, COUNT(*) as shift_count, SUM(net_meters_produced) as total_produced, SUM(beam_meters_consumed) as total_beam, SUM(expected_fabric_meters) as total_expected_fabric, SUM(leakage_waste_meters) as total_leakage, AVG(efficiency_percentage) as avg_eff')
            ->groupBy('loom_no')
            ->orderBy('loom_no')
            ->get();

        return view('production.index', compact(
            'page',
            'productions',
            'activeBeams',
            'mountedBeams',
            'beamLeakageRows',
            'sizingSlipProductionRows',
            'fabricQualities',
            'looms',
            'loomQualityIds',
            'expectedTotal',
            'actualTotal',
            'gapTotal',
            'finalLeakageTotal',
            'totalFabricProduced',
            'totalBeamConsumed',
            'totalLeakageMeters',
            'overallLeakagePercentage',
            'avgEfficiency',
            'loomWiseStats'
        ));
    }

    public function storeProduction(Request $request): RedirectResponse
    {
        if ($request->has('rolls')) {
            return redirect()->route('factory.checking')->with('error', 'Enter each checked Thaan on the Checking screen. The old bulk roll register is disabled to prevent duplicate stock and beam consumption.');
        }

        $validated = $request->validate([
            'production_date' => 'required|date',
            'shift' => 'required|in:Day,Night',
            'loom_no' => ['required', 'string', Rule::in(config('loomflow.looms'))],
            'operator_name' => 'required|string|max:150',
            'sizing_beam_id' => 'required|exists:sizing_beams,id',
            'fabric_stock_id' => 'required|exists:fabric_stocks,id',
            'start_meter' => 'required|numeric|min:0',
            'end_meter' => 'required|numeric|gt:start_meter',
            'beam_meters_consumed' => 'required|numeric|min:1',
            'picks_per_inch' => 'required|integer|min:20|max:200',
            'rpm' => 'required|integer|min:100|max:800',
            'remarks' => 'nullable|string',
        ]);

        $netMetersProduced = $validated['end_meter'] - $validated['start_meter'];
        $beamConsumed = (float) $validated['beam_meters_consumed'];

        $production = DB::transaction(function () use ($validated, $beamConsumed, $netMetersProduced) {
            $beam = SizingBeam::whereKey($validated['sizing_beam_id'])->lockForUpdate()->firstOrFail();
            $beam->loadMissing('sizingSlip');
            $assignment = LoomAssignment::where('loom_no', $validated['loom_no'])
                ->where('start_date', '<=', $validated['production_date'])
                ->where(function ($q) use ($validated): void {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $validated['production_date']);
                })->first();
            if (! $assignment) {
                throw ValidationException::withMessages(['loom_no' => 'Allocate the loom to an active order for this date first.']);
            }
            $order = WorkOrder::findOrFail($assignment->work_order_id);
            if ($beam->work_order_id && (int) $beam->work_order_id !== (int) $order->id) {
                throw ValidationException::withMessages(['sizing_beam_id' => 'This beam belongs to a different contract. Settle or transfer the beam first.']);
            }
            if ($beam->fabric_stock_id && (int) $beam->fabric_stock_id !== (int) $order->fabric_stock_id) {
                throw ValidationException::withMessages(['sizing_beam_id' => 'The mounted beam quality does not match the loom order.']);
            }
            $beam->work_order_id ??= $order->id;
            $beam->fabric_stock_id = $order->fabric_stock_id;
            $fabric = FabricStock::whereKey($order->fabric_stock_id)->lockForUpdate()->firstOrFail();
            $validated['fabric_stock_id'] = $fabric->id;
            $validated['work_order_id'] = $order->id;
            $validated['loom_assignment_id'] = $assignment->id;
            $validated['is_provisional'] = true;

            if ($beam->status !== 'mounted_on_loom' || $beam->assigned_loom_no !== $validated['loom_no']) {
                return null;
            }

            if ($beamConsumed > (float) $beam->remaining_length_meters) {
                return null;
            }

            $widthMultiplier = (float) ($beam->sizingSlip?->width_multiplier ?? 1);
            $expectedFabricMeters = round($beamConsumed * $widthMultiplier, 2);
            $leakageMeters = max(0, $expectedFabricMeters - $netMetersProduced);
            $leakagePercentage = $expectedFabricMeters > 0 ? round(($leakageMeters / $expectedFabricMeters) * 100, 2) : 0;
            $theoreticalMeters = ($validated['rpm'] * 60 * 12) / ($validated['picks_per_inch'] * 39.37);
            $efficiency = $theoreticalMeters > 0 ? min(99.9, round(($netMetersProduced / $theoreticalMeters) * 100, 2)) : 85.0;

            $validated['fabric_quality'] = $fabric->quality_name;
            $validated['net_meters_produced'] = $netMetersProduced;
            $validated['expected_fabric_meters'] = $expectedFabricMeters;
            $validated['leakage_waste_meters'] = $leakageMeters;
            $validated['leakage_percentage'] = $leakagePercentage;
            $validated['efficiency_percentage'] = $efficiency;

            $production = Production::create($validated);

            $newRemaining = max(0, $beam->remaining_length_meters - $beamConsumed);
            $beam->remaining_length_meters = $newRemaining;
            if ($newRemaining <= 0) {
                $beam->status = 'exhausted';
            }
            $beam->save();

            // Meter readings are provisional. Only office-checked Thaan lengths enter stock.
            $fabric->total_meters_produced = (float) $fabric->total_meters_produced + $netMetersProduced;
            $fabric->save();

            return $production;
        });

        if (! $production) {
            return back()->withInput()->with('error', 'Select a beam mounted on the chosen loom and enter consumption within its remaining meters.');
        }

        $leakageMeters = (float) $production->leakage_waste_meters;
        $leakagePercentage = (float) $production->leakage_percentage;
        $efficiency = (float) $production->efficiency_percentage;

        return redirect()->route('production.index')
            ->with('success', "Production recorded for {$production->loom_no}! Produced: {$netMetersProduced}m | Consumed: {$beamConsumed}m | Leakage: {$leakageMeters}m ({$leakagePercentage}%) | Efficiency: {$efficiency}% (pending office checking)");
    }

    private function storeRollRegister(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'production_date' => 'required|date',
            'rolls' => 'required|array|min:1|max:200',
            'rolls.*.loom_no' => ['required', 'string', Rule::in(config('loomflow.looms'))],
            'rolls.*.fabric_stock_id' => 'nullable|exists:fabric_stocks,id',
            'rolls.*.gazana_per_roll' => 'required|numeric|gt:0|max:100000',
            'rolls.*.quantity' => 'required|integer|min:1|max:10000',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $rollRows = array_map(static function (array $roll): array {
            $totalGazana = round((float) $roll['gazana_per_roll'] * (int) $roll['quantity'], 2);

            return [
                ...$roll,
                'total_gazana' => $totalGazana,
                'total_meters' => round($totalGazana * 0.9144, 2),
            ];
        }, $validated['rolls']);
        $totalMeters = round(array_sum(array_column($rollRows, 'total_meters')), 2);
        $totalGazana = round(array_sum(array_column($rollRows, 'total_gazana')), 2);
        $totalRollCount = array_sum(array_column($rollRows, 'quantity'));

        DB::transaction(function () use ($validated, $rollRows): void {
            $movementDate = $validated['production_date'];
            $stockTotals = [];

            foreach ($rollRows as $roll) {
                $beam = SizingBeam::where('assigned_loom_no', $roll['loom_no'])
                    ->where('status', 'mounted_on_loom')
                    ->lockForUpdate()
                    ->first();
                $qualityId = $beam?->fabric_stock_id ?: ($roll['fabric_stock_id'] ?? null);

                if (! $qualityId) {
                    throw ValidationException::withMessages([
                        'rolls' => "Link or choose a fabric quality for {$roll['loom_no']} before saving production.",
                    ]);
                }

                $fabric = FabricStock::whereKey($qualityId)->lockForUpdate()->firstOrFail();
                $beamMetersConsumed = 0.0;
                $expectedFabricMeters = 0.0;
                $leakageMeters = 0.0;
                $leakagePercentage = 0.0;
                $efficiencyPercentage = 0.0;

                if ($beam) {
                    $beam->loadMissing('sizingSlip');
                    $widthMultiplier = max(0.01, (float) ($beam->sizingSlip?->width_multiplier ?? 1));
                    $beamMetersConsumed = round((float) $roll['total_meters'] / $widthMultiplier, 2);

                    if ($beamMetersConsumed > (float) $beam->remaining_length_meters) {
                        throw ValidationException::withMessages([
                            'rolls' => 'The warp meters entered exceed the mounted beam length for one of the selected looms. Check the warp-used value or mount a beam with enough length.',
                        ]);
                    }

                    $expectedFabricMeters = round($beamMetersConsumed * $widthMultiplier, 2);
                    $leakageMeters = max(0, $expectedFabricMeters - (float) $roll['total_meters']);
                    $leakagePercentage = $expectedFabricMeters > 0
                        ? round(($leakageMeters / $expectedFabricMeters) * 100, 2)
                        : 0;
                    $beam->remaining_length_meters = round((float) $beam->remaining_length_meters - $beamMetersConsumed, 2);
                    if ($beam->remaining_length_meters <= 0) {
                        $beam->remaining_length_meters = 0;
                        $beam->status = 'exhausted';
                    }
                    $beam->save();
                }

                Production::create([
                    'production_date' => $movementDate,
                    'shift' => 'Day',
                    'loom_no' => $roll['loom_no'],
                    'operator_name' => 'Production register',
                    'sizing_beam_id' => $beam?->id,
                    'fabric_stock_id' => $fabric->id,
                    'fabric_quality' => $fabric->quality_name,
                    'gazana_per_roll' => $roll['gazana_per_roll'],
                    'roll_quantity' => $roll['quantity'],
                    'total_gazana' => $roll['total_gazana'],
                    'start_meter' => 0,
                    'end_meter' => $roll['total_meters'],
                    'net_meters_produced' => $roll['total_meters'],
                    'beam_meters_consumed' => $beamMetersConsumed,
                    'expected_fabric_meters' => $expectedFabricMeters,
                    'leakage_waste_meters' => $leakageMeters,
                    'leakage_percentage' => $leakagePercentage,
                    'picks_per_inch' => 60,
                    'rpm' => 350,
                    'efficiency_percentage' => $efficiencyPercentage,
                    'remarks' => $validated['remarks'] ?? null,
                ]);

                $stockTotals[$fabric->id]['meters'] = ($stockTotals[$fabric->id]['meters'] ?? 0) + $roll['total_meters'];
                $stockTotals[$fabric->id]['rolls'] = ($stockTotals[$fabric->id]['rolls'] ?? 0) + (int) $roll['quantity'];
            }

            foreach ($stockTotals as $fabricId => $stockTotal) {
                $fabric = FabricStock::whereKey($fabricId)->lockForUpdate()->firstOrFail();
                $movement = FabricStockMovement::where('fabric_stock_id', $fabric->id)
                    ->whereDate('movement_date', $movementDate)->lockForUpdate()->first();
                $previousStock = $movement ? (float) $movement->previous_stock_meters : (float) $fabric->current_stock_meters;
                $receivedTotal = (float) ($movement?->received_meters ?? 0) + $stockTotal['meters'];
                $adjustment = (float) ($movement?->adjustment_meters ?? 0);
                $dispatch = (float) ($movement?->dispatch_meters ?? 0);
                $closingStock = $previousStock + $receivedTotal + $adjustment - $dispatch;

                FabricStockMovement::updateOrCreate(
                    ['fabric_stock_id' => $fabric->id, 'movement_date' => $movementDate],
                    [
                        'previous_stock_meters' => $previousStock,
                        'received_meters' => $receivedTotal,
                        'adjustment_meters' => $adjustment,
                        'dispatch_meters' => $dispatch,
                        'closing_stock_meters' => $closingStock,
                        'rolls_received' => (int) ($movement?->rolls_received ?? 0) + $stockTotal['rolls'],
                        'area' => $movement?->area ?? $fabric->area,
                        'remarks' => $validated['remarks'] ?? $movement?->remarks,
                    ]
                );

                $fabric->current_stock_meters = $closingStock;
                $fabric->daily_received_meters = $receivedTotal;
                $fabric->total_meters_produced = (float) $fabric->total_meters_produced + $stockTotal['meters'];
                $fabric->total_rolls_in_stock += $stockTotal['rolls'];
                $fabric->available_open_meters = max(0, $closingStock - (float) $fabric->committed_contract_meters);
                $fabric->save();
            }
        });

        return redirect()->route('production.index')->with('success', "Recorded {$totalGazana} Gazana ({$totalMeters}m) across {$totalRollCount} rolls and updated each selected fabric quality's stock.");
    }
}
