<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\PartIssuance;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PartController extends Controller
{
    public function index(): View
    {
        $parts = Part::withCount('issuances')->latest('id')->limit(5)->get();
        $issuances = PartIssuance::with('part')->latest('issuance_date')->latest('id')->limit(5)->get();
        $page = 'dashboard';

        return $this->renderPage($page, $parts, $issuances);
    }

    public function inventory(): View
    {
        return $this->renderPage(
            'inventory',
            Part::withCount('issuances')->orderBy('category')->orderBy('part_name')->paginate(15),
            collect()
        );
    }

    public function issuances(): View
    {
        return $this->renderPage(
            'issuances',
            collect(),
            PartIssuance::with('part')->latest('issuance_date')->latest('id')->paginate(20)
        );
    }

    /**
     * @param  Collection<int, Part>|LengthAwarePaginator  $parts
     * @param  Collection<int, PartIssuance>|LengthAwarePaginator  $issuances
     */
    private function renderPage(
        string $page,
        Collection|LengthAwarePaginator $parts,
        Collection|LengthAwarePaginator $issuances
    ): View {
        $issuanceParts = Part::orderBy('part_name')->get();

        // Stats
        $totalItemsCount = Part::count();
        $totalStockUnits = Part::sum('stock_quantity');
        $lowStockParts = Part::whereColumn('stock_quantity', '<=', 'min_alert_threshold')->get();
        $totalInventoryValuation = Part::selectRaw('SUM(stock_quantity * unit_price) as total')->value('total') ?? 0;
        $totalIssuancesCount = PartIssuance::count();
        $totalIssuanceCost = PartIssuance::sum('total_cost');

        $categories = ['Gripper', 'Rapier', 'Heald & Dropper', 'Sensor', 'Mechanical', 'Electrical', 'Consumables'];
        $looms = collect(range(1, 24))->map(fn ($i) => sprintf('Loom-%02d', $i));

        return view('parts.index', compact(
            'page',
            'parts',
            'issuanceParts',
            'issuances',
            'totalItemsCount',
            'totalStockUnits',
            'lowStockParts',
            'totalInventoryValuation',
            'totalIssuancesCount',
            'totalIssuanceCost',
            'categories',
            'looms'
        ));
    }

    public function storePart(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'part_name' => 'required|string|max:150',
            'part_code' => 'required|string|max:50|unique:parts,part_code',
            'category' => 'required|string|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'min_alert_threshold' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'rack_location' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $part = DB::transaction(function () use ($validated, $ledgerPostingService): Part {
            $part = Part::create($validated);
            $openingValue = (float) $part->stock_quantity * (float) $part->unit_price;
            if ($openingValue > 0) {
                $ledgerPostingService->post('part_opening_inventory', $part->id, [
                    ['role' => 'inventory', 'party_name' => 'Parts Inventory', 'party_type' => 'inventory', 'transaction_date' => $part->created_at->toDateString(), 'voucher_no' => 'PART-OPEN-'.$part->id, 'description' => 'Opening parts inventory: '.$part->part_name, 'debit' => $openingValue, 'credit' => 0, 'notes' => $part->part_code],
                    ['role' => 'equity', 'party_name' => 'Opening Inventory Equity', 'party_type' => 'equity', 'transaction_date' => $part->created_at->toDateString(), 'voucher_no' => 'PART-OPEN-'.$part->id, 'description' => 'Opening value for parts inventory', 'debit' => 0, 'credit' => $openingValue, 'notes' => $part->part_code],
                ]);
            }

            return $part;
        });

        return redirect()->route('parts.index')
            ->with('success', "Part '{$part->part_name}' [{$part->part_code}] added to store inventory successfully!");
    }

    public function issuePart(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'part_id' => 'required|exists:parts,id',
            'loom_no' => 'required|string|max:50',
            'issued_to_person' => 'required|string|max:150',
            'quantity' => 'required|integer|min:1',
            'issuance_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $issuance = DB::transaction(function () use ($validated, $ledgerPostingService): PartIssuance|false {
            $part = Part::whereKey($validated['part_id'])->lockForUpdate()->firstOrFail();
            if ($part->stock_quantity < $validated['quantity']) {
                return false;
            }

            $totalCost = $validated['quantity'] * (float) $part->unit_price;
            $issuance = PartIssuance::create([
                'part_id' => $part->id,
                'loom_no' => $validated['loom_no'],
                'issued_to_person' => $validated['issued_to_person'],
                'quantity' => $validated['quantity'],
                'unit_price' => $part->unit_price,
                'total_cost' => $totalCost,
                'issuance_date' => $validated['issuance_date'],
                'remarks' => $validated['remarks'],
            ]);
            $part->decrement('stock_quantity', $validated['quantity']);
            if ($totalCost > 0) {
                $ledgerPostingService->post('part_issuance', $issuance->id, [
                    ['role' => 'expense', 'party_name' => 'Factory Parts Expense', 'party_type' => 'expense', 'transaction_date' => $issuance->issuance_date, 'voucher_no' => 'PART-ISSUE-'.$issuance->id, 'description' => 'Parts issued to '.$issuance->loom_no.': '.$part->part_name, 'debit' => $totalCost, 'credit' => 0, 'notes' => $issuance->remarks],
                    ['role' => 'inventory', 'party_name' => 'Parts Inventory', 'party_type' => 'inventory', 'transaction_date' => $issuance->issuance_date, 'voucher_no' => 'PART-ISSUE-'.$issuance->id, 'description' => 'Parts issued from inventory: '.$part->part_name, 'debit' => 0, 'credit' => $totalCost, 'notes' => $issuance->remarks],
                ]);
            }

            return $issuance;
        });

        if (! $issuance) {
            $part = Part::findOrFail($validated['part_id']);

            return back()->withInput()->with('error', "Insufficient stock for '{$part->part_name}'. In stock: {$part->stock_quantity} {$part->unit}, requested: {$validated['quantity']} {$part->unit}.");
        }
        $part = $issuance->part()->firstOrFail();

        return redirect()->route('parts.index')
            ->with('success', "Issued {$validated['quantity']} {$part->unit} of '{$part->part_name}' to {$validated['loom_no']} ({$validated['issued_to_person']}) successfully! Remaining stock: {$part->stock_quantity} {$part->unit}.");
    }
}
