<?php

namespace App\Http\Controllers;

use App\Models\FactoryWorkerWage;
use App\Models\KachiParchi;
use App\Models\LoomAssignment;
use App\Models\LoomStoppage;
use App\Models\PakkiParchi;
use App\Models\Production;
use App\Models\ThaanRoll;
use App\Models\WorkOrder;
use App\Models\YarnMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FactoryReportController extends Controller
{
    public function index(): View
    {
        $orders = WorkOrder::with(['fabricStock', 'parta', 'contract'])->orderByDesc('id')->get();
        $rows = $orders->map(function (WorkOrder $order): array {
            $meterOutput = (float) Production::where('work_order_id', $order->id)->sum('net_meters_produced');
            $checked = (float) ThaanRoll::where('work_order_id', $order->id)->sum('length_meters');
            $meterVariance = $meterOutput - $checked;
            $dispatched = (float) KachiParchi::where('work_order_id', $order->id)->sum('dispatched_meters');
            $accepted = (float) PakkiParchi::whereHas('kachi', fn ($q) => $q->where('work_order_id', $order->id))->sum('accepted_meters');
            $billed = (float) PakkiParchi::whereHas('kachi', fn ($q) => $q->where('work_order_id', $order->id))->sum('bill_amount');
            $partaTaanaKg = (float) $order->parta?->warp_lb_per_meter * $checked / 2.2046226218;
            $partaBaanaKg = (float) $order->parta?->weft_lb_per_meter * $checked / 2.2046226218;
            $issuedTaana = (float) YarnMovement::where('work_order_id', $order->id)->where('component', 'warp')
                ->selectRaw('COALESCE(SUM(CASE WHEN movement_type IN (?, ?) THEN weight_kg WHEN movement_type IN (?, ?) THEN -weight_kg ELSE 0 END), 0) as net_weight_kg', ['issue', 'adjustment_out', 'return', 'adjustment_in'])
                ->value('net_weight_kg');
            $issuedBaana = (float) YarnMovement::where('work_order_id', $order->id)->where('component', 'weft')
                ->selectRaw('COALESCE(SUM(CASE WHEN movement_type IN (?, ?) THEN weight_kg WHEN movement_type IN (?, ?) THEN -weight_kg ELSE 0 END), 0) as net_weight_kg', ['issue', 'adjustment_out', 'return', 'adjustment_in'])
                ->value('net_weight_kg');

            return compact('order', 'meterOutput', 'checked', 'meterVariance', 'dispatched', 'accepted', 'billed', 'partaTaanaKg', 'partaBaanaKg', 'issuedTaana', 'issuedBaana');
        });

        return view('factory.reports', [
            'rows' => $rows, 'orders' => $orders,
            'wages' => FactoryWorkerWage::with('order')->latest('wage_date')->limit(40)->get(),
            'stoppages' => LoomStoppage::with('order')->latest('stopped_on')->limit(40)->get(),
            'activeAssignments' => LoomAssignment::with('order.fabricStock')->whereNull('end_date')->orderBy('loom_no')->get(),
        ]);
    }

    public function wage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'worker_name' => 'required|string|max:150', 'work_order_id' => 'nullable|exists:work_orders,id',
            'loom_no' => 'nullable|string|max:50', 'wage_date' => 'required|date',
            'basis' => 'required|in:metre,day,fixed', 'quantity' => 'required|numeric|gt:0',
            'rate' => 'required|numeric|min:0', 'advance_adjustment' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0', 'notes' => 'nullable|string|max:1000',
        ]);
        $gross = round((float) $data['quantity'] * (float) $data['rate'], 2);
        $balance = round($gross - (float) ($data['advance_adjustment'] ?? 0) - (float) ($data['paid_amount'] ?? 0), 2);
        if ($balance < -0.001) {
            return back()->withErrors(['paid_amount' => 'Paid plus advance cannot exceed calculated earnings.'])->withInput();
        }
        FactoryWorkerWage::create([...$data, 'gross_amount' => $gross, 'balance_amount' => $balance]);

        return back()->with('success', 'Mazdoor Khata saved. Payments here are informational: record cash/bank movement separately in Expenses to avoid double posting.');
    }

    public function stoppage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'loom_no' => ['required', 'string', 'regex:/^Loom-(0[1-9]|1[0-9]|2[0-4])$/'],
            'work_order_id' => 'nullable|exists:work_orders,id', 'stopped_on' => 'required|date',
            'downtime_minutes' => 'required|integer|min:1|max:1440',
            'reason' => 'required|string|max:150', 'repair_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        LoomStoppage::create($data);

        return back()->with('success', 'Machine downtime recorded. Repairs paid from cash/bank should be entered in Expenses.');
    }
}
