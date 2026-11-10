<?php

namespace App\Http\Controllers;

use App\Models\FabricStock;
use App\Models\FabricStockMovement;
use App\Models\SalesContract;
use App\Models\SizingBeam;
use App\YarnQualityWeightCalculator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FabricStockController extends Controller
{
    public function index(): View
    {
        $fabricStocks = FabricStock::latest('updated_at')->limit(5)->get();
        $activeContracts = SalesContract::where('status', 'active')->latest()->limit(5)->get();
        $page = 'dashboard';

        return $this->renderPage($page, $fabricStocks, $activeContracts);
    }

    public function inventory(): View
    {
        return $this->renderPage(
            'inventory',
            FabricStock::latest('updated_at')->paginate(15),
            collect()
        );
    }

    public function contracts(): View
    {
        return $this->renderPage(
            'contracts',
            collect(),
            SalesContract::where('status', 'active')->latest()->paginate(20)
        );
    }

    /**
     * @param  Collection<int, FabricStock>|LengthAwarePaginator  $fabricStocks
     * @param  Collection<int, SalesContract>|LengthAwarePaginator  $activeContracts
     */
    private function renderPage(
        string $page,
        Collection|LengthAwarePaginator $fabricStocks,
        Collection|LengthAwarePaginator $activeContracts
    ): View {

        $allFabricQualities = FabricStock::orderBy('quality_name')->get();
        $mountedBeams = SizingBeam::with('fabricStock')
            ->where('status', 'mounted_on_loom')
            ->orderBy('assigned_loom_no')
            ->get();
        $stockMovements = FabricStockMovement::with('fabricStock')->latest('movement_date')->latest('id')
            ->when($page === 'dashboard', fn ($query) => $query->limit(10))->paginate($page === 'dashboard' ? 10 : 50);

        // Totals
        $totalMetersInStock = FabricStock::sum('current_stock_meters');
        $totalMetersCommitted = FabricStock::sum('committed_contract_meters');
        $totalMetersAvailableOpen = FabricStock::sum('available_open_meters');
        $totalRollsInStock = FabricStock::sum('total_rolls_in_stock');
        $totalInventoryValue = FabricStock::selectRaw('SUM(current_stock_meters * unit_cost_meter) as total')->value('total') ?? 0;

        return view('fabric.index', compact(
            'page',
            'fabricStocks',
            'activeContracts',
            'totalMetersInStock',
            'totalMetersCommitted',
            'totalMetersAvailableOpen',
            'totalRollsInStock',
            'totalInventoryValue',
            'allFabricQualities',
            'mountedBeams',
            'stockMovements'
        ));
    }

    public function storeStock(Request $request, YarnQualityWeightCalculator $weightCalculator, \App\Services\FactoryStockService $factoryStock): RedirectResponse
    {
        $validated = $request->validate([
            'quality_mode' => 'required|in:existing,new',
            'fabric_stock_id' => 'required_if:quality_mode,existing|nullable|exists:fabric_stocks,id',
            'quality_name' => 'required_if:quality_mode,new|nullable|string|max:150|unique:fabric_stocks,quality_name',
            'construction' => 'nullable|string|max:150',
            'width_inches' => 'required_if:quality_mode,new|nullable|numeric|min:20|max:150',
            'read' => 'required_if:quality_mode,new|nullable|numeric|gt:0|max:100000',
            'pick' => 'required_if:quality_mode,new|nullable|numeric|gt:0|max:100000',
            'warp_count' => 'required_if:quality_mode,new|nullable|numeric|gt:0|max:100000',
            'weft_count' => 'required_if:quality_mode,new|nullable|numeric|gt:0|max:100000',
            'previous_stock_meters' => 'nullable|numeric|min:0',
            'daily_received_meters' => 'nullable|numeric|min:0',
            'adjustment_meters' => 'nullable|numeric|between:-999999999999,999999999999',
            'dispatch_meters' => 'nullable|numeric|min:0',
            'rolls_received' => 'nullable|integer|min:0',
            'total_rolls_in_stock' => 'nullable|integer|min:0',
            'area' => 'nullable|string|max:100',
            'unit_cost_meter' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'sizing_beam_id' => 'nullable|exists:sizing_beams,id',
        ]);

        if (! empty($validated['sizing_beam_id']) && ! SizingBeam::whereKey($validated['sizing_beam_id'])
            ->where('status', 'mounted_on_loom')->exists()) {
            throw ValidationException::withMessages([
                'sizing_beam_id' => 'Choose a beam that is currently mounted on a loom.',
            ]);
        }

        $isNew = $validated['quality_mode'] === 'new';
        if ((float) ($validated['daily_received_meters'] ?? 0) > 0 || (int) ($validated['rolls_received'] ?? 0) > 0) {
            throw ValidationException::withMessages(['daily_received_meters' => 'Fabric can enter stock only from the checking report. Use Factory > Checking. Manual adjustments remain available for corrections.']);
        }
        $received = (float) ($validated['daily_received_meters'] ?? 0);
        $adjustment = (float) ($validated['adjustment_meters'] ?? 0);
        $dispatch = (float) ($validated['dispatch_meters'] ?? 0);
        $stock = DB::transaction(function () use ($validated, $isNew, $received, $adjustment, $dispatch, $weightCalculator): FabricStock {
            if ($isNew) {
                $read = (float) $validated['read'];
                $pick = (float) $validated['pick'];
                $width = (float) $validated['width_inches'];
                $warpCount = (float) $validated['warp_count'];
                $weftCount = (float) $validated['weft_count'];
                $weights = $weightCalculator->calculate($read, $pick, $width, $warpCount, $weftCount);

                $stock = FabricStock::create([
                    'quality_name' => trim($validated['quality_name']),
                    'construction' => $validated['construction'] ?? null,
                    'width_inches' => $width,
                    'read' => $read,
                    'pick' => $pick,
                    'warp_count' => $warpCount,
                    'weft_count' => $weftCount,
                    ...$weights,
                    'current_stock_meters' => 0,
                    'total_meters_produced' => 0,
                    'total_rolls_in_stock' => 0,
                    'committed_contract_meters' => 0,
                    'available_open_meters' => 0,
                    'unit_cost_meter' => $validated['unit_cost_meter'] ?? 0,
                    'area' => $validated['area'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);
                $previousStock = 0.0;
            } else {
                $stock = FabricStock::whereKey($validated['fabric_stock_id'])->lockForUpdate()->firstOrFail();
                $previousStock = (float) $stock->current_stock_meters;
            }

            $movementDate = now()->toDateString();
            $existingMovement = FabricStockMovement::where('fabric_stock_id', $stock->id)
                ->whereDate('movement_date', $movementDate)->lockForUpdate()->first();
            if ($existingMovement) {
                $previousStock = (float) $existingMovement->previous_stock_meters;
                $received += (float) $existingMovement->received_meters;
                $adjustment += (float) $existingMovement->adjustment_meters;
                $dispatch += (float) $existingMovement->dispatch_meters;
            }

            $closingStock = $previousStock + $received + $adjustment - $dispatch;
            if ($closingStock < 0) {
                throw ValidationException::withMessages([
                    'dispatch_meters' => 'Dispatch cannot exceed the available fabric stock.',
                ]);
            }

            FabricStockMovement::updateOrCreate(
                ['fabric_stock_id' => $stock->id, 'movement_date' => $movementDate],
                [
                    'previous_stock_meters' => $previousStock,
                    'received_meters' => $received,
                    'adjustment_meters' => $adjustment,
                    'dispatch_meters' => $dispatch,
                    'closing_stock_meters' => $closingStock,
                    'rolls_received' => (int) ($validated['rolls_received'] ?? 0) + (int) ($existingMovement?->rolls_received ?? 0),
                    'area' => $validated['area'] ?? $stock->area,
                    'remarks' => $validated['notes'] ?? null,
                ]
            );

            $stock->current_stock_meters = $closingStock;
            $stock->total_meters_produced = (float) $stock->total_meters_produced + (float) ($validated['daily_received_meters'] ?? 0);
            $stock->total_rolls_in_stock = max(0, (int) $stock->total_rolls_in_stock + (int) ($validated['rolls_received'] ?? 0));
            $stock->available_open_meters = max(0, $closingStock - (float) $stock->committed_contract_meters);
            $stock->area = $validated['area'] ?? $stock->area;
            $stock->save();

            return $stock;
        });

        $factoryStock->refreshAvailability($stock);
        if (! empty($validated['sizing_beam_id'])) {
            SizingBeam::whereKey($validated['sizing_beam_id'])->update(['fabric_stock_id' => $stock->id]);

            return redirect()->route('fabric.index')
                ->with('success', "{$stock->quality_name} was created/selected and linked to its loom.");
        }

        return redirect()->route('fabric.index')
            ->with('success', "Stock updated for {$stock->quality_name}: {$stock->current_stock_meters}m closing balance.");
    }

    public function updateLoomFabricQuality(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sizing_beam_id' => 'required|exists:sizing_beams,id',
            'fabric_stock_id' => 'nullable|exists:fabric_stocks,id',
        ]);

        $beam = SizingBeam::whereKey($validated['sizing_beam_id'])
            ->where('status', 'mounted_on_loom')
            ->firstOrFail();
        $beam->fabric_stock_id = $validated['fabric_stock_id'] ?? null;
        $beam->save();

        return redirect()->route('fabric.index')->with('success', $beam->fabric_stock_id
            ? "Fabric quality linked to {$beam->assigned_loom_no}."
            : "Fabric quality unlinked from {$beam->assigned_loom_no}.");
    }
}
