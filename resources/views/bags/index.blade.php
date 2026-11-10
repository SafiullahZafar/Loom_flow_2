@extends('layouts.app')

@section('title', 'Bags (Yarn) Management & Stock')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[250px_minmax(0,1fr)] gap-6 items-start">
    <aside class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm lg:sticky lg:top-24">

        {{-- Dashboard main link --}}
        <a href="{{ route('bags.index') }}"
           class="mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 transition-all
                  {{ $page === 'dashboard'
                       ? 'bg-zinc-900 text-zinc-50 shadow-md'
                       : 'bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 hover:text-zinc-900' }}">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                         {{ $page === 'dashboard' ? 'bg-zinc-700' : 'bg-zinc-200' }}">
                <svg class="h-5 w-5 {{ $page === 'dashboard' ? 'text-zinc-100' : 'text-zinc-600' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-extrabold leading-tight">Bags Dashboard</span>
                <span class="block text-[10px] {{ $page === 'dashboard' ? 'text-zinc-400' : 'text-zinc-400' }} mt-0.5">Overview & all stats</span>
            </div>
        </a>

        <div class="mb-2 px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sections</div>

        <nav class="space-y-1.5" aria-label="Bags sections">

            {{-- Purchases --}}
            <a href="{{ route('bags.purchases') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'purchases'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'purchases' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'purchases' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Bags Purchases</span>
                    <span class="block text-[10px] font-normal {{ $page === 'purchases' ? 'text-zinc-400' : 'text-zinc-400' }}">Split history & challan log</span>
                </div>
                @if($page === 'purchases')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Daily Issuance --}}
            <a href="{{ route('bags.ledger') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'ledger'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'ledger' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'ledger' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Daily Stock Issuance</span>
                    <span class="block text-[10px] font-normal {{ $page === 'ledger' ? 'text-zinc-400' : 'text-zinc-400' }}">Factory to sizing ledger</span>
                </div>
                @if($page === 'ledger')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Sizing Consumption --}}
            <a href="{{ route('bags.sizing-usage') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'sizing'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'sizing' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'sizing' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Sizing Bag Consumption</span>
                    <span class="block text-[10px] font-normal {{ $page === 'sizing' ? 'text-zinc-400' : 'text-zinc-400' }}">Usage, returns & held bags</span>
                </div>
                @if($page === 'sizing')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

        </nav>

        {{-- Quick actions at bottom of sidebar --}}
        <div class="mt-5 pt-4 border-t border-zinc-100 space-y-1.5">
            <div class="px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Quick Actions</div>
            <button type="button" onclick="openModal('modalNewPurchase')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Purchase
            </button>
            <button type="button" onclick="openModal('modalIssueStock')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Issue Bags
            </button>
            <button type="button" onclick="openModal('modalReturnStock')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                </svg>
                Receive Return
            </button>
            <button type="button" onclick="openModal('modalYarnQualities')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Manage Yarn Qualities
            </button>
        </div>
    </aside>

    <div class="min-w-0 space-y-6">

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-zinc-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Module 01</span>
            </div>
            <h1 class="text-2xl font-black text-zinc-900 tracking-tight">{{ $page === 'dashboard' ? 'Bags Dashboard' : ['purchases' => 'Bags Purchases', 'ledger' => 'Daily Stock Issuance', 'sizing' => 'Sizing Bag Consumption'][$page] }}</h1>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" onclick="openModal('modalNewPurchase')" class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 font-extrabold text-xs shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ New Bag Purchase (Split)</span>
            </button>
            <button type="button" onclick="openModal('modalIssueStock')" class="px-3.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border border-zinc-300 text-xs font-bold transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Issue Bags Daily</span>
            </button>
            <button type="button" onclick="openModal('modalReturnStock')" class="px-3.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border border-zinc-300 text-xs font-bold transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                <span>Receive Bags Return</span>
            </button>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    @if ($page === 'dashboard')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="glass-card rounded-2xl p-4 border border-zinc-200">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                <span>Total Bags Purchased</span>
                <span class="w-2 h-2 rounded-full bg-zinc-400"></span>
            </div>
            <div class="text-2xl font-black text-zinc-900 mt-1">{{ number_format($totalPurchased) }} <span class="text-xs font-normal text-zinc-400">bags</span></div>
            <div class="text-[11px] text-zinc-400 mt-1 font-mono">Rs. {{ number_format($totalYarnInvestment, 0) }}</div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-zinc-300 bg-zinc-50">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700 flex items-center justify-between">
                <span>Factory Stock Store</span>
                <span class="w-2 h-2 rounded-full bg-zinc-700"></span>
            </div>
            <div class="text-2xl font-black text-zinc-900 mt-1">{{ number_format((float) $factoryStoreStock, 2) }} <span class="text-xs font-normal text-zinc-400">bags</span></div>
            <div class="text-[11px] text-zinc-500 mt-1">Ready in Mill Godown</div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-zinc-300 bg-zinc-50">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700 flex items-center justify-between">
                <span>With Sizing Persons</span>
                <span class="w-2 h-2 rounded-full bg-zinc-600"></span>
            </div>
            <div class="text-2xl font-black text-zinc-900 mt-1">{{ number_format((float) $sizingStockRemaining, 2) }} <span class="text-xs font-normal text-zinc-400">bags</span></div>
            <div class="text-[11px] text-zinc-500 mt-1">Allocated to Sizing Mills</div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-zinc-300 bg-zinc-50">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700 flex items-center justify-between">
                <span>Bags Used In Beams</span>
                <span class="w-2 h-2 rounded-full bg-zinc-600"></span>
            </div>
            <div class="text-2xl font-black text-zinc-900 mt-1">{{ number_format((float) $totalBagsUsedInBeams, 2) }} <span class="text-xs font-normal text-zinc-400">bags</span></div>
            <div class="text-[11px] text-zinc-500 mt-1">Converted to Sizing Beams</div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-zinc-300 bg-zinc-50">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700 flex items-center justify-between">
                <span>Unused Bags Status</span>
                <span class="w-2 h-2 rounded-full bg-zinc-500"></span>
            </div>
            <div class="text-xs font-bold text-zinc-800 mt-1">
                <div class="flex justify-between py-0.5"><span class="text-zinc-500">Returned:</span> <span class="font-mono">{{ number_format((float) $unusedBagsReturned, 2) }} bags</span></div>
                <div class="flex justify-between py-0.5"><span class="text-zinc-500">Held in Sizing:</span> <span class="font-mono">{{ number_format((float) $unusedBagsHeldWithSizing, 2) }} bags</span></div>
            </div>
        </div>
    </div>
    <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
        <div class="mb-3">
            <h2 class="text-sm font-extrabold text-zinc-900">Stock by Yarn Quality</h2>
            <p class="mt-1 text-[11px] text-zinc-500">Each quality is balanced separately across the factory store and sizing mills.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[520px] text-xs">
                <thead><tr class="border-b border-zinc-100 text-left text-[10px] uppercase tracking-wide text-zinc-400"><th class="py-2 pr-3">Yarn quality</th><th class="px-3 py-2 text-right">Factory stock</th><th class="px-3 py-2 text-right">With sizing</th><th class="py-2 pl-3 text-right">Total remaining</th></tr></thead>
                <tbody>
                    @forelse($qualityBalances as $balance)
                        <tr class="border-b border-zinc-50 last:border-0"><td class="py-2.5 pr-3 font-semibold text-zinc-800">{{ $balance['name'] }}</td><td class="px-3 py-2.5 text-right font-mono">{{ number_format($balance['factory_stock'], 2) }}</td><td class="px-3 py-2.5 text-right font-mono">{{ number_format($balance['sizing_stock'], 2) }}</td><td class="py-2.5 pl-3 text-right font-mono font-bold">{{ number_format($balance['total'], 2) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-zinc-400">Create a yarn quality to start tracking stock.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ============================================================
         SECTION 1 — Bag Purchases
    ============================================================ --}}
    @if (in_array($page, ['dashboard', 'purchases'], true))
    <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

        {{-- Card header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                    <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">
                        {{ $page === 'dashboard' ? 'Recent Purchases' : 'Bags Purchases & Split History' }}
                    </h2>
                    <p class="text-[10px] text-zinc-400 mt-0.5">Challan-wise purchase log with sizing / factory split</p>
                </div>
            </div>
            @if($page === 'dashboard')
                <a href="{{ route('bags.purchases') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">
                    View all &rarr;
                </a>
            @endif
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] tracking-wider bg-zinc-50">
                        <th class="px-4 py-3">Challan #</th>
                        <th class="px-4 py-3">Purchase Date</th>
                        <th class="px-4 py-3">Spinning Supplier</th>
                        <th class="px-4 py-3">Quality / Count</th>
                        <th class="px-4 py-3 text-right">Total Bags</th>
                        <th class="px-4 py-3 text-center bg-zinc-100/70 text-zinc-600 border-x border-zinc-200">To Sizing</th>
                        <th class="px-4 py-3 text-center bg-zinc-100/70 text-zinc-600 border-r border-zinc-200">To Factory Store</th>
                        <th class="px-4 py-3 text-right">Rate / Bag</th>
                        <th class="px-4 py-3 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($purchases as $p)
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-zinc-900">{{ $p->challan_no }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ $p->purchase_date->format('d M, Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-zinc-800">{{ $p->supplier_name }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 font-mono text-[11px]">{{ $p->quality }}</span></td>
                            <td class="px-4 py-3 text-right font-black text-zinc-900">{{ number_format((float) $p->total_bags, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold font-mono text-zinc-800 bg-zinc-50 border-x border-zinc-200">
                                {{ number_format((float) $p->bags_to_sizing, 2) }}
                                <span class="block text-[10px] font-normal text-zinc-500">{{ $p->sizing_destination ?: '—' }} · {{ number_format((float) $p->bags_to_sizing * 45.359237, 2) }} kg</span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold font-mono text-zinc-800 bg-zinc-50 border-r border-zinc-200">{{ number_format((float) $p->bags_to_factory, 2) }}</td>
                            <td class="px-4 py-3 text-right font-mono text-zinc-500">Rs. {{ number_format($p->rate_per_bag, 2) }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-zinc-900">Rs. {{ number_format($p->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-zinc-400">No bag purchases entered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($page === 'purchases')
            <div class="px-6 py-4 border-t border-zinc-100">{{ $purchases->links() }}</div>
        @endif
    </div>
    @endif


    {{-- ============================================================
         SECTION 2 — Daily Stock Issuance Ledger
    ============================================================ --}}
    @if (in_array($page, ['dashboard', 'ledger'], true))
    <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                    <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">
                        {{ $page === 'dashboard' ? 'Recent Stock Movements' : 'Daily Stock Issuance & Ledger Log' }}
                    </h2>
                    <p class="text-[10px] text-zinc-400 mt-0.5">Factory godown → sizing mills, returns, and all bag movements</p>
                </div>
            </div>
            @if($page === 'dashboard')
                <a href="{{ route('bags.ledger') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">
                    View all &rarr;
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] tracking-wider bg-zinc-50">
                        <th class="px-4 py-3">Date</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Ref #</th>
                        <th class="px-4 py-3">Quality</th><th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3">Source</th><th class="px-4 py-3">Destination</th>
                        <th class="px-4 py-3">Party / Mill</th><th class="px-4 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="px-4 py-3 text-zinc-500 font-mono">{{ $tx->transaction_date->format('d M, Y') }}</td>
                            <td class="px-4 py-3">
                                @if($tx->transaction_type === 'purchase_factory')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-200 text-zinc-700 border border-zinc-300">Factory Purchase</span>
                                @elseif($tx->transaction_type === 'purchase_sizing')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-200 text-zinc-700 border border-zinc-300">Direct Sizing</span>
                                @elseif($tx->transaction_type === 'issue_to_sizing')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-900 text-zinc-50">Issued To Sizing</span>
                                @elseif($tx->transaction_type === 'return_from_sizing')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-600 border border-zinc-200">Returned</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-500">{{ $tx->transaction_type }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono font-semibold text-zinc-900">{{ $tx->reference_no ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-zinc-700">{{ $tx->quality }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-zinc-900">{{ number_format((float) $tx->quantity_bags, 2) }} Bags</td>
                            <td class="px-4 py-3 text-zinc-500">{{ $tx->source }}</td>
                            <td class="px-4 py-3 text-zinc-700 font-semibold">{{ $tx->destination }}</td>
                            <td class="px-4 py-3 text-zinc-600">{{ $tx->party_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-zinc-400 italic">{{ $tx->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-zinc-400">No stock transactions logged yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($page === 'ledger')
            <div class="px-6 py-4 border-t border-zinc-100">{{ $transactions->links() }}</div>
        @endif
    </div>
    @endif


    {{-- ============================================================
         SECTION 3 — Sizing Bag Consumption & Returns
    ============================================================ --}}
    @if (in_array($page, ['dashboard', 'sizing'], true))
    <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                    <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">
                        {{ $page === 'dashboard' ? 'Recent Sizing Usage & Returns' : 'Sizing Bag Consumption & Returns' }}
                    </h2>
                    <p class="text-[10px] text-zinc-400 mt-0.5">Bags used in beams, returned to factory, or held with sizing persons</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($page === 'dashboard')
                    <a href="{{ route('bags.sizing-usage') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">
                        View all &rarr;
                    </a>
                @endif
                <a href="{{ route('sizing.index') }}" class="px-3 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 font-bold text-[11px] transition-colors">
                    Open Sizing &rarr;
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] tracking-wider bg-zinc-50">
                        <th class="px-4 py-3">Sizing Slip #</th>
                        <th class="px-4 py-3">Sizing Party</th>
                        <th class="px-4 py-3">Quality</th>
                        <th class="px-4 py-3 text-center">Bags Allocated</th>
                        <th class="px-4 py-3 text-center bg-zinc-900/5 text-zinc-700 border-x border-zinc-200">Used in Beams</th>
                        <th class="px-4 py-3 text-center bg-zinc-100/70 text-zinc-600 border-r border-zinc-200">Returned to Factory</th>
                        <th class="px-4 py-3 text-center bg-zinc-100/70 text-zinc-600 border-r border-zinc-200">Held With Sizing</th>
                        <th class="px-4 py-3 text-center">Beams Made</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($sizingSlips as $slip)
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-zinc-900">{{ $slip->slip_no }}</td>
                            <td class="px-4 py-3 font-semibold text-zinc-800">{{ $slip->sizing_party }}</td>
                            <td class="px-4 py-3 text-zinc-600">{{ $slip->yarn_quality }}</td>
                            <td class="px-4 py-3 text-center font-bold text-zinc-900 font-mono">{{ number_format((float) $slip->bags_allocated, 2) }}</td>
                            <td class="px-4 py-3 text-center font-black text-zinc-900 bg-zinc-50 border-x border-zinc-200 font-mono">{{ number_format((float) $slip->bags_used, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-zinc-700 bg-zinc-50 border-r border-zinc-200 font-mono">{{ number_format((float) $slip->bags_returned_to_factory, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-zinc-700 bg-zinc-50 border-r border-zinc-200 font-mono">{{ number_format((float) $slip->bags_held_with_sizing, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-zinc-700">{{ $slip->total_beams }} Beams ({{ number_format($slip->total_length_meters) }}m)</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                             {{ $slip->status === 'completed'
                                                  ? 'bg-zinc-900 text-zinc-50'
                                                  : 'bg-zinc-100 text-zinc-500 border border-zinc-200' }}">
                                    {{ $slip->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-zinc-400">No sizing slips found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($page === 'sizing')
            <div class="px-6 py-4 border-t border-zinc-100">{{ $sizingSlips->links() }}</div>
        @endif
    </div>
    @endif

    </div>
</div>

<!-- MODAL 1: NEW BAG PURCHASE -->
<div id="modalNewPurchase" class="fixed inset-0 z-50 hidden bg-zinc-900/50 backdrop-blur-sm overflow-y-auto p-4">
    <div class="mx-auto my-8 bg-white border border-zinc-200 rounded-3xl max-w-xl w-full p-6 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
            <div>
                <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                    New Bag Purchase (Yarn)
                </h3>
                <p class="text-xs text-zinc-400">Enter purchase & split between Sizing and Factory Stock Store</p>
            </div>
            <button type="button" onclick="closeModal('modalNewPurchase')" class="text-zinc-400 hover:text-zinc-900 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('bags.purchase') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Challan / Invoice # *</label>
                    <input type="text" name="challan_no" required placeholder="e.g. CH-8901" value="{{ old('challan_no') }}" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Purchase Date *</label>
                    <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Spinning Mill / Supplier *</label>
                    <input type="text" name="supplier_name" required placeholder="e.g. Al-Karam Spinning" value="{{ old('supplier_name') }}" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Yarn Quality / Count *</label>
                    <div class="flex gap-2">
                        <select name="quality" required data-yarn-quality-select class="min-w-0 flex-1 bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                            <option value="">Select yarn quality</option>
                            @foreach($yarnQualities as $yarnQuality)<option value="{{ $yarnQuality->name }}">{{ $yarnQuality->name }}</option>@endforeach
                        </select>
                        <button type="button" onclick="openModal('modalYarnQualities')" class="shrink-0 rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs font-bold text-zinc-700 transition-colors hover:bg-zinc-100">+ Create</button>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Total Bags Purchased *</label>
                    <input type="number" step="0.01" id="inpTotalBags" name="total_bags" required min="0.01" placeholder="100" oninput="calculateSplit()" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Rate Per Bag (Rs.) *</label>
                    <input type="number" step="0.01" id="inpRatePerBag" name="rate_per_bag" required min="0" placeholder="14500" oninput="calculateSplit()" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-600">📦 Dispatch Split: Sizing vs Factory</span>
                    <span id="splitStatus" class="text-xs font-bold font-mono text-zinc-500">Total: 0 Bags</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-600 mb-1">Direct to Sizing (Bags)</label>
                        <input type="number" step="0.01" id="inpBagsToSizing" name="bags_to_sizing" value="0" min="0" oninput="validateSplitNumbers(); updateSizingDestination(); updateSizingBagWeight()" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        <span class="text-[10px] text-zinc-400">Directly sent to sizing mill</span>
                        <span id="displaySizingBagWeight" class="block text-[10px] font-semibold text-zinc-600">0 bags · 0.00 kg / 0 lb</span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-600 mb-1">To Factory Godown (Bags)</label>
                        <input type="number" step="0.01" id="inpBagsToFactory" name="bags_to_factory" value="0" min="0" oninput="validateSplitNumbers()" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        <span class="text-[10px] text-zinc-400">Stored in mill warehouse</span>
                    </div>
                </div>
                <div id="sizingDestinationWrap" class="hidden">
                    <label class="block text-[11px] font-semibold text-zinc-600 mb-1">Sizing Mill / Contractor *</label>
                    <input type="text" id="inpSizingDestination" name="sizing_destination" placeholder="e.g. National Sizing Mill" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    <span class="mt-1 block text-[10px] text-zinc-500">This will appear as a pending sizing entry with the bag quantity and quality already filled.</span>
                </div>
                <div class="pt-2 border-t border-zinc-200 flex items-center justify-between text-xs">
                    <span class="text-zinc-500">Calculated Total Amount:</span>
                    <strong id="displayTotalAmount" class="font-mono text-zinc-900 text-sm">Rs. 0.00</strong>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Notes / Contract Details</label>
                <textarea name="notes" rows="2" placeholder="e.g. Contract #889, direct dispatch" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500"></textarea>
            </div>
            <div class="pt-3 border-t border-zinc-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalNewPurchase')" class="px-4 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-semibold">Cancel</button>
                <button type="submit" id="btnSubmitPurchase" class="px-5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-black shadow-sm">Save Purchase & Split</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: ISSUE BAGS DAILY -->
<div id="modalIssueStock" class="fixed inset-0 z-50 hidden bg-zinc-900/50 backdrop-blur-sm overflow-y-auto p-4">
    <div class="bg-white border border-zinc-200 rounded-3xl max-w-md w-full p-6 shadow-xl relative">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
            <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-zinc-600"></span>
                Issue Stock Daily (From Factory)
            </h3>
            <button type="button" onclick="closeModal('modalIssueStock')" class="text-zinc-400 hover:text-zinc-900 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('bags.issue') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Issuance Reference / Slip # *</label>
                <input type="text" name="reference_no" required placeholder="e.g. ISS-105" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Date *</label>
                    <input type="date" name="transaction_date" required value="{{ date('Y-m-d') }}" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Quantity (Bags) *</label>
                    <input type="number" id="inpIssueBags" name="quantity_bags" required min="0.01" step="0.01" max="{{ $factoryStoreStock }}" placeholder="10" oninput="updateIssueBagWeight()" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    <span class="text-[10px] text-zinc-500">In Godown: {{ $factoryStoreStock }} bags · <span id="displayIssueBagWeight">0.00 kg</span></span>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Yarn Quality *</label>
                <div class="flex gap-2">
                    <select name="quality" required data-yarn-quality-select class="min-w-0 flex-1 bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        <option value="">Select yarn quality</option>
                        @foreach($yarnQualities as $yarnQuality)<option value="{{ $yarnQuality->name }}">{{ $yarnQuality->name }}</option>@endforeach
                    </select>
                    <button type="button" onclick="openModal('modalYarnQualities')" class="shrink-0 rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs font-bold text-zinc-700 transition-colors hover:bg-zinc-100">+ Create</button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Destination / Sizing Mill *</label>
                <input type="text" name="destination" required placeholder="e.g. National Sizing Mill" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Receiver Name / Contractor *</label>
                <input type="text" name="party_name" required placeholder="e.g. Master Aslam" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Remarks</label>
                <input type="text" name="notes" placeholder="e.g. Lot for beam set 4" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div class="pt-3 border-t border-zinc-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalIssueStock')" class="px-4 py-2 rounded-xl bg-zinc-100 text-zinc-600 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-black">Confirm Issuance</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: RECEIVE RETURNED BAGS -->
<div id="modalReturnStock" class="fixed inset-0 z-50 hidden bg-zinc-900/50 backdrop-blur-sm overflow-y-auto p-4">
    <div class="bg-white border border-zinc-200 rounded-3xl max-w-md w-full p-6 shadow-xl relative">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
            <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-zinc-500"></span>
                Receive Unused Bags Back
            </h3>
            <button type="button" onclick="closeModal('modalReturnStock')" class="text-zinc-400 hover:text-zinc-900 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('bags.return') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Return Slip / Ref # *</label>
                <input type="text" name="reference_no" required placeholder="e.g. RET-45" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Date *</label>
                    <input type="date" name="transaction_date" required value="{{ date('Y-m-d') }}" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Quantity Returned (Bags) *</label>
                    <input type="number" name="quantity_bags" required min="0.01" step="0.01" placeholder="5" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Yarn Quality *</label>
                <div class="flex gap-2">
                    <select name="quality" required data-yarn-quality-select class="min-w-0 flex-1 bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        <option value="">Select yarn quality</option>
                        @foreach($yarnQualities as $yarnQuality)<option value="{{ $yarnQuality->name }}">{{ $yarnQuality->name }}</option>@endforeach
                    </select>
                    <button type="button" onclick="openModal('modalYarnQualities')" class="shrink-0 rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs font-bold text-zinc-700 transition-colors hover:bg-zinc-100">+ Create</button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Returning Sizing Mill / Party *</label>
                <input type="text" name="source" required placeholder="e.g. National Sizing Mill" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Contractor / Person Name *</label>
                <input type="text" name="party_name" required placeholder="e.g. Sizing Contractor" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Reason / Notes</label>
                <input type="text" name="notes" placeholder="e.g. Surplus bags from completed beam set" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div class="pt-3 border-t border-zinc-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalReturnStock')" class="px-4 py-2 rounded-xl bg-zinc-100 text-zinc-600 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-black">Receive into Godown</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function updateSizingDestination() {
        const quantity = parseFloat(document.getElementById('inpBagsToSizing').value) || 0;
        const wrap = document.getElementById('sizingDestinationWrap');
        const destination = document.getElementById('inpSizingDestination');
        wrap.classList.toggle('hidden', quantity <= 0);
        destination.required = quantity > 0;
    }

    function updateSizingBagWeight() {
        const bags = parseFloat(document.getElementById('inpBagsToSizing').value) || 0;
        document.getElementById('displaySizingBagWeight').innerText = `${bags} bags · ${(bags * 45.359237).toFixed(2)} kg / ${(bags * 100).toLocaleString()} lb`;
    }

    function updateIssueBagWeight() {
        const bags = parseFloat(document.getElementById('inpIssueBags').value) || 0;
        document.getElementById('displayIssueBagWeight').innerText = `${(bags * 45.359237).toFixed(2)} kg (${(bags * 100).toLocaleString()} lb)`;
    }

    function calculateSplit() {
        const total = parseFloat(document.getElementById('inpTotalBags').value) || 0;
        const sizing = parseFloat(document.getElementById('inpBagsToSizing').value) || 0;
        const factory = parseFloat(document.getElementById('inpBagsToFactory').value) || 0;
        if (sizing === 0 && factory === 0 && total > 0) {
            const half = Math.floor(total * 50) / 100;
            document.getElementById('inpBagsToSizing').value = half;
            document.getElementById('inpBagsToFactory').value = total - half;
        }
        updateSizingDestination();
        updateSizingBagWeight();
        validateSplitNumbers();
    }

    function validateSplitNumbers() {
        const total = parseFloat(document.getElementById('inpTotalBags').value) || 0;
        const rate = parseFloat(document.getElementById('inpRatePerBag').value) || 0;
        const sizing = parseFloat(document.getElementById('inpBagsToSizing').value) || 0;
        const factory = parseFloat(document.getElementById('inpBagsToFactory').value) || 0;
        const sum = sizing + factory;
        const statusEl = document.getElementById('splitStatus');
        const submitBtn = document.getElementById('btnSubmitPurchase');
        document.getElementById('displayTotalAmount').innerText = 'Rs. ' + (total * rate).toLocaleString('en-US', {minimumFractionDigits: 2});
        if (Math.round(sum * 100) === Math.round(total * 100) && total > 0) {
            statusEl.innerText = `Matched: ${sizing} Sizing + ${factory} Factory = ${total} ✓`;
            statusEl.className = 'text-xs font-bold font-mono text-zinc-700';
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            statusEl.innerText = `Sum: ${sum} / ${total} Bags ✗`;
            statusEl.className = 'text-xs font-bold font-mono text-zinc-400';
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
</script>
@endpush
@include('yarn-qualities.modal')
@endsection
