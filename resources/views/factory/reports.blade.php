@extends('layouts.app')
@section('title', 'Factory Reports and Mazdoori')
@section('content')
<style>
    .factory-aside .nav-link.is-active {
        background: #133e2b;
        color: #ffffff;
        box-shadow: 0 1px 2px rgba(19, 62, 43, 0.15);
    }
    .factory-aside .nav-link.is-active:hover {
        background: #133e2b;
        color: #ffffff;
    }
    .factory-aside .nav-link.is-active .nav-chip {
        background: #0b261a;
    }
    .factory-aside .nav-link.is-active .nav-chip svg {
        color: #c6f16d;
    }
    .factory-aside .nav-link.is-active .nav-dot {
        display: inline-block;
    }
</style>

<div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-6 items-start">

    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    <aside id="factoryReportAside" class="factory-aside rounded-3xl border border-[#e2e8e0] bg-white p-4 shadow-sm lg:sticky lg:top-24">

        {{-- Reports dashboard / overview main link --}}
        <a href="#order-production" data-spy="order-production"
           class="nav-link is-active mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 transition-all bg-[#f5f7f5] text-stone-700 border border-[#e2e8e0] hover:bg-[#edf2ec] hover:text-stone-900">
            <span class="nav-chip flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-stone-100">
                <svg class="h-5 w-5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-extrabold leading-tight">Reports Dashboard</span>
                <span class="block text-[10px] text-stone-400 mt-0.5">Order-wise audit overview</span>
            </div>
        </a>

        <div class="mb-2 px-1 text-[10px] font-bold uppercase tracking-widest text-stone-400">Sections</div>

        <nav class="space-y-1.5" aria-label="Factory reports sections">

            {{-- Mazdoor Khata / Wage Entry --}}
            <a href="#wage-entry" data-spy="wage-entry"
               class="nav-link flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all text-stone-600 hover:bg-stone-100 hover:text-stone-900">
                <span class="nav-chip flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-stone-100">
                    <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Mazdoor Khata / Wages</span>
                    <span class="block text-[10px] font-normal text-stone-400">Per metre, day or fixed earnings</span>
                </div>
                <span class="nav-dot ml-auto w-1.5 h-1.5 rounded-full bg-[#c6f16d] shrink-0 hidden"></span>
            </a>

            {{-- Loom Downtime / Maintenance --}}
            <a href="#downtime" data-spy="downtime"
               class="nav-link flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all text-stone-600 hover:bg-stone-100 hover:text-stone-900">
                <span class="nav-chip flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-stone-100">
                    <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Loom Downtime</span>
                    <span class="block text-[10px] font-normal text-stone-400">Breakdown minutes &amp; repair cost</span>
                </div>
                <span class="nav-dot ml-auto w-1.5 h-1.5 rounded-full bg-[#c6f16d] shrink-0 hidden"></span>
            </a>

            {{-- Recent Mazdoori Entries --}}
            <a href="#recent-wages" data-spy="recent-wages"
               class="nav-link flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all text-stone-600 hover:bg-stone-100 hover:text-stone-900">
                <span class="nav-chip flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-stone-100">
                    <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Recent Mazdoori</span>
                    <span class="block text-[10px] font-normal text-stone-400">Latest wage entries</span>
                </div>
                <span class="nav-dot ml-auto w-1.5 h-1.5 rounded-full bg-[#c6f16d] shrink-0 hidden"></span>
            </a>

            {{-- Recent Machine Stoppages --}}
            <a href="#recent-stoppages" data-spy="recent-stoppages"
               class="nav-link flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all text-stone-600 hover:bg-stone-100 hover:text-stone-900">
                <span class="nav-chip flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-stone-100">
                    <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M5.06 19h13.88c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.33 16c-.77 1.33.19 3 1.73 3z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Machine Stoppages</span>
                    <span class="block text-[10px] font-normal text-stone-400">Latest downtime log</span>
                </div>
                <span class="nav-dot ml-auto w-1.5 h-1.5 rounded-full bg-[#c6f16d] shrink-0 hidden"></span>
            </a>

        </nav>

        {{-- Floor snapshot --}}
        <div class="mt-4 rounded-2xl bg-[#f5f7f5] border border-[#e2e8e0] p-3 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Floor Snapshot</div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-stone-500">Active Orders</span>
                <span class="font-mono font-bold text-stone-900">{{ $orders->count() }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-stone-500">Looms Running</span>
                <span class="font-mono font-bold text-stone-800">{{ $activeAssignments->count() }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-stone-500">Wage Entries</span>
                <span class="font-mono font-bold text-stone-700">{{ $wages->count() }}</span>
            </div>
            <div class="pt-1.5 border-t border-[#e2e8e0] flex items-center justify-between text-xs">
                <span class="text-stone-700 font-bold">Stoppages Logged</span>
                <span class="font-mono font-black text-stone-900">{{ $stoppages->count() }}</span>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="mt-5 pt-4 border-t border-[#e2e8e0] space-y-1.5">
            <div class="px-1 text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Quick Actions</div>
            <a href="#wage-entry" class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-stone-700 hover:bg-stone-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Record Wage
            </a>
            <a href="#downtime" class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-stone-700 hover:bg-stone-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M5.06 19h13.88c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.33 16c-.77 1.33.19 3 1.73 3z"/>
                </svg>
                Record Downtime
            </a>
            <a href="{{ route('factory.orders') }}" class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-stone-700 hover:bg-stone-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Factory Orders
            </a>
            <a href="{{ route('factory.checking') }}" class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-stone-700 hover:bg-stone-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Thaan Checking
            </a>
        </div>
    </aside>

    {{-- ============================================================
         MAIN CONTENT
    ============================================================ --}}
    <div class="min-w-0 space-y-6">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#15803d]">OPERATIONAL AUDIT &amp; WAGES</span>
            <h1 class="text-2xl sm:text-3xl font-black text-[#133e2b] tracking-tight">Factory Reports &amp; Mazdoor Khata</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">Order-wise loom output, checking, dispatch, acceptance and estimated yarn usage. Includes a simple wages and downtime register.</p>
        </div>
    </header>

    <!-- ORDER-WISE PRODUCTION AND PARTY HISAAB -->
    <section id="order-production" class="card p-6 space-y-4 shadow-2xs scroll-mt-24">
        <div class="pb-3 border-b border-[#e2e8e0]">
            <h2 class="text-base font-extrabold text-[#133e2b]">Order-wise Production and Party Hisaab</h2>
            <p class="text-xs text-stone-500">Parta yarn estimates use checked metres and are converted from lb to kg. Recorded Taana/Baana issues are shown in kg. Metre variance is loom output minus checked length; investigate any difference before settling.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-stone-600 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-3 text-left">Order / Type</th>
                        <th class="p-3">Quality</th>
                        <th class="p-3 text-right">Loom (m)</th>
                        <th class="p-3 text-right">Checked (m)</th>
                        <th class="p-3 text-right">Loom − Checked (m)</th>
                        <th class="p-3 text-right">Dispatched (m)</th>
                        <th class="p-3 text-right">Accepted (m)</th>
                        <th class="p-3 text-right">Job Bill (Rs)</th>
                        <th class="p-3 text-right">Parta estimate (kg)</th>
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
                            <td class="p-3 text-right font-semibold tabular-nums {{ abs($r['meterVariance']) > 0.01 ? 'text-amber-700' : 'text-stone-700' }}" title="Loom output minus checked metres">{{ $r['meterVariance'] > 0 ? '+' : '' }}{{ number_format($r['meterVariance'],2) }}</td>
                            <td class="p-3 text-right text-stone-700 tabular-nums">{{ number_format($r['dispatched'],2) }}</td>
                            <td class="p-3 text-right text-stone-700 tabular-nums">{{ number_format($r['accepted'],2) }}</td>
                            <td class="p-3 text-right font-black text-[#133e2b] tabular-nums">Rs {{ number_format($r['billed'],2) }}</td>
                            <td class="p-3 text-right text-stone-700 tabular-nums">
                                Taana {{ number_format($r['partaTaanaKg'],2) }} / Baana {{ number_format($r['partaBaanaKg'],2) }}
                            </td>
                            <td class="p-3 text-right font-semibold text-stone-700 tabular-nums">
                                Taana {{ number_format($r['issuedTaana'],2) }} / Baana {{ number_format($r['issuedBaana'],2) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="p-6 text-center text-stone-400 italic">No orders created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-[11px] text-stone-400 leading-relaxed">Do not call these amounts net profit: full energy, labour, preparation and overhead allocation is not automated. The report shows traceable amounts, not an invented margin.</p>
    </section>

    <!-- WAGES & DOWNTIME REGISTER -->
    <div class="grid lg:grid-cols-2 gap-6">
        
        <!-- MAZDOOR KHATA / WAGE ENTRY -->
        <section id="wage-entry" class="card p-6 space-y-4 shadow-2xs scroll-mt-24">
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
                            @foreach($looms as $loom)
                                <option value="{{ $loom }}">{{ $loom }}</option>
                            @endforeach
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
        <section id="downtime" class="card p-6 space-y-4 shadow-2xs scroll-mt-24">
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
                            @foreach($looms as $loom)
                                <option value="{{ $loom }}">{{ $loom }}</option>
                            @endforeach
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
        
        <section id="recent-wages" class="card p-6 shadow-2xs space-y-3 scroll-mt-24">
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

        <section id="recent-stoppages" class="card p-6 shadow-2xs space-y-3 scroll-mt-24">
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
    </div>{{-- end main content --}}
</div>{{-- end grid --}}

<script>
(function () {
    var aside = document.getElementById('factoryReportAside');
    if (!aside) {
        return;
    }
    var links = Array.prototype.slice.call(aside.querySelectorAll('[data-spy]'));
    var sections = links.map(function (l) { return document.getElementById(l.dataset.spy); }).filter(Boolean);

    function setActive(id) {
        links.forEach(function (l) { l.classList.toggle('is-active', l.dataset.spy === id); });
    }

    aside.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var id = this.getAttribute('href').slice(1);
            var el = document.getElementById(id);
            if (!el) {
                return;
            }
            e.preventDefault();
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            setActive(id);
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', '#' + id);
            }
        });
    });

    if ('IntersectionObserver' in window && sections.length) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    setActive(entry.target.id);
                }
            });
        }, { rootMargin: '-25% 0px -65% 0px', threshold: 0 });
        sections.forEach(function (s) { observer.observe(s); });
    }
})();
</script>
@endsection
