@extends('layouts.app')
@section('title', 'Factory Reports and Mazdoori')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    <header><h1 class="text-2xl font-black">Factory Reports & Mazdoor Khata</h1><p class="text-sm text-zinc-500 mt-1">Order-wise loom output, checking, dispatch, acceptance and estimated yarn usage. Includes a simple wages and downtime register.</p></header>
    @include('factory._nav')
    <section class="card p-5 space-y-3"><h2 class="text-lg font-extrabold">Order-wise Production and Party Hisaab</h2>
        <p class="text-xs text-zinc-500">Yarn weight is theoretical from the saved Parta. The recorded Taana/Baana issues are entered manually; compare them with actual verified consumption before settling.</p>
        <div class="overflow-x-auto"><table class="min-w-full text-xs"><thead class="bg-zinc-100"><tr><th class="p-2 text-left">Order / Type</th><th>Quality</th><th>Loom (m)</th><th>Checked (m)</th><th>Dispatched (m)</th><th>Accepted (m)</th><th>Job Bill (Rs)</th><th>Parta warp+weft (lb)</th><th>Recorded yarn issued (kg)</th></tr></thead><tbody>@forelse($rows as $r)<tr class="border-t"><td class="p-2 font-bold">{{ $r['order']->order_no }}<small class="block text-zinc-500 font-normal">{{ $r['order']->type }} · {{ $r['order']->party_name ?? 'Factory' }}</small></td><td>{{ $r['order']->fabricStock?->quality_name }}</td><td class="text-right">{{ number_format($r['meterOutput'],2) }}</td><td class="text-right">{{ number_format($r['checked'],2) }}</td><td class="text-right">{{ number_format($r['dispatched'],2) }}</td><td class="text-right">{{ number_format($r['accepted'],2) }}</td><td class="text-right">{{ number_format($r['billed'],2) }}</td><td class="text-right">{{ number_format($r['taana']+$r['baana'],2) }}</td><td class="text-right p-2">Taana {{ number_format($r['issuedTaana'],2) }} / Baana {{ number_format($r['issuedBaana'],2) }}</td></tr>@empty<tr><td colspan="9" class="p-4">No orders created yet.</td></tr>@endforelse</tbody></table></div>
        <p class="text-xs text-zinc-500">Do not call these amounts net profit: full energy, labour, preparation and overhead allocation is not automated. The report shows traceable amounts, not an invented margin.</p>
    </section>
    <div class="grid lg:grid-cols-2 gap-5">
        <section class="card p-5 space-y-3"><h2 class="text-lg font-extrabold">Mazdoor Khata / Wage Entry</h2>
            <form method="POST" action="{{ route('factory.wage.store') }}" class="space-y-3">@csrf
                <label class="block text-xs font-semibold">Worker Name<input name="worker_name" required class="w-full border rounded-lg p-2 mt-1"></label>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Order (optional)<select name="work_order_id" class="w-full border rounded-lg p-2 mt-1"><option value="">General labour</option>@foreach($orders as $o)<option value="{{ $o->id }}">{{ $o->order_no }}</option>@endforeach</select></label><label class="text-xs font-semibold">Loom<select name="loom_no" class="w-full border rounded-lg p-2 mt-1"><option value="">Not linked</option>@for($i=1;$i<=24;$i++)<option>{{ sprintf('Loom-%02d',$i) }}</option>@endfor</select></label></div>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Wage Date<input name="wage_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-2 mt-1"></label><label class="text-xs font-semibold">Payment Basis<select name="basis" class="w-full border rounded-lg p-2 mt-1"><option value="metre">Per metre</option><option value="day">Per day</option><option value="fixed">Fixed amount</option></select></label>
                <label class="text-xs font-semibold">Quantity<input name="quantity" required type="number" min=".001" step=".001" value="1" class="w-full border rounded-lg p-2 mt-1"></label><label class="text-xs font-semibold">Rate per unit (Rs)<input name="rate" required type="number" min="0" step=".0001" class="w-full border rounded-lg p-2 mt-1"></label>
                <label class="text-xs font-semibold">Advance adjusted (Rs)<input name="advance_adjustment" type="number" min="0" value="0" step=".01" class="w-full border rounded-lg p-2 mt-1"></label><label class="text-xs font-semibold">Already paid (Rs)<input name="paid_amount" type="number" min="0" value="0" step=".01" class="w-full border rounded-lg p-2 mt-1"></label></div>
                <input name="notes" placeholder="Remarks" class="w-full border rounded-lg p-2 text-sm">
                <button class="w-full bg-zinc-900 text-white rounded-lg p-3 font-bold">Save Wage Entry</button>
                <p class="text-xs text-zinc-500">The wage register calculates earnings and balance. For actual cash/bank payments use Expenses separately; this form does not debit a bank account.</p>
            </form>
        </section>
        <section class="card p-5 space-y-3"><h2 class="text-lg font-extrabold">Loom Downtime / Maintenance</h2>
            <form method="POST" action="{{ route('factory.stoppage.store') }}" class="space-y-3">@csrf
                <label class="block text-xs font-semibold">Loom<select name="loom_no" class="w-full border rounded-lg p-2 mt-1">@for($i=1;$i<=24;$i++)<option>{{ sprintf('Loom-%02d',$i) }}</option>@endfor</select></label>
                <label class="block text-xs font-semibold">Order (if applicable)<select name="work_order_id" class="w-full border rounded-lg p-2 mt-1"><option value="">General machine fault</option>@foreach($orders as $o)<option value="{{ $o->id }}">{{ $o->order_no }}</option>@endforeach</select></label>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Date<input name="stopped_on" type="date" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-2 mt-1"></label><label class="text-xs font-semibold">Downtime (minutes)<input name="downtime_minutes" required min="1" max="1440" type="number" class="w-full border rounded-lg p-2 mt-1"></label></div>
                <label class="block text-xs font-semibold">Reason<input name="reason" required placeholder="Yarn break, beam, mechanical..." class="w-full border rounded-lg p-2 mt-1"></label>
                <label class="block text-xs font-semibold">Estimated Repair Cost (Rs)<input name="repair_cost" min="0" step=".01" type="number" class="w-full border rounded-lg p-2 mt-1"></label>
                <textarea name="notes" rows="2" placeholder="Notes, parts used, mechanic" class="w-full border rounded-lg p-2 text-sm"></textarea>
                <button class="w-full bg-zinc-900 text-white rounded-lg p-3 font-bold">Record Stoppage</button>
                <p class="text-xs text-zinc-500">Issue spare parts through Parts. Record actual paid repairs through Expenses.</p>
            </form>
        </section>
    </div>
    <div class="grid lg:grid-cols-2 gap-5">
        <section class="card p-5"><h3 class="font-bold mb-3">Recent Mazdoori Entries</h3><div class="overflow-auto max-h-72"><table class="w-full text-xs"><thead class="bg-zinc-100"><tr><th class="text-left p-2">Worker</th><th>Qty × Rate</th><th>Gross</th><th>Balance</th></tr></thead><tbody>@forelse($wages as $w)<tr class="border-t"><td class="p-2">{{ $w->worker_name }}<small class="block text-zinc-500">{{ $w->wage_date }} · {{ $w->order?->order_no }}</small></td><td>{{ $w->quantity }} × {{ $w->rate }}</td><td>{{ number_format((float)$w->gross_amount,2) }}</td><td>{{ number_format((float)$w->balance_amount,2) }}</td></tr>@empty<tr><td colspan="4" class="p-3">No wages yet.</td></tr>@endforelse</tbody></table></div></section>
        <section class="card p-5"><h3 class="font-bold mb-3">Recent Machine Stoppages</h3><div class="overflow-auto max-h-72"><table class="w-full text-xs"><thead class="bg-zinc-100"><tr><th class="text-left p-2">Machine / Date</th><th>Reason</th><th>Minutes</th><th>Cost</th></tr></thead><tbody>@forelse($stoppages as $m)<tr class="border-t"><td class="p-2">{{ $m->loom_no }}<small class="block text-zinc-500">{{ $m->stopped_on }}</small></td><td>{{ $m->reason }}</td><td>{{ $m->downtime_minutes }}</td><td>{{ number_format((float)$m->repair_cost,2) }}</td></tr>@empty<tr><td colspan="4" class="p-3">No stoppages yet.</td></tr>@endforelse</tbody></table></div></section>
    </div>
</div>
@endsection
