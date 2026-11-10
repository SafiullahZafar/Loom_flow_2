@extends('layouts.app')

@section('title', 'Sales Slips & Contract Management')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-6 items-start">

    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    <aside class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm lg:sticky lg:top-24">

        {{-- Dashboard main link --}}
        <a href="{{ route('sales.index') }}"
           class="mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 transition-all
                  {{ $page === 'dashboard'
                       ? 'bg-zinc-900 text-zinc-50 shadow-md'
                       : 'bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 hover:text-zinc-900' }}">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                         {{ $page === 'dashboard' ? 'bg-zinc-700' : 'bg-zinc-200' }}">
                <svg class="h-5 w-5 {{ $page === 'dashboard' ? 'text-zinc-100' : 'text-zinc-600' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-extrabold leading-tight">Sales Dashboard</span>
                <span class="block text-[10px] text-zinc-400 mt-0.5">Revenue overview & activity</span>
            </div>
        </a>

        <div class="mb-2 px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sections</div>

        <nav class="space-y-1.5" aria-label="Sales sections">

            {{-- Sales Slips --}}
            <a href="{{ route('sales.slips') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'slips'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'slips' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'slips' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Sales Slips</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Invoices & dispatch log</span>
                </div>
                @if($page === 'slips')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Contracts --}}
            <a href="{{ route('sales.contracts') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'contracts'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'contracts' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'contracts' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Multi-Quality Contracts</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Buyer orders & delivery progress</span>
                </div>
                @if($page === 'contracts')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

        </nav>

        {{-- Revenue snapshot --}}
        @if(isset($totalRevenue))
        <div class="mt-4 rounded-2xl bg-zinc-50 border border-zinc-200 p-3 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Revenue Snapshot</div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Open Market</span>
                <span class="font-mono font-bold text-zinc-900">Rs. {{ number_format($openMarketRevenue, 0) }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Contract Sales</span>
                <span class="font-mono font-bold text-zinc-600">Rs. {{ number_format($contractSalesRevenue, 0) }}</span>
            </div>
            <div class="pt-1.5 border-t border-zinc-200 flex items-center justify-between text-xs">
                <span class="text-zinc-700 font-bold">Total Revenue</span>
                <span class="font-mono font-black text-zinc-900">Rs. {{ number_format($totalRevenue, 0) }}</span>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="mt-5 pt-4 border-t border-zinc-100 space-y-1.5">
            <div class="px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Quick Actions</div>
            <button type="button" onclick="openModal('modalNewSaleSlip')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Enter Sale Slip
            </button>
            <button type="button" onclick="openModal('modalNewContract')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                New Contract
            </button>
            <a href="{{ route('fabric.index') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Fabric Stock
            </a>
        </div>
    </aside>

    {{-- ============================================================
         MAIN CONTENT
    ============================================================ --}}
    <div class="min-w-0 space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-zinc-200">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Module 05</span>
                </div>
                <h1 class="text-2xl font-black text-zinc-900 tracking-tight">
                    {{ ['dashboard' => 'Sales Dashboard', 'slips' => 'Sales Slips & Invoices', 'contracts' => 'Multi-Quality Buyer Contracts'][$page] }}
                </h1>
                <p class="text-xs text-zinc-400 mt-0.5">
                    {{ ['dashboard' => 'Open market sales, contract deliveries, and full revenue overview.', 'slips' => 'All fabric dispatch invoices — open market and contract sales.', 'contracts' => 'Long-term buyer contracts with delivery tracking and remaining meters.'][$page] }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" onclick="openModal('modalNewSaleSlip')"
                        class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 font-extrabold text-xs shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    + Enter Sale Slip
                </button>
                <button type="button" onclick="openModal('modalNewContract')"
                        class="px-3.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border border-zinc-300 text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    + New Contract
                </button>
            </div>
        </div>

        {{-- KPI Cards (dashboard only) --}}
        @if($page === 'dashboard')
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl p-4 border border-zinc-300 bg-zinc-50 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-600 flex items-center justify-between">
                    <span>Total Net Sales</span><span class="w-2 h-2 rounded-full bg-zinc-500"></span>
                </div>
                <div class="text-xl font-black text-zinc-900 mt-1 font-mono">Rs. {{ number_format($totalRevenue, 0) }}</div>
                <div class="text-[11px] text-zinc-400 mt-1">{{ number_format($totalMetersSold) }}m dispatched</div>
            </div>
            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Contract Revenue</span><span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-xl font-black text-zinc-900 mt-1 font-mono">Rs. {{ number_format($contractSalesRevenue, 0) }}</div>
                <div class="text-[11px] text-zinc-400 mt-1">From long-term contracts</div>
            </div>
            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Open Market Revenue</span><span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-xl font-black text-zinc-900 mt-1 font-mono">Rs. {{ number_format($openMarketRevenue, 0) }}</div>
                <div class="text-[11px] text-zinc-400 mt-1">Spot & cash sales</div>
            </div>
            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Active Contracts</span><span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-2xl font-black text-zinc-900 mt-1">{{ $activeContractsCount }}</div>
                <div class="text-[11px] text-zinc-400 mt-1 font-mono">{{ number_format($totalContractMetersDelivered) }}m / {{ number_format($totalContractMetersAgreed) }}m</div>
            </div>
        </div>
        @endif


        {{-- ============================================================
             SECTION 1 — Sales Slips Table
        ============================================================ --}}
        @if(in_array($page, ['dashboard', 'slips'], true))
        <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">
                            {{ $page === 'dashboard' ? 'Recent Sales Slips' : 'Sales Slips & Invoices' }}
                        </h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Fabric dispatch invoices for open market and contract sales</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($page === 'dashboard')
                        <a href="{{ route('sales.slips') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
                    @else
                        <span class="text-xs text-zinc-400 font-mono">{{ $sales->total() }} slips</span>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] tracking-wider bg-zinc-50">
                            <th class="px-4 py-3">Slip #</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Buyer Name</th>
                            <th class="px-4 py-3">Fabric Quality</th>
                            <th class="px-4 py-3 text-right">Meters Sold</th>
                            <th class="px-4 py-3 text-center">Rolls</th>
                            <th class="px-4 py-3 text-right">Rate / m</th>
                            <th class="px-4 py-3 text-right bg-zinc-900/5 border-x border-zinc-200 font-black text-zinc-800">Net Total</th>
                            <th class="px-4 py-3 text-center">Payment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($sales as $s)
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-zinc-900">{{ $s->slip_no }}</td>
                                <td class="px-4 py-3 text-zinc-500 font-mono">{{ $s->sale_date->format('d M, Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($s->sale_type === 'contract')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-900 text-zinc-50">
                                            Contract
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-600 border border-zinc-200">
                                            Open Market
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-zinc-900">{{ $s->buyer_name }}</div>
                                    @if($s->sale_type === 'contract' && $s->contract)
                                        <div class="text-[10px] text-zinc-400 font-mono mt-0.5">{{ $s->contract->contract_no }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-600">{{ $s->fabric_quality }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-zinc-900 text-sm">{{ number_format($s->meters_sold, 0) }}m</td>
                                <td class="px-4 py-3 text-center font-mono text-zinc-600">{{ $s->rolls_sold }}</td>
                                <td class="px-4 py-3 text-right font-mono text-zinc-500">Rs. {{ number_format($s->rate_per_meter, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono font-black text-zinc-900 text-sm bg-zinc-50 border-x border-zinc-200">
                                    Rs. {{ number_format($s->net_total, 0) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                                 {{ $s->payment_status === 'paid'
                                                      ? 'bg-zinc-900 text-zinc-50'
                                                      : ($s->payment_status === 'partial'
                                                           ? 'bg-zinc-300 text-zinc-800'
                                                           : 'bg-zinc-100 text-zinc-500 border border-zinc-200') }}">
                                        {{ $s->payment_status }}
                                    </span>
                                    <div class="text-[9px] text-zinc-400 mt-0.5">{{ $s->payment_type }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-12 text-center">
                                    <div class="text-zinc-400 text-sm font-medium">No sales slips entered yet</div>
                                    <div class="text-zinc-300 text-xs mt-1">Click "+ Enter Sale Slip" to log your first sale</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($page === 'slips')
                <div class="px-6 py-4 border-t border-zinc-100">{{ $sales->links() }}</div>
            @endif
        </div>
        @endif


        {{-- ============================================================
             SECTION 2 — Contracts Cards
        ============================================================ --}}
        @if(in_array($page, ['dashboard', 'contracts'], true))
        <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">
                            {{ $page === 'dashboard' ? 'Active Buyer Contracts' : 'Multi-Quality Buyer Contracts' }}
                        </h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Delivery progress, remaining meters, and contract values</p>
                    </div>
                </div>
                @if($page === 'dashboard')
                    <a href="{{ route('sales.contracts') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
                @endif
            </div>

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($contracts as $cnt)
                    @php
                        $pct = $cnt->total_meters > 0
                            ? min(100, round(($cnt->delivered_meters / $cnt->total_meters) * 100))
                            : 0;
                    @endphp
                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 space-y-3 hover:border-zinc-300 transition-colors">

                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-zinc-200 text-zinc-700 border border-zinc-300 mb-1">
                                    {{ $cnt->contract_no }}
                                </span>
                                <div class="font-extrabold text-zinc-900 leading-tight">{{ $cnt->buyer_name }}</div>
                                <div class="text-[11px] text-zinc-500 mt-0.5">{{ $cnt->fabric_quality }}</div>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                         {{ $cnt->status === 'active'
                                              ? 'bg-zinc-900 text-zinc-50'
                                              : 'bg-zinc-200 text-zinc-500' }}">
                                {{ $cnt->status }}
                            </span>
                        </div>

                        {{-- Progress bar --}}
                        <div>
                            <div class="flex justify-between text-[11px] mb-1.5 font-semibold">
                                <span class="text-zinc-500">Delivery Progress</span>
                                <span class="text-zinc-800 font-mono font-black">{{ $pct }}%</span>
                            </div>
                            <div class="w-full bg-zinc-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-zinc-800 h-2 rounded-full transition-all duration-500"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-zinc-400 mt-1 font-mono">
                                <span>{{ number_format($cnt->delivered_meters) }}m delivered</span>
                                <span>{{ number_format($cnt->total_meters) }}m total</span>
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-zinc-200 text-xs">
                            <div>
                                <span class="block text-[9px] text-zinc-400 uppercase font-bold mb-0.5">Remaining</span>
                                <strong class="text-zinc-900 font-mono text-sm">{{ number_format($cnt->remaining_meters) }}m</strong>
                            </div>
                            <div>
                                <span class="block text-[9px] text-zinc-400 uppercase font-bold mb-0.5">Rate / m</span>
                                <strong class="text-zinc-900 font-mono">Rs.&nbsp;{{ $cnt->rate_per_meter }}</strong>
                            </div>
                            <div>
                                <span class="block text-[9px] text-zinc-400 uppercase font-bold mb-0.5">Total Value</span>
                                <strong class="text-zinc-900 font-mono">Rs.&nbsp;{{ number_format($cnt->total_amount, 0) }}</strong>
                            </div>
                        </div>

                        <a href="{{ route('invoices.invoices', ['contract_id' => $cnt->id]) }}#invoice-entry" class="block rounded-xl border border-zinc-300 bg-white px-3 py-2 text-center text-[11px] font-bold text-zinc-700 hover:bg-zinc-100">Create invoice for this contract</a>

                        <details class="rounded-xl border border-zinc-200 bg-white">
                            <summary class="cursor-pointer px-3 py-2.5 text-[11px] font-bold text-zinc-700">View contract details and costing</summary>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2 border-t border-zinc-100 px-3 py-3 text-[10px] sm:grid-cols-3">
                                <div><span class="text-zinc-400">Contract type / loom</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->contract_type ?? '—' }} / {{ $cnt->loom_type ?? '—' }}</strong></div>
                                <div><span class="text-zinc-400">Contract date / delivery</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->start_date?->format('d M Y') ?? '—' }} / {{ $cnt->end_date?->format('d M Y') ?? '—' }}</strong></div>
                                <div><span class="text-zinc-400">Party contract #</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->party_contract_no ?? '—' }}</strong></div>
                                <div><span class="text-zinc-400">Buyer code / GST</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->buyer_code ?? '—' }} / {{ $cnt->buyer_gst ?? '—' }}</strong></div>
                                <div><span class="text-zinc-400">Quality code / detail</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->quality_code ?? '—' }} / {{ $cnt->contract_quality ?? '—' }}</strong></div>
                                <div><span class="text-zinc-400">Quality width / panna</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->contract_quality_width ?? '—' }} in / {{ $cnt->panna ?? '—' }}</strong></div>
                                <div><span class="text-zinc-400">Read / pick</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->read ?? '—' }} / {{ $cnt->pick ?? '—' }}</strong></div>
                                <div><span class="text-zinc-400">Warp / weft count</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->warp_count ?? '—' }} / {{ $cnt->weft_count ?? '—' }}</strong></div>
                                <div><span class="text-zinc-400">Fabric width / GSM</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->width ?? '—' }} in / {{ $cnt->gsm ?? '—' }}</strong></div>
                                <div><span class="text-zinc-400">Warp / weft weight</span><strong class="mt-0.5 block text-zinc-800">{{ number_format((float) ($cnt->warp_weight_1m ?? 0), 4) }} / {{ number_format((float) ($cnt->weft_weight_1m ?? 0), 4) }} lb/m</strong></div>
                                <div><span class="text-zinc-400">Warp / weft yarn rate</span><strong class="mt-0.5 block text-zinc-800">Rs. {{ number_format((float) ($cnt->yarn_warp_rate ?? 0), 4) }} / Rs. {{ number_format((float) ($cnt->yarn_weft_rate ?? 0), 4) }} per lb</strong></div>
                                <div><span class="text-zinc-400">Conversion rate / pick</span><strong class="mt-0.5 block text-zinc-800">Rs. {{ number_format((float) ($cnt->conversion_rate ?? 0), 4) }}</strong></div>
                                <div><span class="text-zinc-400">Weight per 40m (warp / weft)</span><strong class="mt-0.5 block text-zinc-800">{{ number_format((float) ($cnt->warp_wt_40m ?? 0), 4) }} / {{ number_format((float) ($cnt->weft_wt_40m ?? 0), 4) }} lb</strong></div>
                                <div><span class="text-zinc-400">Total yarn weight / meter</span><strong class="mt-0.5 block text-zinc-800">{{ number_format((float) ($cnt->total_weight_1m_lb ?? 0), 4) }} lb · {{ number_format((float) ($cnt->total_weight_1m_kg ?? 0), 4) }} kg</strong></div>
                                <div><span class="text-zinc-400">Warp / weft bags</span><strong class="mt-0.5 block text-zinc-800">{{ number_format((float) ($cnt->warp_bags ?? 0), 2) }} / {{ number_format((float) ($cnt->weft_bags ?? 0), 2) }}</strong></div>
                                <div><span class="text-zinc-400">Yarn / conversion total</span><strong class="mt-0.5 block text-zinc-800">Rs. {{ number_format((float) ($cnt->yarn_value ?? 0), 2) }} / Rs. {{ number_format((float) ($cnt->conv_value ?? 0), 2) }}</strong></div>
                                <div><span class="text-zinc-400">Estimated sales tax</span><strong class="mt-0.5 block text-zinc-800">Rs. {{ number_format((float) ($cnt->sales_tax_amount ?? 0), 2) }}</strong></div>
                                <div><span class="text-zinc-400">Broker commission (separate)</span><strong class="mt-0.5 block text-zinc-800">Rs. {{ number_format((float) ($cnt->broker_commission_per_meter ?? 0) * (float) $cnt->total_meters, 2) }}</strong></div>
                                <div><span class="text-zinc-400">KP / PP</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->kp_percentage ?? 0 }}% / {{ $cnt->kp_days ?? 0 }} days · {{ $cnt->pp_percentage ?? 0 }}% / {{ $cnt->pp_days ?? 0 }} days</strong></div>
                                @foreach(['Delivery' => $cnt->delivery_instructions, 'Payment' => $cnt->payment_instructions, 'Quality' => $cnt->quality_instructions, 'Other' => $cnt->other_instructions] as $instructionType => $instruction)
                                    @if($instruction)<div class="sm:col-span-3"><span class="text-zinc-400">{{ $instructionType }} instructions</span><strong class="mt-0.5 block whitespace-normal text-zinc-800">{{ $instruction }}</strong></div>@endif
                                @endforeach
                            </div>
                        </details>

                        @if($cnt->notes)
                            <div class="text-[11px] text-zinc-400 italic bg-white border border-zinc-200 px-3 py-2 rounded-xl">
                                {{ $cnt->notes }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center">
                        <div class="text-zinc-400 text-sm font-medium">No buyer contracts registered yet</div>
                        <div class="text-zinc-300 text-xs mt-1">Click "+ New Contract" to register your first buyer contract</div>
                    </div>
                @endforelse
            </div>

            @if($page === 'contracts')
                <div class="px-6 py-4 border-t border-zinc-100">{{ $contracts->links() }}</div>
            @endif
        </div>
        @endif

    </div>{{-- end main --}}
</div>{{-- end grid --}}


{{-- ============================================================
     MODAL 1: ENTER SALE SLIP
============================================================ --}}
<div id="modalNewSaleSlip" class="fixed inset-0 z-50 hidden bg-zinc-950/60 backdrop-blur-sm overflow-y-auto p-4 sm:p-6">
    <div class="mx-auto my-8 w-full max-w-2xl overflow-y-auto rounded-3xl border border-zinc-200 bg-white shadow-2xl">
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-zinc-100 bg-white/95 px-6 py-4 backdrop-blur">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-zinc-900">
                    <svg class="h-5 w-5 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <div>
                    <h3 class="text-base font-extrabold text-zinc-900">Create Sales Slip</h3>
                    <p class="text-xs text-zinc-400">Sale details, fabric dispatch & payment</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalNewSaleSlip')" class="text-zinc-400 hover:text-zinc-900 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('sales.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Sale Dispatch Type *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 rounded-2xl bg-zinc-50 border border-zinc-300 cursor-pointer has-[:checked]:border-zinc-900 has-[:checked]:bg-zinc-100">
                        <input type="radio" name="sale_type" value="open_market" checked onchange="handleSaleTypeChange()" class="text-zinc-800 focus:ring-zinc-500">
                        <div>
                            <span class="block text-xs font-bold text-zinc-900">Open Market Sale</span>
                            <span class="block text-[10px] text-zinc-400">Spot sale ex-factory</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-2xl bg-zinc-50 border border-zinc-300 cursor-pointer has-[:checked]:border-zinc-900 has-[:checked]:bg-zinc-100">
                        <input type="radio" name="sale_type" value="contract" onchange="handleSaleTypeChange()" class="text-zinc-800 focus:ring-zinc-500">
                        <div>
                            <span class="block text-xs font-bold text-zinc-900">Contract Sale</span>
                            <span class="block text-[10px] text-zinc-400">Under buyer contract</span>
                        </div>
                    </label>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Slip / Invoice # *</label>
                    <input type="text" name="slip_no" required placeholder="e.g. SLIP-9025"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Sale Date *</label>
                    <input type="date" name="sale_date" required value="{{ date('Y-m-d') }}"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div id="contractSelectContainer" class="hidden">
                <label class="block text-xs font-semibold text-zinc-700 mb-1">Select Active Contract *</label>
                <select name="sales_contract_id" id="selectContract" onchange="handleContractSelected()"
                        class="w-full bg-zinc-50 border border-zinc-400 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-600">
                    <option value="">— Choose Contract —</option>
                    @foreach($contractOptions as $c)
                        <option value="{{ $c->id }}" data-buyer="{{ $c->buyer_name }}" data-phone="{{ $c->buyer_phone }}"
                                data-stock-id="{{ $c->fabric_stock_id }}" data-rate="{{ $c->rate_per_meter }}"
                                data-remaining="{{ $c->remaining_meters }}">
                            {{ $c->contract_no }} — {{ $c->buyer_name }} ({{ $c->fabric_quality }}) — Remaining: {{ number_format($c->remaining_meters) }}m @ Rs.{{ $c->rate_per_meter }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Buyer / Party Name *</label>
                    <input type="text" id="inpBuyerName" name="buyer_name" required placeholder="e.g. Haji Cloth Merchant"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Buyer Phone</label>
                    <input type="text" id="inpBuyerPhone" name="buyer_phone" placeholder="e.g. +92 300 1234567"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Fabric Quality *</label>
                <select name="fabric_stock_id" id="selectFabricQuality" required
                        class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    @foreach($fabrics as $f)
                        <option value="{{ $f->id }}">{{ $f->quality_name }} (Open: {{ number_format($f->available_open_meters) }}m)</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Meters Sold *</label>
                    <input type="number" step="0.01" id="inpMetersSold" name="meters_sold" required min="1"
                           placeholder="1000" oninput="calculateSaleNet()"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Rolls Count *</label>
                    <input type="number" name="rolls_sold" required min="1" value="10"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Rate / Meter *</label>
                    <input type="number" step="0.01" id="inpSaleRate" name="rate_per_meter" required min="1"
                           placeholder="235" oninput="calculateSaleNet()"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Discount (Rs.)</label>
                    <input type="number" step="0.01" id="inpDiscount" name="discount" value="0" min="0"
                           oninput="calculateSaleNet()"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Payment Method & Status *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select name="payment_type" class="bg-zinc-50 border border-zinc-300 rounded-xl px-2 py-2 text-xs text-zinc-900">
                            <option value="credit">Credit</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank</option>
                        </select>
                        <select name="payment_status" class="bg-zinc-50 border border-zinc-300 rounded-xl px-2 py-2 text-xs text-zinc-900">
                            <option value="paid">Paid</option>
                            <option value="partial">Partial</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </div>
                </div>
            </div>
            {{-- Live total panel --}}
            <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-between">
                <div>
                    <span class="text-zinc-400 block text-[11px]">Subtotal: <span id="displaySubtotal" class="font-mono text-zinc-600">Rs. 0.00</span></span>
                    <span class="text-zinc-700 font-bold text-xs">Net Total Amount</span>
                </div>
                <strong id="displayNetTotal" class="font-mono text-2xl font-black text-zinc-900">Rs. 0.00</strong>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Vehicle # / Gate Pass Notes</label>
                <input type="text" name="vehicle_no" placeholder="e.g. Truck LEA-4491, Gate Pass #GP-88"
                       class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div class="pt-3 border-t border-zinc-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalNewSaleSlip')"
                        class="px-4 py-2 rounded-xl bg-zinc-100 text-zinc-600 text-xs font-semibold">Cancel</button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-black shadow-sm">
                    Generate Sales Slip
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ============================================================
     MODAL 2: NEW CONTRACT
============================================================ --}}
<div id="modalNewContract" class="fixed inset-0 z-50 hidden bg-zinc-950/60 backdrop-blur-sm overflow-y-auto p-4 sm:p-6">
    <div class="mx-auto my-8 w-full max-w-5xl overflow-y-auto rounded-3xl border border-zinc-200 bg-white shadow-2xl">
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-zinc-100 bg-white/95 px-6 py-4 backdrop-blur">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sales · Contract</p>
                <h3 class="mt-1 text-lg font-extrabold text-zinc-900">Create Grey Sale / Conversion Contract</h3>
                <p class="mt-0.5 text-xs text-zinc-500">Contract pricing and fabric estimates are calculated from the fields below.</p>
            </div>
            <button type="button" onclick="closeModal('modalNewContract')" class="rounded-lg p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-900" aria-label="Close contract form">✕</button>
        </div>
        <form action="{{ route('sales.contract.store') }}" method="POST" class="space-y-6 px-5 py-5 sm:px-6">
            @csrf
            @if($errors->hasAny(['contract_no', 'party_contract_no', 'contract_type', 'loom_type', 'buyer_code', 'buyer_name', 'buyer_phone', 'buyer_gst', 'fabric_stock_id', 'fabric_quality', 'quality_code', 'contract_quality', 'contract_quality_width', 'panna', 'total_meters', 'read', 'pick', 'warp_count', 'weft_count', 'width', 'yarn_warp_rate', 'yarn_weft_rate', 'conversion_rate', 'broker_code', 'broker_name', 'broker_commission_per_meter', 'kp_percentage', 'kp_days', 'pp_percentage', 'pp_days', 'start_date', 'end_date', 'delivery_instructions', 'payment_instructions', 'quality_instructions', 'other_instructions', 'notes']) )
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700" role="alert">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <section>
                <h4 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-zinc-500">Contract and delivery</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <label class="text-xs font-semibold text-zinc-600">Contract # *<input name="contract_no" required maxlength="100" value="{{ old('contract_no') }}" placeholder="e.g. CNT-2026-045" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Party Contract #<input name="party_contract_no" maxlength="80" value="{{ old('party_contract_no') }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Contract Date *<input type="date" name="start_date" required value="{{ old('start_date', date('Y-m-d')) }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Delivery Date<input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Contract Type *<select name="contract_type" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"><option value="Conversion" @selected(old('contract_type', 'Conversion') === 'Conversion')>Conversion</option><option value="Grey Sale" @selected(old('contract_type') === 'Grey Sale')>Grey Sale</option></select></label>
                    <label class="text-xs font-semibold text-zinc-600">Loom Type<select name="loom_type" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"><option value="">Select loom type</option>@foreach(['Shuttleless', 'Shuttle', 'Rapier', 'Air jet', 'Water jet'] as $loomType)<option value="{{ $loomType }}" @selected(old('loom_type') === $loomType)>{{ $loomType }}</option>@endforeach</select></label>
                </div>
            </section>

            <section>
                <h4 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-zinc-500">Buyer and broker</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <label class="text-xs font-semibold text-zinc-600">Buyer Name *<input name="buyer_name" required maxlength="150" value="{{ old('buyer_name') }}" placeholder="Buyer / mill" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Buyer Code<input name="buyer_code" maxlength="40" value="{{ old('buyer_code') }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Buyer Phone<input name="buyer_phone" maxlength="50" value="{{ old('buyer_phone') }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">GST / Tax #<input name="buyer_gst" maxlength="60" value="{{ old('buyer_gst') }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Broker Name<input name="broker_name" maxlength="120" value="{{ old('broker_name') }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Broker Code<input name="broker_code" maxlength="40" value="{{ old('broker_code') }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Commission / Meter (Rs.)<input type="number" step="0.0001" min="0" id="inpContractCommission" name="broker_commission_per_meter" value="{{ old('broker_commission_per_meter', 0) }}" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                </div>
            </section>

            <section>
                <h4 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-zinc-500">Fabric quality</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <label class="text-xs font-semibold text-zinc-600">Fabric Quality / Stock Quality *<select name="fabric_stock_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"><option value="">Select registered fabric quality</option>@foreach($fabrics as $fabric)<option value="{{ $fabric->id }}" @selected((string) old('fabric_stock_id') === (string) $fabric->id)>{{ $fabric->quality_name }} · {{ number_format($fabric->available_open_meters) }}m open stock</option>@endforeach</select></label>
                    <label class="text-xs font-semibold text-zinc-600">Quality Code<input name="quality_code" maxlength="40" value="{{ old('quality_code') }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Contract Quality Detail<input name="contract_quality" maxlength="120" value="{{ old('contract_quality') }}" placeholder="e.g. 52/1 cotton twill" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Quality Width (inches)<input type="number" step="0.001" min="0.001" name="contract_quality_width" value="{{ old('contract_quality_width') }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Panna<input type="number" step="0.001" min="0.001" name="panna" value="{{ old('panna', 1) }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                </div>
            </section>

            <section>
                <h4 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-zinc-500">Quantity, construction and costing inputs</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <label class="text-xs font-semibold text-zinc-600">Agreed Meters *<input type="number" step="0.01" min="100" required id="inpContractMeters" name="total_meters" value="{{ old('total_meters') }}" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 font-mono text-sm font-bold text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Read / EPI *<input type="number" step="0.001" min="0.001" required id="inpContractRead" name="read" value="{{ old('read') }}" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Pick / PPI *<input type="number" step="0.001" min="0.001" required id="inpContractPick" name="pick" value="{{ old('pick') }}" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Warp Count *<input type="number" step="0.0001" min="0.0001" required id="inpContractWarpCount" name="warp_count" value="{{ old('warp_count') }}" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Weft Count *<input type="number" step="0.0001" min="0.0001" required id="inpContractWeftCount" name="weft_count" value="{{ old('weft_count') }}" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Fabric Width (inches) *<input type="number" step="0.001" min="0.001" required id="inpContractWidth" name="width" value="{{ old('width') }}" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Warp Yarn Rate (Rs./lb) *<input type="number" step="0.0001" min="0" required id="inpContractWarpRate" name="yarn_warp_rate" value="{{ old('yarn_warp_rate') }}" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Weft Yarn Rate (Rs./lb) *<input type="number" step="0.0001" min="0" required id="inpContractWeftRate" name="yarn_weft_rate" value="{{ old('yarn_weft_rate') }}" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">Conversion / Pick (Rs.) *<input type="number" step="0.0001" min="0" required id="inpContractConversion" name="conversion_rate" value="{{ old('conversion_rate') }}" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">KP %<input type="number" step="0.001" min="0" max="100" name="kp_percentage" value="{{ old('kp_percentage', 0) }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">KP Days<input type="number" step="1" min="0" max="3650" name="kp_days" value="{{ old('kp_days', 0) }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">PP %<input type="number" step="0.001" min="0" max="100" name="pp_percentage" value="{{ old('pp_percentage', 0) }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                    <label class="text-xs font-semibold text-zinc-600">PP Days<input type="number" step="1" min="0" max="3650" name="pp_days" value="{{ old('pp_days', 0) }}" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900"></label>
                </div>
            </section>

            <section class="rounded-xl border border-zinc-200 bg-white p-3 flex flex-wrap gap-4 items-center text-xs">
                <label class="font-bold flex gap-2 items-center"><input type="checkbox" name="tax_enabled" id="inpContractTaxEnabled" value="1" onchange="calculateContractPreview()" @checked(old('tax_enabled'))> Optional tax estimate (off by default)</label>
                <label class="font-semibold">Tax rate (decimal, e.g. 0.17 for 17%) <input id="inpContractTaxRate" name="tax_rate" type="number" min="0" max="1" step="0.0001" value="{{ old('tax_rate', 0) }}" oninput="calculateContractPreview()" class="border rounded p-2 w-28"></label>
            </section>
            <section class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div><h4 class="text-sm font-extrabold text-zinc-900">Live Contract Calculation</h4><p class="mt-1 text-[10px] text-zinc-500">Warp + weft yarn cost + (pick × conversion rate); taxes are optional and disabled by default.</p></div>
                    <span class="rounded-lg bg-white px-2.5 py-1 text-[10px] font-bold text-zinc-500">100 lb per bag · tax optional</span>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-6">
                    <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Warp / m</span><strong id="displayContractWarpWeight" class="font-mono text-xs text-zinc-900">0.0000 lb</strong></div>
                    <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Weft / m</span><strong id="displayContractWeftWeight" class="font-mono text-xs text-zinc-900">0.0000 lb</strong></div>
                    <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Warp bags</span><strong id="displayContractWarpBags" class="font-mono text-xs text-zinc-900">0.00</strong></div>
                    <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Weft bags</span><strong id="displayContractWeftBags" class="font-mono text-xs text-zinc-900">0.00</strong></div>
                    <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">GSM</span><strong id="displayContractGsm" class="font-mono text-xs text-zinc-900">0</strong></div>
                    <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Width</span><strong id="displayContractWidthMeters" class="font-mono text-xs text-zinc-900">0.0000m</strong></div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                    <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Warp cost / m</span><strong id="displayContractWarpCost" class="font-mono text-sm text-zinc-900">Rs. 0.00</strong></div>
                    <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Weft cost / m</span><strong id="displayContractWeftCost" class="font-mono text-sm text-zinc-900">Rs. 0.00</strong></div>
                    <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Conversion / m</span><strong id="displayContractConversionCost" class="font-mono text-sm text-zinc-900">Rs. 0.00</strong></div>
                    <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Commission total (separate)</span><strong id="displayContractCommissionTotal" class="font-mono text-sm text-zinc-900">Rs. 0.00</strong></div>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-3">
                    <div class="rounded-xl bg-zinc-900 p-3 text-white"><span class="block text-[10px] text-zinc-300">Contract Rate / Meter</span><strong id="displayContractRate" class="font-mono text-lg">Rs. 0.00</strong></div>
                    <div class="rounded-xl bg-zinc-900 p-3 text-white"><span class="block text-[10px] text-zinc-300">Contract Amount (before tax)</span><strong id="displayContractAmount" class="font-mono text-lg">Rs. 0.00</strong></div>
                    <div class="rounded-xl bg-zinc-100 p-3 text-zinc-900"><span class="block text-[10px] text-zinc-500">Estimated Sales Tax (optional)</span><strong id="displayContractTax" class="font-mono text-lg">Rs. 0.00</strong></div>
                </div>
            </section>

            <section>
                <h4 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-zinc-500">Instructions and notes</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label class="text-xs font-semibold text-zinc-600">Delivery Instructions<textarea name="delivery_instructions" rows="2" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900">{{ old('delivery_instructions') }}</textarea></label>
                    <label class="text-xs font-semibold text-zinc-600">Payment Instructions<textarea name="payment_instructions" rows="2" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900">{{ old('payment_instructions') }}</textarea></label>
                    <label class="text-xs font-semibold text-zinc-600">Quality Instructions<textarea name="quality_instructions" rows="2" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900">{{ old('quality_instructions') }}</textarea></label>
                    <label class="text-xs font-semibold text-zinc-600">Other Instructions / Notes<textarea name="other_instructions" rows="2" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900">{{ old('other_instructions') }}</textarea></label>
                    <label class="text-xs font-semibold text-zinc-600 sm:col-span-2">Internal Contract Notes<textarea name="notes" rows="2" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900">{{ old('notes') }}</textarea></label>
                </div>
            </section>

            <div class="sticky bottom-0 flex justify-end gap-3 border-t border-zinc-100 bg-white/95 pt-4 backdrop-blur">
                <button type="button" onclick="closeModal('modalNewContract')" class="rounded-xl bg-zinc-100 px-4 py-2.5 text-xs font-semibold text-zinc-600">Cancel</button>
                <button type="submit" class="rounded-xl bg-zinc-900 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-zinc-700">Save Contract</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function handleSaleTypeChange() {
        const selected = document.querySelector('input[name="sale_type"]:checked').value;
        const cc = document.getElementById('contractSelectContainer');
        selected === 'contract' ? cc.classList.remove('hidden') : cc.classList.add('hidden');
    }

    function handleContractSelected() {
        const select = document.getElementById('selectContract');
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            document.getElementById('inpBuyerName').value = opt.dataset.buyer || '';
            document.getElementById('inpBuyerPhone').value = opt.dataset.phone || '';
            document.getElementById('inpSaleRate').value = opt.dataset.rate || '';
            document.getElementById('selectFabricQuality').value = opt.dataset.stockId || '';
            calculateSaleNet();
        }
    }

    function calculateSaleNet() {
        const meters   = parseFloat(document.getElementById('inpMetersSold').value) || 0;
        const rate     = parseFloat(document.getElementById('inpSaleRate').value)    || 0;
        const discount = parseFloat(document.getElementById('inpDiscount').value)    || 0;
        const subtotal = meters * rate;
        const net      = Math.max(0, subtotal - discount);
        document.getElementById('displaySubtotal').innerText  = 'Rs. ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('displayNetTotal').innerText  = 'Rs. ' + net.toLocaleString('en-US', {minimumFractionDigits: 0});
    }

    function calculateContractPreview() {
        const value = (id) => parseFloat(document.getElementById(id)?.value) || 0;
        const meters = value('inpContractMeters');
        const read = value('inpContractRead');
        const pick = value('inpContractPick');
        const warpCount = value('inpContractWarpCount');
        const weftCount = value('inpContractWeftCount');
        const width = value('inpContractWidth');
        const warpRate = value('inpContractWarpRate');
        const weftRate = value('inpContractWeftRate');
        const conversionRate = value('inpContractConversion');
        const commissionRate = value('inpContractCommission');
        const warpWeight = warpCount > 0 ? Math.floor((((read * width / warpCount / 20 * 1.0936) / 40) * 10000)) / 10000 : 0;
        const weftWeight = weftCount > 0 ? Math.floor((((pick * width / weftCount / 20 * 1.0936) / 40) * 10000)) / 10000 : 0;
        const warpCost = Math.floor(warpWeight * warpRate * 10000) / 10000;
        const weftCost = Math.floor(weftWeight * weftRate * 10000) / 10000;
        const conversionCost = pick * conversionRate;
        const ratePerMeter = warpCost + weftCost + conversionCost;
        const contractAmount = ratePerMeter * meters;
        const money = (amount) => 'Rs. ' + amount.toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        document.getElementById('displayContractWarpWeight').innerText = warpWeight.toFixed(4) + ' lb';
        document.getElementById('displayContractWeftWeight').innerText = weftWeight.toFixed(4) + ' lb';
        document.getElementById('displayContractWarpBags').innerText = (warpWeight * meters / 100).toFixed(2);
        document.getElementById('displayContractWeftBags').innerText = (weftWeight * meters / 100).toFixed(2);
        document.getElementById('displayContractGsm').innerText = warpCount > 0 && weftCount > 0 ? Math.round((read / warpCount + pick / weftCount) * 25.25) : '0';
        document.getElementById('displayContractWidthMeters').innerText = (width * 0.0254).toFixed(4) + 'm';
        document.getElementById('displayContractWarpCost').innerText = money(warpCost);
        document.getElementById('displayContractWeftCost').innerText = money(weftCost);
        document.getElementById('displayContractConversionCost').innerText = money(conversionCost);
        document.getElementById('displayContractCommissionTotal').innerText = money(commissionRate * meters);
        document.getElementById('displayContractRate').innerText = money(ratePerMeter);
        document.getElementById('displayContractAmount').innerText = money(contractAmount);
        document.getElementById('displayContractTax').innerText = money(document.getElementById('inpContractTaxEnabled')?.checked ? contractAmount * value('inpContractTaxRate') : 0);
    }

    calculateContractPreview();
    @if($errors->hasAny(['contract_no', 'party_contract_no', 'contract_type', 'loom_type', 'buyer_code', 'buyer_name', 'buyer_phone', 'buyer_gst', 'fabric_stock_id', 'fabric_quality', 'quality_code', 'contract_quality', 'contract_quality_width', 'panna', 'total_meters', 'read', 'pick', 'warp_count', 'weft_count', 'width', 'yarn_warp_rate', 'yarn_weft_rate', 'conversion_rate', 'broker_code', 'broker_name', 'broker_commission_per_meter', 'kp_percentage', 'kp_days', 'pp_percentage', 'pp_days', 'start_date', 'end_date', 'delivery_instructions', 'payment_instructions', 'quality_instructions', 'other_instructions', 'notes']))
        openModal('modalNewContract');
    @endif
</script>
@endpush
@endsection
