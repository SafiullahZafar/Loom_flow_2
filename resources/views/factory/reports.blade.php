@extends('layouts.app')
@section('title', 'Factory Reports and Mazdoori')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#15803d]">OPERATIONAL AUDIT &amp; WAGES</span>
            <h1 class="text-2xl sm:text-3xl font-black text-[#133e2b] tracking-tight">Factory Reports &amp; Mazdoor Khata</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">Order-wise loom output, checking, dispatch, acceptance and estimated yarn usage. Includes a simple wages and downtime register.</p>
        </div>
    </header>

    @include('factory._nav')

    <!-- ORDER-WISE PRODUCTION AND PARTY HISAAB -->
    <section class="card p-6 space-y-4 shadow-2xs">
        <div class="pb-3 border-b border-[#e2e8e0]">
            <h2 class="text-base font-extrabold text-[#133e2b]">Order-wise Production and Party Hisaab</h2>
            <p class="text-xs text-stone-500">Yarn weight is theoretical from the saved Parta. The recorded Taana/Baana issues are entered manually; compare them with actual verified consumption before settling.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-stone-600 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 text-left">Order / Type</th>
                        <th class="p-3">Quality</th>
                        <th class="p-3 text-right">Loom (m)</th>
                        <th class="p-3 text-right">Checked (m)</th>
                        <th class="p-3 text-right">Dispatched (m)</th>
                        <th class="p-3 text-right">Accepted (m)</th>
                        <th class="p-3 text-right">Job Bill (Rs)</th>
                        <th class="p-3 text-right">Parta warp+weft (lb)</th>
                        <th class="p-3 text-right">Recorded yarn issued (kg)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($rows as $r)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <td class="p-3 font-bold text-stone-900">
                                {{ $r['order']->order_no }}
                                <small class="block text-stone-400 font-medium text-[10px]">{{ $r['order']->type }} &middot; {{ $r['order']->party_name ?? 'Factory' }}</small>
                            </td>
                            <td class="p-3 text-stone-700">{{ $r['order']->fabricStock?->quality_name }}</td>
                            <td class="p-3 text-right text-stone-700 tabular-nums">{{ number_format($r['meterOutput'],2) }}</td>
                            <td class="p-3 text-right text-stone-700 tabular-nums">{{ number_format($r['checked'],2) }}</td>
                            <td class="p-3 text-right text-stone-700 tabular-nums">{{ number_format($r['dispatched'],2) }}</td>
                            <td class="p-3 text-right text-stone-700 tabular-nums">{{ number_format($r['accepted'],2) }}</td>
                            <td class="p-3 text-right font-black text-[#133e2b] tabular-nums">Rs {{ number_format($r['billed'],2) }}</td>
                            <td class="p-3 text-right text-stone-700 tabular-nums">{{ number_format($r['taana']+$r['baana'],2) }}</td>
                            <td class="p-3 text-right font-semibold text-stone-700 tabular-nums">
                                Taana {{ number_format($r['issuedTaana'],2) }} / Baana {{ number_format($r['issuedBaana'],2) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="p-6 text-center text-stone-400 italic">No orders created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-[11px] text-stone-400 leading-relaxed">Do not call these amounts net profit: full energy, labour, preparation and overhead allocation is not automated. The report shows traceable amounts, not an invented margin.</p>
    </section>

    <!-- WAGES & DOWNTIME REGISTER -->
    <div class="grid lg:grid-cols-2 gap-6">
        
        <!-- MAZDOOR KHATA / WAGE ENTRY -->
        <section class="card p-6 space-y-4 shadow-2xs">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="text-base font-extrabold text-[#133e2b]">Mazdoor Khata / Wage Entry</h2>
                <p class="text-xs text-stone-500">Record weaver earnings per metre, per day or fixed salary.</p>
            </div>

            <form method="POST" action="{{ route('factory.wage.store') }}" class="space-y-3.5">
                @csrf
                <label class="block text-xs font-bold text-stone-700">
                    Worker Name
                    <input name="worker_name" required placeholder="Weaver / Worker Name" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs focus:outline-none focus:border-[#133e2b]">
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Order (optional)
                        <select name="work_order_id" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white">
                            <option value="">General labour</option>
                            @foreach($orders as $o)
                                <option value="{{ $o->id }}">{{ $o->order_no }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Loom
                        <select name="loom_no" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white">
                            <option value="">Not linked</option>
                            @for($i=1;$i<=24;$i++)
                                <option>{{ sprintf('Loom-%02d',$i) }}</option>
                            @endfor
                        </select>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Wage Date
                        <input name="wage_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Payment Basis
                        <select name="basis" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white">
                            <option value="metre">Per metre</option>
                            <option value="day">Per day</option>
                            <option value="fixed">Fixed amount</option>
                        </select>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Quantity
                        <input name="quantity" required type="number" min=".001" step=".001" value="1" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Rate per unit (Rs)
                        <input name="rate" required type="number" min="0" step=".0001" placeholder="0.00" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs">
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Advance adjusted (Rs)
                        <input name="advance_adjustment" type="number" min="0" value="0" step=".01" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Already paid (Rs)
                        <input name="paid_amount" type="number" min="0" value="0" step=".01" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs">
                    </label>
                </div>

                <input name="notes" placeholder="Remarks / Shift details" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]">

                <button class="w-full bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-3 font-bold text-xs shadow-2xs transition-all">
                    Save Wage Entry
                </button>
                <p class="text-[11px] text-stone-400">The wage register calculates earnings and balance. For actual cash/bank payments use Expenses separately; this form does not debit a bank account.</p>
            </form>
        </section>

        <!-- LOOM DOWNTIME / MAINTENANCE -->
        <section class="card p-6 space-y-4 shadow-2xs">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="font-extrabold text-base text-[#133e2b]">Loom Downtime / Maintenance</h2>
                <p class="text-xs text-stone-500">Record breakdown minutes, causes, and estimated repair costs.</p>
            </div>

            <form method="POST" action="{{ route('factory.stoppage.store') }}" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <label class="block text-xs font-bold text-stone-700">
                        Loom
                        <select name="loom_no" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white">
                            @for($i=1;$i<=24;$i++)
                                <option>{{ sprintf('Loom-%02d',$i) }}</option>
                            @endfor
                        </select>
                    </label>
                    <label class="block text-xs font-bold text-stone-700">
                        Order (if applicable)
                        <select name="work_order_id" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs bg-white">
                            <option value="">General machine fault</option>
                            @foreach($orders as $o)
                                <option value="{{ $o->id }}">{{ $o->order_no }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Date
                        <input name="stopped_on" type="date" required value="{{ date('Y-m-d') }}" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Downtime (minutes)
                        <input name="downtime_minutes" required min="1" max="1440" type="number" placeholder="60" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs">
                    </label>
                </div>

                <label class="block text-xs font-bold text-stone-700">
                    Reason
                    <input name="reason" required placeholder="Yarn break, beam change, mechanical, motor fault..." class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs focus:outline-none focus:border-[#133e2b]">
                </label>

                <label class="block text-xs font-bold text-stone-700">
                    Estimated Repair Cost (Rs)
                    <input name="repair_cost" min="0" step=".01" type="number" placeholder="0.00" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 mt-1 text-xs focus:outline-none focus:border-[#133e2b]">
                </label>

                <textarea name="notes" rows="2" placeholder="Notes, parts used, electrician / mechanic name" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]"></textarea>

                <button class="w-full bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-3 font-bold text-xs shadow-2xs transition-all">
                    Record Stoppage
                </button>
                <p class="text-[11px] text-stone-400">Issue spare parts through Parts. Record actual paid repairs through Expenses.</p>
            </form>
        </section>

    </div>

    <!-- RECENT ENTRIES TABLES -->
    <div class="grid lg:grid-cols-2 gap-6">
        
        <section class="card p-6 shadow-2xs space-y-3">
            <h3 class="font-extrabold text-sm text-[#133e2b] pb-3 border-b border-[#e2e8e0]">Recent Mazdoori Entries</h3>
            <div class="overflow-auto max-h-72 border border-stone-200 rounded-xl">
                <table class="w-full text-xs">
                    <thead class="bg-stone-50 border-b border-stone-200 text-stone-600 text-[10px] font-bold uppercase tracking-wider">
                        <tr>
                            <th class="text-left p-2.5">Worker</th>
                            <th class="p-2.5">Qty &times; Rate</th>
                            <th class="p-2.5 text-right">Gross</th>
                            <th class="p-2.5 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($wages as $w)
                            <tr class="hover:bg-stone-50/60">
                                <td class="p-2.5 font-bold text-stone-900">
                                    {{ $w->worker_name }}
                                    <small class="block text-stone-400 font-medium text-[10px]">{{ $w->wage_date }} &middot; {{ $w->order?->order_no }}</small>
                                </td>
                                <td class="p-2.5 text-stone-600 text-center">{{ $w->quantity }} &times; {{ $w->rate }}</td>
                                <td class="p-2.5 text-right text-stone-800 font-semibold tabular-nums">{{ number_format((float)$w->gross_amount,2) }}</td>
                                <td class="p-2.5 text-right font-black text-[#133e2b] tabular-nums">{{ number_format((float)$w->balance_amount,2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-stone-400 italic">No wages recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card p-6 shadow-2xs space-y-3">
            <h3 class="font-extrabold text-sm text-[#133e2b] pb-3 border-b border-[#e2e8e0]">Recent Machine Stoppages</h3>
            <div class="overflow-auto max-h-72 border border-stone-200 rounded-xl">
                <table class="w-full text-xs">
                    <thead class="bg-stone-50 border-b border-stone-200 text-stone-600 text-[10px] font-bold uppercase tracking-wider">
                        <tr>
                            <th class="text-left p-2.5">Machine / Date</th>
                            <th class="p-2.5">Reason</th>
                            <th class="p-2.5 text-center">Minutes</th>
                            <th class="p-2.5 text-right">Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($stoppages as $m)
                            <tr class="hover:bg-stone-50/60">
                                <td class="p-2.5 font-bold text-stone-900">
                                    {{ $m->loom_no }}
                                    <small class="block text-stone-400 font-medium text-[10px]">{{ $m->stopped_on }}</small>
                                </td>
                                <td class="p-2.5 text-stone-700">{{ $m->reason }}</td>
                                <td class="p-2.5 text-center font-bold text-amber-700">{{ $m->downtime_minutes }}m</td>
                                <td class="p-2.5 text-right font-semibold text-stone-800 tabular-nums">{{ number_format((float)$m->repair_cost,2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-stone-400 italic">No stoppages recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>
@endsection
