<?php

namespace App\Http\Controllers;

use App\Models\BagPurchase;
use App\Models\BagTransaction;
use App\Models\SizingSlip;
use App\Models\YarnQuality;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BagController extends Controller
{
    public function index(): View
    {
        $purchases = BagPurchase::latest('purchase_date')->latest('id')->limit(5)->get();
        $transactions = BagTransaction::latest('transaction_date')->latest('id')->limit(5)->get();
        $sizingSlips = SizingSlip::latest('slip_date')->latest('id')->limit(5)->get();
        $page = 'dashboard';

        return $this->renderPage($page, $purchases, $transactions, $sizingSlips);
    }

    public function purchases(): View
    {
        return $this->renderPage(
            'purchases',
            BagPurchase::latest('purchase_date')->latest('id')->paginate(15),
            collect(),
            collect()
        );
    }

    public function ledger(): View
    {
        return $this->renderPage(
            'ledger',
            collect(),
            BagTransaction::latest('transaction_date')->latest('id')->paginate(20),
            collect()
        );
    }

    public function sizingUsage(): View
    {
        return $this->renderPage(
            'sizing',
            collect(),
            collect(),
            SizingSlip::latest('slip_date')->paginate(20)
        );
    }

    /**
     * @param  Collection<int, BagPurchase>|LengthAwarePaginator  $purchases
     * @param  Collection<int, BagTransaction>|LengthAwarePaginator  $transactions
     * @param  Collection<int, SizingSlip>|LengthAwarePaginator  $sizingSlips
     */
    private function renderPage(
        string $page,
        Collection|LengthAwarePaginator $purchases,
        Collection|LengthAwarePaginator $transactions,
        Collection|LengthAwarePaginator $sizingSlips
    ): View {

        $totalPurchased = (float) BagPurchase::sum('total_bags');
        $bagsDirectToSizing = (float) BagPurchase::sum('bags_to_sizing');
        $bagsDirectToFactory = (float) BagPurchase::sum('bags_to_factory');
        $factoryIssuedToSizing = (float) BagTransaction::where('transaction_type', 'issue_to_sizing')->sum('quantity_bags');
        $factoryReturnedFromSizing = (float) BagTransaction::where('transaction_type', 'return_from_sizing')->sum('quantity_bags');
        $totalBagsUsedInBeams = (float) SizingSlip::sum('bags_used');
        $sizingSlipReturns = (float) SizingSlip::sum('bags_returned_to_factory');

        $factoryStoreStock = max(0, $bagsDirectToFactory - $factoryIssuedToSizing + $factoryReturnedFromSizing);
        $sizingStockRemaining = max(0, $bagsDirectToSizing + $factoryIssuedToSizing - $totalBagsUsedInBeams - $sizingSlipReturns);

        // Unused bags breakdown
        $unusedBagsReturned = SizingSlip::sum('bags_returned_to_factory');
        $unusedBagsHeldWithSizing = SizingSlip::sum('bags_held_with_sizing');
        $qualityBalances = YarnQuality::orderBy('name')->get()->map(function (YarnQuality $yarnQuality): array {
            $quality = $yarnQuality->name;
            $directToFactory = (float) BagPurchase::where('quality', $quality)->sum('bags_to_factory');
            $directToSizing = (float) BagPurchase::where('quality', $quality)->sum('bags_to_sizing');
            $issuedToSizing = (float) BagTransaction::where('quality', $quality)->where('transaction_type', 'issue_to_sizing')->sum('quantity_bags');
            $returnedToFactory = (float) BagTransaction::where('quality', $quality)->where('transaction_type', 'return_from_sizing')->sum('quantity_bags');
            $usedInSizing = (float) SizingSlip::where('yarn_quality', $quality)->sum('bags_used');
            $sizingReturns = (float) SizingSlip::where('yarn_quality', $quality)->sum('bags_returned_to_factory');

            return [
                'name' => $quality,
                'factory_stock' => max(0, $directToFactory - $issuedToSizing + $returnedToFactory),
                'sizing_stock' => max(0, $directToSizing + $issuedToSizing - $usedInSizing - $sizingReturns),
                'total' => max(0, $directToFactory - $issuedToSizing + $returnedToFactory)
                    + max(0, $directToSizing + $issuedToSizing - $usedInSizing - $sizingReturns),
            ];
        });

        // Total Yarn Investment
        $totalYarnInvestment = BagPurchase::sum('total_amount');
        $yarnQualities = YarnQuality::orderBy('name')->get();

        return view('bags.index', compact(
            'page',
            'purchases',
            'transactions',
            'sizingSlips',
            'totalPurchased',
            'bagsDirectToSizing',
            'bagsDirectToFactory',
            'factoryStoreStock',
            'sizingStockRemaining',
            'totalBagsUsedInBeams',
            'unusedBagsReturned',
            'unusedBagsHeldWithSizing',
            'totalYarnInvestment',
            'yarnQualities', 'qualityBalances'
        ));
    }

    public function storePurchase(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'challan_no' => 'required|string|max:100',
            'supplier_name' => 'required|string|max:150',
            'quality' => 'required|string|exists:yarn_qualities,name',
            'total_bags' => 'required|numeric|min:0.01|decimal:0,2',
            'bags_to_sizing' => 'required|numeric|min:0|decimal:0,2',
            'bags_to_factory' => 'required|numeric|min:0|decimal:0,2',
            'sizing_destination' => 'nullable|string|max:150',
            'rate_per_bag' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if (round($validated['bags_to_sizing'] + $validated['bags_to_factory'], 2) !== round($validated['total_bags'], 2)) {
            return back()->withInput()->with('error', 'Error: Split bags (Sizing + Factory) must exactly equal Total Bags ('.$validated['total_bags'].').');
        }

        if ($validated['bags_to_sizing'] > 0 && empty($validated['sizing_destination'])) {
            return back()->withInput()->withErrors([
                'sizing_destination' => 'Enter the sizing mill or contractor receiving these bags.',
            ]);
        }

        $totalAmount = $validated['total_bags'] * $validated['rate_per_bag'];
        $validated['total_amount'] = $totalAmount;

        $purchase = DB::transaction(function () use ($validated, $totalAmount, $ledgerPostingService): BagPurchase {
            $purchase = BagPurchase::create([...$validated, 'total_amount' => $totalAmount]);

            foreach ([
                ['quantity' => 'bags_to_factory', 'type' => 'purchase_factory', 'destination' => 'Factory Yarn Store', 'party' => 'Internal Store', 'notes' => 'Direct receipt into factory yarn warehouse'],
                ['quantity' => 'bags_to_sizing', 'type' => 'purchase_sizing', 'destination' => $purchase->sizing_destination, 'party' => $purchase->sizing_destination, 'notes' => 'Direct delivery from spinning mill to sizing unit'],
            ] as $movement) {
                if ((float) $purchase->{$movement['quantity']} <= 0) {
                    continue;
                }

                BagTransaction::create([
                    'transaction_type' => $movement['type'],
                    'reference_no' => $purchase->challan_no,
                    'quality' => $purchase->quality,
                    'quantity_bags' => $purchase->{$movement['quantity']},
                    'source' => $purchase->supplier_name,
                    'destination' => $movement['destination'],
                    'party_name' => $movement['party'],
                    'transaction_date' => $purchase->purchase_date,
                    'notes' => $movement['notes'],
                ]);
            }

            $ledgerPostingService->post('bag_purchase', $purchase->id, [
                [
                    'role' => 'supplier',
                    'party_name' => $purchase->supplier_name,
                    'party_type' => 'yarn_supplier',
                    'transaction_date' => $purchase->purchase_date,
                    'voucher_no' => $purchase->challan_no,
                    'description' => "Yarn Purchase: {$purchase->total_bags} bags ({$purchase->quality}) @ Rs. {$purchase->rate_per_bag}",
                    'debit' => 0,
                    'credit' => $totalAmount,
                    'notes' => $purchase->notes,
                ],
                [
                    'role' => 'inventory',
                    'party_name' => 'Yarn Inventory',
                    'party_type' => 'inventory',
                    'transaction_date' => $purchase->purchase_date,
                    'voucher_no' => $purchase->challan_no,
                    'description' => "Yarn purchased: {$purchase->total_bags} bags ({$purchase->quality})",
                    'debit' => $totalAmount,
                    'credit' => 0,
                    'notes' => $purchase->notes,
                ],
            ]);

            return $purchase;
        });

        return redirect()->route('bags.index')
            ->with('success', "Bag Purchase {$purchase->challan_no} recorded successfully! ({$purchase->bags_to_sizing} to Sizing, {$purchase->bags_to_factory} to Factory)");
    }

    public function issueStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reference_no' => 'required|string|max:100',
            'quality' => 'required|string|exists:yarn_qualities,name',
            'quantity_bags' => 'required|numeric|min:0.01|decimal:0,2',
            'destination' => 'required|string|max:150',
            'party_name' => 'required|string|max:150',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $issued = DB::transaction(function () use ($validated): bool {
            YarnQuality::where('name', $validated['quality'])->lockForUpdate()->firstOrFail();

            $factoryReceived = BagTransaction::where('quality', $validated['quality'])
                ->where('transaction_type', 'purchase_factory')
                ->sum('quantity_bags');
            $alreadyIssued = BagTransaction::where('quality', $validated['quality'])
                ->where('transaction_type', 'issue_to_sizing')
                ->sum('quantity_bags');
            $returned = BagTransaction::where('quality', $validated['quality'])
                ->where('transaction_type', 'return_from_sizing')
                ->sum('quantity_bags');
            $availableBags = $factoryReceived - $alreadyIssued + $returned;

            if ($validated['quantity_bags'] > $availableBags) {
                return false;
            }

            $validated['transaction_type'] = 'issue_to_sizing';
            $validated['source'] = 'Factory Yarn Store';
            BagTransaction::create($validated);

            return true;
        });

        if (! $issued) {
            return back()->withInput()->with('error', 'The Factory Yarn Store does not have enough bags of this yarn quality to issue.');
        }

        return redirect()->route('bags.index')
            ->with('success', "Issued {$validated['quantity_bags']} bags of {$validated['quality']} from Factory Store to {$validated['party_name']}.");
    }

    public function returnStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reference_no' => 'required|string|max:100',
            'quality' => 'required|string|exists:yarn_qualities,name',
            'quantity_bags' => 'required|numeric|min:0.01|decimal:0,2',
            'source' => 'required|string|max:150',
            'party_name' => 'required|string|max:150',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $returned = DB::transaction(function () use ($validated): bool {
            YarnQuality::where('name', $validated['quality'])->lockForUpdate()->firstOrFail();

            $issue = BagTransaction::where('transaction_type', 'issue_to_sizing')
                ->where('reference_no', $validated['reference_no'])
                ->where('quality', $validated['quality'])
                ->where('destination', $validated['source'])
                ->where('party_name', $validated['party_name'])
                ->lockForUpdate()
                ->first();

            if (! $issue) {
                return false;
            }

            $sizingSlip = SizingSlip::where('bag_transaction_id', $issue->id)->first();
            $slipReturned = (float) ($sizingSlip?->bags_returned_to_factory ?? 0);
            $allManualReturns = (float) BagTransaction::where('transaction_type', 'return_from_sizing')
                ->where('quality', $validated['quality'])
                ->where('source', $validated['source'])
                ->where('party_name', $validated['party_name'])
                ->where('reference_no', $issue->reference_no)
                ->sum('quantity_bags');
            $availableToReturn = max(0, (float) $issue->quantity_bags - $slipReturned - $allManualReturns);

            if ((float) $validated['quantity_bags'] > $availableToReturn) {
                return false;
            }

            BagTransaction::create([
                ...$validated,
                'reference_no' => $issue->reference_no,
                'transaction_type' => 'return_from_sizing',
                'destination' => 'Factory Yarn Store',
            ]);

            return true;
        });

        if (! $returned) {
            return back()->withInput()->with('error', 'Return not recorded. Check the original issue reference, quality, party, and remaining unreturned quantity.');
        }

        return redirect()->route('bags.index')
            ->with('success', "Received back {$validated['quantity_bags']} unused bags of {$validated['quality']} into Factory Store.");
    }
}
