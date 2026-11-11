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
                    <span class="block leading-tight">Contracts · Create & View</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Buyer agreements & delivery progress</span>
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
            @if($page === 'dashboard')
            <div class="flex flex-wrap gap-2 shrink-0">
                <button type="button" onclick="openSalesParchi('kachi')" class="rounded-xl bg-zinc-900 px-3 py-2 text-xs font-bold text-white">Kachi Parchi</button>
                <button type="button" onclick="openSalesParchi('pakki')" class="rounded-xl bg-white px-3 py-2 text-xs font-bold text-zinc-700 border border-zinc-300">Pakki Parchi</button>
                <button type="button" onclick="openSalesParchi('returns')" class="rounded-xl bg-white px-3 py-2 text-xs font-bold text-zinc-700 border border-zinc-300">Returns</button>
                <button type="button" onclick="openSalesParchi('disposition')" class="rounded-xl bg-white px-3 py-2 text-xs font-bold text-zinc-700 border border-zinc-300">Disposition</button>
                <button type="button" onclick="openSalesParchi('own-sale')" class="rounded-xl bg-white px-3 py-2 text-xs font-bold text-zinc-700 border border-zinc-300">Own Fabric Sale</button>
            </div>
            @endif
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
                                    <div class="text-zinc-400 text-sm font-medium">No sales slips recorded yet.</div>
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
        @if($page === 'contracts')
        <section class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-extrabold text-zinc-900">Create a buyer contract</h2>
                <p class="mt-1 text-xs text-zinc-500">Conversion contracts can be linked to a job work order in Factory → Orders. Grey Sale contracts reserve finished stock for sales.</p>
            </div>
            <style>
                .contract-form input:not([type="checkbox"]), .contract-form select, .contract-form textarea {
                    width: 100%;
                    border: 1px solid #d4d4d8;
                    border-radius: 0.75rem;
                    background: #fff;
                    padding: 0.625rem 0.75rem;
                    color: #18181b;
                    font-size: 0.875rem;
                    line-height: 1.25rem;
                }
                .contract-form input:focus, .contract-form select:focus, .contract-form textarea:focus {
                    border-color: #71717a;
                    outline: 2px solid transparent;
                    box-shadow: 0 0 0 2px rgb(161 161 170 / 25%);
                }
                .contract-form input[type="checkbox"] { accent-color: #18181b; }
                .contract-form .tax-rate-input { width: 7rem; }
            </style>
            <form method="POST" action="{{ route('sales.contract.store') }}" class="contract-form space-y-5">
                @csrf
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <h3 class="col-span-full border-b border-zinc-200 pb-2 text-xs font-extrabold uppercase tracking-wider text-zinc-500">Buyer and agreement</h3>
                <label class="text-xs font-semibold text-zinc-600">Our contract number
                    <input name="contract_no" value="{{ old('contract_no') }}" required maxlength="100" class="mt-1 w-full rounded-xl border-zinc-300 text-sm" placeholder="e.g. C-2026-001">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Customer
                    <select name="customer_id" required class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                        <option value="">Choose customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-phone="{{ $customer->phone }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <span id="contractBuyerSummary" class="mt-1 block text-[10px] text-zinc-500">Buyer name and phone are taken from the selected customer.</span>
                </label>
                <label class="text-xs font-semibold text-zinc-600">Buyer code
                    <input name="buyer_code" value="{{ old('buyer_code') }}" maxlength="40" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Buyer GST / tax ID
                    <input name="buyer_gst" value="{{ old('buyer_gst') }}" maxlength="60" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Contract type
                    <select name="contract_type" required class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                        <option value="Conversion" @selected(old('contract_type', 'Conversion') === 'Conversion')>Job work / Conversion</option>
                        <option value="Grey Sale" @selected(old('contract_type') === 'Grey Sale')>Grey fabric sale</option>
                    </select>
                </label>
                <label class="text-xs font-semibold text-zinc-600">Who supplies the yarn bags?
                    <select name="yarn_supply" required>
                        <option value="">Choose supply source</option>
                        <option value="customer" @selected(old('yarn_supply') === 'customer')>Customer supplies the yarn bags</option>
                        <option value="factory" @selected(old('yarn_supply') === 'factory')>Factory supplies the yarn</option>
                    </select>
                    <span class="mt-1 block text-[10px] font-normal text-zinc-500">Customer supply will be carried to the linked order. Record received bags in the Bags module.</span>
                </label>
                <label class="text-xs font-semibold text-zinc-600">Loom type
                    <input name="loom_type" value="{{ old('loom_type') }}" maxlength="40" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Fabric quality
                    <select id="contractFabricQuality" name="fabric_stock_id" required class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                        <option value="">Choose quality</option>
                        @foreach($fabrics as $fabric)
                            @php
                                $parta = $partas->get($fabric->id);
                            @endphp
                            <option value="{{ $fabric->id }}"
                                    data-read="{{ $parta?->read ?? '' }}"
                                    data-pick="{{ $parta?->pick ?? '' }}"
                                    data-warp-count="{{ $parta?->warp_count ?? '' }}"
                                    data-weft-count="{{ $parta?->weft_count ?? '' }}"
                                    data-width="{{ $parta?->width_inches ?? '' }}"
                                    data-warp-rate="{{ $parta?->warp_rate_per_lb ?? '' }}"
                                    data-weft-rate="{{ $parta?->weft_rate_per_lb ?? '' }}"
                                    data-conversion-rate="{{ $parta?->conversion_rate_per_pick ?? '' }}"
                                    @selected(old('fabric_stock_id') == $fabric->id)
                                    @disabled(! $parta)>{{ $fabric->quality_name }}{{ $parta ? '' : ' (Parta required)' }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-xs font-semibold text-zinc-600">Contract metres
                    <input id="inpContractMeters" type="number" name="total_meters" value="{{ old('total_meters') }}" min="100" step="0.01" required oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Start date
                    <input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Delivery date
                    <input type="date" name="end_date" value="{{ old('end_date', now()->addDays(30)->toDateString()) }}" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Party contract number
                    <input name="party_contract_no" value="{{ old('party_contract_no') }}" maxlength="80" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <h3 class="col-span-full mt-2 border-b border-zinc-200 pb-2 text-xs font-extrabold uppercase tracking-wider text-zinc-500">Fabric quality and costing</h3>
                <label class="text-xs font-semibold text-zinc-600">Quality code
                    <input name="quality_code" value="{{ old('quality_code') }}" maxlength="40" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Contract quality description
                    <input name="contract_quality" value="{{ old('contract_quality') }}" maxlength="120" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Contract quality width (inches)
                    <input type="number" name="contract_quality_width" value="{{ old('contract_quality_width') }}" min="0.0001" step="0.0001" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Panna
                    <input type="number" name="panna" value="{{ old('panna', 1) }}" min="0.0001" step="0.0001" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Read
                    <input id="inpContractRead" type="number" name="read" value="{{ old('read') }}" min="0.0001" step="0.0001" required oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Pick
                    <input id="inpContractPick" type="number" name="pick" value="{{ old('pick') }}" min="0.0001" step="0.0001" required oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Warp count
                    <input id="inpContractWarpCount" type="number" name="warp_count" value="{{ old('warp_count') }}" min="0.0001" step="0.0001" required oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Weft count
                    <input id="inpContractWeftCount" type="number" name="weft_count" value="{{ old('weft_count') }}" min="0.0001" step="0.0001" required oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Width (inches)
                    <input id="inpContractWidth" type="number" name="width" value="{{ old('width') }}" min="0.0001" step="0.0001" required oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Warp yarn rate (Rs/lb)
                    <input id="inpContractWarpRate" type="number" name="yarn_warp_rate" value="{{ old('yarn_warp_rate', 0) }}" min="0" step="0.0001" required oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Weft yarn rate (Rs/lb)
                    <input id="inpContractWeftRate" type="number" name="yarn_weft_rate" value="{{ old('yarn_weft_rate', 0) }}" min="0" step="0.0001" required oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Conversion rate (Rs/pick)
                    <input id="inpContractConversion" type="number" name="conversion_rate" value="{{ old('conversion_rate', 0) }}" min="0" step="0.0001" required oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Broker code
                    <input name="broker_code" value="{{ old('broker_code') }}" maxlength="40" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Broker name
                    <input name="broker_name" value="{{ old('broker_name') }}" maxlength="120" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">Broker commission (Rs/metre)
                    <input id="inpContractCommission" type="number" name="broker_commission_per_meter" value="{{ old('broker_commission_per_meter', 0) }}" min="0" step="0.0001" oninput="calculateContractPreview()" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <h3 class="col-span-full mt-2 border-b border-zinc-200 pb-2 text-xs font-extrabold uppercase tracking-wider text-zinc-500">Broker and payment terms</h3>
                <label class="text-xs font-semibold text-zinc-600">KP percentage
                    <input type="number" name="kp_percentage" value="{{ old('kp_percentage', 0) }}" min="0" max="100" step="0.001" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">KP days
                    <input type="number" name="kp_days" value="{{ old('kp_days', 0) }}" min="0" max="3650" step="1" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">PP percentage
                    <input type="number" name="pp_percentage" value="{{ old('pp_percentage', 0) }}" min="0" max="100" step="0.001" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600">PP days
                    <input type="number" name="pp_days" value="{{ old('pp_days', 0) }}" min="0" max="3650" step="1" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-zinc-600 sm:col-span-2 xl:col-span-3">Notes
                    <input name="notes" value="{{ old('notes') }}" maxlength="2000" class="mt-1 w-full rounded-xl border-zinc-300 text-sm" placeholder="Optional contract notes">
                </label>
                </div>
                <section class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4">
                    <h3 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-zinc-600">Contract instructions</h3>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <label class="text-xs font-semibold text-zinc-600">Delivery instructions<textarea name="delivery_instructions" rows="2" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">{{ old('delivery_instructions') }}</textarea></label>
                        <label class="text-xs font-semibold text-zinc-600">Payment instructions<textarea name="payment_instructions" rows="2" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">{{ old('payment_instructions') }}</textarea></label>
                        <label class="text-xs font-semibold text-zinc-600">Quality instructions<textarea name="quality_instructions" rows="2" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">{{ old('quality_instructions') }}</textarea></label>
                        <label class="text-xs font-semibold text-zinc-600">Other instructions<textarea name="other_instructions" rows="2" class="mt-1 w-full rounded-xl border-zinc-300 text-sm">{{ old('other_instructions') }}</textarea></label>
                    </div>
                </section>
                <section class="flex flex-wrap items-center gap-4 rounded-xl border border-zinc-200 bg-white p-3 text-xs">
                    <label class="flex items-center gap-2 font-bold"><input type="checkbox" name="tax_enabled" id="inpContractTaxEnabled" value="1" @checked(old('tax_enabled')) onchange="calculateContractPreview()"> Optional tax estimate</label>
                    <label class="font-semibold">Tax rate (decimal, e.g. 0.17)
                        <input id="inpContractTaxRate" name="tax_rate" type="number" min="0" max="1" step="0.0001" value="{{ old('tax_rate', 0) }}" oninput="calculateContractPreview()" class="tax-rate-input ml-2 rounded-lg border-zinc-300 text-sm">
                    </label>
                </section>
                <section class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div><h3 class="text-sm font-extrabold text-zinc-900">Live contract calculation</h3><p class="mt-1 text-[10px] text-zinc-500">Warp + weft yarn cost + pick × conversion rate. Tax is optional.</p></div>
                        <span class="rounded-lg bg-white px-2.5 py-1 text-[10px] font-bold text-zinc-500">100 lb per bag</span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-6">
                        <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Warp / m</span><strong id="displayContractWarpWeight" class="font-mono text-xs text-zinc-900">0.0000 lb</strong></div>
                        <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Weft / m</span><strong id="displayContractWeftWeight" class="font-mono text-xs text-zinc-900">0.0000 lb</strong></div>
                        <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Warp bags</span><strong id="displayContractWarpBags" class="font-mono text-xs text-zinc-900">0.00</strong></div>
                        <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Weft bags</span><strong id="displayContractWeftBags" class="font-mono text-xs text-zinc-900">0.00</strong></div>
                        <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">GSM</span><strong id="displayContractGsm" class="font-mono text-xs text-zinc-900">0</strong></div>
                        <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Width</span><strong id="displayContractWidthMeters" class="font-mono text-xs text-zinc-900">0.0000 m</strong></div>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Warp cost / m</span><strong id="displayContractWarpCost" class="font-mono text-sm text-zinc-900">Rs. 0.00</strong></div>
                        <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Weft cost / m</span><strong id="displayContractWeftCost" class="font-mono text-sm text-zinc-900">Rs. 0.00</strong></div>
                        <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Conversion / m</span><strong id="displayContractConversionCost" class="font-mono text-sm text-zinc-900">Rs. 0.00</strong></div>
                        <div class="rounded-xl bg-white p-3"><span class="block text-[10px] text-zinc-400">Commission total (separate)</span><strong id="displayContractCommissionTotal" class="font-mono text-sm text-zinc-900">Rs. 0.00</strong></div>
                    </div>
                    <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-3">
                        <div class="rounded-xl bg-zinc-900 p-3 text-white"><span class="block text-[10px] text-zinc-300">Rate / metre</span><strong id="displayContractRate" class="font-mono text-lg">Rs. 0.00</strong></div>
                        <div class="rounded-xl bg-zinc-900 p-3 text-white"><span class="block text-[10px] text-zinc-300">Contract total before tax</span><strong id="displayContractAmount" class="font-mono text-lg">Rs. 0.00</strong></div>
                        <div class="rounded-xl bg-zinc-100 p-3 text-zinc-900"><span class="block text-[10px] text-zinc-500">Estimated tax</span><strong id="displayContractTax" class="font-mono text-lg">Rs. 0.00</strong></div>
                    </div>
                </section>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl bg-zinc-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-zinc-700">Save contract</button>
                </div>
            </form>
            @if($errors->any())
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700">
                    <p class="font-bold">Please review these contract fields:</p>
                    <ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
        </section>
        @endif
        <script>
            (() => {
                const qualitySelect = document.querySelector('[name="fabric_stock_id"]');
                const customerSelect = document.querySelector('[name="customer_id"]');
                const fields = {
                    read: 'read', pick: 'pick', warpCount: 'warp_count',
                    weftCount: 'weft_count', width: 'width',
                    warpRate: 'yarn_warp_rate', weftRate: 'yarn_weft_rate',
                    conversionRate: 'conversion_rate',
                };
                const value = (id) => parseFloat(document.getElementById(id)?.value) || 0;
                const money = (amount) => 'Rs. ' + amount.toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                window.calculateContractPreview = () => {
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
                    const warpWeight = warpCount > 0 ? Math.floor(((read * width / warpCount / 20 * 1.0936 / 40) * 10000)) / 10000 : 0;
                    const weftWeight = weftCount > 0 ? Math.floor(((pick * width / weftCount / 20 * 1.0936 / 40) * 10000)) / 10000 : 0;
                    const warpCost = Math.floor(warpWeight * warpRate * 10000) / 10000;
                    const weftCost = Math.floor(weftWeight * weftRate * 10000) / 10000;
                    const conversionCost = pick * conversionRate;
                    const ratePerMeter = warpCost + weftCost + conversionCost;
                    const amount = ratePerMeter * meters;
                    const taxEnabled = document.getElementById('inpContractTaxEnabled')?.checked;
                    const tax = taxEnabled ? amount * value('inpContractTaxRate') : 0;
                    const setText = (id, text) => {
                        const node = document.getElementById(id);
                        if (node) node.textContent = text;
                    };
                    setText('displayContractWarpWeight', warpWeight.toFixed(4) + ' lb');
                    setText('displayContractWeftWeight', weftWeight.toFixed(4) + ' lb');
                    setText('displayContractWarpBags', (warpWeight * meters / 100).toFixed(2));
                    setText('displayContractWeftBags', (weftWeight * meters / 100).toFixed(2));
                    setText('displayContractGsm', warpCount > 0 && weftCount > 0 ? Math.round((read / warpCount + pick / weftCount) * 25.25) : '0');
                    setText('displayContractWidthMeters', (width * 0.0254).toFixed(4) + ' m');
                    setText('displayContractWarpCost', money(warpCost));
                    setText('displayContractWeftCost', money(weftCost));
                    setText('displayContractConversionCost', money(conversionCost));
                    setText('displayContractCommissionTotal', money(commissionRate * meters));
                    setText('displayContractRate', money(ratePerMeter));
                    setText('displayContractAmount', money(amount));
                    setText('displayContractTax', money(tax));
                };
                const updateBuyerSummary = () => {
                    const option = customerSelect?.selectedOptions[0];
                    const summary = document.getElementById('contractBuyerSummary');
                    if (summary && option?.value) summary.textContent = `${option.textContent.trim()} · ${option.dataset.phone || 'No phone on customer record'}`;
                    else if (summary) summary.textContent = 'Buyer name and phone are taken from the selected customer.';
                };
                const fillParta = () => {
                    const option = qualitySelect?.selectedOptions[0];
                    if (!option) return;
                    Object.entries(fields).forEach(([dataKey, fieldName]) => {
                        const input = document.querySelector(`[name="${fieldName}"]`);
                        if (input && option.dataset[dataKey]) input.value = option.dataset[dataKey];
                    });
                    window.calculateContractPreview();
                };
                qualitySelect?.addEventListener('change', fillParta);
                customerSelect?.addEventListener('change', updateBuyerSummary);
                if (qualitySelect?.value && !document.querySelector('[name="read"]').value) fillParta();
                updateBuyerSummary();
                window.calculateContractPreview();
            })();
        </script>
        <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

            <div class="flex items-center justify-between gap-4 px-6 py-5 border-b border-zinc-100 bg-gradient-to-r from-zinc-50 via-white to-zinc-50">
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
                        <p class="text-[11px] text-zinc-500 mt-1">Track delivery, remaining metres, contract value, and customer yarn receipts.</p>
                    </div>
                </div>
                @if($page === 'dashboard')
                    <a href="{{ route('sales.contracts') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
                @endif
            </div>

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 bg-zinc-50/60">
                @forelse($contracts as $cnt)
                    @php
                        $pct = $cnt->total_meters > 0
                            ? min(100, round(($cnt->delivered_meters / $cnt->total_meters) * 100))
                            : 0;
                        $customerReceipt = $contractBagReceipts->get($cnt->id);
                        $receiptReference = ($cnt->party_contract_no ?: $cnt->contract_no).'-BAG-'.str_pad((string) ((int) ($customerReceipt?->receipt_count ?? 0) + 1), 2, '0', STR_PAD_LEFT);
                    @endphp
                    <div class="rounded-2xl border border-zinc-200 bg-white p-4 space-y-3 shadow-sm hover:border-zinc-400 hover:shadow-md transition-all">

                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block px-2 py-1 rounded-lg text-[10px] font-mono font-bold bg-zinc-900 text-white mb-1">
                                    {{ $cnt->contract_no }}
                                </span>
                                <div class="font-extrabold text-zinc-900 leading-tight">{{ $cnt->buyer_name }}</div>
                                <div class="text-[11px] text-zinc-500 mt-1">{{ $cnt->fabric_quality }} <span class="mx-1 text-zinc-300">/</span> {{ $cnt->contract_type }}</div>
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
                            <div class="w-full bg-zinc-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-zinc-700 to-zinc-900 h-2.5 rounded-full transition-all duration-500"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-zinc-400 mt-1 font-mono">
                                <span>{{ number_format($cnt->delivered_meters) }}m delivered</span>
                                <span>{{ number_format($cnt->total_meters) }}m total</span>
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="grid grid-cols-3 gap-2 pt-3 border-t border-zinc-100 text-xs">
                            <div>
                                <span class="block text-[9px] text-zinc-400 uppercase font-bold mb-1">Remaining</span>
                                <strong class="text-zinc-900 font-mono text-sm">{{ number_format($cnt->remaining_meters, 0) }} <small class="text-[10px] text-zinc-400">m</small></strong>
                            </div>
                            <div>
                                <span class="block text-[9px] text-zinc-400 uppercase font-bold mb-1">Rate / m</span>
                                <strong class="text-zinc-900 font-mono">Rs. {{ number_format((float) $cnt->rate_per_meter, 2) }}</strong>
                            </div>
                            <div>
                                <span class="block text-[9px] text-zinc-400 uppercase font-bold mb-1">Contract Value</span>
                                <strong class="text-zinc-900 font-mono">Rs. {{ number_format((float) $cnt->total_amount, 0) }}</strong>
                            </div>
                        </div>

                        @if($cnt->contract_type === 'Conversion')
                            <a href="{{ route('factory.orders') }}" class="block rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-center text-[11px] font-bold text-zinc-700 hover:bg-zinc-100">
                                {{ $contractOrderNumbers->has($cnt->id) ? 'Linked order '.$contractOrderNumbers->get($cnt->id).' · Open Factory Orders' : 'Link to a Factory Order' }}
                            </a>
                            @if($cnt->yarn_supply === 'customer' && $cnt->status === 'active')
                                <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="flex items-center gap-2 text-[11px] font-extrabold text-amber-950">
                                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-amber-100">📦</span>
                                                Customer-owned yarn bags
                                            </div>
                                            <p class="mt-1 text-[10px] text-amber-900/70">
                                                {{ number_format((float) ($customerReceipt?->bags_received ?? 0), 2) }} bags · {{ number_format((float) ($customerReceipt?->weight_received_kg ?? 0), 3) }} kg received
                                                @if($customerReceipt)<span class="text-amber-800"> · {{ $customerReceipt->receipt_count }} receipt(s)</span>@endif
                                            </p>
                                        </div>
                                        <a href="{{ route('bags.ledger') }}" class="shrink-0 text-[10px] font-bold text-amber-900 underline underline-offset-2">Bags log</a>
                                    </div>
                                    <details open class="mt-3 rounded-lg border border-amber-200 bg-white">
                                        <summary class="cursor-pointer px-3 py-2.5 text-center text-[11px] font-extrabold text-amber-950 hover:bg-amber-50">＋ Receive bags for this contract</summary>
                                        <form method="POST" action="{{ route('bags.customer-receipts.store') }}" class="grid grid-cols-2 gap-2.5 border-t border-amber-100 p-3">
                                            @csrf
                                            <input type="hidden" name="sales_contract_id" value="{{ $cnt->id }}">
                                            <label class="col-span-2 text-[10px] font-bold text-zinc-600">Challan reference <span class="font-normal text-zinc-400">(suggested from contract; editable)</span>
                                                <input name="reference_no" value="{{ $receiptReference }}" required maxlength="100" class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs text-zinc-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                            </label>
                                            <label class="text-[10px] font-bold text-zinc-600">Yarn quality
                                                <select name="quality" required class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs text-zinc-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                                    <option value="">Select quality</option>
                                                    @foreach($yarnQualities as $yarnQuality)<option value="{{ $yarnQuality->name }}">{{ $yarnQuality->name }}</option>@endforeach
                                                </select>
                                            </label>
                                            <label class="text-[10px] font-bold text-zinc-600">Bags received
                                                <input type="number" name="quantity_bags" min="0.01" step="0.01" required oninput="this.form.querySelector('[data-weight-output]').textContent = ((Number(this.value) || 0) * 45.359237).toFixed(3) + ' kg (' + ((Number(this.value) || 0) * 100).toFixed(2) + ' lb)'" class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs font-mono text-zinc-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                            </label>
                                            <label class="text-[10px] font-bold text-zinc-600">Cones in each bag
                                                <input type="number" name="cones_per_bag" min="1" max="1000" step="1" required class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs font-mono text-zinc-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                            </label>
                                            <label class="text-[10px] font-bold text-zinc-600">Received date
                                                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs text-zinc-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                            </label>
                                            <label class="text-[10px] font-bold text-zinc-600">Notes <span class="font-normal text-zinc-400">(optional)</span>
                                                <input name="notes" maxlength="2000" class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs text-zinc-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200" placeholder="Lot or delivery notes">
                                            </label>
                                            <p class="col-span-2 rounded-lg bg-amber-50 px-2.5 py-2 text-[10px] leading-relaxed text-amber-900">Each bag is counted as 100 lb. Total weight: <strong data-weight-output>0.000 kg (0.00 lb)</strong>. Saved as customer property, outside factory stock.</p>
                                            <button type="submit" class="col-span-2 rounded-lg bg-zinc-900 px-3 py-2.5 text-[11px] font-extrabold text-white hover:bg-zinc-700">Save customer bag receipt</button>
                                        </form>
                                    </details>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('invoices.invoices', ['contract_id' => $cnt->id]) }}#invoice-entry" class="block rounded-xl border border-zinc-300 bg-white px-3 py-2 text-center text-[11px] font-bold text-zinc-700 hover:bg-zinc-100">Create invoice for this contract</a>
                        @endif

                        <details class="rounded-xl border border-zinc-200 bg-white">
                            <summary class="cursor-pointer px-3 py-2.5 text-[11px] font-bold text-zinc-700">View contract details and costing</summary>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2 border-t border-zinc-100 px-3 py-3 text-[10px] sm:grid-cols-3">
                                <div><span class="text-zinc-400">Contract type / loom</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->contract_type ?? '—' }} / {{ $cnt->loom_type ?? '—' }}</strong></div>
                                <div><span class="text-zinc-400">Yarn bag supply</span><strong class="mt-0.5 block text-zinc-800">{{ $cnt->yarn_supply === 'customer' ? 'Customer supplies' : ($cnt->yarn_supply === 'factory' ? 'Factory supplies' : 'Mixed / not specified') }}</strong></div>
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
                        <div class="text-zinc-400 text-sm font-medium">No buyer contracts registered yet.</div>
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

@if($page === 'dashboard')
    @include('factory._dispatch_workflow')
@endif



@endsection
