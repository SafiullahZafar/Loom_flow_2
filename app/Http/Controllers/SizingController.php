<?php

namespace App\Http\Controllers;

use App\Models\BagTransaction;
use App\Models\BeamEvent;
use App\Models\LoomAssignment;
use App\Models\WorkOrder;
use App\Models\SizingBeam;
use App\Models\SizingSlip;
use App\Models\YarnQuality;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SizingController extends Controller
{
    private const BAG_WEIGHT_POUNDS = 100;

    private const KILOGRAMS_PER_POUND = 0.45359237;

    public function index(): View
    {
        $slips = SizingSlip::with(['beams' => fn ($query) => $query
            ->withSum('productions', 'net_meters_produced')
            ->withSum('productions', 'expected_fabric_meters')
            ->withCount('productions')])
            ->latest('id')->limit(5)->get();
        $beams = SizingBeam::with(['sizingSlip.beams' => fn ($query) => $query
            ->withSum('productions', 'net_meters_produced')
            ->withSum('productions', 'expected_fabric_meters')
            ->withCount('productions')])
            ->withSum('productions', 'net_meters_produced')
            ->withSum('productions', 'expected_fabric_meters')
            ->withCount('productions')
            ->latest('id')->limit(5)->get();
        $page = 'dashboard';

        return $this->renderPage($page, $slips, $beams);
    }

    public function beams(): View
    {
        return $this->renderPage(
            'beams',
            collect(),
            SizingBeam::with(['sizingSlip.beams' => fn ($query) => $query
                ->withSum('productions', 'net_meters_produced')
                ->withSum('productions', 'expected_fabric_meters')
                ->withCount('productions')])
                ->withSum('productions', 'net_meters_produced')
                ->withSum('productions', 'expected_fabric_meters')
                ->withCount('productions')
                ->latest('id')->paginate(15)
        );
    }

    public function slips(): View
    {
        return $this->renderPage(
            'slips',
            SizingSlip::with(['beams' => fn ($query) => $query
                ->withSum('productions', 'net_meters_produced')
                ->withSum('productions', 'expected_fabric_meters')
                ->withCount('productions')])
                ->latest('slip_date')->latest('id')->paginate(10),
            collect()
        );
    }

    public function balance(): View
    {
        return $this->renderPage(
            'balance',
            SizingSlip::with(['beams' => fn ($query) => $query
                ->withSum('productions', 'net_meters_produced')
                ->withSum('productions', 'expected_fabric_meters')
                ->withSum('productions', 'leakage_waste_meters')
                ->withSum('productions', 'beam_meters_consumed')])
                ->latest('slip_date')->latest('id')->paginate(10),
            collect()
        );
    }

    /**
     * @param  Collection<int, SizingSlip>|LengthAwarePaginator  $slips
     * @param  Collection<int, SizingBeam>|LengthAwarePaginator  $beams
     */
    private function renderPage(
        string $page,
        Collection|LengthAwarePaginator $slips,
        Collection|LengthAwarePaginator $beams
    ): View {

        // Stats
        $totalSlips = SizingSlip::count();
        $totalAllocatedBags = SizingSlip::sum('bags_allocated');
        $totalUsedBags = SizingSlip::sum('bags_used');
        $totalReturnedBags = SizingSlip::sum('bags_returned_to_factory');
        $totalHeldWithSizing = SizingSlip::sum('bags_held_with_sizing');

        $totalBeamsCount = SizingBeam::count();
        $mountedBeamsCount = SizingBeam::where('status', 'mounted_on_loom')->count();
        $readyBeamsCount = SizingBeam::where('status', 'ready_in_store')->count();
        $exhaustedBeamsCount = SizingBeam::where('status', 'exhausted')->count();
        $totalBeamMetersProduced = SizingBeam::sum('total_length_meters');
        $remainingBeamMetersOnLooms = SizingBeam::where('status', 'mounted_on_loom')->sum('remaining_length_meters');
        $pendingSizingTransactions = BagTransaction::whereIn('transaction_type', ['purchase_sizing', 'issue_to_sizing', 'customer_sizing_receipt'])
            ->whereDoesntHave('sizingSlip')
            ->latest('transaction_date')
            ->latest('id')
            ->get();
        $kilogramsPerBag = self::BAG_WEIGHT_POUNDS * self::KILOGRAMS_PER_POUND;
        $yarnQualities = YarnQuality::orderBy('name')->get();

        // All active looms list (1 to 24)
        $looms = collect(range(1, 24))->map(fn ($i) => sprintf('Loom-%02d', $i));

        return view('sizing.index', compact(
            'page',
            'slips',
            'beams',
            'totalSlips',
            'totalAllocatedBags',
            'totalUsedBags',
            'totalReturnedBags',
            'totalHeldWithSizing',
            'totalBeamsCount',
            'mountedBeamsCount',
            'readyBeamsCount',
            'exhaustedBeamsCount',
            'totalBeamMetersProduced',
            'remainingBeamMetersOnLooms',
            'pendingSizingTransactions',
            'kilogramsPerBag',
            'yarnQualities',
            'looms'
        ));
    }

    public function storeSlip(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'slip_no' => 'required|string|max:100|unique:sizing_slips,slip_no',
            'slip_type' => 'required|in:raw_perch,final_perch',
            'sizing_party' => 'required|string|max:150',
            'yarn_quality' => 'required|string|exists:yarn_qualities,name',
            'bags_allocated' => 'required|numeric|min:0.01|decimal:0,2',
            'bags_used' => 'required|numeric|min:0|decimal:0,2',
            'bags_returned_to_factory' => 'required|numeric|min:0|decimal:0,2',
            'bags_held_with_sizing' => 'required|numeric|min:0|decimal:0,2',
            'total_beams' => 'required|integer|min:1|max:100',
            'beam_names' => ['required', 'array', 'size:'.$request->input('total_beams')],
            'beam_names.*' => 'required|string|max:100',
            'back_beam_count' => 'nullable|integer|min:1|max:1000',
            'width_multiplier' => 'required|numeric|min:0.01|max:100',
            'set_length_value' => 'required|numeric|min:0.01',
            'set_length_unit' => 'required|in:meters,gaz',
            'total_ends' => 'nullable|integer|min:1',
            'reed_count' => 'nullable|integer|min:1',
            'fabric_width_inches' => 'nullable|numeric|min:0.01',
            'selvedge_ends' => 'nullable|integer|min:0',
            'sizing_gross_weight_kg' => 'nullable|numeric|min:0.01',
            'sizing_weight_kg' => 'nullable|numeric|min:0.01',
            'wet_cone_bags' => 'nullable|integer|min:0',
            'wet_cone_weight_kg' => 'nullable|numeric|min:0',
            'empty_bag_count' => 'nullable|integer|min:0',
            'empty_bag_weight_kg' => 'nullable|numeric|min:0',
            'paper_bag_count' => 'nullable|integer|min:0',
            'paper_bag_weight_kg' => 'nullable|numeric|min:0',
            'sound_cone_count' => 'nullable|integer|min:0',
            'sound_cone_weight_kg' => 'nullable|numeric|min:0',
            'carton_count' => 'nullable|integer|min:0',
            'carton_weight_kg' => 'nullable|numeric|min:0',
            'sizing_rate_per_kg' => 'nullable|numeric|min:0',
            'bag_transaction_id' => 'required|integer|exists:bag_transactions,id',
            'slip_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validated['slip_type'] === 'final_perch') {
            if (! isset($validated['sizing_gross_weight_kg'], $validated['sizing_rate_per_kg'])) {
                return back()->withInput()->withErrors([
                    'sizing_gross_weight_kg' => 'Final Perch needs the invoice gross yarn weight and sizing rate per kg.',
                ]);
            }

            $deductionFields = [
                'wet_cone_weight_kg', 'empty_bag_weight_kg', 'paper_bag_weight_kg',
                'sound_cone_weight_kg', 'carton_weight_kg',
            ];
            $returnedMaterialsWeight = array_sum(array_map(
                static fn (string $field): float => (float) ($validated[$field] ?? 0),
                $deductionFields
            ));
            $validated['returned_materials_weight_kg'] = round($returnedMaterialsWeight, 2);
            $validated['sizing_weight_kg'] = round((float) $validated['sizing_gross_weight_kg'] - $returnedMaterialsWeight, 2);
            if ($validated['sizing_weight_kg'] <= 0) {
                return back()->withInput()->withErrors([
                    'sizing_weight_kg' => 'Returned-material deductions must be less than the gross yarn weight.',
                ]);
            }
            $validated['sizing_bill_amount'] = round($validated['sizing_weight_kg'] * (float) $validated['sizing_rate_per_kg'], 2);
            foreach ([
                'wet_cone_bags', 'empty_bag_count', 'paper_bag_count', 'sound_cone_count', 'carton_count',
            ] as $field) {
                $validated[$field] = (int) ($validated[$field] ?? 0);
            }
            foreach ($deductionFields as $field) {
                $validated[$field] = round((float) ($validated[$field] ?? 0), 2);
            }
        } else {
            $validated['returned_materials_weight_kg'] = 0;
            $validated['sizing_bill_amount'] = null;
            $validated['sizing_rate_per_kg'] = null;
            foreach ([
                'wet_cone_bags', 'wet_cone_weight_kg', 'empty_bag_count', 'empty_bag_weight_kg',
                'paper_bag_count', 'paper_bag_weight_kg', 'sound_cone_count', 'sound_cone_weight_kg',
                'carton_count', 'carton_weight_kg',
            ] as $field) {
                $validated[$field] = 0;
            }
        }

        if (empty($validated['total_ends']) && (empty($validated['reed_count']) || empty($validated['fabric_width_inches']))) {
            return back()->withInput()->withErrors([
                'total_ends' => 'Enter gate pass total ends, or provide both reed count and fabric width.',
            ]);
        }

        if (isset($validated['sizing_gross_weight_kg'], $validated['sizing_weight_kg'])
            && $validated['sizing_weight_kg'] > $validated['sizing_gross_weight_kg']) {
            return back()->withInput()->withErrors([
                'sizing_weight_kg' => 'Net warp yarn weight cannot exceed the gross bill weight.',
            ]);
        }

        $beamNames = $validated['beam_names'];
        unset($validated['beam_names']);

        $slipResult = DB::transaction(function () use ($validated, $beamNames, $ledgerPostingService): SizingSlip|false {
            $sourceTransaction = BagTransaction::whereKey($validated['bag_transaction_id'])
                ->whereIn('transaction_type', ['purchase_sizing', 'issue_to_sizing', 'customer_sizing_receipt'])
                ->whereDoesntHave('sizingSlip')
                ->lockForUpdate()
                ->first();

            if (! $sourceTransaction) {
                return false;
            }

            $validated['work_order_id'] = $sourceTransaction->work_order_id;
            $validated['bags_allocated'] = $sourceTransaction->quantity_bags;
            $validated['yarn_quality'] = $sourceTransaction->quality;
            $validated['sizing_party'] = $sourceTransaction->destination ?: ($sourceTransaction->party_name ?: $sourceTransaction->source);

            if (empty($validated['total_ends'])) {
                $validated['total_ends'] = (int) round($validated['reed_count'] * $validated['fabric_width_inches'])
                    + (int) ($validated['selvedge_ends'] ?? 0);
            }

            $validated['selvedge_ends'] = (int) ($validated['selvedge_ends'] ?? 0);
            $validated['total_length_meters'] = round(
                $validated['set_length_value'] * ($validated['set_length_unit'] === 'gaz' ? 0.9144 : 1),
                2
            );
            $validated['back_beam_count'] = $validated['back_beam_count'] ?? null;
            $validated['sizing_weight_pounds'] = isset($validated['sizing_weight_kg'])
                ? round($validated['sizing_weight_kg'] / self::KILOGRAMS_PER_POUND, 2)
                : null;
            $validated['sizing_gross_weight_pounds'] = isset($validated['sizing_gross_weight_kg'])
                ? round($validated['sizing_gross_weight_kg'] / self::KILOGRAMS_PER_POUND, 2)
                : null;
            unset($validated['sizing_weight_kg']);
            unset($validated['sizing_gross_weight_kg']);

            $slip = SizingSlip::create($validated);
            $beamCount = (int) $validated['total_beams'];
            $totalLengthCentimeters = (int) round((float) $validated['total_length_meters'] * 100);
            $baseBeamLengthCentimeters = intdiv($totalLengthCentimeters, $beamCount);
            $remainingCentimeters = $totalLengthCentimeters % $beamCount;
            $lastBeamId = SizingBeam::max('id') ?? 100;
            for ($i = 1; $i <= $beamCount; $i++) {
                $beamLengthCentimeters = $baseBeamLengthCentimeters + ($i <= $remainingCentimeters ? 1 : 0);
                $beamLengthMeters = $beamLengthCentimeters / 100;

                SizingBeam::create([
                    'sizing_slip_id' => $slip->id,
                    'work_order_id' => $slip->work_order_id,
                    'beam_no' => 'BM-'.($lastBeamId + $i),
                    'beam_name' => trim($beamNames[$i - 1]),
                    'yarn_quality' => $slip->yarn_quality,
                    'total_length_meters' => $beamLengthMeters,
                    'remaining_length_meters' => $beamLengthMeters,
                    'status' => 'ready_in_store',
                    'notes' => "Beam {$i} of {$beamCount} from Sizing Slip {$slip->slip_no}",
                ]);
            }

            if ($slip->bags_returned_to_factory > 0) {
                BagTransaction::create([
                    'transaction_type' => $sourceTransaction->owner_type === 'customer' ? 'customer_return_sizing' : 'return_from_sizing',
                    'reference_no' => $slip->slip_no,
                    'quality' => $slip->yarn_quality,
                    'quantity_bags' => $slip->bags_returned_to_factory,
                    'source' => $slip->sizing_party,
                    'destination' => 'Factory Yarn Store',
                    'party_name' => $slip->sizing_party,
                    'transaction_date' => $slip->slip_date,
                    'notes' => "Automatic return of unused bags from Sizing Slip {$slip->slip_no}",
                    'work_order_id' => $sourceTransaction->work_order_id,
                    'owner_type' => $sourceTransaction->owner_type,
                ]);
            }

            if ($slip->sizing_bill_amount !== null && (float) $slip->sizing_bill_amount > 0) {
                $ledgerPostingService->post('sizing_slip', $slip->id, [
                    [
                        'role' => 'sizing_mill',
                        'party_name' => $slip->sizing_party,
                        'party_type' => 'sizing_mill',
                        'transaction_date' => $slip->slip_date,
                        'voucher_no' => $slip->slip_no,
                        'description' => 'Sizing service bill for '.$slip->slip_no,
                        'debit' => 0,
                        'credit' => (float) $slip->sizing_bill_amount,
                        'notes' => $slip->notes,
                    ],
                    [
                        'role' => 'expense',
                        'party_name' => 'Sizing Service Expense',
                        'party_type' => 'expense',
                        'transaction_date' => $slip->slip_date,
                        'voucher_no' => $slip->slip_no,
                        'description' => 'Sizing service expense for '.$slip->slip_no,
                        'debit' => (float) $slip->sizing_bill_amount,
                        'credit' => 0,
                        'notes' => $slip->notes,
                    ],
                ]);
            }

            return $slip;
        });

        if (! $slipResult) {
            return back()->withInput()->with('error', 'This bag transaction is already linked or is not available for a sizing slip.');
        }

        $slip = $slipResult;
        $beamCount = (int) $slip->total_beams;
        $meterPerBeam = round((float) $slip->total_length_meters / $beamCount, 2);

        $bagVariance = round(
            (float) $slip->bags_allocated
                - (float) $slip->bags_used
                - (float) $slip->bags_returned_to_factory
                - (float) $slip->bags_held_with_sizing,
            2
        );
        $bagNotice = $bagVariance > 0
            ? ' Bag check: '.number_format($bagVariance, 2).' bag(s) are unaccounted for.'
            : ($bagVariance < 0 ? ' Bag check: recorded used/returned/held bags exceed the amount sent.' : ' Bag check balanced.');

        return redirect()->route('sizing.index')
            ->with('success', "Sizing Slip {$slip->slip_no} saved! Generated {$beamCount} full beams ({$meterPerBeam}m warp length each), representing {$slip->expectedFabricMetersPerBeam()}m expected fabric per beam.".$bagNotice);
    }

    public function assignBeam(Request $request, SizingBeam $beam): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_loom_no' => 'required|string|max:50',
            'mounted_date' => 'required|date',
        ]);

        $alreadyMounted = SizingBeam::where('assigned_loom_no', $validated['assigned_loom_no'])
            ->where('status', 'mounted_on_loom')
            ->whereKeyNot($beam->getKey())
            ->exists();

        if ($alreadyMounted) {
            return back()->withInput()->withErrors([
                'assigned_loom_no' => 'This loom already has a beam mounted. Mark it finished before mounting another beam.',
            ]);
        }

        if (! in_array($beam->status, ['ready_in_store', 'mounted_on_loom'], true) || $beam->remaining_length_meters <= 0) {
            return back()->with('error', 'This beam is exhausted or is not available to mount.');
        }

        DB::transaction(function () use ($beam, $validated): void {
            $assignment = LoomAssignment::where('loom_no', $validated['assigned_loom_no'])
                ->where('start_date', '<=', $validated['mounted_date'])
                ->where(function ($query) use ($validated): void {
                    $query->whereNull('end_date')->orWhere('end_date', '>=', $validated['mounted_date']);
                })->first();
            if (! $assignment) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'assigned_loom_no' => 'Allocate this loom to a production order for the mounting date first.',
                ]);
            }
            $order = WorkOrder::findOrFail($assignment->work_order_id);
            if ($beam->work_order_id && (int) $beam->work_order_id !== (int) $order->id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'assigned_loom_no' => 'This beam belongs to its previous contract; use Settle & Reassign.',
                ]);
            }
            if ($beam->fabric_stock_id && (int) $beam->fabric_stock_id !== (int) $order->fabric_stock_id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'assigned_loom_no' => 'This beam quality differs from the loom order.',
                ]);
            }
            $previousLoom = $beam->assigned_loom_no;
            $previousOrder = $beam->work_order_id;
            $beam->update([
                'assigned_loom_no' => $validated['assigned_loom_no'],
                'mounted_date' => $validated['mounted_date'],
                'status' => 'mounted_on_loom',
                'work_order_id' => $order->id,
                'fabric_stock_id' => $order->fabric_stock_id,
            ]);
            BeamEvent::create([
                'sizing_beam_id' => $beam->id, 'from_work_order_id' => $previousOrder,
                'to_work_order_id' => $order->id, 'from_loom' => $previousLoom,
                'to_loom' => $beam->assigned_loom_no, 'action' => 'mount',
                'remaining_length_meters' => $beam->remaining_length_meters,
                'event_date' => $validated['mounted_date'],
            ]);
        });

        return redirect()->route('sizing.index')
            ->with('success', "Beam {$beam->beam_no} mounted onto {$beam->assigned_loom_no} successfully!");
    }

    public function exhaustBeam(SizingBeam $beam): RedirectResponse
    {
        if ((float) $beam->remaining_length_meters > 0.01) {
            return back()->with('error', 'Beam still has remaining length. Transfer/unmount it in Factory Orders instead of deleting its balance.');
        }
        $beam->update([
            'status' => 'exhausted',
            'remaining_length_meters' => 0,
        ]);

        return redirect()->route('sizing.index')
            ->with('info', "Beam {$beam->beam_no} marked as finished / exhausted.");
    }
}
