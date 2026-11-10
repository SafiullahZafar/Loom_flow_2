@extends('layouts.app')

@section('title', 'Loom Parts Store & Issuance')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-6 items-start">

    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    <aside class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm lg:sticky lg:top-24">

        {{-- Dashboard main link --}}
        <a href="{{ route('parts.index') }}"
           class="mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 transition-all
                  {{ $page === 'dashboard'
                       ? 'bg-zinc-900 text-zinc-50 shadow-md'
                       : 'bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 hover:text-zinc-900' }}">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                         {{ $page === 'dashboard' ? 'bg-zinc-700' : 'bg-zinc-200' }}">
                <svg class="h-5 w-5 {{ $page === 'dashboard' ? 'text-zinc-100' : 'text-zinc-600' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-extrabold leading-tight">Parts Store Dashboard</span>
                <span class="block text-[10px] text-zinc-400 mt-0.5">Overview & stock health</span>
            </div>
        </a>

        <div class="mb-2 px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sections</div>

        <nav class="space-y-1.5" aria-label="Parts sections">

            {{-- Parts Inventory --}}
            <a href="{{ route('parts.inventory') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'inventory'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'inventory' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'inventory' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Parts Store Inventory</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Stock, pricing & rack locations</span>
                </div>
                @if($page === 'inventory')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Issuance Log --}}
            <a href="{{ route('parts.issuances') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'issuances'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'issuances' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'issuances' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Loom Issuance History</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Parts issued to factory looms</span>
                </div>
                @if($page === 'issuances')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

        </nav>

        {{-- Low stock alert panel --}}
        @if(isset($lowStockParts) && $lowStockParts->count() > 0)
        <div class="mt-4 p-3 rounded-2xl bg-zinc-100 border border-zinc-300">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-zinc-700 animate-ping"></span>
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-700">Low Stock Alert</span>
            </div>
            <div class="space-y-1">
                @foreach($lowStockParts->take(4) as $lsp)
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-zinc-600 truncate max-w-[130px]">{{ $lsp->part_name }}</span>
                        <span class="font-mono font-bold text-zinc-900 shrink-0 ml-1">{{ $lsp->stock_quantity }} {{ $lsp->unit }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="mt-5 pt-4 border-t border-zinc-100 space-y-1.5">
            <div class="px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Quick Actions</div>
            <button type="button" onclick="openModal('modalIssuePart')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Issue Part to Loom
            </button>
            <button type="button" onclick="openModal('modalAddPart')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Add New Part Item
            </button>
            <a href="{{ route('production.index') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Production Module
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
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Module 03</span>
                </div>
                <h1 class="text-2xl font-black text-zinc-900 tracking-tight">
                    {{ ['dashboard' => 'Parts Store Dashboard', 'inventory' => 'Parts Store Inventory', 'issuances' => 'Loom Issuance History Log'][$page] }}
                </h1>
                <p class="text-xs text-zinc-400 mt-0.5">
                    {{ ['dashboard' => 'Grippers, rapier tapes, heald wires, sensors — full store overview.', 'inventory' => 'All catalogued parts with stock levels, pricing, and rack locations.', 'issuances' => 'Complete log of every part issued to factory looms.'][$page] }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" onclick="openModal('modalIssuePart')"
                        class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 font-extrabold text-xs shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Issue Part to Loom
                </button>
                <button type="button" onclick="openModal('modalAddPart')"
                        class="px-3.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border border-zinc-300 text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    + Add New Part
                </button>
            </div>
        </div>

        {{-- KPI Cards (dashboard only) --}}
        @if($page === 'dashboard')
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Total Part Categories</span>
                    <span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-2xl font-black text-zinc-900 mt-1">{{ $totalItemsCount }} <span class="text-xs font-normal text-zinc-400">cataloged</span></div>
                <div class="text-[11px] text-zinc-400 mt-1 font-mono">{{ number_format($totalStockUnits) }} total units</div>
            </div>

            <div class="rounded-2xl p-4 border {{ $lowStockParts->count() > 0 ? 'border-zinc-400 bg-zinc-100' : 'border-zinc-200 bg-white' }} shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700 flex items-center justify-between">
                    <span>Low Stock Alert</span>
                    <span class="w-2 h-2 rounded-full {{ $lowStockParts->count() > 0 ? 'bg-zinc-700 animate-ping' : 'bg-zinc-300' }}"></span>
                </div>
                <div class="text-2xl font-black text-zinc-900 mt-1">
                    {{ $lowStockParts->count() }} <span class="text-xs font-normal text-zinc-400">items low</span>
                </div>
                <div class="text-[11px] text-zinc-500 mt-1">
                    {{ $lowStockParts->count() > 0 ? 'Needs restock now' : 'All parts healthy' }}
                </div>
            </div>

            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Store Valuation</span>
                    <span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-xl font-black text-zinc-900 mt-1 font-mono">Rs. {{ number_format($totalInventoryValuation, 0) }}</div>
                <div class="text-[11px] text-zinc-400 mt-1">Cost of parts in godown</div>
            </div>

            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Total Issuances</span>
                    <span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-2xl font-black text-zinc-900 mt-1">{{ $totalIssuancesCount }}</div>
                <div class="text-[11px] text-zinc-400 mt-1 font-mono">Rs. {{ number_format($totalIssuanceCost, 0) }} spent</div>
            </div>
        </div>
        @endif


        {{-- ============================================================
             SECTION 1 — Parts Inventory
        ============================================================ --}}
        @if(in_array($page, ['dashboard', 'inventory'], true))
        <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">
                            {{ $page === 'dashboard' ? 'Parts Store Snapshot' : 'Parts Store Inventory & Stock' }}
                        </h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Stock levels, pricing, and rack location for every catalogued part</p>
                    </div>
                </div>
                @if($page === 'dashboard')
                    <a href="{{ route('parts.inventory') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] tracking-wider bg-zinc-50">
                            <th class="px-4 py-3">Part Code</th>
                            <th class="px-4 py-3">Part Name</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3 text-right">In Stock</th>
                            <th class="px-4 py-3 text-right">Min Threshold</th>
                            <th class="px-4 py-3 text-right">Unit Price</th>
                            <th class="px-4 py-3 text-right bg-zinc-100/70 border-x border-zinc-200">Total Value</th>
                            <th class="px-4 py-3">Location / Rack</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($parts as $part)
                            <tr class="hover:bg-zinc-50 transition-colors {{ $part->stock_quantity <= $part->min_alert_threshold ? 'bg-zinc-50/80' : '' }}">
                                <td class="px-4 py-3 font-mono font-bold text-zinc-600">{{ $part->part_code }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-zinc-900">{{ $part->part_name }}</div>
                                    @if($part->notes)
                                        <div class="text-[10px] text-zinc-400 mt-0.5">{{ Str::limit($part->notes, 40) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-zinc-100 text-zinc-600 border border-zinc-200">{{ $part->category }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-mono font-black text-sm {{ $part->stock_quantity <= $part->min_alert_threshold ? 'text-zinc-500' : 'text-zinc-900' }}">
                                        {{ number_format($part->stock_quantity) }}
                                    </span>
                                    <span class="text-zinc-400 text-[11px] ml-0.5">{{ $part->unit }}</span>
                                    @if($part->stock_quantity <= $part->min_alert_threshold)
                                        <span class="block text-[9px] uppercase font-bold text-zinc-500 mt-0.5">⚠ Low Stock</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-zinc-400">{{ $part->min_alert_threshold }} {{ $part->unit }}</td>
                                <td class="px-4 py-3 text-right font-mono text-zinc-600">Rs. {{ number_format($part->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-zinc-900 bg-zinc-50 border-x border-zinc-200">
                                    Rs. {{ number_format($part->stock_quantity * $part->unit_price, 0) }}
                                </td>
                                <td class="px-4 py-3 text-zinc-400 font-mono text-[11px]">{{ $part->rack_location ?? '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button"
                                            onclick="preselectPartForIssuance('{{ $part->id }}', '{{ addslashes($part->part_name) }}', '{{ $part->stock_quantity }}', '{{ $part->unit }}', '{{ $part->unit_price }}')"
                                            class="px-3 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-[11px] font-bold transition-all whitespace-nowrap">
                                        Issue &rarr;
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center">
                                    <div class="text-zinc-400 text-sm font-medium">No parts catalogued yet</div>
                                    <div class="text-zinc-300 text-xs mt-1">Click "+ Add New Part" to start building your inventory</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($page === 'inventory')
                <div class="px-6 py-4 border-t border-zinc-100">{{ $parts->links() }}</div>
            @endif
        </div>
        @endif


        {{-- ============================================================
             SECTION 2 — Issuance Log
        ============================================================ --}}
        @if(in_array($page, ['dashboard', 'issuances'], true))
        <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">
                            {{ $page === 'dashboard' ? 'Recent Loom Issuances' : 'Loom Issuance History Log' }}
                        </h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Parts issued to looms, who received them, cost, and reason</p>
                    </div>
                </div>
                @if($page === 'dashboard')
                    <a href="{{ route('parts.issuances') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] tracking-wider bg-zinc-50">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Target Loom #</th>
                            <th class="px-4 py-3">Part Issued</th>
                            <th class="px-4 py-3">Mechanic / Incharge</th>
                            <th class="px-4 py-3 text-right">Qty</th>
                            <th class="px-4 py-3 text-right">Unit Price</th>
                            <th class="px-4 py-3 text-right bg-zinc-100/70 border-x border-zinc-200">Total Cost</th>
                            <th class="px-4 py-3">Reason / Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($issuances as $iss)
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-zinc-500">{{ $iss->issuance_date->format('d M, Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-zinc-900 text-zinc-50 font-mono font-black text-xs">
                                        {{ $iss->loom_no }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold text-zinc-800">{{ $iss->part ? $iss->part->part_name : '—' }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-700">{{ $iss->issued_to_person }}</td>
                                <td class="px-4 py-3 text-right font-mono font-black text-zinc-900">{{ $iss->quantity }}</td>
                                <td class="px-4 py-3 text-right font-mono text-zinc-500">Rs. {{ number_format($iss->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-zinc-900 bg-zinc-50 border-x border-zinc-200">
                                    Rs. {{ number_format($iss->total_cost, 2) }}
                                </td>
                                <td class="px-4 py-3 text-zinc-400 italic">{{ $iss->remarks ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <div class="text-zinc-400 text-sm font-medium">No issuances recorded yet</div>
                                    <div class="text-zinc-300 text-xs mt-1">Click "Issue Part to Loom" to log your first issuance</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($page === 'issuances')
                <div class="px-6 py-4 border-t border-zinc-100">{{ $issuances->links() }}</div>
            @endif
        </div>
        @endif

    </div>{{-- end main content --}}
</div>{{-- end grid --}}


{{-- ============================================================
     MODAL 1: ADD NEW PART
============================================================ --}}
<div id="modalAddPart" class="fixed inset-0 z-50 hidden bg-zinc-900/50 backdrop-blur-sm overflow-y-auto p-4">
    <div class="mx-auto my-8 bg-white border border-zinc-200 rounded-3xl max-w-lg w-full p-6 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
            <div>
                <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                    Add New Loom Part to Store
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">Add a new part to the store catalogue with opening stock</p>
            </div>
            <button type="button" onclick="closeModal('modalAddPart')" class="text-zinc-400 hover:text-zinc-900 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('parts.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Part Code / Item # *</label>
                    <input type="text" name="part_code" required placeholder="e.g. GRP-RH-02"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Category *</label>
                    <select name="category" required
                            class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Part Name / Specification *</label>
                <input type="text" name="part_name" required placeholder="e.g. Rapier Gripper Head (Right)"
                       class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Opening Stock *</label>
                    <input type="number" name="stock_quantity" required min="0" placeholder="10"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Low Alert Limit *</label>
                    <input type="number" name="min_alert_threshold" required min="0" placeholder="5"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Unit *</label>
                    <input type="text" name="unit" required value="pcs" placeholder="pcs, set, kg"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Unit Cost (Rs.) *</label>
                    <input type="number" step="0.01" name="unit_price" required min="0" placeholder="3800"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Rack / Bin Location</label>
                    <input type="text" name="rack_location" placeholder="e.g. Rack P-1"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Notes / Description</label>
                <input type="text" name="notes" placeholder="e.g. Compatible with Picanol / Dornier rapier looms"
                       class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div class="pt-3 border-t border-zinc-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalAddPart')"
                        class="px-4 py-2 rounded-xl bg-zinc-100 text-zinc-600 text-xs font-semibold">Cancel</button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-black shadow-sm">
                    Save Part to Store
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ============================================================
     MODAL 2: ISSUE PART TO LOOM
============================================================ --}}
<div id="modalIssuePart" class="fixed inset-0 z-50 hidden bg-zinc-900/50 backdrop-blur-sm overflow-y-auto p-4">
    <div class="mx-auto my-8 bg-white border border-zinc-200 rounded-3xl max-w-md w-full p-6 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
            <div>
                <h3 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-600"></span>
                    Issue Part to Factory Loom
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">Select a part and assign it to a specific loom</p>
            </div>
            <button type="button" onclick="closeModal('modalIssuePart')" class="text-zinc-400 hover:text-zinc-900 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('parts.issue') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Select Part from Store *</label>
                <select name="part_id" id="selectIssuePart" required onchange="handlePartSelectChange()"
                        class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    <option value="">— Choose Part Item —</option>
                    @foreach($issuanceParts as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->unit_price }}"
                                data-stock="{{ $p->stock_quantity }}" data-unit="{{ $p->unit }}">
                            {{ $p->part_name }} ({{ $p->part_code }}) — In Stock: {{ $p->stock_quantity }} {{ $p->unit }}
                        </option>
                    @endforeach
                </select>
                <div id="issueStockNotice" class="text-[11px] text-zinc-500 mt-1 min-h-[16px]"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Target Loom # *</label>
                    <select name="loom_no" required
                            class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        @foreach($looms as $loom)
                            <option value="{{ $loom }}">{{ $loom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Issuance Date *</label>
                    <input type="date" name="issuance_date" required value="{{ date('Y-m-d') }}"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Quantity to Issue *</label>
                    <input type="number" id="inpIssueQty" name="quantity" required min="1" value="1"
                           oninput="calculateIssueTotalCost()"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Issued to (Person) *</label>
                    <input type="text" name="issued_to_person" required placeholder="e.g. Master Aslam"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div class="p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-between text-xs">
                <span class="text-zinc-500 font-medium">Total Part Cost:</span>
                <strong id="displayIssueCost" class="font-mono text-zinc-900 text-base">Rs. 0.00</strong>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Reason / Maintenance Remarks</label>
                <input type="text" name="remarks" placeholder="e.g. Routine replacement, worn gripper clip"
                       class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div class="pt-3 border-t border-zinc-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalIssuePart')"
                        class="px-4 py-2 rounded-xl bg-zinc-100 text-zinc-600 text-xs font-semibold">Cancel</button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-black shadow-sm">
                    Confirm Issuance
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let selectedPartPrice = 0;

    function handlePartSelectChange() {
        const select = document.getElementById('selectIssuePart');
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            selectedPartPrice = parseFloat(opt.dataset.price) || 0;
            document.getElementById('issueStockNotice').innerHTML =
                `In store: <strong class="text-zinc-800">${opt.dataset.stock} ${opt.dataset.unit}</strong> &bull; Unit rate: Rs. ${Number(selectedPartPrice).toLocaleString()}`;
        } else {
            selectedPartPrice = 0;
            document.getElementById('issueStockNotice').innerHTML = '';
        }
        calculateIssueTotalCost();
    }

    function calculateIssueTotalCost() {
        const qty = parseFloat(document.getElementById('inpIssueQty').value) || 0;
        document.getElementById('displayIssueCost').innerText =
            'Rs. ' + (qty * selectedPartPrice).toLocaleString('en-US', { minimumFractionDigits: 2 });
    }

    function preselectPartForIssuance(partId, partName, stock, unit, price) {
        document.getElementById('selectIssuePart').value = partId;
        handlePartSelectChange();
        openModal('modalIssuePart');
    }
</script>
@endpush
@endsection
