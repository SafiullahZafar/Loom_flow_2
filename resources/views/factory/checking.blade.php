@extends('layouts.app')
@section('title', 'Checking and Thaan Stock')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    <header><h1 class="text-2xl font-black">Checking & Thaan Register</h1><p class="text-sm text-zinc-500 mt-1">Only checked and measured fabric enters stock. Meter readings remain provisional.</p></header>
    @include('factory._nav')
    <div class="grid lg:grid-cols-5 gap-5">
        <section class="card p-5 lg:col-span-3 space-y-3">
            <h2 class="font-extrabold">Enter Office Checking Report</h2>
            <p class="text-xs text-zinc-500">Record the checking date, loom number, and every Thaan's own marking and measured length. All enter as A-grade.</p>
            <form method="POST" action="{{ route('factory.checking.store') }}" id="checking-form" class="space-y-3">@csrf
                <div class="grid sm:grid-cols-2 gap-3">
                    <label class="text-xs font-semibold">Date checked<input name="checked_on" type="date" required value="{{ date('Y-m-d') }}" class="w-full mt-1 border rounded-lg p-2"></label>
                    <label class="text-xs font-semibold">Loom Number<select name="loom_no" class="w-full mt-1 border rounded-lg p-2" required>@for($i=1;$i<=24;$i++)<option>{{ sprintf('Loom-%02d',$i) }}</option>@endfor</select></label>
                </div>
                <div class="border rounded-xl overflow-hidden"><table class="w-full text-sm" id="roll-table"><thead class="bg-zinc-100"><tr><th class="p-2 text-left">Thaan Marking / Number</th><th class="p-2 text-left">Measured Length (m)</th><th class="p-2">Remove</th></tr></thead><tbody><tr><td class="p-2"><input name="rolls[0][roll_no]" required placeholder="TH-0001" class="w-full border rounded-lg p-2"></td><td class="p-2"><input name="rolls[0][length_meters]" required type="number" min="0.01" step=".01" class="w-full border rounded-lg p-2"></td><td class="text-center text-zinc-400">—</td></tr></tbody></table></div>
                <div class="flex flex-wrap items-center justify-between gap-2"><button type="button" id="more-rolls" class="border rounded-lg py-2 px-4 font-bold text-xs">+ Add Another Thaan</button><strong class="text-sm">Total: <span id="checked-total">0.00</span>m · <span id="checked-pieces">1</span> Thaans</strong></div>
                <input name="notes" class="w-full border rounded-lg p-2 text-sm" placeholder="Optional checking report notes">
                <button class="w-full rounded-lg bg-zinc-900 text-white p-3 font-bold" onclick="return confirm('Confirm each Thaan number and length? These metres will enter stock as A-grade.')">Save Checked Thaans</button>
            </form>
        </section>
        <section class="card p-5 lg:col-span-2 space-y-3">
            <h2 class="font-extrabold">Active Loom → Quality → Order</h2>
            <p class="text-xs text-zinc-500">The system finds the order automatically using loom and checking date.</p>
            <div class="max-h-96 overflow-auto text-xs"><table class="w-full"><thead><tr class="bg-zinc-100"><th class="p-2 text-left">Loom</th><th>Order</th><th>Quality</th></tr></thead><tbody>@forelse($assignments as $a)<tr class="border-t"><td class="p-2 font-bold">{{ $a->loom_no }}</td><td>{{ $a->order?->order_no }}</td><td>{{ $a->order?->fabricStock?->quality_name }}</td></tr>@empty<tr><td colspan="3" class="p-4">Allocate a loom first in Factory Orders.</td></tr>@endforelse</tbody></table></div>
            <p class="text-xs text-zinc-500">One loom can work on only one contract per day. Switching contracts is handled in the order allocation screen.</p>
        </section>
    </div>
    <section class="card p-5 space-y-3"><h2 class="font-extrabold">Checked Thaan Stock and History</h2><div class="overflow-x-auto"><table class="min-w-full text-xs"><thead class="bg-zinc-100"><tr><th class="text-left p-2">Checked</th><th>Thaan marking</th><th>Loom</th><th>Order</th><th>Quality</th><th>Measured (m)</th><th>Owner</th><th>Status</th></tr></thead><tbody>@forelse($rolls as $r)<tr class="border-t"><td class="p-2">{{ $r->checked_on }}</td><td class="font-bold">{{ $r->roll_no }}</td><td>{{ $r->loom_no }}</td><td>{{ $r->order?->order_no }}</td><td>{{ $r->order?->fabricStock?->quality_name }}</td><td class="text-right pr-2">{{ number_format((float)$r->length_meters,2) }}</td><td class="text-center">{{ $r->owner_type }}</td><td class="text-center">{{ str_replace('_',' ',$r->status) }}</td></tr>@empty<tr><td class="p-4" colspan="8">No checked Thaans yet.</td></tr>@endforelse</tbody></table></div>{{ $rolls->links() }}</section>
    <section class="card p-5"><h2 class="font-extrabold mb-3">Recent Provisional Loom Meter Readings</h2><p class="text-xs text-zinc-500 mb-3">These readings track production; they do not create inventory. Compare with the checking report by loom and date.</p><div class="overflow-x-auto"><table class="w-full text-xs"><thead class="bg-zinc-100"><tr><th class="text-left p-2">Date</th><th>Loom</th><th>Meter Reading Output</th></tr></thead><tbody>@forelse($loomMeters as $m)<tr class="border-t"><td class="p-2">{{ $m->production_date }}</td><td>{{ $m->loom_no }}</td><td>{{ number_format((float)$m->loom_meters,2) }} m</td></tr>@empty<tr><td colspan="3" class="p-3">No loom meter readings entered yet.</td></tr>@endforelse</tbody></table></div></section>
</div>
<script>
(function(){let n=1;const table=document.querySelector('#roll-table tbody');const recalc=()=>{const l=[...table.querySelectorAll('input[type=number]')];document.getElementById('checked-total').textContent=l.reduce((t,x)=>t+(Number(x.value)||0),0).toFixed(2);document.getElementById('checked-pieces').textContent=table.rows.length};
 document.getElementById('more-rolls').addEventListener('click',()=>{if(n>=200)return;let tr=document.createElement('tr');tr.innerHTML='<td class="p-2"><input required name="rolls['+n+'][roll_no]" placeholder="Thaan marking" class="w-full border rounded-lg p-2"></td><td class="p-2"><input required type="number" name="rolls['+n+'][length_meters]" min="0.01" step=".01" class="w-full border rounded-lg p-2"></td><td class="text-center"><button type="button" class="text-red-700 font-bold px-3">×</button></td>';tr.querySelector('button').addEventListener('click',()=>{tr.remove();recalc()});table.appendChild(tr);n++;recalc()});table.addEventListener('input',recalc);recalc();})();
</script>
@endsection
