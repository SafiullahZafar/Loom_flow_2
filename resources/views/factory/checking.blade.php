@extends('layouts.app')
@section('title', 'Checking and Thaan Stock')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#15803d]">QUALITY CONTROL &amp; INVENTORY</span>
            <h1 class="text-2xl sm:text-3xl font-black text-[#133e2b] tracking-tight">Checking &amp; Thaan Register</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">Only checked and measured fabric enters stock. Meter readings remain provisional.</p>
        </div>
    </header>

    <div class="grid lg:grid-cols-5 gap-6">
        
        <!-- ENTER OFFICE CHECKING REPORT (3 Cols) -->
        <section class="card p-6 lg:col-span-3 space-y-4 shadow-2xs">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="font-extrabold text-base text-[#133e2b]">Enter Office Checking Report</h2>
                <p class="text-xs text-stone-500">Choose own or job work, link the order or contract, and record each Thaan marking and measured length into that order's fabric quality stock.</p>
            </div>

            <form method="POST" action="{{ route('factory.checking.store') }}" id="checking-form" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-3.5">
                    <label class="text-xs font-bold text-stone-700">
                        Work Type
                        <select name="work_type" id="checking-work-type" required class="w-full mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white">
                            <option value="own">Own Factory Work</option>
                            <option value="job">Job Work</option>
                        </select>
                    </label>
                    <label class="text-xs font-bold text-stone-700" id="own-order-field">
                        Own Production Order
                        <select name="work_order_id" id="own-work-order" required class="w-full mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white">
                            <option value="">Choose own order</option>
                            @foreach($orders->where('type', 'own') as $order)
                                <option value="{{ $order->id }}" data-quality-id="{{ $order->fabric_stock_id }}">{{ $order->order_no }} · {{ $order->fabricStock?->quality_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="hidden text-xs font-bold text-stone-700" id="job-order-field">
                        Job Contract
                        <select name="work_order_id" id="job-work-order" disabled class="w-full mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white">
                            <option value="">Choose job contract</option>
                            @foreach($orders->where('type', 'job')->filter(fn ($order) => $order->contract) as $order)
                                <option value="{{ $order->id }}" data-quality-id="{{ $order->fabric_stock_id }}">{{ $order->contract->contract_no }} · {{ $order->customer?->name ?? $order->contract->buyer_name }} · {{ $order->order_no }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Fabric Quality
                        <select name="fabric_stock_id" id="checking-quality" required class="w-full mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white">
                            <option value="">Select an order first</option>
                            @foreach($fabrics as $fabric)
                                <option value="{{ $fabric->id }}">{{ $fabric->quality_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Date checked
                        <input name="checked_on" type="date" required value="{{ date('Y-m-d') }}" class="w-full mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Loom Number
                        <select name="loom_no" class="w-full mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]" required>
                            @foreach($looms as $loom)
                                <option value="{{ $loom }}">{{ $loom }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="border border-[#e2e8e0] rounded-2xl overflow-hidden">
                    <table class="w-full text-xs" id="roll-table">
                        <thead class="bg-stone-50 border-b border-stone-200 text-stone-600 text-[10px] font-bold uppercase tracking-wider">
                            <tr>
                                <th class="p-2.5 text-left">Thaan Marking / Number</th>
                                <th class="p-2.5 text-left">Measured Length (m)</th>
                                <th class="p-2.5 text-center w-16">Remove</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <tr>
                                <td class="p-2">
                                    <input name="rolls[0][roll_no]" required placeholder="TH-0001" class="w-full border border-[#e2e8e0] rounded-xl p-2 text-xs focus:outline-none focus:border-[#133e2b]">
                                </td>
                                <td class="p-2">
                                    <input name="rolls[0][length_meters]" required type="number" min="0.01" step=".01" placeholder="0.00" class="w-full border border-[#e2e8e0] rounded-xl p-2 text-xs focus:outline-none focus:border-[#133e2b]">
                                </td>
                                <td class="text-center text-stone-300 font-bold">&mdash;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-stone-50 border border-stone-200 rounded-xl">
                    <button type="button" id="more-rolls" class="border border-stone-300 bg-white hover:bg-stone-50 text-stone-700 rounded-xl py-2 px-3.5 font-bold text-xs shadow-2xs transition-all">
                        + Add Another Thaan
                    </button>
                    <div class="text-xs font-bold text-stone-800">
                        Total: <span id="checked-total" class="text-sm font-black text-[#133e2b]">0.00</span>m &middot; <span id="checked-pieces" class="text-sm font-black text-[#133e2b]">1</span> Thaans
                    </div>
                </div>

                <input name="notes" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]" placeholder="Optional checking report notes (inspector name, lot details)">

                <button class="w-full rounded-xl bg-[#133e2b] hover:bg-[#1b5239] text-white p-3 font-bold text-xs shadow-2xs transition-all" onclick="return confirm('Confirm each Thaan number and length? These metres will enter stock as A-grade.')">
                    Save Checked Thaans
                </button>
            </form>
        </section>

        <!-- ACTIVE LOOM QUALITY MAPPING (2 Cols) -->
        <section class="card p-6 lg:col-span-2 space-y-4 shadow-2xs">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="font-extrabold text-base text-[#133e2b]">Active Loom &rarr; Quality</h2>
                <p class="text-xs text-stone-500">The system finds the order automatically using loom and checking date.</p>
            </div>

            <div class="max-h-96 overflow-auto border border-stone-200 rounded-xl text-xs">
                <table class="w-full">
                    <thead class="bg-stone-50 border-b border-stone-200 text-[10px] font-bold uppercase text-stone-600">
                        <tr>
                            <th class="p-2.5 text-left">Loom</th>
                            <th class="p-2.5 text-left">Order</th>
                            <th class="p-2.5 text-left">Quality</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($assignments as $a)
                            <tr class="hover:bg-stone-50/60">
                                <td class="p-2.5 font-bold text-stone-900">{{ $a->loom_no }}</td>
                                <td class="p-2.5 text-stone-700">{{ $a->order?->order_no }}</td>
                                <td class="p-2.5 text-stone-600">{{ $a->order?->fabricStock?->quality_name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-4 text-center text-stone-400 italic">Allocate a loom first in Sizing &gt; Beams Tracking.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="text-[11px] text-stone-400">One loom can work on only one contract per day. Switching contracts is handled in the order allocation screen.</p>
        </section>

    </div>

    <!-- CHECKED THAAN STOCK HISTORY -->
    <section class="card p-6 space-y-4 shadow-2xs">
        <div class="pb-3 border-b border-[#e2e8e0] flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-base text-[#133e2b]">Checked Thaan Stock and History</h2>
                <p class="text-xs text-stone-500">Every saved Thaan with its work type, contract or order, quality, marking, length, owner, and checking notes.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[1400px] w-full text-xs">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-[10px] font-bold uppercase tracking-wider text-stone-600">
                        <th class="text-left p-3">Checked</th>
                        <th class="text-left p-3">Thaan marking</th>
                        <th class="p-3">Work type</th>
                        <th class="text-left p-3">Contract / Order</th>
                        <th class="text-left p-3">Quality</th>
                        <th class="p-3">Loom</th>
                        <th class="text-right p-3">Measured (m)</th>
                        <th class="text-center p-3">Owner</th>
                        <th class="text-center p-3">Status</th>
                        <th class="text-left p-3">Checking notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($rolls as $r)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <td class="p-3 text-stone-500">{{ $r->checked_on }}</td>
                            <td class="p-3 font-bold text-stone-900">{{ $r->roll_no }}</td>
                            <td class="text-center p-3 text-stone-700">{{ $r->order?->type === 'job' ? 'Job Work' : 'Own Factory' }}</td>
                            <td class="p-3 text-stone-700">
                                @if($r->order?->type === 'job')
                                    {{ $r->order?->contract?->contract_no ?? 'Contract not linked' }}
                                    <span class="block text-[10px] text-stone-400">Order {{ $r->order?->order_no }}</span>
                                @else
                                    {{ $r->order?->order_no ?? 'Order unavailable' }}
                                @endif
                            </td>
                            <td class="p-3 text-stone-700">{{ $r->fabricStock?->quality_name ?? $r->order?->fabricStock?->quality_name }}</td>
                            <td class="p-3 text-stone-700">{{ $r->loom_no }}</td>
                            <td class="text-right p-3 font-black text-[#133e2b] tabular-nums">{{ number_format((float)$r->length_meters,2) }}m</td>
                            <td class="text-center p-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $r->owner_type === 'customer' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-[#133e2b]' }}">
                                    {{ $r->owner_type }}
                                </span>
                            </td>
                            <td class="text-center p-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $r->status === 'ready' ? 'bg-[#e6f4ea] text-[#137333]' : 'bg-stone-100 text-stone-700' }}">
                                    {{ str_replace('_',' ',$r->status) }}
                                </span>
                            </td>
                            <td class="p-3 text-stone-600">{{ $r->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td class="p-6 text-center text-stone-400 italic" colspan="10">No checked Thaans yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $rolls->links() }}
    </section>

    <!-- PROVISIONAL METER READINGS -->
    <section class="card p-6 shadow-2xs space-y-3">
        <div class="pb-3 border-b border-[#e2e8e0]">
            <h2 class="font-extrabold text-base text-[#133e2b]">Recent Provisional Loom Meter Readings</h2>
            <p class="text-xs text-stone-500">These readings track production; they do not create inventory. Compare with the checking report by loom and date.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-[10px] font-bold uppercase tracking-wider text-stone-600">
                        <th class="text-left p-3">Date</th>
                        <th class="p-3 text-left">Loom</th>
                        <th class="p-3 text-right">Meter Reading Output</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($loomMeters as $m)
                        <tr class="hover:bg-stone-50/60">
                            <td class="p-3 text-stone-600">{{ $m->production_date }}</td>
                            <td class="p-3 font-bold text-stone-900">{{ $m->loom_no }}</td>
                            <td class="p-3 text-right font-black text-[#133e2b] tabular-nums">{{ number_format((float)$m->loom_meters,2) }} m</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-4 text-center text-stone-400 italic">No loom meter readings entered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
(function(){
    let n = 1;
    const table = document.querySelector('#roll-table tbody');
    const workType = document.getElementById('checking-work-type');
    const ownOrderField = document.getElementById('own-order-field');
    const jobOrderField = document.getElementById('job-order-field');
    const ownOrder = document.getElementById('own-work-order');
    const jobOrder = document.getElementById('job-work-order');
    const quality = document.getElementById('checking-quality');
    const updateWorkSelection = () => {
        const isJob = workType.value === 'job';
        ownOrderField.classList.toggle('hidden', isJob);
        jobOrderField.classList.toggle('hidden', !isJob);
        ownOrder.disabled = isJob;
        ownOrder.required = !isJob;
        jobOrder.disabled = !isJob;
        jobOrder.required = isJob;
        updateQuality();
    };
    const updateQuality = () => {
        const selectedOrder = workType.value === 'job' ? jobOrder : ownOrder;
        const qualityId = selectedOrder.selectedOptions[0]?.dataset.qualityId || '';
        Array.from(quality.options).forEach((option) => {
            option.disabled = option.value !== '' && option.value !== qualityId;
        });
        quality.value = qualityId;
    };
    workType.addEventListener('change', updateWorkSelection);
    ownOrder.addEventListener('change', updateQuality);
    jobOrder.addEventListener('change', updateQuality);
    updateWorkSelection();
    const recalc = () => {
        const l = [...table.querySelectorAll('input[type=number]')];
        document.getElementById('checked-total').textContent = l.reduce((t,x) => t + (Number(x.value)||0), 0).toFixed(2);
        document.getElementById('checked-pieces').textContent = table.rows.length;
    };
    document.getElementById('more-rolls').addEventListener('click', () => {
        if (n >= 200) return;
        let tr = document.createElement('tr');
        tr.innerHTML = '<td class="p-2"><input required name="rolls['+n+'][roll_no]" placeholder="Thaan marking" class="w-full border border-[#e2e8e0] rounded-xl p-2 text-xs focus:outline-none focus:border-[#133e2b]"></td><td class="p-2"><input required type="number" name="rolls['+n+'][length_meters]" min="0.01" step=".01" placeholder="0.00" class="w-full border border-[#e2e8e0] rounded-xl p-2 text-xs focus:outline-none focus:border-[#133e2b]"></td><td class="text-center"><button type="button" class="text-rose-600 hover:text-rose-800 font-bold px-3 text-sm">&times;</button></td>';
        tr.querySelector('button').addEventListener('click', () => { tr.remove(); recalc(); });
        table.appendChild(tr);
        n++;
        recalc();
    });
    table.addEventListener('input', recalc);
    recalc();
})();
</script>
@endsection
