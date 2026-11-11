@extends('layouts.app')

@section('title', 'Operations Home')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- GREETING & PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#15803d]">OPERATIONS HOME</span>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#133e2b] tracking-tight mt-1">
                @php
                    $hour = (int) date('H');
                    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
                @endphp
                {{ $greeting }}. Here's the factory.
            </h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">
                One glance. The most important work is right here.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('factory.orders') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white border border-[#e2e8e0] text-[#133e2b] hover:bg-stone-50 shadow-2xs transition-all">
                Orders & Looms &rarr;
            </a>
            <a href="{{ route('factory.checking') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-[#133e2b] text-white hover:bg-[#163e2d] shadow-2xs transition-all">
                + Checking Thaan
            </a>
        </div>
    </div>

    <!-- HERO PRIORITY BANNER (DARK FOREST GREEN CARD) -->
    <div class="rounded-3xl bg-[#133e2b] text-white p-6 sm:p-8 shadow-md border border-[#1e563d] relative overflow-hidden">
        <!-- Subtle background organic curve accent -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full border border-emerald-500/10 pointer-events-none"></div>
        <div class="absolute -right-10 -top-10 w-96 h-96 rounded-full border border-emerald-500/10 pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Left Info & CTA -->
            <div class="lg:col-span-7 space-y-4">
                <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-[#c6f16d]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#c6f16d]"></span>
                    Next Priority &middot; Customer Acceptance
                </span>
                
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    @if($pendingPakkiCount > 0)
                        {{ $pendingPakkiCount }} Pakki {{ $pendingPakkiCount == 1 ? 'Parchi needs' : 'Parchis need' }} recording.
                    @else
                        Factory dispatches and Pakki records are up to date.
                    @endif
                </h2>

                <p class="text-xs sm:text-sm text-emerald-100/80 max-w-xl leading-relaxed">
                    Customer fabric has been dispatched. Record accepted metres to calculate the correct weaving bill and close party dispatches.
                </p>

                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#c6f16d] hover:bg-[#b5e759] text-[#133e2b] font-black text-xs shadow-sm transition-all hover:translate-x-0.5">
                        <span>Open Sales &amp; Parchis</span>
                        <span class="text-sm">&nearr;</span>
                    </a>
                    <a href="{{ route('factory.checking') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#194933] hover:bg-[#1a4934] text-emerald-200 font-semibold text-xs border border-[#205c41] transition-all">
                        <span>Check Thaans</span>
                    </a>
                </div>
            </div>

            <!-- Right: Dispatched items awaiting acceptance -->
            <div class="lg:col-span-5 border-t lg:border-t-0 lg:border-l border-[#205c41] pt-5 lg:pt-0 lg:pl-8 space-y-3">
                <div class="text-[10px] uppercase font-bold tracking-wider text-emerald-400/80 mb-2">
                    Recent Dispatches Awaiting Acceptance
                </div>
                
                @forelse($pendingPakkis as $kachi)
                    <div class="py-2.5 border-b border-[#205c41]/70 last:border-0 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-white flex items-center gap-2">
                                <span>{{ $kachi->parchi_no }}</span>
                                <span class="text-[10px] font-normal text-emerald-300">
                                    {{ $kachi->order?->customer_name ?? $kachi->order?->order_no ?? 'Customer Dispatch' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-emerald-400/70 mt-0.5">
                                {{ $kachi->thaan_count }} Thaans &middot; {{ $kachi->order?->fabricStock?->quality_name ?? 'Grey Fabric' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-extrabold text-[#c6f16d] text-sm tabular-nums">
                                {{ number_format((float) $kachi->dispatched_meters, 0) }} m
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-3 text-xs text-emerald-300/70 italic">
                        No pending dispatches awaiting Pakki recording.
                    </div>
                @endforelse

                @if($pendingPakkiCount > 4)
                    <div class="pt-1 text-right">
                        <a href="{{ route('sales.index') }}" class="text-[11px] text-[#c6f16d] font-bold hover:underline">
                            + {{ $pendingPakkiCount - 4 }} more pending dispatches &nearr;
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- 3 KEY STAT KPI CARDS ROW -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        <!-- Stat 1: Looms Running -->
        <div class="card p-6 flex flex-col justify-between shadow-2xs hover:border-stone-300 transition-all">
            <span class="text-xs font-semibold text-stone-500">Looms running</span>
            <div class="mt-3 flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-[#133e2b] tabular-nums">
                    {{ $activeLoomAssignments->count() }}
                </span>
                <span class="text-base font-semibold text-stone-400">/ 24</span>
            </div>
            <span class="text-xs text-stone-500 mt-2 font-medium">
                {{ max(0, 24 - $activeLoomAssignments->count()) }} currently stopped
            </span>
        </div>

        <!-- Stat 2: Checked Fabric -->
        <div class="card p-6 flex flex-col justify-between shadow-2xs hover:border-stone-300 transition-all">
            <span class="text-xs font-semibold text-stone-500">Checked fabric</span>
            <div class="mt-3 flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-[#133e2b] tabular-nums">
                    {{ number_format($jobWorkBalanceMeters > 0 ? $jobWorkBalanceMeters : $todayCheckedMeters, 0) }}
                </span>
                <span class="text-base font-semibold text-stone-500">m</span>
            </div>
            <span class="text-xs text-stone-500 mt-2 font-medium">
                Actual measured stock
            </span>
        </div>

        <!-- Stat 3: Awaiting Pakki -->
        <div class="card p-6 flex flex-col justify-between shadow-2xs hover:border-stone-300 transition-all">
            <span class="text-xs font-semibold text-stone-500">Awaiting Pakki</span>
            <div class="mt-3 flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-[#133e2b] tabular-nums">
                    {{ number_format($pendingPakkiMeters, 0) }}
                </span>
                <span class="text-base font-semibold text-stone-500">m</span>
            </div>
            <span class="text-xs text-stone-500 mt-2 font-medium">
                {{ $pendingPakkiCount }} pending dispatches
            </span>
        </div>

    </div>

    <!-- LOWER DUAL GRID (LOOMS AT A GLANCE + DO THIS NEXT) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Looms at a glance (Col 7) -->
        <div class="lg:col-span-7 card p-6 shadow-2xs">
            <div class="flex items-center justify-between pb-4 border-b border-[#e2e8e0]">
                <h3 class="text-sm font-extrabold text-[#133e2b] tracking-tight">Looms at a glance</h3>
                <a href="{{ route('production.index') }}" class="text-xs font-bold text-stone-600 hover:text-[#133e2b] transition-colors flex items-center gap-1">
                    <span>Full Loom Board</span>
                    <span>&nearr;</span>
                </a>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase tracking-wider text-stone-400 border-b border-stone-100 pb-2">
                            <th class="py-2 pr-3">Loom</th>
                            <th class="py-2 px-3">Quality</th>
                            <th class="py-2 px-3">Order</th>
                            <th class="py-2 pl-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @php
                            $displayLooms = [];
                            // Populate active loom assignments
                            foreach($activeLoomAssignments as $assignment) {
                                $displayLooms[$assignment->loom_no] = [
                                    'loom' => str_pad($assignment->loom_no, 2, '0', STR_PAD_LEFT),
                                    'quality' => $assignment->order?->fabricStock?->quality_name ?? 'Grey Standard',
                                    'order' => $assignment->order?->order_no ?? 'Active',
                                    'status' => 'Running'
                                ];
                            }
                            // Fill sample or stopped looms up to 6 rows if few running
                            for($i = 1; $i <= 6; $i++) {
                                if(!isset($displayLooms[$i])) {
                                    $displayLooms[$i] = [
                                        'loom' => str_pad($i, 2, '0', STR_PAD_LEFT),
                                        'quality' => '&mdash;',
                                        'order' => '&mdash;',
                                        'status' => 'Stopped'
                                    ];
                                }
                            }
                            ksort($displayLooms);
                            $displayLooms = array_slice($displayLooms, 0, 6, true);
                        @endphp

                        @foreach($displayLooms as $loom)
                            <tr class="hover:bg-stone-50/60 transition-colors">
                                <td class="py-3 pr-3 font-black text-stone-800">
                                    {{ $loom['loom'] }}
                                </td>
                                <td class="py-3 px-3 font-medium text-stone-700">
                                    {!! $loom['quality'] !!}
                                </td>
                                <td class="py-3 px-3 font-semibold text-stone-600">
                                    {!! $loom['order'] !!}
                                </td>
                                <td class="py-3 pl-3 text-right">
                                    @if($loom['status'] === 'Running')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#e6f4ea] text-[#137333]">
                                            Running
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#fef3c7] text-[#92400e]">
                                            Stopped
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Do this next (Col 5) -->
        <div class="lg:col-span-5 card p-6 shadow-2xs flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-[#133e2b] tracking-tight pb-4 border-b border-[#e2e8e0]">
                    Do this next
                </h3>

                <div class="divide-y divide-stone-100 mt-2">
                    
                    <!-- Item 1 -->
                    <div class="py-3.5 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold text-stone-900">Enter checked Thaans</div>
                            <div class="text-[11px] text-stone-500 mt-0.5">Measured checking report</div>
                        </div>
                        <a href="{{ route('factory.checking') }}" class="px-3 py-1.5 rounded-xl border border-stone-200 bg-white text-stone-700 hover:bg-stone-50 hover:border-stone-300 text-xs font-bold flex items-center gap-1 shadow-2xs transition-all shrink-0">
                            <span>Enter</span>
                            <span>&nearr;</span>
                        </a>
                    </div>

                    <!-- Item 2 -->
                    <div class="py-3.5 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold text-stone-900">Receive yarn or bags</div>
                            <div class="text-[11px] text-stone-500 mt-0.5">Factory or customer-owned material</div>
                        </div>
                        <a href="{{ route('bags.index') }}" class="px-3 py-1.5 rounded-xl border border-stone-200 bg-white text-stone-700 hover:bg-stone-50 hover:border-stone-300 text-xs font-bold flex items-center gap-1 shadow-2xs transition-all shrink-0">
                            <span>Enter</span>
                            <span>&nearr;</span>
                        </a>
                    </div>

                    <!-- Item 3 -->
                    <div class="py-3.5 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold text-stone-900">Review active contracts</div>
                            <div class="text-[11px] text-stone-500 mt-0.5">Quality, Parta, looms and acceptance</div>
                        </div>
                        <a href="{{ route('factory.orders') }}" class="px-3 py-1.5 rounded-xl border border-stone-200 bg-white text-stone-700 hover:bg-stone-50 hover:border-stone-300 text-xs font-bold flex items-center gap-1 shadow-2xs transition-all shrink-0">
                            <span>Review</span>
                            <span>&nearr;</span>
                        </a>
                    </div>

                    <!-- Item 4 -->
                    <div class="py-3.5 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold text-stone-900">Record loom production</div>
                            <div class="text-[11px] text-stone-500 mt-0.5">Shift meter readings & beam waste</div>
                        </div>
                        <a href="{{ route('production.index') }}" class="px-3 py-1.5 rounded-xl border border-stone-200 bg-white text-stone-700 hover:bg-stone-50 hover:border-stone-300 text-xs font-bold flex items-center gap-1 shadow-2xs transition-all shrink-0">
                            <span>Record</span>
                            <span>&nearr;</span>
                        </a>
                    </div>

                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500">
                <span>Single Operator Mode</span>
                <span class="font-bold text-[#133e2b]">LoomFlow 100% Weaving ERP</span>
            </div>
        </div>

    </div>

    <!-- CONTRACT PRODUCTION FLOW SECTION -->
    <section class="card p-6 sm:p-8 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-5 border-b border-[#e2e8e0]">
            <div>
                <h2 class="text-base sm:text-lg font-black text-[#133e2b] tracking-tight">Active Contracts & Delivery Status</h2>
                <p class="text-xs text-stone-500 mt-0.5">Contract meters, factory production, dispatches, and accepted totals.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="text-xs font-bold text-[#133e2b] hover:underline flex items-center gap-1">
                <span>View all contracts</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-stone-50 text-[10px] font-bold uppercase tracking-wider text-stone-500 border-b border-stone-200">
                        <th class="px-4 py-3">Contract / Quality</th>
                        <th class="px-4 py-3 text-right">Target</th>
                        <th class="px-4 py-3 text-right">Produced*</th>
                        <th class="px-4 py-3 text-right">Dispatched</th>
                        <th class="px-4 py-3 text-right">Accepted</th>
                        <th class="px-4 py-3 text-right">Remaining</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($activeContractRows as $contract)
                        @php
                            $dispatchedMeters = (float) ($contractDispatchedMeters[$contract->id] ?? 0);
                            $remainingMeters = max(0, (float) $contract->total_meters - (float) $contract->accepted_meters);
                            $producedMeters = $contract->fabric_stock_id ? (float) ($contractProductionMeters[$contract->id] ?? 0) : null;
                        @endphp
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-stone-900 block">{{ $contract->contract_no }}</span>
                                <span class="text-stone-500 text-[11px]">{{ $contract->fabricStock?->quality_name ?? $contract->fabric_quality }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-right tabular-nums text-stone-700 font-semibold">
                                {{ number_format((float) $contract->total_meters, 0) }}m
                            </td>
                            <td class="px-4 py-3.5 text-right tabular-nums text-stone-700">
                                {{ $producedMeters === null ? '—' : number_format($producedMeters, 0).'m' }}
                            </td>
                            <td class="px-4 py-3.5 text-right tabular-nums text-stone-700 font-medium">
                                {{ number_format($dispatchedMeters, 0) }}m
                            </td>
                            <td class="px-4 py-3.5 text-right tabular-nums text-stone-700 font-medium">
                                {{ number_format((float)$contract->accepted_meters, 0) }}m
                            </td>
                            <td class="px-4 py-3.5 text-right tabular-nums font-black text-[#133e2b]">
                                {{ number_format($remainingMeters, 0) }}m
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-stone-400 italic">
                                No active customer contracts found. Create one in Sales or Orders.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- CORE ERP MODULES DIRECT ACCESS (ELEGANT WHITE CARDS) -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-stone-600">All Factory Modules</h3>
                <p class="text-xs text-stone-400">Directly jump to any mill station</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            <!-- MODULE 1: BAGS -->
            <a href="{{ route('bags.index') }}" class="card p-6 card-hover group relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#133e2b] flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100/70 text-[#133e2b]">Sutar</span>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-lg font-black text-stone-900 group-hover:text-[#133e2b] transition-colors">Yarn Bags</h4>
                        <p class="text-xs text-stone-500 mt-1 line-clamp-2">Godown stock, Sizing usage, and unused bag returns.</p>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between text-xs">
                    <span class="text-stone-500">Stock in Godown:</span>
                    <strong class="text-stone-900 font-bold">{{ number_format((float) $factoryBagStock, 1) }} Bags</strong>
                </div>
            </a>

            <!-- MODULE 2: SIZING -->
            <a href="{{ route('sizing.index') }}" class="card p-6 card-hover group relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#133e2b] flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100/70 text-[#133e2b]">Beams</span>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-lg font-black text-stone-900 group-hover:text-[#133e2b] transition-colors">Sizing & Beams</h4>
                        <p class="text-xs text-stone-500 mt-1 line-clamp-2">Sizing slips, beam generation & loom-wise beam mounting.</p>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between text-xs">
                    <span class="text-stone-500">Active Beams:</span>
                    <strong class="text-stone-900 font-bold">{{ $activeBeamsCount }} Mounted</strong>
                </div>
            </a>

            <!-- MODULE 3: PRODUCTION -->
            <a href="{{ route('production.index') }}" class="card p-6 card-hover group relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#133e2b] flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100/70 text-[#133e2b]">Looms</span>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-lg font-black text-stone-900 group-hover:text-[#133e2b] transition-colors">Production Board</h4>
                        <p class="text-xs text-stone-500 mt-1 line-clamp-2">Loom-wise shift meters, efficiency and beam leakage analysis.</p>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between text-xs">
                    <span class="text-stone-500">Total Produced:</span>
                    <strong class="text-stone-900 font-bold">{{ number_format($totalProductionMeters, 0) }}m</strong>
                </div>
            </a>

            <!-- MODULE 4: FABRIC STOCK -->
            <a href="{{ route('fabric.index') }}" class="card p-6 card-hover group relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#133e2b] flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100/70 text-[#133e2b]">Stock</span>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-lg font-black text-stone-900 group-hover:text-[#133e2b] transition-colors">Fabric Stock</h4>
                        <p class="text-xs text-stone-500 mt-1 line-clamp-2">Open sale stock vs committed contract stock across all qualities.</p>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between text-xs">
                    <span class="text-stone-500">Open Ready:</span>
                    <strong class="text-stone-900 font-bold">{{ number_format($openFabricMeters, 0) }}m</strong>
                </div>
            </a>

            <!-- MODULE 5: INVOICES & KHATA -->
            <a href="{{ route('invoices.index') }}" class="card p-6 card-hover group relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#133e2b] flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100/70 text-[#133e2b]">Khata</span>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-lg font-black text-stone-900 group-hover:text-[#133e2b] transition-colors">Khata & Invoices</h4>
                        <p class="text-xs text-stone-500 mt-1 line-clamp-2">Customer bills, payments, bank receipts & ledger postings.</p>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between text-xs">
                    <span class="text-stone-500">Revenue Recorded:</span>
                    <strong class="text-stone-900 font-bold">Rs. {{ number_format($totalSalesAmount, 0) }}</strong>
                </div>
            </a>

            <!-- MODULE 6: TOOLS & PARTS -->
            <a href="{{ route('parts.index') }}" class="card p-6 card-hover group relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#133e2b] flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100/70 text-[#133e2b]">Tools</span>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-lg font-black text-stone-900 group-hover:text-[#133e2b] transition-colors">Factory Tools & Parts</h4>
                        <p class="text-xs text-stone-500 mt-1 line-clamp-2">Grippers, rapier tapes, sensors, and loom maintenance issuance.</p>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between text-xs">
                    <span class="text-stone-500">Items in Store:</span>
                    <strong class="text-stone-900 font-bold">{{ number_format($totalPartsStock) }} Units</strong>
                </div>
            </a>

        </div>
    </div>

</div>
@endsection
