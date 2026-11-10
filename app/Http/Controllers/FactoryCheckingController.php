<?php

namespace App\Http\Controllers;

use App\Models\LoomAssignment;
use App\Models\Production;
use App\Models\ThaanRoll;
use App\Models\WorkOrder;
use App\Services\FactoryStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FactoryCheckingController extends Controller
{
    public function index(): View
    {
        return view('factory.checking', [
            'assignments' => LoomAssignment::with('order.fabricStock')->whereNull('end_date')->orderBy('loom_no')->get(),
            'orders' => WorkOrder::with('fabricStock')->whereIn('status', ['active', 'fulfilled'])->orderBy('order_no')->get(),
            'rolls' => ThaanRoll::with('order.fabricStock')->latest('checked_on')->latest('id')->paginate(40),
            'loomMeters' => Production::selectRaw('loom_no, production_date, SUM(net_meters_produced) as loom_meters')
                ->groupBy('loom_no', 'production_date')->orderByDesc('production_date')->limit(40)->get(),
        ]);
    }

    public function store(Request $request, FactoryStockService $stock): RedirectResponse
    {
        $data = $request->validate([
            'checked_on' => 'required|date',
            'loom_no' => 'required|string|regex:/^Loom-(0[1-9]|1[0-9]|2[0-4])$/',
            'rolls' => 'required|array|min:1|max:200',
            'rolls.*.roll_no' => 'required|string|max:100|distinct|unique:thaan_rolls,roll_no',
            'rolls.*.length_meters' => 'required|numeric|gt:0|max:100000',
            'notes' => 'nullable|string|max:1000',
        ]);
        DB::transaction(function () use ($data, $stock): void {
            $assignment = LoomAssignment::where('loom_no', $data['loom_no'])
                ->where('start_date', '<=', $data['checked_on'])
                ->where(function ($q) use ($data): void {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $data['checked_on']);
                })->lockForUpdate()->first();
            if (! $assignment) {
                throw ValidationException::withMessages(['loom_no' => 'No order assigned to this loom for the checking date.']);
            }
            $order = WorkOrder::with('fabricStock')->findOrFail($assignment->work_order_id);
            if ($order->status === 'closed') {
                throw ValidationException::withMessages(['checked_on' => 'This production order is already closed.']);
            }
            foreach ($data['rolls'] as $entry) {
                ThaanRoll::create([
                    'roll_no' => trim($entry['roll_no']), 'work_order_id' => $order->id,
                    'fabric_stock_id' => $order->fabric_stock_id, 'loom_no' => $data['loom_no'],
                    'checked_on' => $data['checked_on'], 'length_meters' => round((float) $entry['length_meters'], 2),
                    'grade' => 'A', 'status' => 'ready',
                    'owner_type' => $order->type === 'job' ? 'customer' : 'factory',
                    'notes' => $data['notes'] ?? null,
                ]);
            }
            $total = round(array_sum(array_map(fn ($row): float => (float) $row['length_meters'], $data['rolls'])), 2);
            $stock->move($order->fabricStock, $total, count($data['rolls']), $data['checked_on'], 'Checking report '.$data['loom_no'].' / '.$order->order_no);
        });
        return back()->with('success', 'Checking report saved. Only the measured Thaan lengths were added to stock.');
    }
}
