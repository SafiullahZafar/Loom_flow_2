@extends('layouts.app')
@section('title', 'Factory Orders & Parta')
@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-6">
    <header><h1 class="text-2xl font-black text-zinc-900">Factory Orders, Looms & Parta</h1><p class="text-sm text-zinc-500 mt-1">The master register for own-manufacturing and job-work weaving.</p></header>
    @include('factory._nav')
    <div class="grid lg:grid-cols-2 gap-5" id="setup">
        <section class="card p-5 space-y-4">
            <div><h2 class="font-extrabold text-lg">Save Quality Parta</h2><p class="text-xs text-zinc-500">Saves a new costing version. Local yarn-weight divisor = 800; old order costing is retained.</p></div>
            <form method="POST" action="{{ route('factory.parta') }}" class="space-y-3" id="parta-form">@csrf
                <label class="block text-sm font-semibold">Existing Fabric Quality<select name="fabric_stock_id" class="mt-1 w-full border rounded-lg p-2" required>@foreach($fabrics as $f)<option value="{{ $f->id }}">{{ $f->quality_name }}</option>@endforeach</select></label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach(['read'=>'Read (ends/in)', 'pick'=>'Pick (picks/in)', 'width_inches'=>'Panna/Width (in)', 'warp_count'=>'Taana Count', 'weft_count'=>'Baana Count', 'warp_rate_per_lb'=>'Taana rate (Rs/lb)', 'weft_rate_per_lb'=>'Baana rate (Rs/lb)', 'conversion_rate_per_pick'=>'Mazdoori per pick (Rs)'] as $field=>$label)
                        <label class="block text-xs font-semibold">{{ $label }}<input name="{{ $field }}" type="number" step="any" min="0" required value="{{ old($field, 0) }}" class="mt-1 w-full border rounded-lg p-2 text-sm parta-field"></label>
                    @endforeach
                </div>
                <div class="bg-zinc-100 rounded-lg p-3 text-sm space-y-1" id="parta-preview"><strong>Preview:</strong> enter read, pick, counts and width to calculate weight and cost.</div>
                <button class="w-full rounded-lg bg-zinc-900 text-white font-bold p-3">Save Parta Version</button>
                <p class="text-xs text-zinc-500">To create a new quality first, use <a class="underline font-bold" href="{{ route('fabric.index') }}">Fabric Stock → New Quality</a>.</p>
            </form>
        </section>
        <section class="card p-5 space-y-4">
            <div><h2 class="font-extrabold text-lg">Create Production Order</h2><p class="text-xs text-zinc-500">One order may run on several looms. Own-manufacturing target can be blank.</p></div>
            <form method="POST" action="{{ route('factory.order.store') }}" class="space-y-3">@csrf
                <div class="grid grid-cols-2 gap-3"><label class="block text-xs font-semibold">Order no.<input required name="order_no" value="{{ old('order_no') }}" placeholder="PO-001" class="mt-1 w-full border rounded-lg p-2"></label><label class="block text-xs font-semibold">Type<select name="type" id="order-type" class="mt-1 w-full border rounded-lg p-2"><option value="own">Own Manufacturing</option><option value="job">Job Work</option></select></label></div>
                <label class="block text-xs font-semibold">Fabric Quality<select required name="fabric_stock_id" class="mt-1 w-full border rounded-lg p-2">@foreach($fabrics as $f)<option value="{{ $f->id }}">{{ $f->quality_name }}</option>@endforeach</select></label>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Target metres (own: optional)<input name="target_meters" type="number" step="0.01" min="0.01" class="mt-1 w-full border rounded-lg p-2"></label><label class="text-xs font-semibold">Start Date<input name="start_date" type="date" required value="{{ date('Y-m-d') }}" class="mt-1 w-full border rounded-lg p-2"></label></div>
                <div id="job-order-fields" class="space-y-3 bg-zinc-50 rounded-lg p-3 border border-zinc-200">
                    <p class="text-xs text-zinc-500">Fill these for job work. To link an existing Sales Contract, select it and match its fabric quality.</p>
                    <label class="block text-xs font-semibold">Existing Sales Contract (optional)<select name="existing_contract_id" class="mt-1 w-full border rounded-lg p-2"><option value="">Create a new contract below</option>@foreach($existingContracts as $c)<option value="{{ $c->id }}">{{ $c->contract_no }} — {{ $c->buyer_name }}</option>@endforeach</select></label>
                    <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">New Contract No.<input name="contract_no" placeholder="JC-001" class="mt-1 w-full border rounded-lg p-2"></label><label class="text-xs font-semibold">Customer / Party<input name="party_name" class="mt-1 w-full border rounded-lg p-2"></label></div>
                    <label class="block text-xs font-semibold">Weaving rate per pick (Rs.)<input name="rate_per_pick" type="number" min="0" step="any" placeholder="e.g. 0.50" class="mt-1 w-full border rounded-lg p-2"></label>
                    <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Taana supplied by<select name="warp_supply" class="mt-1 w-full border rounded-lg p-2"><option value="customer">Customer</option><option value="factory">Factory</option><option value="mixed">Both</option></select></label><label class="text-xs font-semibold">Baana supplied by<select name="weft_supply" class="mt-1 w-full border rounded-lg p-2"><option value="customer">Customer</option><option value="factory">Factory</option><option value="mixed">Both</option></select></label></div>
                </div>
                <label class="block text-xs font-semibold">Notes<textarea name="notes" rows="2" class="mt-1 w-full border rounded-lg p-2"></textarea></label>
                <button class="w-full bg-zinc-900 text-white rounded-lg p-3 font-bold">Save Production Order</button>
            </form>
        </section>
    </div>
    <section class="card p-5 space-y-3" id="orders"><div class="flex justify-between items-center"><h2 class="text-lg font-extrabold">Production Orders</h2><span class="text-xs text-zinc-500">Completed target = Pakki accepted metres for job work</span></div>
        <div class="overflow-x-auto"><table class="min-w-full text-xs"><thead class="bg-zinc-100"><tr class="text-left"><th class="p-2">Order / Type</th><th>Party</th><th>Quality</th><th>Target</th><th>Accepted (job)</th><th>Parta cost/m</th><th>Status</th><th>Close</th></tr></thead><tbody>@forelse($orders as $o)<tr class="border-t"><td class="p-2 font-bold">{{ $o->order_no }}<span class="block text-zinc-400 font-normal">{{ strtoupper($o->type) }}</span></td><td>{{ $o->party_name ?? 'Factory' }}</td><td>{{ $o->fabricStock?->quality_name }}</td><td>{{ $o->target_meters ?? 'Open target' }}</td><td>{{ number_format((float) $o->accepted_meters, 2) }}</td><td>Rs {{ number_format((float) $o->parta?->cost_per_meter, 2) }}</td><td>{{ ucfirst($o->status) }}</td><td>@if($o->status !== 'closed')<form method="POST" action="{{ route('factory.order.close', $o) }}" class="flex flex-col gap-1">@csrf<input name="closed_on" type="date" value="{{ date('Y-m-d') }}" class="border rounded p-1" required><input name="closure_reason" placeholder="Reason if early" class="border rounded p-1"><button class="bg-zinc-900 text-white rounded p-1" onclick="return confirm('Close this order? Any remaining rejected fabric must be settled first.')">Close</button></form>@else{{ $o->closed_on }}@endif</td></tr>@empty<tr><td colspan="8" class="p-5 text-center">No production orders yet. Save Parta and create one above.</td></tr>@endforelse</tbody></table></div>{{ $orders->links() }}
    </section>
    <div class="grid lg:grid-cols-2 gap-5">
        <section class="card p-5 space-y-3" id="looms"><h2 class="text-lg font-extrabold">Assign Loom to Order</h2><p class="text-xs text-zinc-500">A loom runs one contract on any given day. Switching is allowed from a later date, after recording the beam balance.</p>
            <form method="POST" action="{{ route('factory.loom.assign') }}" class="space-y-3">@csrf
                <label class="block text-xs font-semibold">Order<select name="work_order_id" required class="mt-1 w-full border rounded-lg p-2">@foreach($activeOrders as $o)<option value="{{ $o->id }}">{{ $o->order_no }} — {{ $o->fabricStock?->quality_name }}</option>@endforeach</select></label>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Loom<select name="loom_no" class="mt-1 w-full border rounded-lg p-2">@for($i=1;$i<=24;$i++)<option>{{ sprintf('Loom-%02d',$i) }}</option>@endfor</select></label><label class="text-xs font-semibold">Effective from<input type="date" required name="start_date" value="{{ date('Y-m-d') }}" class="mt-1 w-full border rounded-lg p-2"></label></div>
                <input name="change_reason" placeholder="Reason for switch (optional)" class="border rounded-lg p-2 w-full text-sm">
                <button class="bg-zinc-900 text-white rounded-lg p-3 font-bold w-full">Save Loom Allocation</button>
            </form><div class="overflow-auto max-h-52"><table class="text-xs w-full"><thead><tr><th class="text-left p-2">Loom</th><th class="text-left">Running order</th><th>From</th></tr></thead><tbody>@forelse($assignments as $a)<tr class="border-t"><td class="p-2 font-bold">{{ $a->loom_no }}</td><td>{{ $a->order?->order_no }} · {{ $a->order?->fabricStock?->quality_name }}</td><td>{{ $a->start_date }}</td></tr>@empty<tr><td class="p-2">No active loom allocations</td></tr>@endforelse</tbody></table></div>
        </section>
        <section class="card p-5 space-y-3" id="beams"><h2 class="text-lg font-extrabold">Beam Transfer / Settlement</h2><p class="text-xs text-zinc-500">Beam keeps its original order during loom transfers. Change its order only through Settle & Reassign.</p>
            <form method="POST" action="{{ route('factory.beam') }}" class="space-y-3">@csrf
                <label class="block text-xs font-semibold">Beam<select id="beam-selector" name="sizing_beam_id" required class="mt-1 w-full border rounded-lg p-2">@foreach($beams as $b)<option value="{{ $b->id }}" data-weight="{{ $b->initial_net_weight_kg }}" data-total="{{ $b->total_length_meters }}" data-left="{{ $b->remaining_length_meters }}" data-rate="{{ $b->workOrder?->contract?->yarn_warp_rate ?? $b->workOrder?->parta?->warp_rate_per_lb ?? 0 }}">{{ $b->beam_name ?? $b->beam_no }} · Left {{ number_format((float)$b->remaining_length_meters,2) }}m · {{ $b->assigned_loom_no ?? 'Store' }}</option>@endforeach</select></label>
                <label class="block text-xs font-semibold">Action<select name="action" class="mt-1 w-full border rounded-lg p-2"><option value="mount">Mount / Transfer to another loom (same order)</option><option value="unmount">Unmount to Store</option><option value="settle_reassign">Settle customer yarn and reassign to another order</option></select></label>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Target order<select name="work_order_id" class="mt-1 w-full border rounded-lg p-2"><option value="">Keep original order</option>@foreach($activeOrders as $o)<option value="{{ $o->id }}">{{ $o->order_no }}</option>@endforeach</select></label><label class="text-xs font-semibold">Target Loom<select name="loom_no" class="mt-1 w-full border rounded-lg p-2"><option value="">Unmount / no loom</option>@for($i=1;$i<=24;$i++)<option>{{ sprintf('Loom-%02d',$i) }}</option>@endfor</select></label></div>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Original Beam Weight (kg, if known)<input type="number" step=".001" name="initial_net_weight_kg" class="mt-1 w-full border rounded-lg p-2"></label><label class="text-xs font-semibold">Remaining Yarn (kg, optional override)<input type="number" step=".001" name="remaining_weight_kg" class="mt-1 w-full border rounded-lg p-2"></label></div>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Agreed settlement (Rs, optional when predicted)<input name="agreed_settlement" type="number" step=".01" min="0" class="mt-1 w-full border rounded-lg p-2"></label><label class="text-xs font-semibold">Date<input type="date" name="event_date" required value="{{ date('Y-m-d') }}" class="mt-1 w-full border rounded-lg p-2"></label></div>
                <textarea name="notes" placeholder="Notes" rows="2" class="w-full border rounded-lg p-2 text-xs"></textarea>
                <p id="settlement-preview" class="p-2 bg-zinc-100 rounded-lg text-xs font-semibold">Predicted settlement: select a beam and weight</p>
                <button class="bg-zinc-900 text-white rounded-lg p-3 font-bold w-full">Record Beam Action</button>
            </form>
            <p class="text-xs text-zinc-500">Predicted settlement = remaining warp kg × 2.20462 × original contract Taana rate per lb. Final amount may be overridden.</p>
            <div class="max-h-44 overflow-auto text-xs">@foreach($recentBeams as $e)<p class="border-t py-2">{{ $e->event_date }} · {{ $e->beam?->beam_no }} · {{ $e->action }} · {{ number_format((float)$e->remaining_length_meters,2) }}m left @if($e->agreed_settlement !== null)· Rs {{ number_format((float)$e->agreed_settlement,2) }} agreed (predicted Rs {{ number_format((float)$e->predicted_settlement,2) }})@endif</p>@endforeach</div>
        </section>
    </div>
    <section class="card p-5 space-y-4" id="yarn"><h2 class="text-lg font-extrabold">Sutar / Taana / Baana Movement Khata</h2><p class="text-xs text-zinc-500">Record customer- or factory-supplied yarn per order. Actual weight is used, not an assumed bag size. This order khata does not itself post a supplier purchase or payment.</p>
        <form method="POST" action="{{ route('factory.yarn') }}" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">@csrf
            <label class="text-xs font-semibold">Order<select name="work_order_id" required class="mt-1 border rounded-lg p-2 w-full">@foreach($activeOrders as $o)<option value="{{ $o->id }}">{{ $o->order_no }}</option>@endforeach</select></label>
            <label class="text-xs font-semibold">Material<select name="component" class="mt-1 border rounded-lg p-2 w-full"><option value="warp">Taana / Warp</option><option value="weft">Baana / Weft</option></select></label>
            <label class="text-xs font-semibold">Owner<select name="owner_type" class="mt-1 border rounded-lg p-2 w-full"><option value="customer">Customer</option><option value="factory">Factory</option></select></label>
            <label class="text-xs font-semibold">Movement<select name="movement_type" class="mt-1 border rounded-lg p-2 w-full"><option value="receipt">Receipt</option><option value="issue">Issued / Consumed</option><option value="return">Returned to Order Stock</option><option value="adjustment_in">Adjustment (+)</option><option value="adjustment_out">Adjustment (−)</option></select></label>
            <label class="text-xs font-semibold">Net weight (kg)<input required name="weight_kg" type="number" min=".001" step=".001" class="mt-1 border rounded-lg p-2 w-full"></label>
            <label class="text-xs font-semibold">Bags (optional)<input name="bags" type="number" min="0" step=".01" class="mt-1 border rounded-lg p-2 w-full"></label>
            <label class="text-xs font-semibold">Count / Lot<input name="yarn_count" placeholder="20/1" class="mt-1 border rounded-lg p-2 w-full"></label>
            <label class="text-xs font-semibold">Lot No<input name="lot_no" class="mt-1 border rounded-lg p-2 w-full"></label>
            <label class="text-xs font-semibold">Reference No<input name="reference_no" class="mt-1 border rounded-lg p-2 w-full"></label>
            <label class="text-xs font-semibold">Date<input type="date" name="movement_date" value="{{ date('Y-m-d') }}" required class="mt-1 border rounded-lg p-2 w-full"></label>
            <label class="text-xs font-semibold">Where is the yarn going?<select name="destination_type" class="mt-1 border rounded-lg w-full p-2"><option value="factory_store">Factory Store</option><option value="sizing">Sizing Contractor</option><option value="loom">Loom</option></select></label>
            <input name="destination_name" placeholder="Sizing contractor / destination (if applicable)" class="border rounded-lg p-2 text-sm">
            <input name="notes" placeholder="Notes / Sizing / Weaver" class="border rounded-lg p-2 text-sm lg:col-span-2">
            <button class="lg:col-span-4 bg-zinc-900 text-white rounded-lg p-3 font-bold">Save Yarn Movement</button>
        </form>
        <div class="overflow-x-auto"><h3 class="text-sm font-bold mb-2">Order-wise yarn balances</h3><table class="w-full text-xs"><thead><tr class="bg-zinc-100"><th class="p-2 text-left">Order</th><th>Material</th><th>Owner</th><th>Balance (kg)</th></tr></thead><tbody>@forelse($yarnBalances as $y)<tr class="border-t"><td class="p-2">{{ $activeOrders->firstWhere('id',$y->work_order_id)?->order_no ?? '#'.$y->work_order_id }}</td><td>{{ $y->component }}</td><td>{{ $y->owner_type }}</td><td class="text-right p-2 font-bold">{{ number_format((float)$y->balance_kg,3) }}</td></tr>@empty<tr><td colspan="4" class="p-3">No yarn transactions recorded.</td></tr>@endforelse</tbody></table></div>
    </section>
</div>
<script>
(function(){
  const type=document.getElementById('order-type'), job=document.getElementById('job-order-fields');
  function update(){job.style.display=type.value==='job'?'block':'none'}; type.addEventListener('change',update); update();
  const f=document.getElementById('parta-form'), display=document.getElementById('parta-preview');
  function preview(){const v=n=>Number(f.elements[n].value)||0;const read=v('read'),pick=v('pick'),width=v('width_inches'),warp=v('warp_count'),weft=v('weft_count');
   if(!read||!pick||!width||!warp||!weft){display.textContent='Enter fabric specification to preview weight and cost.';return;}
   const tr=x=>Math.floor((x+0.000000001)*10000)/10000;
   const a=tr(read*width/warp*1.0936/800),b=tr(pick*width/weft*1.0936/800),kg=(a+b)/2.2046226218;
   const cost=a*v('warp_rate_per_lb')+b*v('weft_rate_per_lb')+pick*v('conversion_rate_per_pick');
   display.textContent='Taana '+a.toFixed(4)+' lb/m · Baana '+b.toFixed(4)+' lb/m · Total '+(kg*1000).toFixed(2)+' grams/m · GSM '+(kg*1000/(width*.0254)).toFixed(2)+' · Cost Rs '+cost.toFixed(2)+'/m'; }
  f.querySelectorAll('input').forEach(el=>el.addEventListener('input',preview));preview();
  const beam=document.getElementById('beam-selector'),out=document.getElementById('settlement-preview');
  const beamForm=beam.closest('form');function settlement(){const op=beam.selectedOptions[0];const supplied=Number(beamForm.elements['remaining_weight_kg'].value)||0;
    const original=Number(beamForm.elements['initial_net_weight_kg'].value)||Number(op?.dataset.weight)||0;
    const kg=supplied||original*(Number(op?.dataset.left)||0)/(Number(op?.dataset.total)||1);
    const rate=Number(op?.dataset.rate)||0;
    out.textContent=kg>0&&rate>0?'Predicted: Rs '+(kg*2.2046226218*rate).toFixed(2)+' ('+kg.toFixed(3)+' kg × '+rate+' Rs/lb). You can enter a different agreed amount.':'Predicted: supply the beam weight and ensure the original contract has a Taana rate; otherwise enter the final agreed amount.';}
  beam.addEventListener('change',settlement);beamForm.querySelectorAll('input').forEach(el=>el.addEventListener('input',settlement));settlement();
})();
</script>
@endsection
