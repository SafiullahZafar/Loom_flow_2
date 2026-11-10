@extends('layouts.app')

@section('title', 'Fabric Stock Management')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-6 items-start">

    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    <aside class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm lg:sticky lg:top-24">

        {{-- Dashboard main link --}}
        <a href="{{ route('fabric.index') }}"
           class="mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 transition-all
                  {{ $page === 'dashboard'
                       ? 'bg-zinc-900 text-zinc-50 shadow-md'
                       : 'bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 hover:text-zinc-900' }}">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                         {{ $page === 'dashboard' ? 'bg-zinc-700' : 'bg-zinc-200' }}">
                <svg class="h-5 w-5 {{ $page === 'dashboard' ? 'text-zinc-100' : 'text-zinc-600' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-extrabold leading-tight">Fabric Stock Dashboard</span>
                <span class="block text-[10px] text-zinc-400 mt-0.5">Overview & stock valuation</span>
            </div>
        </a>

        <div class="mb-2 px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sections</div>

        <nav class="space-y-1.5" aria-label="Fabric sections">

            {{-- Fabric Inventory --}}
            <a href="{{ route('fabric.inventory') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'inventory'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'inventory' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'inventory' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Fabric Stock Inventory</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Qualities, meters & valuation</span>
                </div>
                @if($page === 'inventory')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Active Contracts --}}
            <a href="{{ route('fabric.contracts') }}"
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
                    <span class="block leading-tight">Active Contracts</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Buyer orders & delivery progress</span>
                </div>
                @if($page === 'contracts')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

        </nav>

        {{-- Stock snapshot summary --}}
        @if(isset($totalMetersInStock))
        <div class="mt-4 rounded-2xl bg-zinc-50 border border-zinc-200 p-3 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Stock Snapshot</div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Open Market</span>
                <span class="font-mono font-bold text-zinc-900">{{ number_format($totalMetersAvailableOpen) }}m</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Committed</span>
                <span class="font-mono font-bold text-zinc-600">{{ number_format($totalMetersCommitted) }}m</span>
            </div>
            <div class="pt-1.5 border-t border-zinc-200 flex items-center justify-between text-xs">
                <span class="text-zinc-700 font-bold">Total In Store</span>
                <span class="font-mono font-black text-zinc-900">{{ number_format($totalMetersInStock) }}m</span>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="mt-5 pt-4 border-t border-zinc-100 space-y-1.5">
            <div class="px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Quick Actions</div>
            <a href="{{ route('sales.index') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Sell Fabric
            </a>
            <button type="button" onclick="openModal('modalAddFabric')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Quality Spec
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
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Module 04</span>
                </div>
                <h1 class="text-2xl font-black text-zinc-900 tracking-tight">
                    {{ ['dashboard' => 'Fabric Stock Dashboard', 'inventory' => 'Fabric Stock Inventory', 'contracts' => 'Active Buyer Contracts'][$page] }}
                </h1>
                <p class="text-xs text-zinc-400 mt-0.5">
                    {{ ['dashboard' => 'Finished woven cloth — open market stock vs committed contract stock.', 'inventory' => 'All fabric qualities with meters available for open sale and committed to contracts.', 'contracts' => 'Active buyer contracts reserving fabric stock with delivery progress.'][$page] }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('sales.index') }}"
                   class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 font-extrabold text-xs shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Sell Fabric
                </a>
                <button type="button" onclick="openModal('modalAddFabric')"
                        class="px-3.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border border-zinc-300 text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Add Quality Spec
                </button>
                <button type="button" onclick="openModal('modalLinkLoomQuality')"
                        class="px-3.5 py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-2 2a4 4 0 01-5.656-5.656l1-1m3.656-2.172a4 4 0 010-5.656l2-2a4 4 0 015.656 5.656l-1 1M8 12h8"/></svg>
                    Link Loom Quality
                </button>
            </div>
        </div>

    <div id="modalLinkLoomQuality" class="fixed inset-0 z-50 hidden overflow-y-auto bg-zinc-950/60 p-4 sm:p-6">
        <div class="mx-auto my-8 w-full max-w-2xl rounded-3xl border border-zinc-200 bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-zinc-100 px-6 py-4">
                <div><h3 class="text-base font-extrabold text-zinc-900">Link Fabric Quality to Loom</h3><p class="mt-1 text-xs text-zinc-500">Choose a mounted loom, then link an existing fabric quality or create a new one.</p></div>
                <button type="button" onclick="closeModal('modalLinkLoomQuality')" class="rounded-lg px-3 py-2 text-zinc-500 hover:bg-zinc-100">×</button>
            </div>
            <form id="linkLoomQualityForm" method="POST" action="{{ route('fabric.loom-quality') }}" onsubmit="return submitLoomFabricQuality()" class="space-y-4 px-6 py-5">
                @csrf
                <label class="block text-xs font-bold text-zinc-600">Mounted loom
                    <select id="linkSizingBeamId" name="sizing_beam_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-sm">
                        <option value="">Choose mounted loom</option>
                        @foreach($mountedBeams as $beam)
                            <option value="{{ $beam->id }}">{{ $beam->assigned_loom_no }} · {{ $beam->beam_no }}{{ $beam->fabricStock ? ' · '.$beam->fabricStock->quality_name : ' · No quality linked' }}</option>
                        @endforeach
                    </select>
                    @if($mountedBeams->isEmpty())<span class="mt-1 block text-amber-700">Mount a sizing beam before linking a loom quality.</span>@endif
                </label>
                <label class="block text-xs font-bold text-zinc-600">Action
                    <select id="linkQualityMode" name="quality_mode" onchange="toggleLoomLinkQualityMode()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-sm">
                        <option value="existing">Link an existing Fabric Stock quality</option>
                        <option value="new">Create a new quality and link it</option>
                        <option value="unlink">Unlink current quality</option>
                    </select>
                </label>
                <div id="linkExistingQualityFields">
                    <label class="block text-xs font-bold text-zinc-600">Fabric quality
                        <select id="linkFabricStockId" name="fabric_stock_id" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-sm">
                            <option value="">Choose Fabric Stock quality</option>
                            @foreach($allFabricQualities as $quality)
                                <option value="{{ $quality->id }}">{{ $quality->quality_name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div id="linkNewQualityFields" class="hidden space-y-3">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="text-xs font-bold text-zinc-600">New quality name<input id="linkNewQualityName" name="quality_name" placeholder="e.g. 90 x 70 - 43 inch" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm"></label>
                        <label class="text-xs font-bold text-zinc-600">Construction<input name="construction" placeholder="e.g. 52x52 / 72x78" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm"></label>
                        <label class="text-xs font-bold text-zinc-600">Read (ends per inch)<input id="linkRead" name="read" type="number" min="0.0001" step="0.0001" oninput="calculateLinkFabricWeight()" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm"></label>
                        <label class="text-xs font-bold text-zinc-600">Pick (picks per inch)<input id="linkPick" name="pick" type="number" min="0.0001" step="0.0001" oninput="calculateLinkFabricWeight()" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm"></label>
                        <label class="text-xs font-bold text-zinc-600">Warp yarn count<input id="linkWarpCount" name="warp_count" type="number" min="0.0001" step="0.0001" oninput="calculateLinkFabricWeight()" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm"></label>
                        <label class="text-xs font-bold text-zinc-600">Weft yarn count<input id="linkWeftCount" name="weft_count" type="number" min="0.0001" step="0.0001" oninput="calculateLinkFabricWeight()" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm"></label>
                        <label class="text-xs font-bold text-zinc-600">Width (inches)<input id="linkWidth" name="width_inches" type="number" min="20" max="150" step="0.01" value="63" oninput="calculateLinkFabricWeight()" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm"></label>
                        <label class="text-xs font-bold text-zinc-600">Rate per meter<input name="unit_cost_meter" type="number" min="0" step="0.01" value="0" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm"></label>
                    </div>
                    <div class="rounded-xl bg-zinc-50 p-3 text-xs text-zinc-600">New quality specifications and weight are saved to Fabric Stock. It starts with zero stock. Total fabric weight: <strong id="linkWeightPreview">0.0000 lb/m</strong></div>
                </div>
                <div class="flex justify-end gap-2 border-t pt-4">
                    <button type="button" onclick="closeModal('modalLinkLoomQuality')" class="rounded-xl bg-zinc-100 px-4 py-3 text-xs font-bold">Cancel</button>
                    <button id="saveLoomQualityButton" {{ $mountedBeams->isEmpty() ? 'disabled' : '' }} class="rounded-xl bg-blue-700 px-5 py-3 text-xs font-bold text-white disabled:cursor-not-allowed disabled:opacity-50">Save link</button>
                </div>
            </form>
        </div>
    </div>

        {{-- KPI Cards (dashboard only) --}}
        @if($page === 'dashboard')
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Open Market Stock</span><span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-2xl font-black text-zinc-900 mt-1">{{ number_format($totalMetersAvailableOpen) }} <span class="text-xs font-normal text-zinc-400">m</span></div>
                <div class="text-[11px] text-zinc-400 mt-1">Ready for walk-in sales</div>
            </div>
            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Committed to Contracts</span><span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-2xl font-black text-zinc-900 mt-1">{{ number_format($totalMetersCommitted) }} <span class="text-xs font-normal text-zinc-400">m</span></div>
                <div class="text-[11px] text-zinc-400 mt-1">Reserved for buyer orders</div>
            </div>
            <div class="rounded-2xl p-4 border border-zinc-300 bg-zinc-50 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-600 flex items-center justify-between">
                    <span>Total In Store</span><span class="w-2 h-2 rounded-full bg-zinc-500"></span>
                </div>
                <div class="text-2xl font-black text-zinc-900 mt-1">{{ number_format($totalMetersInStock) }} <span class="text-xs font-normal text-zinc-400">m</span></div>
                <div class="text-[11px] text-zinc-400 mt-1 font-mono">{{ number_format($totalRollsInStock) }} rolls / thaans</div>
            </div>
            <div class="rounded-2xl p-4 border border-zinc-300 bg-zinc-50 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-600 flex items-center justify-between">
                    <span>Inventory Valuation</span><span class="w-2 h-2 rounded-full bg-zinc-500"></span>
                </div>
                <div class="text-xl font-black text-zinc-900 mt-1 font-mono">Rs. {{ number_format($totalInventoryValue, 0) }}</div>
                <div class="text-[11px] text-zinc-400 mt-1">Estimated market value</div>
            </div>
        </div>
        @endif

        {{-- ============================================================
             SECTION 1 — Fabric Inventory Table
        ============================================================ --}}
        @if(in_array($page, ['dashboard', 'inventory'], true))
        <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">
                            {{ $page === 'dashboard' ? 'Fabric Stock Snapshot' : 'Fabric Qualities & Inventory Balances' }}
                        </h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Open sale stock vs committed contract meters per quality</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($page === 'dashboard')
                        <a href="{{ route('fabric.inventory') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
                    @else
                        <span class="text-xs text-zinc-400 font-mono">{{ $fabricStocks->total() }} qualities</span>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead><tr class="border-b border-zinc-200 bg-zinc-50 text-left text-[10px] font-semibold uppercase tracking-wider text-zinc-500"><th class="px-4 py-3">Quality / Date</th><th class="px-4 py-3 text-right">Previous</th><th class="px-4 py-3 text-right">Received</th><th class="px-4 py-3 text-right">Adjustment (+/−)</th><th class="px-4 py-3 text-right">Total Stock</th><th class="px-4 py-3 text-right">Dispatch</th><th class="px-4 py-3">Area</th><th class="px-4 py-3">Remarks</th></tr></thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($stockMovements as $movement)
                            <tr class="hover:bg-zinc-50"><td class="px-4 py-3"><div class="font-bold text-zinc-900">{{ $movement->fabricStock->quality_name }}</div><div class="text-[10px] text-zinc-400">{{ $movement->movement_date->format('d-m-Y') }}</div></td><td class="px-4 py-3 text-right font-mono">{{ number_format($movement->previous_stock_meters, 0) }}</td><td class="px-4 py-3 text-right font-mono">{{ number_format($movement->received_meters, 0) }}</td><td class="px-4 py-3 text-right font-mono">{{ number_format($movement->adjustment_meters, 0) }}</td><td class="px-4 py-3 text-right font-mono font-black">{{ number_format($movement->closing_stock_meters, 0) }}</td><td class="px-4 py-3 text-right font-mono">{{ number_format($movement->dispatch_meters, 0) }}</td><td class="px-4 py-3">{{ $movement->area ?: '—' }}</td><td class="px-4 py-3 text-zinc-500">{{ $movement->remarks ?: '—' }}</td></tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-12 text-center text-zinc-400">No daily stock entries yet. Add a quality or record today’s stock.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-zinc-100 px-5 py-3">{{ $stockMovements->links() }}</div>

            @if($page === 'inventory')
                <div class="px-6 py-4 border-t border-zinc-100">{{ $fabricStocks->links() }}</div>
            @endif
        </div>
        @endif


        {{-- ============================================================
             SECTION 2 — Active Contracts
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
                            {{ $page === 'dashboard' ? 'Active Buyer Contracts' : 'Party Contracts Reserving Fabric Stock' }}
                        </h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Delivery progress, remaining meters, and agreed rates per contract</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($page === 'dashboard')
                        <a href="{{ route('fabric.contracts') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
                    @endif
                    <a href="{{ route('sales.index') }}"
                       class="px-3 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 font-bold text-[11px] transition-colors whitespace-nowrap">
                        Manage Contracts &rarr;
                    </a>
                </div>
            </div>

            {{-- Contracts grid --}}
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($activeContracts as $cnt)
                    @php
                        $pct = $cnt->total_meters > 0
                            ? min(100, round(($cnt->delivered_meters / $cnt->total_meters) * 100))
                            : 0;
                    @endphp
                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 space-y-3 hover:border-zinc-300 transition-colors">

                        {{-- Header row --}}
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-zinc-200 text-zinc-700 border border-zinc-300 mb-1">
                                    {{ $cnt->contract_no }}
                                </span>
                                <div class="font-extrabold text-zinc-900 text-sm leading-tight">{{ $cnt->buyer_name }}</div>
                                <div class="text-[11px] text-zinc-500 mt-0.5">{{ $cnt->fabric_quality }}</div>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                         {{ $cnt->status === 'active' ? 'bg-zinc-900 text-zinc-50' : 'bg-zinc-200 text-zinc-500' }}">
                                {{ $cnt->status }}
                            </span>
                        </div>

                        {{-- Progress bar --}}
                        <div>
                            <div class="flex justify-between text-[11px] mb-1.5 font-semibold">
                                <span class="text-zinc-500">Delivery Progress</span>
                                <span class="text-zinc-700 font-mono">{{ $pct }}%</span>
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

                        {{-- Stats row --}}
                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-zinc-200 text-xs">
                            <div>
                                <span class="block text-[9px] text-zinc-400 uppercase font-bold mb-0.5">Remaining</span>
                                <strong class="text-zinc-900 font-mono">{{ number_format($cnt->remaining_meters) }}m</strong>
                            </div>
                            <div>
                                <span class="block text-[9px] text-zinc-400 uppercase font-bold mb-0.5">Rate / m</span>
                                <strong class="text-zinc-900 font-mono">Rs. {{ $cnt->rate_per_meter }}</strong>
                            </div>
                            <div>
                                <span class="block text-[9px] text-zinc-400 uppercase font-bold mb-0.5">Value</span>
                                <strong class="text-zinc-900 font-mono">Rs. {{ number_format($cnt->total_amount, 0) }}</strong>
                            </div>
                        </div>

                        @if($cnt->notes)
                            <div class="text-[11px] text-zinc-400 italic bg-white border border-zinc-200 px-3 py-2 rounded-xl">
                                {{ $cnt->notes }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center">
                        <div class="text-zinc-400 text-sm font-medium">No active contracts</div>
                        <div class="text-zinc-300 text-xs mt-1">Go to Sales to register a new buyer contract</div>
                    </div>
                @endforelse
            </div>

            @if($page === 'contracts')
                <div class="px-6 py-4 border-t border-zinc-100">{{ $activeContracts->links() }}</div>
            @endif
        </div>
        @endif

    </div>{{-- end main --}}
</div>{{-- end grid --}}


{{-- Stock entry: existing quality auto-fills its current balance; new quality starts at zero. --}}
<div id="modalAddFabric" class="fixed inset-0 z-50 hidden bg-zinc-900/50 backdrop-blur-sm overflow-y-auto p-4 sm:p-6">
    <div class="mx-auto my-8 w-full max-w-2xl rounded-3xl border border-zinc-200 bg-white shadow-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100">
            <div>
                <h3 class="text-base font-extrabold text-zinc-900">Fabric Stock Entry</h3>
                <p class="mt-0.5 text-xs text-zinc-500">Choose an existing quality or create one at zero stock.</p>
            </div>
            <button type="button" onclick="closeModal('modalAddFabric')"
                    class="text-zinc-400 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('fabric.store') }}" method="POST" class="px-6 py-5 space-y-4">@csrf
            <label class="block text-xs font-semibold">Entry type<select id="qualityMode" name="quality_mode" onchange="toggleFabricQualityMode()" class="mt-1 w-full rounded-xl border px-3 py-2"><option value="existing">Update existing quality</option><option value="new">Create new quality</option></select></label>
            <div id="existingQualityFields"><label class="block text-xs font-semibold">Fabric quality<select id="existingFabricQuality" name="fabric_stock_id" onchange="fillExistingFabricStock()" class="mt-1 w-full rounded-xl border bg-white px-3 py-2"><option value="">Choose quality</option>@foreach($allFabricQualities as $quality)<option value="{{ $quality->id }}" data-stock="{{ $quality->current_stock_meters }}" data-area="{{ $quality->area }}" data-weight-per-meter="{{ $quality->total_weight_per_meter_lb }}">{{ $quality->quality_name }} ({{ number_format($quality->current_stock_meters, 0) }}m) @if($quality->total_weight_per_meter_lb !== null) · {{ number_format($quality->total_weight_per_meter_lb, 4) }} lb/m @endif</option>@endforeach</select></label></div>
            <div id="newQualityFields" class="hidden space-y-3">
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">New quality name<input id="newQualityName" name="quality_name" placeholder="90 x 70 - 43 inch" class="mt-1 w-full rounded-xl border px-3 py-2"></label><label class="text-xs font-semibold">Construction<input name="construction" placeholder="52x52 / 72x78" class="mt-1 w-full rounded-xl border px-3 py-2"></label></div>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Read (ends per inch)<input id="fabricRead" name="read" type="number" min="0.0001" step="0.0001" oninput="calculateFabricWeightPerMeter()" placeholder="60" class="mt-1 w-full rounded-xl border px-3 py-2"></label><label class="text-xs font-semibold">Pick (picks per inch)<input id="fabricPick" name="pick" type="number" min="0.0001" step="0.0001" oninput="calculateFabricWeightPerMeter()" placeholder="66" class="mt-1 w-full rounded-xl border px-3 py-2"></label></div>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Warp yarn count<input id="fabricWarpCount" name="warp_count" type="number" min="0.0001" step="0.0001" oninput="calculateFabricWeightPerMeter()" placeholder="58.5" class="mt-1 w-full rounded-xl border px-3 py-2"></label><label class="text-xs font-semibold">Weft yarn count<input id="fabricWeftCount" name="weft_count" type="number" min="0.0001" step="0.0001" oninput="calculateFabricWeightPerMeter()" placeholder="58.5" class="mt-1 w-full rounded-xl border px-3 py-2"></label></div>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Width (inches)<input id="fabricWidthInches" name="width_inches" type="number" min="20" max="150" step="0.01" value="63" oninput="calculateFabricWeightPerMeter()" class="mt-1 w-full rounded-xl border px-3 py-2"></label><label class="text-xs font-semibold">Rate per meter<input name="unit_cost_meter" type="number" step="0.01" min="0" value="0" class="mt-1 w-full rounded-xl border px-3 py-2"></label></div>
                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-3 text-xs"><p class="font-bold text-zinc-700">Weight calculation (stored per quality)</p><p class="mt-1 text-zinc-500">Warp lb/m = TRUNC(Read × Width ÷ Warp Count ÷ 20 × 1.0936 ÷ 40, 4)</p><p class="text-zinc-500">Weft lb/m = TRUNC(Pick × Width ÷ Weft Count ÷ 20 × 1.0936 ÷ 40, 4)</p><div class="mt-2 grid grid-cols-3 gap-2 border-t border-zinc-200 pt-2 font-mono"><span>Warp: <strong id="fabricWarpWeightPreview">0.0000</strong></span><span>Weft: <strong id="fabricWeftWeightPreview">0.0000</strong></span><span>Total lb/m: <strong id="fabricTotalWeightPreview">0.0000</strong></span></div></div>
                <p class="rounded-xl bg-zinc-50 p-3 text-xs text-zinc-500">A new quality starts with zero stock. Total weight is calculated from these specifications and saved with the quality.</p>
            </div>
            <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Previous stock (m)<input id="previousStockMeters" name="previous_stock_meters" type="number" readonly value="0" class="mt-1 w-full rounded-xl border bg-zinc-100 px-3 py-2 font-mono font-bold"></label><label class="text-xs font-semibold">Received today (m)<input name="daily_received_meters" type="number" min="0" step="0.01" value="0" class="mt-1 w-full rounded-xl border px-3 py-2"></label></div>
            <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Adjustment (+ / − m)<input name="adjustment_meters" type="number" step="0.01" value="0" class="mt-1 w-full rounded-xl border px-3 py-2"></label><label class="text-xs font-semibold">Dispatch (m)<input name="dispatch_meters" type="number" min="0" step="0.01" value="0" class="mt-1 w-full rounded-xl border px-3 py-2"></label></div>
            <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Rolls received<input name="rolls_received" type="number" min="0" value="0" class="mt-1 w-full rounded-xl border px-3 py-2"></label><label class="text-xs font-semibold">Area<input id="stockArea" name="area" class="mt-1 w-full rounded-xl border px-3 py-2"></label></div>
            <label class="block text-xs font-semibold">Remarks<input name="notes" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
            <div class="flex justify-end gap-2 border-t pt-3"><button type="button" onclick="closeModal('modalAddFabric')" class="rounded-xl bg-zinc-100 px-4 py-2 text-xs">Cancel</button><button class="rounded-xl bg-zinc-900 px-5 py-2 text-xs font-bold text-white">Save stock entry</button></div>
        </form>
    </div>
</div>
<script>
function toggleFabricQualityMode(){const isNew=document.getElementById('qualityMode').value==='new';document.getElementById('existingQualityFields').classList.toggle('hidden',isNew);document.getElementById('newQualityFields').classList.toggle('hidden',!isNew);document.getElementById('existingFabricQuality').required=!isNew;document.getElementById('newQualityName').required=isNew;['fabricRead','fabricPick','fabricWarpCount','fabricWeftCount','fabricWidthInches'].forEach((id)=>{document.getElementById(id).required=isNew});document.getElementById('previousStockMeters').value='0';if(!isNew){fillExistingFabricStock()}}
function fillExistingFabricStock(){const option=document.getElementById('existingFabricQuality').selectedOptions[0];document.getElementById('previousStockMeters').value=option?.dataset.stock||'0';document.getElementById('stockArea').value=option?.dataset.area||''}
function calculateFabricWeightPerMeter(){const read=parseFloat(document.getElementById('fabricRead').value)||0;const pick=parseFloat(document.getElementById('fabricPick').value)||0;const width=parseFloat(document.getElementById('fabricWidthInches').value)||0;const warpCount=parseFloat(document.getElementById('fabricWarpCount').value)||0;const weftCount=parseFloat(document.getElementById('fabricWeftCount').value)||0;const truncate4=(value)=>Math.trunc(value*10000)/10000;const warpWeight=read>0&&width>0&&warpCount>0?truncate4(read*width/warpCount/20*1.0936/40):0;const weftWeight=pick>0&&width>0&&weftCount>0?truncate4(pick*width/weftCount/20*1.0936/40):0;document.getElementById('fabricWarpWeightPreview').textContent=warpWeight.toFixed(4);document.getElementById('fabricWeftWeightPreview').textContent=weftWeight.toFixed(4);document.getElementById('fabricTotalWeightPreview').textContent=(warpWeight+weftWeight).toFixed(4)}
function toggleLoomLinkQualityMode(){const mode=document.getElementById('linkQualityMode').value;const isNew=mode==='new';const isUnlink=mode==='unlink';const existing=document.getElementById('linkExistingQualityFields');const newFields=document.getElementById('linkNewQualityFields');const qualitySelect=document.getElementById('linkFabricStockId');const newQualityName=document.getElementById('linkNewQualityName');const form=document.getElementById('linkLoomQualityForm');const button=document.getElementById('saveLoomQualityButton');existing.classList.toggle('hidden',isNew||isUnlink);newFields.classList.toggle('hidden',!isNew);qualitySelect.required=!isNew&&!isUnlink;qualitySelect.disabled=isNew||isUnlink;newQualityName.required=isNew;['linkRead','linkPick','linkWarpCount','linkWeftCount','linkWidth'].forEach((id)=>{document.getElementById(id).required=isNew});form.action=isNew?"{{ route('fabric.store') }}":"{{ route('fabric.loom-quality') }}";button.textContent=isNew?'Create quality & link':(isUnlink?'Unlink quality':'Save quality link')}
function calculateLinkFabricWeight(){const read=parseFloat(document.getElementById('linkRead').value)||0;const pick=parseFloat(document.getElementById('linkPick').value)||0;const width=parseFloat(document.getElementById('linkWidth').value)||0;const warpCount=parseFloat(document.getElementById('linkWarpCount').value)||0;const weftCount=parseFloat(document.getElementById('linkWeftCount').value)||0;const truncate4=(value)=>Math.trunc(value*10000)/10000;const warp=read>0&&width>0&&warpCount>0?truncate4(read*width/warpCount/20*1.0936/40):0;const weft=pick>0&&width>0&&weftCount>0?truncate4(pick*width/weftCount/20*1.0936/40):0;document.getElementById('linkWeightPreview').textContent=(warp+weft).toFixed(4)+' lb/m'}
function submitLoomFabricQuality(){const beamId=document.getElementById('linkSizingBeamId').value;const mode=document.getElementById('linkQualityMode').value;if(!beamId){return false}if(mode==='new'){return true}return mode==='unlink'||Boolean(document.getElementById('linkFabricStockId').value)}
</script>
@endsection
