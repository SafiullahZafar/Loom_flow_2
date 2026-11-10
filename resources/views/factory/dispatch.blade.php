@extends('layouts.app')
@section('title', 'Kachi & Pakki Parchi')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#15803d]">CUSTOMER DISPATCH &amp; WEAVING BILLING</span>
            <h1 class="text-2xl sm:text-3xl font-black text-[#133e2b] tracking-tight">Dispatch &middot; Kachi / Pakki Parchi &middot; Returns</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">Kachi = sent fabric. Pakki = accepted fabric. Bill is based on Pakki metres only.</p>
        </div>
    </header>

    @include('factory._nav')

    <div class="grid lg:grid-cols-2 gap-6">
        
        <!-- 1. NEW KACHI PARCHI -->
        <section class="card p-6 space-y-4 shadow-2xs" id="kachi">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="font-extrabold text-base text-[#133e2b]">1. New Kachi Parchi (Job Work)</h2>
                <p class="text-xs text-stone-500">Dispatch checked Thaans to the buyer/customer.</p>
            </div>

            <form method="POST" action="{{ route('factory.kachi.store') }}" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Kachi No.
                        <input name="parchi_no" required placeholder="KP-001" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Dispatch Date
                        <input name="dispatch_date" type="date" value="{{ date('Y-m-d') }}" required class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                    </label>
                </div>

                <label class="block text-xs font-bold text-stone-700">
                    Order
                    <select name="work_order_id" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]" id="kachi-order" required>
                        <option value="">Choose order</option>
                        @foreach($jobOrders as $o)
                            <option value="{{ $o->id }}">{{ $o->order_no }} &middot; {{ $o->customer?->name ?? $o->party_name }} ({{ $o->fabricStock?->quality_name }})</option>
                        @endforeach
                    </select>
                </label>

                <div>
                    <label class="text-xs font-bold text-stone-700 block mb-1.5">Select checked Thaans (lengths added automatically):</label>
                    <div class="border border-[#e2e8e0] rounded-xl p-3 max-h-56 overflow-auto bg-stone-50 space-y-1.5" id="kachi-rolls">
                        @forelse($rolls->whereIn('status',['ready','returned_ready'])->filter(fn($r)=>$r->order?->type==='job') as $r)
                            <label data-order="{{ $r->work_order_id }}" class="kachi-option hidden text-xs flex items-center gap-2 p-1.5 rounded-lg hover:bg-stone-200/50 cursor-pointer">
                                <input type="checkbox" name="roll_ids[]" value="{{ $r->id }}" data-meters="{{ $r->length_meters }}" class="rounded text-[#133e2b] focus:ring-[#133e2b]">
                                <span class="font-bold text-stone-900">{{ $r->roll_no }}</span>
                                <span class="text-stone-500">&middot; {{ $r->loom_no }} &middot;</span>
                                <span class="font-extrabold text-[#133e2b]">{{ number_format((float)$r->length_meters,2) }}m</span>
                                @if($r->status==='returned_ready')
                                    <span class="text-[10px] text-amber-700 font-bold bg-amber-100 px-1.5 py-0.5 rounded">(resent)</span>
                                @endif
                            </label>
                        @empty
                            <p class="text-xs text-stone-400 italic">No available checked customer Thaans.</p>
                        @endforelse
                    </div>
                </div>

                <div class="p-3 bg-emerald-50/80 border border-emerald-200/60 rounded-xl flex items-center justify-between text-xs text-[#133e2b]">
                    <span>Selected Thaans:</span>
                    <span class="font-bold">
                        <strong id="kachi-count" class="font-black text-sm">0</strong> Thaans &middot;
                        <strong id="kachi-meters" class="font-black text-sm">0.00</strong>m
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <input name="vehicle_no" placeholder="Vehicle / Bilty No (optional)" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]">
                    <input name="notes" placeholder="Notes / Gate pass details" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]">
                </div>

                <button class="w-full bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-3 font-bold text-xs shadow-2xs transition-all">
                    Save Kachi Dispatch
                </button>
            </form>
        </section>

        <!-- 2. RECEIVE PAKKI PARCHI & GENERATE BILL -->
        <section class="card p-6 space-y-4 shadow-2xs" id="pakki">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="font-extrabold text-base text-[#133e2b]">2. Receive Pakki Parchi &amp; Generate Bill</h2>
                <p class="text-xs text-stone-500">One Pakki per Kachi. Invoice is created automatically for accepted metres &times; picks &times; per-pick rate.</p>
            </div>

            <form method="POST" action="{{ route('factory.pakki.store') }}" class="space-y-3.5">
                @csrf
                <label class="block text-xs font-bold text-stone-700">
                    Kachi awaiting Pakki
                    <select name="kachi_parchi_id" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white focus:outline-none focus:border-[#133e2b]" required>
                        <option value="">Select Kachi</option>
                        @foreach($pendingKachis as $k)
                            @if(!$k->pakki)
                                <option value="{{ $k->id }}">
                                    {{ $k->parchi_no }} &middot; {{ $k->order?->customer?->name ?? $k->order?->party_name }} &middot; {{ number_format((float)$k->dispatched_meters,0) }}m &middot; {{ $k->thaan_count }} Thaans
                                </option>
                            @endif
                        @endforeach
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Pakki No.
                        <input name="parchi_no" required placeholder="PKP-001" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs focus:outline-none focus:border-[#133e2b]">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Received On
                        <input type="date" name="received_date" value="{{ date('Y-m-d') }}" required class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs focus:outline-none focus:border-[#133e2b]">
                    </label>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Accepted Thaans
                        <input type="number" min="0" step="1" name="accepted_thaans" required class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs focus:outline-none focus:border-[#133e2b]">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Accepted Metres
                        <input type="number" min="0" step=".01" name="accepted_meters" required class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs focus:outline-none focus:border-[#133e2b]">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Rejected Metres
                        <input type="number" min="0" step=".01" name="rejected_meters" value="0" required class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs focus:outline-none focus:border-[#133e2b]">
                    </label>
                </div>

                <input name="notes" placeholder="Customer remarks / deductions / quality feedback" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]">

                <button class="w-full bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-3 font-bold text-xs shadow-2xs transition-all" onclick="return confirm('Confirm Pakki accepted quantities? A weaving invoice will be generated immediately.')">
                    Save Pakki &amp; Generate Weaving Bill
                </button>
            </form>
        </section>

    </div>

    <!-- REJECTED RETURNS & DISPOSITION -->
    <div class="grid lg:grid-cols-2 gap-6">
        
        <!-- 3. RECORD REJECTED THAAN RETURN -->
        <section class="card p-6 space-y-4 shadow-2xs" id="returns">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="text-base font-extrabold text-[#133e2b]">3. Record Rejected Thaan Return</h2>
                <p class="text-xs text-stone-500">Customer sends complete Thaans with your original markings. Original lengths are retained.</p>
            </div>

            <form method="POST" action="{{ route('factory.returns.store') }}" class="space-y-3.5">
                @csrf
                <label class="block text-xs font-bold text-stone-700">
                    Kachi with Pakki rejection
                    <select name="kachi_parchi_id" id="return-kachi" required class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white">
                        <option value="">Choose Kachi</option>
                        @foreach($rejectedKachis as $k)
                            @if($k->pakki && (float)$k->pakki->rejected_meters > 0)
                                <option value="{{ $k->id }}">{{ $k->parchi_no }} &middot; Rejected {{ $k->pakki->rejected_meters }}m</option>
                            @endif
                        @endforeach
                    </select>
                </label>

                <div class="border border-[#e2e8e0] rounded-xl bg-stone-50 p-2.5 max-h-48 overflow-auto space-y-1">
                    @foreach($rejectedKachis as $k)
                        @if($k->pakki && (float)$k->pakki->rejected_meters > 0)
                            @foreach($k->items as $item)
                                @if($item->roll?->status==='dispatched')
                                    <label data-kachi="{{ $k->id }}" class="return-option hidden flex items-center gap-2 p-1.5 rounded-lg hover:bg-stone-200/50 text-xs">
                                        <input type="checkbox" name="roll_ids[]" value="{{ $item->roll->id }}" class="rounded text-[#133e2b]">
                                        <span class="font-bold text-stone-900">{{ $item->roll->roll_no }}</span>
                                        <span class="text-stone-500">&mdash; {{ $item->length_meters }}m</span>
                                    </label>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                <label class="block text-xs font-bold text-stone-700">
                    Returned On
                    <input name="returned_on" type="date" value="{{ date('Y-m-d') }}" required class="mt-1 border border-[#e2e8e0] rounded-xl w-full p-2.5 text-xs">
                </label>

                <button class="bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-3 font-bold text-xs w-full shadow-2xs transition-all">
                    Record Physical Return
                </button>
            </form>
        </section>

        <!-- 4. DECIDE REJECTED FABRIC USE -->
        <section class="card p-6 space-y-4 shadow-2xs" id="disposition">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="text-base font-extrabold text-[#133e2b]">4. Decide Rejected Fabric Use</h2>
                <p class="text-xs text-stone-500">Resend as A-category, return to party, settle and keep, approved external sale, or carry forward.</p>
            </div>

            <form method="POST" action="{{ route('factory.disposition.store') }}" class="space-y-3.5">
                @csrf
                <label class="block text-xs font-bold text-stone-700">
                    Returned rejected Thaan
                    <select name="thaan_roll_id" required class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white">
                        <option value="">Choose returned Thaan</option>
                        @foreach($rolls->where('status','returned_hold') as $r)
                            <option value="{{ $r->id }}">{{ $r->roll_no }} &middot; {{ $r->order?->order_no }} &middot; {{ $r->length_meters }}m</option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-xs font-bold text-stone-700">
                    Decision
                    <select name="action" required class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white">
                        <option value="resend_a">Reclassify A &rarr; Send again on new Kachi</option>
                        <option value="return_customer">Return fabric to customer</option>
                        <option value="settle_keep">Settle with customer and keep in factory stock</option>
                        <option value="sell_approved">Settle and sell elsewhere with customer approval</option>
                        <option value="carry_forward">Carry to another contract of same party and quality</option>
                    </select>
                </label>

                <label class="block text-xs font-bold text-stone-700">
                    Destination Order (only if carry forward)
                    <select name="to_work_order_id" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white">
                        <option value="">Not required</option>
                        @foreach($jobOrders as $o)
                            <option value="{{ $o->id }}">{{ $o->order_no }} &middot; {{ $o->customer?->name ?? $o->party_name }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Agreed settlement (Rs if applicable)
                        <input type="number" name="settlement_amount" min="0" step=".01" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Approval Ref
                        <input name="approval_reference" placeholder="Ref / Date" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs">
                    </label>
                </div>

                <label class="block text-xs font-bold text-stone-700">
                    Decision Date
                    <input name="action_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs">
                </label>

                <input name="notes" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs" placeholder="Notes about settlement agreement">

                <button class="w-full bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-3 font-bold text-xs shadow-2xs transition-all">
                    Save Fabric Decision
                </button>
            </form>
        </section>

    </div>

    <!-- OWN MANUFACTURING SALE -->
    <section class="card p-6 space-y-4 shadow-2xs" id="own-sale">
        <div class="pb-3 border-b border-[#e2e8e0]">
            <h2 class="text-base font-extrabold text-[#133e2b]">Own Manufacturing &mdash; Sell Checked A-Grade Thaans</h2>
            <p class="text-xs text-stone-500">Sell self-manufactured stock directly with instant sales slip generation.</p>
        </div>

        <form method="POST" action="{{ route('factory.own-sale.store') }}" class="space-y-3.5">
            @csrf
            <div class="grid sm:grid-cols-4 gap-3">
                <label class="text-xs font-bold text-stone-700">
                    Sale Slip No.
                    <input required name="slip_no" placeholder="SL-001" class="w-full mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]">
                </label>
                <label class="text-xs font-bold text-stone-700">
                    Customer
                    <select required name="customer_id" class="w-full mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white focus:outline-none focus:border-[#133e2b]">
                        <option value="">Choose customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}{{ $customer->phone ? ' · '.$customer->phone : '' }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-xs font-bold text-stone-700">
                    Rs per metre
                    <input required name="rate_per_meter" type="number" step=".01" min="0" placeholder="0.00" class="w-full mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]">
                </label>
                <label class="text-xs font-bold text-stone-700">
                    Sale Date
                    <input required name="sale_date" type="date" value="{{ date('Y-m-d') }}" class="w-full mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]">
                </label>
            </div>

            <p class="text-xs font-bold text-stone-700">Select only Thaans of ONE quality:</p>
            <div class="border border-[#e2e8e0] rounded-xl p-3 max-h-40 overflow-auto bg-stone-50 grid sm:grid-cols-3 gap-2">
                @forelse($rolls->filter(fn($r)=>$r->status==='ready' && $r->owner_type==='factory') as $r)
                    <label class="text-xs flex items-center gap-2 p-1.5 rounded-lg hover:bg-stone-200/50 cursor-pointer">
                        <input type="checkbox" name="roll_ids[]" value="{{ $r->id }}" class="rounded text-[#133e2b]">
                        <span class="font-bold text-stone-900">{{ $r->roll_no }}</span>
                        <span class="text-stone-500">&mdash; {{ $r->length_meters }}m</span>
                    </label>
                @empty
                    <p class="text-xs text-stone-400 italic col-span-3">No factory-owned checked Thaans are currently ready.</p>
                @endforelse
            </div>

            <input name="notes" placeholder="Optional notes" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]">
            <button class="w-full bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-3 font-bold text-xs shadow-2xs transition-all">
                Record Own-Fabric Sale
            </button>
        </form>
    </section>

    <!-- KACHI / PAKKI HISTORY TABLE -->
    <section class="card p-6 shadow-2xs space-y-4">
        <div class="pb-3 border-b border-[#e2e8e0] flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-base text-[#133e2b]">Kachi / Pakki History</h2>
                <p class="text-xs text-stone-500">Audit trail of dispatches, customer acceptances and billing.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-stone-600 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 text-left">Kachi</th>
                        <th class="p-3">Order</th>
                        <th class="p-3 text-center">Thaans</th>
                        <th class="p-3 text-right">Dispatched</th>
                        <th class="p-3">Pakki</th>
                        <th class="p-3 text-right">Accepted</th>
                        <th class="p-3 text-right">Rejected</th>
                        <th class="p-3 text-right">Bill (Rs)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($kachis as $k)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <td class="p-3 font-bold text-stone-900">
                                <a href="{{ route('factory.kachi.print',$k) }}" target="_blank" class="text-[#133e2b] hover:underline font-black">
                                    {{ $k->parchi_no }} &nearr;
                                </a>
                            </td>
                            <td class="p-3 text-stone-700">{{ $k->order?->order_no }}</td>
                            <td class="p-3 text-center font-bold text-stone-800">{{ $k->thaan_count }}</td>
                            <td class="p-3 text-right font-semibold text-stone-800 tabular-nums">{{ number_format((float)$k->dispatched_meters,2) }}m</td>
                            <td class="p-3">
                                @if($k->pakki)
                                    <a href="{{ route('factory.pakki.print',$k->pakki) }}" target="_blank" class="text-emerald-700 font-bold hover:underline">
                                        {{ $k->pakki->parchi_no }} &nearr;
                                    </a>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 text-right font-black text-[#133e2b] tabular-nums">{{ number_format((float)$k->pakki?->accepted_meters,2) }}</td>
                            <td class="p-3 text-right text-rose-600 font-bold tabular-nums">{{ number_format((float)$k->pakki?->rejected_meters,2) }}</td>
                            <td class="p-3 text-right font-black text-[#133e2b] tabular-nums">
                                {{ $k->pakki ? 'Rs '.number_format((float)$k->pakki->bill_amount,2) : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="p-6 text-center text-stone-400 italic">No Kachi Parchis recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $kachis->links() }}
    </section>
</div>

<script>
(function(){
    const order = document.getElementById('kachi-order');
    const labels = [...document.querySelectorAll('.kachi-option')];
    const update = () => {
        let total = 0, count = 0;
        labels.forEach(el => {
            const on = el.dataset.order === order.value;
            el.classList.toggle('hidden', !on);
            const c = el.querySelector('input');
            if (!on) c.checked = false;
            if (c.checked) {
                count++;
                total += Number(c.dataset.meters) || 0;
            }
        });
        document.getElementById('kachi-count').textContent = count;
        document.getElementById('kachi-meters').textContent = total.toFixed(2);
    };
    order.addEventListener('change', update);
    labels.forEach(e => e.querySelector('input').addEventListener('change', update));
    update();

    const ret = document.getElementById('return-kachi');
    const rt = [...document.querySelectorAll('.return-option')];
    const updateRet = () => rt.forEach(e => {
        const on = e.dataset.kachi === ret.value;
        e.classList.toggle('hidden', !on);
        if (!on) e.querySelector('input').checked = false;
    });
    ret.addEventListener('change', updateRet);
    updateRet();
})();
</script>
@endsection
