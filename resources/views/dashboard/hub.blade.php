@extends('layouts.app')

@section('title', 'Main Operations Hub')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-zinc-900 text-white p-5 flex flex-wrap items-center justify-between gap-4">
        <div><h2 class="font-bold text-lg">Grey Fabric Factory Operations</h2><p class="text-sm text-zinc-300">Parta → Order → Loom → Checking → Kachi/Pakki</p></div>
        <div class="flex flex-wrap gap-2"><a href="{{ route('factory.orders') }}" class="rounded-lg px-3 py-2 bg-white text-zinc-900 font-semibold text-sm">Orders & Looms</a><a href="{{ route('factory.checking') }}" class="rounded-lg px-3 py-2 bg-zinc-700 text-white font-semibold text-sm">Checking</a><a href="{{ route('factory.parchis') }}" class="rounded-lg px-3 py-2 bg-zinc-700 text-white font-semibold text-sm">Parchis</a></div>
    </div>
    
    <!-- Welcome heading -->
    <div class="rounded-3xl bg-white border border-zinc-200 p-6 sm:p-8 shadow-sm">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 tracking-tight">
            Welcome back, {{ Auth::user()->name }}
        </h1>
    </div>

    <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-zinc-900">Loom & Contract Flow</h2>
                <p class="mt-1 text-sm text-zinc-500">Active loom allocations, checked Thaans, Pakki acceptance and dispatches.</p>
            </div>
            <a href="{{ route('production.loom-summary') }}" class="text-xs font-bold text-zinc-600 hover:text-zinc-900">Open loom summary →</a>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="rounded-2xl bg-zinc-50 p-4">
                <span class="text-[10px] font-bold uppercase tracking-wide text-zinc-500">Running looms</span>
                <strong class="mt-1 block text-2xl font-black text-zinc-900">{{ $activeLoomAssignments->count() }}</strong>
            </div>
            <div class="rounded-2xl bg-zinc-50 p-4">
                <span class="text-[10px] font-bold uppercase tracking-wide text-zinc-500">Active contracts</span>
                <strong class="mt-1 block text-2xl font-black text-zinc-900">{{ $activeContractRows->count() }}</strong>
            </div>
            <div class="rounded-2xl bg-zinc-50 p-4">
                <span class="text-[10px] font-bold uppercase tracking-wide text-zinc-500">Job-work stock balance*</span>
                <strong class="mt-1 block text-2xl font-black text-zinc-900">{{ number_format($jobWorkBalanceMeters, 2) }}m</strong>
            </div>
            <div class="rounded-2xl bg-zinc-50 p-4">
                <span class="text-[10px] font-bold uppercase tracking-wide text-zinc-500">Quality stages</span>
                <strong class="mt-1 block text-sm font-extrabold text-zinc-900">{{ number_format($todayCheckedMeters,2) }} m checked today</strong><span class="text-[10px] text-zinc-500">{{ number_format($totalAcceptedMeters,2) }} m job accepted total</span>
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <div>
                <h3 class="mb-3 text-sm font-extrabold text-zinc-800">Running loom + quality</h3>
                <div class="overflow-x-auto rounded-2xl border border-zinc-200">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 text-[10px] uppercase tracking-wide text-zinc-500">
                            <tr><th class="px-3 py-2.5">Loom</th><th class="px-3 py-2.5">Beam</th><th class="px-3 py-2.5">Fabric quality</th><th class="px-3 py-2.5 text-right">Beam left</th></tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            @forelse($runningLooms as $beam)
                                <tr>
                                    <td class="px-3 py-3 font-bold text-zinc-800">{{ $beam->assigned_loom_no ?: 'Unassigned' }}</td>
                                    <td class="px-3 py-3 text-zinc-600">{{ $beam->beam_no }}</td>
                                    <td class="px-3 py-3 text-zinc-600">{{ $beam->fabricStock?->quality_name ?? 'Quality not linked' }}</td>
                                    <td class="px-3 py-3 text-right tabular-nums text-zinc-700">{{ number_format((float) $beam->remaining_length_meters, 2) }}m</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-6 text-center text-zinc-400">No beams are currently mounted on looms.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h3 class="mb-3 text-sm font-extrabold text-zinc-800">Active contract quantities</h3>
                <div class="overflow-x-auto rounded-2xl border border-zinc-200">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 text-[10px] uppercase tracking-wide text-zinc-500">
                            <tr><th class="px-3 py-2.5">Contract / quality</th><th class="px-3 py-2.5 text-right">Contract</th><th class="px-3 py-2.5 text-right">Produced*</th><th class="px-3 py-2.5 text-right">Dispatched</th><th class="px-3 py-2.5 text-right">Accepted</th><th class="px-3 py-2.5 text-right">Remaining</th></tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            @forelse($activeContractRows as $contract)
                                @php
                                    $dispatchedMeters = (float) ($contractDispatchedMeters[$contract->id] ?? 0);
                                    $remainingMeters = max(0, (float) $contract->total_meters - (float) $contract->accepted_meters);
                                    $producedMeters = $contract->fabric_stock_id ? (float) ($contractProductionMeters[$contract->id] ?? 0) : null;
                                @endphp
                                <tr>
                                    <td class="px-3 py-3"><span class="font-bold text-zinc-800">{{ $contract->contract_no }}</span><span class="block text-zinc-500">{{ $contract->fabricStock?->quality_name ?? $contract->fabric_quality }}</span></td>
                                    <td class="px-3 py-3 text-right tabular-nums text-zinc-700">{{ number_format((float) $contract->total_meters, 0) }}m</td>
                                    <td class="px-3 py-3 text-right tabular-nums text-zinc-700">{{ $producedMeters === null ? '—' : number_format($producedMeters, 0).'m' }}</td>
                                    <td class="px-3 py-3 text-right tabular-nums text-zinc-700">{{ number_format($dispatchedMeters, 0) }}m</td>
                                    <td class="px-3 py-3 text-right tabular-nums text-zinc-700">{{ number_format((float)$contract->accepted_meters, 0) }}m</td><td class="px-3 py-3 text-right tabular-nums font-bold text-zinc-800">{{ number_format($remainingMeters, 0) }}m</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-6 text-center text-zinc-400">No active contracts.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <p class="mt-4 text-[11px] leading-relaxed text-zinc-500">* Provisional loom production and checked stock are separate. New work-order contract production is attributed to the assigned order. Legacy records may not yet have historical order links; reconcile those before relying on old contract totals.</p>
    </section>

    <!-- THE 6 MAIN BOXES -->
    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- BOX 1: BAGS -->
            <a href="{{ route('bags.index') }}" class="glass-card glass-card-hover rounded-3xl p-6 relative group overflow-hidden border border-zinc-200 bg-white">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 border border-zinc-300 text-zinc-600">Module 01</span>
                </div>
                <div class="mt-5">
                    <h3 class="text-2xl font-black text-zinc-900 group-hover:text-zinc-600 transition-colors">Bags</h3>
                    <p class="text-xs text-zinc-400 mt-1 line-clamp-2">
                        Yarn Bag purchases split between Sizing & Factory Store. Daily store issuance, return ledger, and unused bag tracking.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Factory Godown</span>
                        <strong class="text-zinc-900 text-sm font-bold">{{ number_format((float) $factoryBagStock, 2) }} Bags</strong>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">At Sizing Mills</span>
                        <strong class="text-zinc-600 text-sm font-bold">{{ number_format((float) $sizingBagsInHand, 2) }} Bags</strong>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-bold text-zinc-500 group-hover:translate-x-1 transition-transform">
                    <span>Manage Bags & Purchases</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>

            <!-- BOX 2: SIZING -->
            <a href="{{ route('sizing.index') }}" class="glass-card glass-card-hover rounded-3xl p-6 relative group overflow-hidden border border-zinc-200 bg-white">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 border border-zinc-300 text-zinc-600">Module 02</span>
                </div>
                <div class="mt-5">
                    <h3 class="text-2xl font-black text-zinc-900 group-hover:text-zinc-600 transition-colors">Sizing</h3>
                    <p class="text-xs text-zinc-400 mt-1 line-clamp-2">
                        Sizing slip entry, beam generation, and loom-wise beam allocation. Track bags used in beams vs returned to factory or held.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Beams on Looms</span>
                        <strong class="text-zinc-900 text-sm font-bold">{{ $activeBeamsCount }} Active</strong>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Ready in Store</span>
                        <strong class="text-zinc-600 text-sm font-bold">{{ $readyBeamsCount }} Beams</strong>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-bold text-zinc-500 group-hover:translate-x-1 transition-transform">
                    <span>Manage Sizing Slips & Beams</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>

            <!-- BOX 3: PARTS -->
            <a href="{{ route('parts.index') }}" class="glass-card glass-card-hover rounded-3xl p-6 relative group overflow-hidden border border-zinc-200 bg-white">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 border border-zinc-300 text-zinc-600">Module 03</span>
                </div>
                <div class="mt-5">
                    <h3 class="text-2xl font-black text-zinc-900 group-hover:text-zinc-600 transition-colors">Parts</h3>
                    <p class="text-xs text-zinc-400 mt-1 line-clamp-2">
                        Loom parts store inventory (Grippers, rapier tapes, heald wires, drop pins, sensors). Issue parts directly to factory looms.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Total Stock Items</span>
                        <strong class="text-zinc-900 text-sm font-bold">{{ number_format($totalPartsStock) }} Units</strong>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Low Stock Alert</span>
                        <strong class="{{ $lowStockPartsCount > 0 ? 'text-zinc-900' : 'text-zinc-500' }} text-sm font-bold">{{ $lowStockPartsCount }} Items</strong>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-bold text-zinc-500 group-hover:translate-x-1 transition-transform">
                    <span>Manage Parts & Issuance</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>

            <!-- BOX 4: FABRIC STOCK -->
            <a href="{{ route('fabric.index') }}" class="glass-card glass-card-hover rounded-3xl p-6 relative group overflow-hidden border border-zinc-200 bg-white">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 border border-zinc-300 text-zinc-600">Module 04</span>
                </div>
                <div class="mt-5">
                    <h3 class="text-2xl font-black text-zinc-900 group-hover:text-zinc-600 transition-colors">Fabric Stock</h3>
                    <p class="text-xs text-zinc-400 mt-1 line-clamp-2">
                        All fabric varieties produced on looms. Sell openly in spot market or allocate against multi-quality party contracts.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Open Market Ready</span>
                        <strong class="text-zinc-900 text-sm font-bold">{{ number_format($openFabricMeters) }}m</strong>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Committed Contract</span>
                        <strong class="text-zinc-600 text-sm font-bold">{{ number_format($committedFabricMeters) }}m</strong>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-bold text-zinc-500 group-hover:translate-x-1 transition-transform">
                    <span>Manage Fabric Stock & Rolls</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>

            <!-- BOX 5: SALES -->
            <a href="{{ route('sales.index') }}" class="glass-card glass-card-hover rounded-3xl p-6 relative group overflow-hidden border border-zinc-200 bg-white">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 border border-zinc-300 text-zinc-600">Module 05</span>
                </div>
                <div class="mt-5">
                    <h3 class="text-2xl font-black text-zinc-900 group-hover:text-zinc-600 transition-colors">Sales</h3>
                    <p class="text-xs text-zinc-400 mt-1 line-clamp-2">
                        Enter sales slips, open cash sales, and long-term buyer contracts. Auto deduct fabric rolls and update buyer ledgers.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Total Revenue</span>
                        <strong class="text-zinc-900 text-sm font-bold">Rs. {{ number_format($totalSalesAmount, 0) }}</strong>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Active Contracts</span>
                        <strong class="text-zinc-600 text-sm font-bold">{{ $activeContractsCount }} Contracts</strong>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-bold text-zinc-500 group-hover:translate-x-1 transition-transform">
                    <span>Generate Sales Slips</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>

            <!-- BOX 6: PRODUCTION -->
            <a href="{{ route('production.index') }}" class="glass-card glass-card-hover rounded-3xl p-6 relative group overflow-hidden border border-zinc-200 bg-white">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 border border-zinc-300 text-zinc-600">Module 06</span>
                </div>
                <div class="mt-5">
                    <h3 class="text-2xl font-black text-zinc-900 group-hover:text-zinc-600 transition-colors">Production</h3>
                    <p class="text-xs text-zinc-400 mt-1 line-clamp-2">
                        Loom-wise shift reports. Live calculation: how much beam it took, how much fabric it made, and leakage/waste loss.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Total Fabric Made</span>
                        <strong class="text-zinc-900 text-sm font-bold">{{ number_format($totalProductionMeters) }}m</strong>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Beam Leakage Loss</span>
                        <strong class="text-zinc-600 text-sm font-bold">{{ number_format($totalLeakageMeters) }}m Waste</strong>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-bold text-zinc-500 group-hover:translate-x-1 transition-transform">
                    <span>Loom-wise Reports & Leakage</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>

            <!-- BOX 8: EXPENSES -->
            <a href="{{ route('expenses.index') }}" class="glass-card glass-card-hover rounded-3xl p-6 relative group overflow-hidden border border-zinc-200 bg-white">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 border border-zinc-300 text-zinc-600">Module 08</span>
                </div>
                <div class="mt-5">
                    <h3 class="text-2xl font-black text-zinc-900 group-hover:text-zinc-600 transition-colors">Expenses</h3>
                    <p class="text-xs text-zinc-400 mt-1 line-clamp-2">Set up recurring fixed bills and record variable factory costs with cash and bank account tracking.</p>
                </div>
                <div class="mt-6 border-t border-zinc-100 pt-4 text-xs">
                    <span class="text-[10px] uppercase text-zinc-400 font-semibold block">Expenses this month</span>
                    <strong class="text-zinc-900 text-sm font-bold">Rs. {{ number_format((float) $monthExpenseTotal, 2) }}</strong>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-bold text-zinc-500 group-hover:translate-x-1 transition-transform">
                    <span>Manage Fixed & Variable Expenses</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>

        </div>
    </div>

</div>
@endsection
