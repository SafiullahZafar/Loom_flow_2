@extends('layouts.app')

@section('title', 'Sizing Slips & Beam Management')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[250px_minmax(0,1fr)] gap-6 items-start">
    <aside class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm lg:sticky lg:top-24">

        {{-- Dashboard main link --}}
        <a href="{{ route('sizing.index') }}"
           class="mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 transition-all
                  {{ $page === 'dashboard'
                       ? 'bg-zinc-900 text-zinc-50 shadow-md'
                       : 'bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 hover:text-zinc-900' }}">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                         {{ $page === 'dashboard' ? 'bg-zinc-700' : 'bg-zinc-200' }}">
                <svg class="h-5 w-5 {{ $page === 'dashboard' ? 'text-zinc-100' : 'text-zinc-600' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-extrabold leading-tight">Sizing Dashboard</span>
                <span class="block text-[10px] text-zinc-400 mt-0.5">Overview & beam stats</span>
            </div>
        </a>

        <div class="mb-2 px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sections</div>

        <nav class="space-y-1.5" aria-label="Sizing sections">

            {{-- Beams Tracking --}}
            <a href="{{ route('sizing.beams') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'beams'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'beams' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'beams' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Beams Tracking</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Loom allocation & status</span>
                </div>
                @if($page === 'beams')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Sizing Slips --}}
            <a href="{{ route('sizing.slips') }}"
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
                    <span class="block leading-tight">Sizing Slips</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Bag usage & returns log</span>
                </div>
                @if($page === 'slips')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Sizing Balance --}}
            <a href="{{ route('sizing.balance') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all {{ $page === 'balance' ? 'bg-zinc-900 text-zinc-50 shadow-sm' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $page === 'balance' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'balance' ? 'text-zinc-100' : 'text-zinc-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a2 2 0 01.707.293l5.414 5.414A2 2 0 0119 9v10a2 2 0 01-2 2z"/></svg>
                </span>
                <div><span class="block leading-tight">Sizing Balance</span><span class="block text-[10px] font-normal text-zinc-400">Bags, weight & loom check</span></div>
                @if($page === 'balance')<span class="ml-auto h-1.5 w-1.5 shrink-0 rounded-full bg-zinc-400"></span>@endif
            </a>

        </nav>

        {{-- Quick actions --}}
        <div class="mt-5 pt-4 border-t border-zinc-100 space-y-1.5">
            <div class="px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Quick Actions</div>
            <button type="button" onclick="openModal('modalNewSizingSlip')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Sizing Slip
            </button>
            <a href="{{ route('bags.index') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Bags Module
            </a>
            <a href="{{ route('production.index') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Production Module
            </a>
        </div>
    </aside>

    <div class="min-w-0 space-y-6">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-zinc-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Module 02</span>
            </div>
            <h1 class="text-2xl font-black text-zinc-900 tracking-tight">{{ ['dashboard' => 'Sizing Dashboard', 'beams' => 'Beams Tracking', 'slips' => 'Sizing Slips', 'balance' => 'Sizing Balance'][$page] }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="openModal('modalNewSizingSlip')" class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 font-extrabold text-xs shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ New Sizing Slip Entry</span>
            </button>
            <a href="{{ route('bags.index') }}" class="px-3.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border border-zinc-300 text-xs font-bold transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Bags History</span>
            </a>
        </div>
    </div>

    <!-- KPIs -->
    @if ($page === 'dashboard')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="glass-card rounded-2xl p-4 border border-zinc-300 bg-zinc-50">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700 flex items-center justify-between">
                <span>Active Beams on Looms</span><span class="w-2 h-2 rounded-full bg-zinc-700"></span>
            </div>
            <div class="text-2xl font-black text-zinc-900 mt-1">{{ $mountedBeamsCount }} <span class="text-xs font-normal text-zinc-400">mounted</span></div>
            <div class="text-[11px] text-zinc-500 mt-1 font-mono">{{ number_format($remainingBeamMetersOnLooms) }}m yarn left</div>
        </div>
        <div class="glass-card rounded-2xl p-4 border border-zinc-300 bg-zinc-50">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700 flex items-center justify-between">
                <span>Beams Ready in Store</span><span class="w-2 h-2 rounded-full bg-zinc-600"></span>
            </div>
            <div class="text-2xl font-black text-zinc-900 mt-1">{{ $readyBeamsCount }} <span class="text-xs font-normal text-zinc-400">ready</span></div>
            <div class="text-[11px] text-zinc-500 mt-1">Available for mounting</div>
        </div>
        <div class="glass-card rounded-2xl p-4 border border-zinc-200">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                <span>Total Beams Produced</span><span class="w-2 h-2 rounded-full bg-zinc-400"></span>
            </div>
            <div class="text-2xl font-black text-zinc-900 mt-1">{{ $totalBeamsCount }} <span class="text-xs font-normal text-zinc-400">beams</span></div>
            <div class="text-[11px] text-zinc-400 mt-1 font-mono">{{ number_format($totalBeamMetersProduced) }} total meters</div>
        </div>
        <div class="glass-card rounded-2xl p-4 border border-zinc-300 bg-zinc-50">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700 flex items-center justify-between">
                <span>Bags Consumed in Beams</span><span class="w-2 h-2 rounded-full bg-zinc-600"></span>
            </div>
            <div class="text-2xl font-black text-zinc-900 mt-1">{{ number_format((float) $totalUsedBags, 2) }} <span class="text-xs font-normal text-zinc-400">bags</span></div>
            <div class="text-[11px] text-zinc-500 mt-1">Out of {{ number_format((float) $totalAllocatedBags, 2) }} allocated</div>
        </div>
        <div class="glass-card rounded-2xl p-4 border border-zinc-300 bg-zinc-50">
            <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-600 flex items-center justify-between">
                <span>Unused Bags Status</span><span class="w-2 h-2 rounded-full bg-zinc-500"></span>
            </div>
            <div class="text-xs space-y-1 mt-1 font-bold">
                <div class="flex justify-between"><span class="text-zinc-500">Returned to Factory:</span> <span class="text-zinc-800 font-mono">{{ number_format((float) $totalReturnedBags, 2) }} bags</span></div>
                <div class="flex justify-between"><span class="text-zinc-500">Held With Sizing:</span> <span class="text-zinc-800 font-mono">{{ number_format((float) $totalHeldWithSizing, 2) }} bags</span></div>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================================
         SECTION 1 — Beams Tracking
    ============================================================ --}}
    @if ($page === 'balance')
    <section class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
            <h2 class="text-sm font-extrabold text-zinc-900">Sizing Reconciliation</h2>
            <p class="mt-1 max-w-4xl text-xs leading-5 text-zinc-500">Expected cloth counts beams mounted or completed on looms. Beams still ready in store are unused and do not add to the used-beam production balance.</p>
            <div class="mt-3 grid gap-2 rounded-xl border border-zinc-200 bg-white p-3 text-[11px] leading-5 text-zinc-600 md:grid-cols-2">
                <p><strong class="text-zinc-800">Estimated yarn count (Ne):</strong> ends × set length in metres ÷ (1693.6 × net yarn weight in kg). The set length is used because it represents the warp yarn length; panna changes expected fabric output, not yarn count.</p>
                <p><strong class="text-zinc-800">Net yarn per woven metre:</strong> net sizing weight in lb ÷ actual woven metres recorded in Production. This is a weight ratio, not the yarn count.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-left text-xs">
                <thead><tr class="border-b border-zinc-200 bg-white text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                    <th class="px-4 py-3">Sizing Slip</th><th class="px-4 py-3">Yarn / Party</th><th class="px-4 py-3 text-right">Bags Sent</th><th class="px-4 py-3 text-right">Used / Returned / Held</th><th class="px-4 py-3">Bag Balance</th><th class="px-4 py-3 text-right">Gross / Net Weight</th><th class="px-4 py-3 text-right">Expected Cloth</th><th class="px-4 py-3 text-right">Woven / Expected on Consumed Warp</th><th class="px-4 py-3 text-right">Recorded Loom Leakage</th><th class="px-4 py-3">Count / Yarn per Woven Metre</th><th class="px-4 py-3">Beam Progress</th>
                </tr></thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($slips as $s)
                        @php
                            $bagVariance = (int) round(((float) $s->bags_allocated - (float) $s->bags_used - (float) $s->bags_returned_to_factory - (float) $s->bags_held_with_sizing) * 100);
                            $grossKg = $s->sizing_gross_weight_pounds !== null ? (float) $s->sizing_gross_weight_pounds * 0.45359237 : null;
                            $netKg = $s->sizing_weight_pounds !== null ? (float) $s->sizing_weight_pounds * 0.45359237 : null;
                            $usedBeams = $s->beams->filter(fn ($beam) => in_array($beam->status, ['mounted_on_loom', 'exhausted'], true) || (int) ($beam->productions_count ?? 0) > 0);
                            $usedBeamCount = $usedBeams->count();
                            $unusedBeamCount = max(0, (int) $s->total_beams - $usedBeamCount);
                            $expectedMeters = (float) $usedBeams->sum(fn ($beam) => (float) $beam->total_length_meters * (float) $s->width_multiplier);
                            $wovenMeters = (float) $usedBeams->sum(fn ($beam) => (float) ($beam->productions_sum_net_meters_produced ?? 0));
                            $expectedConsumedMeters = (float) $usedBeams->sum(fn ($beam) => (float) ($beam->productions_sum_expected_fabric_meters ?? 0));
                            $leakageMeters = (float) $usedBeams->sum(fn ($beam) => (float) ($beam->productions_sum_leakage_waste_meters ?? 0));
                            $estimatedCount = $s->estimatedWarpCountNe();
                            $declaredCount = $s->declaredYarnCountNe();
                            $netWeightPounds = $s->sizing_weight_pounds !== null ? (float) $s->sizing_weight_pounds : null;
                            $weightPerWovenMeter = $netWeightPounds !== null && $wovenMeters > 0 ? $netWeightPounds / $wovenMeters : null;
                            $finishedBeams = $usedBeams->where('status', 'exhausted')->count();
                            $allBeamsFinished = $usedBeams->isNotEmpty() && $finishedBeams === $usedBeamCount;
                            $remainingExpectedMeters = (float) $usedBeams->sum(fn ($beam) => (float) $beam->remaining_length_meters * (float) $s->width_multiplier);
                        @endphp
                        <tr class="align-top hover:bg-zinc-50">
                            <td class="px-4 py-4"><span class="font-mono font-bold text-zinc-900">{{ $s->slip_no }}</span><span class="mt-1 block text-[10px] text-zinc-400">{{ $s->slip_date->format('d M, Y') }}</span><span class="block text-[10px] text-zinc-500">{{ $s->sizing_party }}</span></td>
                            <td class="px-4 py-4"><span class="font-semibold text-zinc-700">{{ $s->yarn_quality }}</span><span class="mt-1 block text-[10px] text-zinc-400">{{ number_format($s->total_ends ?? 0) }} ends · {{ number_format($s->width_multiplier, 2) }} panna</span></td>
                            <td class="px-4 py-4 text-right font-mono font-bold text-zinc-900">{{ number_format((float) $s->bags_allocated, 2) }}</td>
                            <td class="px-4 py-4 text-right font-mono text-zinc-700">{{ number_format((float) $s->bags_used, 2) }} / {{ number_format((float) $s->bags_returned_to_factory, 2) }} / {{ number_format((float) $s->bags_held_with_sizing, 2) }}</td>
                            <td class="px-4 py-4">
                                @if($bagVariance === 0)<span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700">Balanced</span>
                                @elseif($bagVariance > 0)<span class="rounded-full bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-800">{{ number_format($bagVariance / 100, 2) }} unaccounted</span>
                                @else<span class="rounded-full bg-red-50 px-2 py-1 text-[10px] font-bold text-red-700">{{ number_format(abs($bagVariance) / 100, 2) }} over-reported</span>@endif
                            </td>
                            <td class="px-4 py-4 text-right font-mono text-zinc-700">{{ $grossKg !== null ? number_format($grossKg, 2).' kg' : '—' }}<span class="block text-[10px] text-zinc-400">net {{ $netKg !== null ? number_format($netKg, 2).' kg' : 'not entered' }}</span>@if($grossKg !== null && $netKg !== null)<span class="block text-[10px] text-zinc-500">{{ number_format($grossKg - $netKg, 2) }} kg deduction</span>@endif</td>
                            <td class="px-4 py-4 text-right font-mono font-semibold text-zinc-800">{{ number_format($expectedMeters, 2) }}m<span class="block text-[10px] font-normal text-zinc-400">{{ $usedBeamCount }} / {{ $s->total_beams }} beams used × {{ number_format($s->width_multiplier, 2) }} factor</span></td>
                            <td class="px-4 py-4 text-right font-mono text-zinc-700">{{ number_format($wovenMeters, 2) }}m / {{ number_format($expectedConsumedMeters, 2) }}m<span class="block text-[10px] text-zinc-400">actual / expected for warp used</span></td>
                            <td class="px-4 py-4 text-right font-mono font-bold text-amber-700">{{ number_format($leakageMeters, 2) }}m<span class="block text-[10px] font-normal text-zinc-400">from production records</span></td>
                            <td class="px-4 py-4 text-right font-mono text-zinc-700">
                                {{ $estimatedCount !== null ? number_format($estimatedCount, 2).' Ne' : '—' }}
                                <span class="block text-[10px] font-normal text-zinc-400">{{ $declaredCount !== null && $estimatedCount !== null ? 'declared '.$declaredCount.' Ne · difference '.number_format($estimatedCount - $declaredCount, 2) : 'estimate from ends, set length & net kg' }}</span>
                                <span class="mt-1 block font-bold text-zinc-800">{{ $weightPerWovenMeter !== null ? number_format($weightPerWovenMeter, 6).' lb/m' : '— lb/m' }}</span>
                                <span class="block text-[10px] font-normal text-zinc-400">net lb ÷ actual woven m</span>
                            </td>
                            <td class="px-4 py-4"><span class="font-semibold text-zinc-800">{{ $usedBeamCount }} / {{ $s->total_beams }} beams used · {{ $finishedBeams }} finished</span><span class="mt-1 block text-[10px] font-bold {{ $allBeamsFinished ? 'text-emerald-700' : 'text-amber-700' }}">{{ $allBeamsFinished ? 'Used-beam balance final' : 'Used-beam balance provisional' }}</span><span class="block text-[10px] text-zinc-400">{{ $unusedBeamCount }} unused in store · {{ number_format($remainingExpectedMeters, 2) }}m capacity left on used beams</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="px-6 py-12 text-center text-sm text-zinc-400">No sizing slips yet. Once a sizing slip is entered, its bag, weight, beam, and production balance will appear here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-100 px-6 py-4">{{ $slips->links() }}</div>
        <div class="border-t border-zinc-100 bg-zinc-50 px-6 py-3 text-[10px] leading-5 text-zinc-500">A bag or weight mismatch is a reconciliation flag that needs checking against the signed gate pass and sizing bill. It does not by itself establish theft or fraud. Loom leakage shown here is the sum saved on production entries; remaining warp is listed separately.</div>
    </section>
    @endif

    @if($page === 'dashboard' && $pendingSizingTransactions->isNotEmpty())
    <section class="overflow-hidden rounded-3xl border border-amber-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-amber-100 bg-amber-50 px-6 py-4">
            <div>
                <h2 class="text-sm font-extrabold text-zinc-900">Bags Waiting for Sizing Slips</h2>
                <p class="mt-0.5 text-[10px] text-zinc-500">Choose an entry to start a slip with the sent quantity, party, quality, and date filled in.</p>
            </div>
            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-zinc-700">{{ $pendingSizingTransactions->count() }} pending</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead><tr class="bg-zinc-50 text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                    <th class="px-4 py-3">Reference / Date</th><th class="px-4 py-3">Sizing Party</th><th class="px-4 py-3">Quality</th><th class="px-4 py-3 text-right">Bags Sent</th><th class="px-4 py-3 text-right">Weight</th><th class="px-4 py-3 text-right">Action</th>
                </tr></thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach($pendingSizingTransactions as $transaction)
                        @php
                            $sizingParty = $transaction->destination ?: ($transaction->party_name ?: $transaction->source);
                            $sizingSlipNo = 'SZ-'.$transaction->reference_no;
                        @endphp
                        <tr>
                            <td class="px-4 py-3"><span class="font-mono font-bold text-zinc-900">{{ $transaction->reference_no ?: 'Bag entry #'.$transaction->id }}</span><span class="mt-0.5 block text-[10px] text-zinc-400">{{ $transaction->transaction_date->format('d M, Y') }}</span></td>
                            <td class="px-4 py-3 font-semibold text-zinc-700">{{ $sizingParty }}</td>
                            <td class="px-4 py-3 text-zinc-600">{{ $transaction->quality }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-zinc-900">{{ number_format((float) $transaction->quantity_bags, 2) }}</td>
                            <td class="px-4 py-3 text-right font-mono text-zinc-600">{{ number_format($transaction->quantity_bags * $kilogramsPerBag, 2) }} kg<span class="block text-[10px] text-zinc-400">{{ number_format($transaction->quantity_bags * 100) }} lb</span></td>
                            <td class="px-4 py-3 text-right"><button type="button" onclick="openSizingSlipFromBag(this)" data-id="{{ $transaction->id }}" data-reference="{{ $transaction->reference_no }}" data-slip="{{ $sizingSlipNo }}" data-party="{{ $sizingParty }}" data-quality="{{ $transaction->quality }}" data-quantity="{{ $transaction->quantity_bags }}" data-date="{{ $transaction->transaction_date->format('Y-m-d') }}" class="rounded-lg bg-zinc-900 px-3 py-2 text-[11px] font-bold text-white hover:bg-zinc-700">Enter sizing slip</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    @if (in_array($page, ['dashboard', 'beams'], true))
    <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                    <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">
                        {{ $page === 'dashboard' ? 'Recent Beams' : 'Beams Tracking & Loom Allocation' }}
                    </h2>
                    <p class="text-[10px] text-zinc-400 mt-0.5">Active, ready, and exhausted sizing beams with loom assignments</p>
                </div>
            </div>
            @if($page === 'dashboard')
                <a href="{{ route('sizing.beams') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1500px] whitespace-nowrap text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] tracking-wider bg-zinc-50">
                        <th class="px-4 py-3">Beam #</th>
                        <th class="px-4 py-3">Beam Name</th>
                        <th class="px-4 py-3">Sizing Slip Ref</th>
                        <th class="px-4 py-3">Yarn Quality</th>
                        <th class="px-4 py-3 text-right">Warp Length</th>
                        <th class="px-4 py-3 text-right">Expected Fabric</th>
                        <th class="px-4 py-3 text-right">Actual Woven</th>
                        <th class="px-4 py-3 text-right">Short / Over on Used Warp</th>
                        <th class="px-4 py-3 text-right">Remaining Length</th>
                        <th class="px-4 py-3 text-center">Assigned Loom</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($beams as $bm)
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="px-4 py-3 font-mono font-black text-zinc-900 text-sm">{{ $bm->beam_no }}</td>
                            <td class="px-4 py-3 font-semibold text-zinc-800">{{ $bm->beam_name ?: '—' }}</td>
                            <td class="px-4 py-3 font-mono text-zinc-500">{{ $bm->sizingSlip ? $bm->sizingSlip->slip_no : 'Manual' }}</td>
                            <td class="px-4 py-3 font-semibold text-zinc-700">{{ $bm->yarn_quality }}</td>
                            <td class="px-4 py-3 text-right font-mono text-zinc-500">{{ number_format($bm->total_length_meters, 0) }}m</td>
                            @php
                                $actualWovenMeters = (float) ($bm->productions_sum_net_meters_produced ?? 0);
                                $beamWidthMultiplier = (float) ($bm->sizingSlip?->width_multiplier ?? 1);
                                $expectedFabricMeters = (float) $bm->total_length_meters * $beamWidthMultiplier;
                                $remainingExpectedFabricMeters = (float) $bm->remaining_length_meters * $beamWidthMultiplier;
                                $expectedConsumedMeters = (float) ($bm->productions_sum_expected_fabric_meters ?? 0);
                                $productionVarianceMeters = $expectedConsumedMeters - $actualWovenMeters;
                            @endphp
                            <td class="px-4 py-3 text-right font-mono text-zinc-600">{{ number_format($expectedFabricMeters, 2) }}m<span class="block text-[9px] text-zinc-400">remaining warp can make {{ number_format($remainingExpectedFabricMeters, 2) }}m</span></td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-zinc-700">{{ number_format($actualWovenMeters, 2) }}m</td>
                            <td class="px-4 py-3 text-right font-mono {{ $productionVarianceMeters >= 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                                @if(($bm->productions_count ?? 0) > 0)
                                    {{ number_format(abs($productionVarianceMeters), 2) }}m {{ $productionVarianceMeters >= 0 ? 'short' : 'over' }}
                                    <span class="block text-[9px] text-zinc-400">against {{ number_format($expectedConsumedMeters, 2) }}m expected on consumed warp</span>
                                @else
                                    <span class="text-zinc-400">No production yet</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold {{ $bm->remaining_length_meters < 500 ? 'text-zinc-400' : 'text-zinc-900' }}">
                                {{ number_format($bm->remaining_length_meters, 0) }}m
                                @if($bm->remaining_length_meters < 500 && $bm->status !== 'exhausted')
                                    <span class="block text-[9px] font-bold text-zinc-400 uppercase">Low</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($bm->assigned_loom_no)
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-black bg-zinc-900 text-zinc-50">{{ $bm->assigned_loom_no }}</span>
                                @else
                                    <span class="text-zinc-400 italic text-[11px]">Not Mounted</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($bm->status === 'mounted_on_loom')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-900 text-zinc-50 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-300 animate-pulse"></span> Running
                                    </span>
                                @elseif($bm->status === 'ready_in_store')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-700 border border-zinc-300">Ready in Store</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-50 text-zinc-400 border border-zinc-200">Exhausted</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($bm->sizingSlip)
                                        <button type="button" onclick="openModal('modalBeamDetails{{ $bm->id }}')"
                                                class="px-2.5 py-1 rounded-lg border border-zinc-300 bg-white text-zinc-700 hover:bg-zinc-100 text-[11px] font-bold transition-colors">
                                            View
                                        </button>
                                    @endif
                                    @if($bm->status !== 'exhausted')
                                        <button type="button"
                                                onclick="openMountModal(this)"
                                                data-id="{{ $bm->id }}"
                                                data-beam-no="{{ $bm->beam_no }}"
                                                data-beam-name="{{ $bm->beam_name }}"
                                                data-loom="{{ $bm->assigned_loom_no }}"
                                                data-quality="{{ $bm->yarn_quality }}"
                                                data-warp-meters="{{ number_format((float) $bm->total_length_meters, 2, '.', '') }}"
                                                data-remaining-meters="{{ number_format((float) $bm->remaining_length_meters, 2, '.', '') }}"
                                                data-width-factor="{{ number_format((float) ($bm->sizingSlip?->width_multiplier ?? 1), 3, '.', '') }}"
                                                class="px-2.5 py-1 rounded-lg bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-[11px] font-bold transition-colors whitespace-nowrap">
                                            {{ $bm->assigned_loom_no ? 'Change Loom' : 'Mount on Loom' }}
                                        </button>
                                        <form action="{{ route('sizing.beam.exhaust', $bm->id) }}" method="POST"
                                              onsubmit="return confirm('Mark Beam {{ $bm->beam_no }} as finished?')">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-500 hover:text-zinc-800 text-[11px] font-bold transition-colors">
                                                Done
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-zinc-300 italic">Finished</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="px-4 py-10 text-center text-zinc-400">No beams generated yet. Create a Sizing Slip to generate beams.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @foreach($beams as $beamRecord)
            @if($beamRecord->sizingSlip)
                @php
                    $slipDetails = $beamRecord->sizingSlip;
                    $expectedSlipMeters = $slipDetails->expectedFabricMeters();
                    $expectedPerBeamMeters = $slipDetails->expectedFabricMetersPerBeam();
                @endphp
                <div id="modalBeamDetails{{ $beamRecord->id }}" class="fixed inset-0 z-[60] hidden items-center justify-center overflow-y-auto bg-zinc-950/60 p-4 backdrop-blur-sm">
                    <div class="my-auto w-full max-w-3xl rounded-3xl border border-zinc-200 bg-white p-5 shadow-2xl sm:p-6">
                        <div class="flex items-start justify-between gap-4 border-b border-zinc-100 pb-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sizing Slip Details</p>
                                <h2 class="mt-1 text-xl font-black text-zinc-900">{{ $slipDetails->slip_no }}</h2>
                                <p class="mt-1 text-xs text-zinc-500">{{ $slipDetails->sizing_party }} · {{ $slipDetails->slip_date?->format('d M, Y') }}</p>
                            </div>
                            <button type="button" onclick="closeModal('modalBeamDetails{{ $beamRecord->id }}')" class="rounded-lg p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-900" aria-label="Close beam details">✕</button>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="rounded-xl bg-zinc-50 p-3"><span class="block text-[10px] font-bold uppercase text-zinc-400">Beams from slip</span><strong class="mt-1 block font-mono text-lg text-zinc-900">{{ $slipDetails->total_beams }}</strong></div>
                            <div class="rounded-xl bg-zinc-50 p-3"><span class="block text-[10px] font-bold uppercase text-zinc-400">Yarn quality</span><strong class="mt-1 block text-sm text-zinc-900">{{ $slipDetails->yarn_quality }}</strong></div>
                            <div class="rounded-xl bg-zinc-50 p-3"><span class="block text-[10px] font-bold uppercase text-zinc-400">Expected cloth total</span><strong class="mt-1 block font-mono text-lg text-zinc-900">{{ number_format($expectedSlipMeters, 2) }}m</strong></div>
                            <div class="rounded-xl bg-zinc-50 p-3"><span class="block text-[10px] font-bold uppercase text-zinc-400">Expected cloth / beam</span><strong class="mt-1 block font-mono text-lg text-zinc-900">{{ number_format($expectedPerBeamMeters, 2) }}m</strong></div>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="rounded-xl border border-zinc-100 p-3"><span class="block text-[10px] font-bold uppercase text-zinc-400">Set length / width factor</span><strong class="mt-1 block text-sm text-zinc-800">{{ number_format($slipDetails->set_length_value ?? $slipDetails->total_length_meters, 2) }} {{ $slipDetails->set_length_unit === 'gaz' ? 'gaz' : 'meters' }} × {{ number_format($slipDetails->width_multiplier, 2) }}</strong></div>
                            <div class="rounded-xl border border-zinc-100 p-3"><span class="block text-[10px] font-bold uppercase text-zinc-400">Raw / finished beams</span><strong class="mt-1 block text-sm text-zinc-800">{{ $slipDetails->back_beam_count ?? '—' }} / {{ $slipDetails->total_beams }}</strong></div>
                            <div class="rounded-xl border border-zinc-100 p-3"><span class="block text-[10px] font-bold uppercase text-zinc-400">Bags allocated / used</span><strong class="mt-1 block font-mono text-sm text-zinc-800">{{ number_format((float) $slipDetails->bags_allocated, 2) }} / {{ number_format((float) $slipDetails->bags_used, 2) }}</strong></div>
                            <div class="rounded-xl border border-zinc-100 p-3"><span class="block text-[10px] font-bold uppercase text-zinc-400">Returned / held</span><strong class="mt-1 block font-mono text-sm text-zinc-800">{{ number_format((float) $slipDetails->bags_returned_to_factory, 2) }} / {{ number_format((float) $slipDetails->bags_held_with_sizing, 2) }}</strong></div>
                            <div class="rounded-xl border border-zinc-100 p-3"><span class="block text-[10px] font-bold uppercase text-zinc-400">Ends / Taarein</span><strong class="mt-1 block font-mono text-sm text-zinc-800">{{ $slipDetails->total_ends ? number_format($slipDetails->total_ends) : 'Not recorded' }}</strong></div>
                        </div>

                        @php
                            $slipBagVariance = (int) round(((float) $slipDetails->bags_allocated - (float) $slipDetails->bags_used - (float) $slipDetails->bags_returned_to_factory - (float) $slipDetails->bags_held_with_sizing) * 100);
                        @endphp
                        <p class="mt-3 text-xs {{ $slipBagVariance === 0 ? 'text-emerald-700' : 'text-amber-800' }}">
                            Bag reconciliation: {{ number_format((float) $slipDetails->bags_allocated, 2) }} sent − {{ number_format((float) $slipDetails->bags_used, 2) }} used − {{ number_format((float) $slipDetails->bags_returned_to_factory, 2) }} returned − {{ number_format((float) $slipDetails->bags_held_with_sizing, 2) }} held =
                            <strong>{{ $slipBagVariance === 0 ? 'balanced' : ($slipBagVariance > 0 ? number_format($slipBagVariance / 100, 2).' bag(s) unaccounted' : number_format(abs($slipBagVariance) / 100, 2).' bag(s) over-reported') }}</strong>.
                            This flags a discrepancy for review; it does not establish its cause.
                        </p>

                        @if($slipDetails->sizing_weight_pounds !== null)
                            <p class="mt-3 text-xs text-zinc-500">Net yarn bill weight: <strong class="text-zinc-800">{{ number_format($slipDetails->sizing_weight_pounds * 0.45359237, 2) }} kg</strong> ({{ number_format($slipDetails->sizing_weight_pounds, 2) }} lb).
                                @if($slipDetails->sizing_gross_weight_pounds !== null)
                                    Gross bill weight: <strong class="text-zinc-800">{{ number_format($slipDetails->sizing_gross_weight_pounds * 0.45359237, 2) }} kg</strong>.
                                    @if($slipDetails->sizing_weight_pounds !== null)
                                        Gross-to-net difference: <strong class="text-zinc-800">{{ number_format(($slipDetails->sizing_gross_weight_pounds - $slipDetails->sizing_weight_pounds) * 0.45359237, 2) }} kg</strong>.
                                    @endif
                                @endif
                                @if($slipDetails->estimatedWarpCountNe() !== null)
                                    Estimated warp count: <strong class="text-zinc-800">{{ number_format($slipDetails->estimatedWarpCountNe(), 2) }} Ne</strong>
                                    @if($slipDetails->declaredYarnCountNe() !== null)
                                        vs declared {{ number_format($slipDetails->declaredYarnCountNe(), 2) }} Ne.
                                    @endif
                                @endif
                            </p>
                            <p class="mt-1 text-[10px] text-zinc-400">Count is a reconciliation estimate from total ends, unmultiplied set length, and net bill weight. It cannot by itself establish fraud or replace a yarn test.</p>
                        @endif

                        @if(($slipDetails->slip_type ?? 'raw_perch') === 'final_perch')
                            <div class="mt-4 rounded-xl border border-zinc-200 bg-zinc-50 p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wide text-zinc-500">Final Perch invoice reconciliation</p>
                                <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-zinc-600 sm:grid-cols-3">
                                    <span>Used cones-bags ({{ $slipDetails->wet_cone_bags }} bags): <strong>{{ number_format($slipDetails->wet_cone_weight_kg, 2) }} kg</strong></span>
                                    <span>Empty bags ({{ $slipDetails->empty_bag_count }}): <strong>{{ number_format($slipDetails->empty_bag_weight_kg, 2) }} kg</strong></span>
                                    <span>Gatta-bags ({{ $slipDetails->paper_bag_count }}): <strong>{{ number_format($slipDetails->paper_bag_weight_kg, 2) }} kg</strong></span>
                                    <span>Salim/unused cones ({{ $slipDetails->sound_cone_count }}): <strong>{{ number_format($slipDetails->sound_cone_weight_kg, 2) }} kg</strong></span>
                                    <span>Cartons ({{ $slipDetails->carton_count }}): <strong>{{ number_format($slipDetails->carton_weight_kg, 2) }} kg</strong></span>
                                    <span>Total deductions: <strong>{{ number_format($slipDetails->returned_materials_weight_kg, 2) }} kg</strong></span>
                                </div>
                                <p class="mt-2 border-t border-zinc-200 pt-2 text-xs font-semibold text-zinc-800">
                                    Net {{ number_format($slipDetails->sizing_weight_pounds * 0.45359237, 2) }} kg
                                    @if($slipDetails->sizing_rate_per_kg !== null)
                                        × Rs. {{ number_format($slipDetails->sizing_rate_per_kg, 2) }}/kg = Rs. {{ number_format($slipDetails->sizing_bill_amount, 2) }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        <div class="mt-5 overflow-hidden rounded-2xl border border-zinc-200">
                            <div class="bg-zinc-50 px-4 py-3 text-xs font-extrabold text-zinc-800">All beams created from this slip</div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="text-[10px] uppercase tracking-wider text-zinc-500"><tr class="border-t border-zinc-200"><th class="px-4 py-2.5">Beam</th><th class="px-4 py-2.5">Status / Loom</th><th class="px-4 py-2.5 text-right">Expected fabric</th><th class="px-4 py-2.5 text-right">Woven</th><th class="px-4 py-2.5 text-right">Warp remaining</th></tr></thead>
                                    <tbody class="divide-y divide-zinc-100">
                                        @foreach($slipDetails->beams as $slipBeam)
                                            <tr>
                                                <td class="px-4 py-2.5 font-mono font-bold text-zinc-900">{{ $slipBeam->beam_name ?: $slipBeam->beam_no }} <span class="block text-[10px] text-zinc-400">{{ $slipBeam->beam_no }}</span></td>
                                                <td class="px-4 py-2.5 text-zinc-600">{{ str_replace('_', ' ', ucfirst($slipBeam->status)) }}{{ $slipBeam->assigned_loom_no ? ' · '.$slipBeam->assigned_loom_no : '' }}</td>
                                                <td class="px-4 py-2.5 text-right font-mono text-zinc-700">{{ number_format((float) $slipBeam->total_length_meters * (float) $slipDetails->width_multiplier, 2) }}m</td>
                                                <td class="px-4 py-2.5 text-right font-mono text-zinc-700">{{ number_format($slipBeam->productions_sum_net_meters_produced ?? 0, 2) }}m</td>
                                                <td class="px-4 py-2.5 text-right font-mono text-zinc-700">{{ number_format($slipBeam->remaining_length_meters, 2) }}m</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="button" onclick="closeModal('modalBeamDetails{{ $beamRecord->id }}')" class="rounded-xl bg-zinc-100 px-4 py-2 text-xs font-bold text-zinc-700 hover:bg-zinc-200">Close details</button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        @if($page === 'beams')
            <div class="px-6 py-4 border-t border-zinc-100">{{ $beams->links() }}</div>
        @endif
    </div>
    @endif


    {{-- ============================================================
         SECTION 2 — Sizing Slips
    ============================================================ --}}
    @if (in_array($page, ['dashboard', 'slips'], true))
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
                        {{ $page === 'dashboard' ? 'Recent Sizing Slips' : 'Sizing Slips & Bag Consumption History' }}
                    </h2>
                    <p class="text-[10px] text-zinc-400 mt-0.5">Bags allocated per slip, used in beams, returned, and held with sizing</p>
                </div>
            </div>
            @if($page === 'dashboard')
                <a href="{{ route('sizing.slips') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
            @endif
        </div>

        <div class="overflow-x-auto rounded-b-3xl">
            <table class="w-full min-w-[2200px] table-fixed text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] leading-4 tracking-wider bg-zinc-50 align-top">
                        <th class="px-4 py-3">Slip #</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Sizing Party</th>
                        <th class="px-4 py-3">Quality</th>
                        <th class="px-4 py-3 text-center">Bags Given</th>
                        <th class="px-4 py-3 text-center bg-zinc-900/5 text-zinc-700 border-x border-zinc-200">Used in Beams</th>
                        <th class="px-4 py-3 text-center bg-zinc-100/70 text-zinc-600 border-r border-zinc-200">Returned to Factory</th>
                        <th class="px-4 py-3 text-center bg-zinc-100/70 text-zinc-600 border-r border-zinc-200">Held With Sizing</th>
                        <th class="px-4 py-3 text-center">Bag Check</th>
                        <th class="px-4 py-3 text-center">Back / Full Beams</th>
                        <th class="px-4 py-3 text-right">Expected Cloth</th>
                        <th class="px-4 py-3 text-right">Expected / Full Beam</th>
                        <th class="px-4 py-3 text-right">Actually Woven</th>
                        <th class="px-4 py-3 text-right">Short / Over on Used Warp</th>
                        <th class="px-4 py-3 text-right">Ends / Taarein</th>
                        <th class="px-4 py-3 text-right">Net Yarn Weight</th>
                        <th class="px-4 py-3 text-right">Estimated Count</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($slips as $s)
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-zinc-900">{{ $s->slip_no }}<span class="mt-1 block text-[9px] font-sans uppercase text-zinc-400">{{ str_replace('_', ' ', $s->slip_type ?? 'raw_perch') }}</span></td>
                            <td class="px-4 py-3 text-zinc-500 font-mono">{{ $s->slip_date->format('d M, Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-zinc-700">{{ $s->sizing_party }}</td>
                            <td class="px-4 py-3 text-zinc-600 font-mono">{{ $s->yarn_quality }}</td>
                            <td class="px-4 py-3 text-center font-bold text-zinc-900 font-mono">{{ number_format((float) $s->bags_allocated, 2) }}</td>
                            <td class="px-4 py-3 text-center font-black text-zinc-900 bg-zinc-50 border-x border-zinc-200 font-mono">{{ number_format((float) $s->bags_used, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-zinc-600 bg-zinc-50 border-r border-zinc-200 font-mono">{{ number_format((float) $s->bags_returned_to_factory, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-zinc-600 bg-zinc-50 border-r border-zinc-200 font-mono">{{ number_format((float) $s->bags_held_with_sizing, 2) }}</td>
                            @php
                                $bagVariance = (int) round(((float) $s->bags_allocated - (float) $s->bags_used - (float) $s->bags_returned_to_factory - (float) $s->bags_held_with_sizing) * 100);
                            @endphp
                            <td class="px-4 py-3 text-center">
                                @if($bagVariance === 0)
                                    <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700">Balanced</span>
                                @elseif($bagVariance > 0)
                                    <span class="rounded-full bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-800">{{ number_format($bagVariance / 100, 2) }} unaccounted</span>
                                @else
                                    <span class="rounded-full bg-red-50 px-2 py-1 text-[10px] font-bold text-red-700">{{ number_format(abs($bagVariance) / 100, 2) }} over-reported</span>
                                @endif
                            </td>
                            @php
                                $usedSlipBeams = $s->beams->filter(fn ($beam) => in_array($beam->status, ['mounted_on_loom', 'exhausted'], true) || (int) ($beam->productions_count ?? 0) > 0);
                                $usedSlipBeamCount = $usedSlipBeams->count();
                                $unusedSlipBeamCount = max(0, (int) $s->total_beams - $usedSlipBeamCount);
                            @endphp
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold text-zinc-900">{{ $s->back_beam_count ?? '—' }} / {{ $s->total_beams }}</span>
                                <span class="block text-zinc-400 text-[10px]">back / full</span>
                                <span class="mt-1 block text-zinc-600 text-[10px]">{{ $usedSlipBeamCount }} used · {{ $unusedSlipBeamCount }} unused</span>
                            </td>
                            @php
                                $expectedTotalMeters = (float) $usedSlipBeams->sum(fn ($beam) => (float) $beam->total_length_meters * (float) $s->width_multiplier);
                                $expectedMetersPerBeam = $s->expectedFabricMetersPerBeam();
                                $actualSlipProduction = (float) $usedSlipBeams->sum(fn ($beam) => (float) ($beam->productions_sum_net_meters_produced ?? 0));
                                $expectedConsumedMeters = (float) $usedSlipBeams->sum(fn ($beam) => (float) ($beam->productions_sum_expected_fabric_meters ?? 0));
                                $productionCount = (int) $usedSlipBeams->sum(fn ($beam) => (int) ($beam->productions_count ?? 0));
                                $shortageMeters = $expectedConsumedMeters - $actualSlipProduction;
                                $allSlipBeamsFinished = $usedSlipBeams->isNotEmpty() && $usedSlipBeams->every(fn ($beam) => $beam->status === 'exhausted');
                                $estimatedWarpCount = $s->estimatedWarpCountNe();
                            @endphp
                            <td class="px-4 py-3 text-right font-mono font-bold text-zinc-900">
                                {{ number_format($expectedTotalMeters, 2) }}m
                                <span class="block text-[10px] font-normal text-zinc-400">used beams × {{ number_format($s->width_multiplier, 2) }} factor</span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-zinc-700">
                                {{ number_format($expectedMetersPerBeam, 2) }}m
                                <span class="block text-[10px] font-normal text-zinc-400">per full beam</span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-zinc-700">{{ number_format($actualSlipProduction, 2) }}m</td>
                            <td class="px-4 py-3 text-right font-mono {{ $shortageMeters >= 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                                @if($productionCount > 0)
                                    {{ number_format(abs($shortageMeters), 2) }}m {{ $shortageMeters >= 0 ? 'short' : 'over' }}
                                    <span class="block text-[10px]">used warp: {{ number_format($expectedConsumedMeters, 2) }}m expected · {{ $allSlipBeamsFinished ? 'final' : 'provisional' }}</span>
                                @else
                                    <span class="text-zinc-400">No production yet</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-zinc-700">{{ $s->total_ends ? number_format($s->total_ends) : '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono text-zinc-700">{{ $s->sizing_weight_pounds !== null ? number_format($s->sizing_weight_pounds * 0.45359237, 2).' kg net' : '—' }}{{ $s->sizing_gross_weight_pounds !== null ? ' / '.number_format($s->sizing_gross_weight_pounds * 0.45359237, 2).' kg gross' : '' }}@if($s->sizing_bill_amount !== null)<span class="mt-1 block text-[10px] font-bold text-zinc-900">Rs. {{ number_format($s->sizing_bill_amount, 2) }} @ {{ number_format($s->sizing_rate_per_kg, 2) }}/kg</span>@endif</td>
                            <td class="px-4 py-3 text-right font-mono text-zinc-700">{{ $estimatedWarpCount !== null ? number_format($estimatedWarpCount, 2).' Ne' : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="17" class="px-4 py-10 text-center text-zinc-400">No sizing slips found. Click "+ New Sizing Slip Entry" to create one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($page === 'slips')
            <div class="px-6 py-4 border-t border-zinc-100">{{ $slips->links() }}</div>
        @endif
    </div>
    @endif

    </div>
</div>

<!-- MODAL 1: NEW SIZING SLIP -->
{{-- ============================================================
     MODAL: NEW SIZING SLIP
============================================================ --}}
<div id="modalNewSizingSlip" class="fixed inset-0 z-50 hidden bg-zinc-950/60 backdrop-blur-sm overflow-y-auto p-4 sm:p-6">
    <div class="mx-auto my-8 w-full max-w-2xl overflow-y-auto rounded-3xl border border-zinc-200 bg-white shadow-2xl">

        {{-- Sticky header --}}
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-zinc-100 bg-white/95 px-6 py-4 backdrop-blur">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-zinc-900">
                    <svg class="h-5 w-5 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </span>
                <div>
                    <h3 class="text-base font-extrabold text-zinc-900">New Sizing Slip Entry</h3>
                    <p class="text-xs text-zinc-400">Record the gate pass, bill weights, bag returns, and each finished beam</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalNewSizingSlip')"
                    class="rounded-xl p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-900 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('sizing.slip.store') }}" method="POST" class="px-6 py-5 space-y-5">
            @csrf
            <label class="block text-xs font-semibold text-zinc-600">Perch entry type<select id="sizingSlipType" name="slip_type" onchange="toggleFinalPerchFields()" class="mt-1 w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm"><option value="raw_perch">Raw Perch — gate pass and beam details</option><option value="final_perch">Final Perch — sizing invoice and returned-material deductions</option></select></label>
            <div>
                <label class="mb-1 block text-xs font-semibold text-zinc-600">Bag receipt / issue being sized *</label>
                <select id="inpBagTransactionId" name="bag_transaction_id" required onchange="populateBagTransaction(this)"
                        class="w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    <option value="">Select a pending bag transaction</option>
                    @foreach($pendingSizingTransactions as $transaction)
                        @php
                            $sizingParty = $transaction->destination ?: ($transaction->party_name ?: $transaction->source);
                            $sizingSlipNo = 'SZ-' . $transaction->id . '-' . date('ymd', strtotime($transaction->transaction_date));
                        @endphp
                        <option value="{{ $transaction->id }}" data-slip="{{ $sizingSlipNo }}" data-party="{{ $sizingParty }}" data-quality="{{ $transaction->quality }}" data-quantity="{{ $transaction->quantity_bags }}" data-date="{{ $transaction->transaction_date->format('Y-m-d') }}">
                            {{ $transaction->reference_no }} · {{ $transaction->quality }} · {{ number_format((float) $transaction->quantity_bags, 2) }} bags · {{ $sizingParty }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-[10px] text-zinc-400">Each bag receipt can be used for one sizing slip. If none appear, record the yarn purchase or issue to sizing first.</p>
            </div>

            {{-- ── Section 1: Slip Basics ── --}}
            <div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-3">Slip Details</div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Sizing Slip # *</label>
                        <input type="text" id="inpSlipNo" name="slip_no" required placeholder="e.g. SZ-2026-092"
                               class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Slip Date *</label>
                        <input type="date" id="inpSlipDate" name="slip_date" required value="{{ date('Y-m-d') }}"
                               class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Sizing Unit / Party *</label>
                        <input type="text" id="inpSizingParty" name="sizing_party" required placeholder="e.g. National Sizing Industries"
                               class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Yarn Quality *</label>
                        <div class="flex gap-2">
                            <select id="inpYarnQuality" name="yarn_quality" required data-yarn-quality-select onchange="updateSizingCountPreview()"
                                    class="min-w-0 flex-1 bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                                <option value="">Select yarn quality</option>
                                @foreach($yarnQualities as $yarnQuality)
                                <option value="{{ $yarnQuality->name }}" data-warp-weight="{{ $yarnQuality->warp_weight_per_meter_lb }}" data-weft-weight="{{ $yarnQuality->weft_weight_per_meter_lb }}" data-total-weight="{{ $yarnQuality->total_weight_per_meter_lb }}" data-unit-cost="{{ $yarnQuality->unit_cost_meter }}">{{ $yarnQuality->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openModal('modalYarnQualities')" class="shrink-0 rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs font-bold text-zinc-700 transition-colors hover:bg-zinc-100">+ Create</button>
                        </div>
                        <p class="mt-1 text-[10px] text-zinc-400">The selected quality records the declared yarn count, such as 52. This estimate does not change it.</p>
                    </div>
                </div>
            </div>

            {{-- ── Section 2: Bags Reconciliation ── --}}
            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-200 bg-zinc-100">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-700">⚖️ Bags Reconciliation</span>
                        <span class="text-[10px] text-zinc-400">Compare bags sent with used, returned, and held</span>
                    </div>
                    <span id="sizingBagStatus" class="text-xs font-bold font-mono text-zinc-500">—</span>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Total Bags Given to Sizing *</label>
                        <input type="number" id="inpBagsAllocated" name="bags_allocated" required min="0.01" step="0.01" readonly
                               placeholder="Select a bag receipt" oninput="calculateSizingBags(); updateSizingWeightPreview()"
                               class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2.5 text-base text-zinc-900 font-mono font-black focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        <span id="sizingBagWeightPreview" class="mt-1 block text-[10px] text-zinc-500">100 lb per bag · 0.00 kg total</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl border border-zinc-200 bg-white p-3">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-2">Used in Beams *</label>
                            <input type="number" id="inpBagsUsed" name="bags_used" required min="0" step="0.01"
                                   placeholder="65" oninput="calculateSizingBags()"
                                   class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-2.5 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                        <div class="rounded-xl border border-zinc-200 bg-white p-3">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-2">Returned to Factory</label>
                            <input type="number" id="inpBagsReturned" name="bags_returned_to_factory"
                                   value="0" min="0" step="0.01" oninput="calculateSizingBags()"
                                   class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-2.5 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                        <div class="rounded-xl border border-zinc-200 bg-white p-3">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-2">Held with Sizing</label>
                            <input type="number" id="inpBagsHeld" name="bags_held_with_sizing"
                                   value="0" min="0" step="0.01" oninput="calculateSizingBags()"
                                   class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-2.5 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                    </div>
                    <p class="text-[10px] leading-4 text-zinc-500">If counts do not match, save the slip and it will be flagged for review. A mismatch is a record discrepancy, not proof of theft.</p>
                    {{-- Balance indicator bar --}}
                    <div id="bagBalanceBar" class="h-1.5 rounded-full bg-zinc-200 overflow-hidden">
                        <div id="bagBalanceFill" class="h-full rounded-full bg-zinc-300 transition-all duration-300" style="width:0%"></div>
                    </div>
                </div>
            </div>

            {{-- ── Section 3: Beam & Length Info ── --}}
            <div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-3">Beam & Warp Length</div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Raw / Back Beams</label>
                        <input type="number" name="back_beam_count" min="1" max="1000"
                               placeholder="e.g. 5"
                               class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Finished Full Beams Made *</label>
                        <input type="number" id="inpTotalBeams" name="total_beams" required min="1" max="100"
                               placeholder="e.g. 4" oninput="calculateBeamLengthSplit(); renderBeamNameInputs()"
                               class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Finished Beam Names *</label>
                        <div id="beamNamesContainer" class="grid grid-cols-1 gap-3 sm:grid-cols-2"></div>
                        <p class="mt-1 text-[10px] text-zinc-400">Enter the actual name or identifying mark for each finished beam.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Set Warp Length (Gate Pass) *</label>
                        <div class="flex gap-2">
                            <input type="number" step="0.01" id="inpSetLength" name="set_length_value"
                                   required min="0.01" placeholder="e.g. 18481" oninput="calculateBeamLengthSplit(); updateSizingCountPreview()"
                                   class="min-w-0 flex-1 bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                            <select id="selectSetLengthUnit" name="set_length_unit" onchange="calculateBeamLengthSplit(); updateSizingCountPreview()"
                                    class="w-24 bg-zinc-50 border border-zinc-300 rounded-xl px-2 py-2 text-xs text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                                <option value="meters">Meters</option>
                                <option value="gaz">Gaz/yds</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Fabric Panna *</label>
                        <select id="inpWidthMultiplier" name="width_multiplier" required onchange="calculateBeamLengthSplit()"
                                class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-semibold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                            <option value="1" selected>Single panna · factor 1</option>
                            <option value="2">Double panna · factor 2</option>
                        </select>
                        <span class="mt-1 block text-[10px] text-zinc-400">Choose the actual fabric width arrangement on this set.</span>
                    </div>
                </div>

                {{-- Live beam length result --}}
            <div class="mt-3 flex items-center justify-between rounded-xl bg-zinc-900 px-4 py-3">
                    <span class="text-xs text-zinc-400">Expected fabric production:</span>
                    <strong id="displayPerBeamLength" class="font-mono text-sm text-zinc-50">Enter set length, width factor, and full beams</strong>
                </div>
            </div>

            {{-- ── Section 4: Warp Construction ── --}}
            <div>
                <div class="mb-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Warp Construction</div>
                    <p class="mt-1 text-[10px] text-zinc-500">Enter the total warp ends from the gate pass. This is used to estimate and check warp count.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Total Ends / Taarein</label>
                    <input type="number" id="inpTotalEnds" name="total_ends" required min="1" placeholder="e.g. 10080" oninput="updateSizingCountPreview()"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    <span class="mt-1 block text-[10px] text-zinc-400">Required to create a sizing slip.</span>
                </div>
            </div>

            <div>
                <div class="mb-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sizing Bill Weight</div>
                    <p id="sizingWeightSourceHint" class="mt-1 text-[10px] text-zinc-500">Enter the net warp yarn weight from the bill. The gross weight is optional for Raw Perch.</p>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div id="sizingGrossWeightField">
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Gross Weight on Sizing Bill (kg)</label>
                        <input type="number" step="0.01" id="inpSizingGrossWeightKg" name="sizing_gross_weight_kg" min="0.01" placeholder="e.g. 2550" oninput="updateSizingWeightPreview(); updateFinalPerchPreview()"
                               class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                    <div id="sizingNetWeightField">
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Net Warp Yarn Weight (kg)</label>
                        <input type="number" step="0.01" id="inpSizingWeightKg" name="sizing_weight_kg" min="0.01" placeholder="e.g. 2266" oninput="updateSizingCountPreview()"
                               class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                </div>
                <div class="mt-3 rounded-xl border border-zinc-200 bg-zinc-50 p-3">
                    <span id="sizingCountPreview" class="block text-[10px] font-semibold text-zinc-700">Enter total ends, set length, and net weight to estimate yarn count.</span>
                    <span class="mt-1 block text-[10px] text-zinc-400">Estimate = ends × set length in metres ÷ (1693.6 × net kg). It is a reconciliation check and does not change the selected yarn quality.</span>
                    <span id="sizingQualityWeightPreview" class="mt-2 block border-t border-zinc-200 pt-2 text-[10px] font-semibold text-zinc-700">Select a yarn quality with stored Read/Pick and yarn counts to preview fabric weight.</span>
                </div>
            </div>

            <section id="finalPerchFields" class="hidden rounded-2xl border border-zinc-300 bg-zinc-50 p-4 space-y-4">
                <div><h4 class="text-xs font-extrabold uppercase tracking-wider text-zinc-700">Final Perch Invoice — returned materials</h4><p class="mt-1 text-[10px] text-zinc-500">These returned weights are subtracted from the gross bill weight. The remaining net warp weight is used for warp-count reconciliation and sizing charges.</p></div>
                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-semibold">Used cones-bags count<input name="wet_cone_bags" type="number" min="0" placeholder="Enter count if returned" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
                    <label class="text-xs font-semibold">Used cones-bags weight (kg)<input id="wetConeWeightKg" name="wet_cone_weight_kg" type="number" min="0" step="0.01" placeholder="Enter weight if returned" oninput="updateFinalPerchPreview()" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
                    <label class="text-xs font-semibold">Empty bags count<input name="empty_bag_count" type="number" min="0" placeholder="Enter count if returned" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
                    <label class="text-xs font-semibold">Empty bags weight (kg)<input id="emptyBagWeightKg" name="empty_bag_weight_kg" type="number" min="0" step="0.01" placeholder="Enter weight if returned" oninput="updateFinalPerchPreview()" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
                    <label class="text-xs font-semibold">Gatta-bags count<input name="paper_bag_count" type="number" min="0" placeholder="Enter count if returned" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
                    <label class="text-xs font-semibold">Gatta-bags weight (kg)<input id="paperBagWeightKg" name="paper_bag_weight_kg" type="number" min="0" step="0.01" placeholder="Enter weight if returned" oninput="updateFinalPerchPreview()" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
                    <label class="text-xs font-semibold">Salim/unused cones count<input name="sound_cone_count" type="number" min="0" placeholder="Enter count if returned" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
                    <label class="text-xs font-semibold">Salim/unused cones weight (kg)<input id="soundConeWeightKg" name="sound_cone_weight_kg" type="number" min="0" step="0.01" placeholder="Enter weight if returned" oninput="updateFinalPerchPreview()" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
                    <label class="text-xs font-semibold">Cartons count<input name="carton_count" type="number" min="0" placeholder="Enter count if returned" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
                    <label class="text-xs font-semibold">Cartons weight (kg)<input id="cartonWeightKg" name="carton_weight_kg" type="number" min="0" step="0.01" placeholder="Enter weight if returned" oninput="updateFinalPerchPreview()" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
                </div>
                <div class="grid grid-cols-2 gap-3 border-t border-zinc-200 pt-3"><div class="rounded-xl bg-white p-3"><span class="text-[10px] font-bold uppercase text-zinc-500">Total deductions</span><strong id="finalDeductionsPreview" class="mt-1 block font-mono text-lg">0.00 kg</strong></div><label class="text-xs font-semibold">Sizing rate (Rs. / kg)<input id="sizingRatePerKg" name="sizing_rate_per_kg" type="number" min="0" step="0.01" placeholder="61.00" oninput="updateFinalPerchPreview()" class="mt-1 w-full rounded-xl border px-3 py-2"></label></div>
                <div class="rounded-xl bg-zinc-900 p-3 text-white"><div class="flex justify-between text-xs"><span>Net yarn weight</span><strong id="finalNetWeightPreview">0.00 kg</strong></div><div class="mt-2 flex justify-between border-t border-white/20 pt-2 text-sm"><span>Sizing bill</span><strong id="finalBillPreview">Rs. 0.00</strong></div></div>
            </section>

            {{-- Notes --}}
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Notes</label>
                <input type="text" name="notes" placeholder="e.g. Sizing chemicals used, tension settings"
                       class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>

            {{-- Footer actions --}}
            <div class="pt-3 border-t border-zinc-100 flex items-center justify-between gap-3">
                <p class="text-[11px] text-zinc-400">Bag mismatches save with a review flag</p>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeModal('modalNewSizingSlip')"
                            class="px-4 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-semibold transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="btnSubmitSizingSlip"
                            class="px-6 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-black shadow-sm transition-colors opacity-50 cursor-not-allowed"
                            disabled>
                        Create Slip & Beams
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: MOUNT BEAM ON LOOM -->
<div id="modalMountBeam" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-zinc-900/50 p-4 backdrop-blur-sm">
    <div class="relative max-h-[calc(100vh-2rem)] w-full max-w-sm overflow-y-auto rounded-3xl border border-zinc-200 bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
            <h3 class="text-base font-bold text-zinc-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-zinc-700"></span>
                Mount Beam onto Loom
            </h3>
            <button type="button" onclick="closeModal('modalMountBeam')" class="text-zinc-400 hover:text-zinc-900 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="formMountBeam" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Beam Selected</label>
                <input type="text" id="mountBeamName" readonly class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3 py-2 text-sm font-mono font-bold text-zinc-900">
            </div>
            <div id="mountBeamDetails" class="whitespace-pre-line rounded-xl border border-zinc-200 bg-zinc-50 p-3 text-xs leading-5 text-zinc-600">
                Select a beam to see its yarn quality and remaining length.
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Select Loom for this Beam *</label>
                <select name="assigned_loom_no" id="selectMountLoom" required class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    @foreach($looms as $loom)
                        <option value="{{ $loom }}">{{ $loom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Mount Date *</label>
                <input type="date" name="mounted_date" required value="{{ date('Y-m-d') }}" class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div class="pt-3 border-t border-zinc-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalMountBeam')" class="px-4 py-2 rounded-xl bg-zinc-100 text-zinc-600 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-bold">Mount on Loom</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const sizingBeamAssignUrlTemplate = @json(route('sizing.beam.assign', ['beam' => '__BEAM_ID__']));

    function openSizingSlipFromBag(button) {
        const bagTransaction = document.getElementById('inpBagTransactionId');
        bagTransaction.value = button.dataset.id || '';
        populateBagTransaction(bagTransaction);
        document.getElementById('sizingSlipType').value = 'raw_perch';
        toggleFinalPerchFields();
        openModal('modalNewSizingSlip');
    }

    function populateBagTransaction(select) {
        const option = select.options[select.selectedIndex];
        if (!option || !option.value) return;

        document.getElementById('inpSlipNo').value = option.dataset.slip || '';
        document.getElementById('inpSlipDate').value = option.dataset.date || '';
        document.getElementById('inpSizingParty').value = option.dataset.party || '';
        document.getElementById('inpYarnQuality').value = option.dataset.quality || '';
        const quantity = parseFloat(option.dataset.quantity) || 0;
        document.getElementById('inpBagsAllocated').value = quantity.toFixed(2);
        document.getElementById('inpBagsUsed').value = quantity.toFixed(2);
        document.getElementById('inpBagsReturned').value = 0;
        document.getElementById('inpBagsHeld').value = 0;
        document.getElementById('inpSizingGrossWeightKg').value = '';
        document.getElementById('inpSizingWeightKg').value = '';
        document.querySelectorAll('#finalPerchFields input').forEach((field) => {
            field.value = '';
        });
        updateSizingWeightPreview();
        updateSizingCountPreview();
        calculateSizingBags();
    }

    function toggleFinalPerchFields() {
        const isFinal = document.getElementById('sizingSlipType').value === 'final_perch';
        document.getElementById('finalPerchFields').classList.toggle('hidden', !isFinal);
        document.getElementById('inpSizingGrossWeightKg').required = isFinal;
        document.getElementById('sizingRatePerKg').required = isFinal;
        document.getElementById('inpSizingWeightKg').readOnly = isFinal;
        document.getElementById('sizingNetWeightField').classList.toggle('hidden', isFinal);
        document.getElementById('sizingGrossWeightField').classList.toggle('sm:col-span-2', isFinal);
        document.getElementById('sizingWeightSourceHint').innerText = isFinal
            ? 'Final Perch net weight is calculated from gross weight minus all returned-material deductions below.'
            : 'Enter the net warp yarn weight from the bill. The gross weight is optional for Raw Perch.';
        if (isFinal) {
            updateFinalPerchPreview();
        } else {
            updateSizingCountPreview();
        }
    }

    function updateFinalPerchPreview() {
        const gross = parseFloat(document.getElementById('inpSizingGrossWeightKg').value) || 0;
        const deductions = ['wetConeWeightKg', 'emptyBagWeightKg', 'paperBagWeightKg', 'soundConeWeightKg', 'cartonWeightKg']
            .reduce((total, id) => total + (parseFloat(document.getElementById(id).value) || 0), 0);
        const net = Math.max(0, gross - deductions);
        const rate = parseFloat(document.getElementById('sizingRatePerKg').value) || 0;
        document.getElementById('finalDeductionsPreview').innerText = `${deductions.toFixed(2)} kg`;
        document.getElementById('finalNetWeightPreview').innerText = `${net.toFixed(2)} kg`;
        document.getElementById('finalBillPreview').innerText = `Rs. ${(net * rate).toFixed(2)}`;
        document.getElementById('inpSizingWeightKg').value = net > 0 ? net.toFixed(2) : '';
        updateSizingWeightPreview();
        updateSizingCountPreview();
    }

    function updateSizingWeightPreview() {
        const allocated = parseFloat(document.getElementById('inpBagsAllocated').value) || 0;
        document.getElementById('sizingBagWeightPreview').innerText = `100 lb per bag · ${(allocated * 100 * 0.45359237).toFixed(2)} kg nominal total`;
    }

    function calculateSizingBags() {
        const allocated = parseFloat(document.getElementById('inpBagsAllocated').value) || 0;
        const used = parseFloat(document.getElementById('inpBagsUsed').value) || 0;
        const returned = parseFloat(document.getElementById('inpBagsReturned').value) || 0;
        const held = parseFloat(document.getElementById('inpBagsHeld').value) || 0;
        const variance = Math.round((allocated - used - returned - held) * 100);
        const status = document.getElementById('sizingBagStatus');
        const button = document.getElementById('btnSubmitSizingSlip');
        const fill = document.getElementById('bagBalanceFill');
        const accounted = used + returned + held;
        fill.style.width = `${allocated > 0 ? Math.min(100, Math.round(accounted / allocated * 100)) : 0}%`;

        if (allocated <= 0) {
            status.innerText = 'Select a bag receipt';
            status.className = 'text-xs font-bold font-mono text-zinc-400';
            fill.className = 'h-full rounded-full bg-zinc-300 transition-all duration-300';
        } else if (variance === 0) {
            status.innerText = `Balanced: ${used.toFixed(2)} used + ${returned.toFixed(2)} returned + ${held.toFixed(2)} held = ${allocated.toFixed(2)}`;
            status.className = 'text-xs font-bold font-mono text-emerald-700';
            fill.className = 'h-full rounded-full bg-emerald-600 transition-all duration-300';
        } else if (variance > 0) {
            status.innerText = `${(variance / 100).toFixed(2)} bag(s) not accounted for`;
            status.className = 'text-xs font-bold font-mono text-amber-700';
            fill.className = 'h-full rounded-full bg-amber-500 transition-all duration-300';
        } else {
            status.innerText = `${(Math.abs(variance) / 100).toFixed(2)} bag(s) over-reported`;
            status.className = 'text-xs font-bold font-mono text-red-700';
            fill.className = 'h-full rounded-full bg-red-500 transition-all duration-300';
        }

        button.disabled = allocated <= 0;
        button.classList.toggle('opacity-50', allocated <= 0);
        button.classList.toggle('cursor-not-allowed', allocated <= 0);
    }

    function calculateBeamLengthSplit() {
        const beams = parseInt(document.getElementById('inpTotalBeams').value) || 0;
        const setLength = parseFloat(document.getElementById('inpSetLength').value) || 0;
        const unit = document.getElementById('selectSetLengthUnit').value;
        const warpLengthMeters = setLength * (unit === 'gaz' ? 0.9144 : 1);
        const widthMultiplier = parseFloat(document.getElementById('inpWidthMultiplier').value) || 1;
        if (beams > 0 && warpLengthMeters > 0) {
            document.getElementById('displayPerBeamLength').innerText = `${(warpLengthMeters * widthMultiplier).toFixed(2)}m expected fabric total · ${(warpLengthMeters / beams).toFixed(2)}m warp per beam · ${(warpLengthMeters * widthMultiplier / beams).toFixed(2)}m expected fabric per beam`;
        } else {
            document.getElementById('displayPerBeamLength').innerText = 'Enter set length and full beam count';
        }
        updateSizingCountPreview();
    }

    function renderBeamNameInputs() {
        const count = Math.min(100, Math.max(0, parseInt(document.getElementById('inpTotalBeams').value) || 0));
        const container = document.getElementById('beamNamesContainer');
        const existingValues = Array.from(container.querySelectorAll('input')).map(input => input.value);
        container.innerHTML = '';

        for (let index = 0; index < count; index++) {
            const wrapper = document.createElement('div');
            const label = document.createElement('label');
            label.className = 'mb-1 block text-[11px] font-semibold text-zinc-600';
            label.textContent = `Finished Beam ${index + 1} Name *`;
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'beam_names[]';
            input.required = true;
            input.maxLength = 100;
            input.placeholder = `e.g. Beam ${index + 1} / ${index + 1} Number`;
            input.value = existingValues[index] || '';
            input.className = 'w-full rounded-xl border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm font-semibold text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500';
            wrapper.append(label, input);
            container.append(wrapper);
        }
    }

    function updateSizingCountPreview() {
        const ends = parseFloat(document.getElementById('inpTotalEnds').value) || 0;
        const setLength = parseFloat(document.getElementById('inpSetLength').value) || 0;
        const lengthMeters = setLength * (document.getElementById('selectSetLengthUnit').value === 'gaz' ? 0.9144 : 1);
        const netWeightKg = parseFloat(document.getElementById('inpSizingWeightKg').value) || 0;
        const preview = document.getElementById('sizingCountPreview');
        updateSizingQualityWeightPreview(lengthMeters);

        if (!ends || !lengthMeters || !netWeightKg) {
            preview.innerText = 'Enter total ends, set length, and net weight to estimate yarn count.';
            return;
        }

        const estimatedCount = (ends * lengthMeters) / (1693.6 * netWeightKg);
        const quality = document.getElementById('inpYarnQuality').value;
        const declaredCount = quality.match(/\d+(?:\.\d+)?/);
        const comparison = declaredCount
            ? ` · declared count ${declaredCount[0]} · difference ${(estimatedCount - parseFloat(declaredCount[0])).toFixed(2)}`
            : '';

        preview.innerText = `Estimated warp count: ${estimatedCount.toFixed(2)}${comparison}. Reconciliation estimate only; verify with a lab test.`;
    }

    function updateSizingQualityWeightPreview(lengthMeters = null) {
        const qualitySelect = document.getElementById('inpYarnQuality');
        const option = qualitySelect.options[qualitySelect.selectedIndex];
        const preview = document.getElementById('sizingQualityWeightPreview');
        const warpWeight = parseFloat(option?.dataset.warpWeight) || 0;
        const weftWeight = parseFloat(option?.dataset.weftWeight) || 0;
        const totalWeight = parseFloat(option?.dataset.totalWeight) || 0;
        const setLength = lengthMeters ?? (parseFloat(document.getElementById('inpSetLength').value) || 0)
            * (document.getElementById('selectSetLengthUnit').value === 'gaz' ? 0.9144 : 1);
        const widthMultiplier = parseFloat(document.getElementById('inpWidthMultiplier').value) || 1;

        if (!option?.value || totalWeight <= 0) {
            preview.innerText = 'This yarn quality has no stored weight calculation yet.';
            return;
        }

        const expectedFabricMeters = setLength * widthMultiplier;
        const estimatedTotalPounds = totalWeight * expectedFabricMeters;
        preview.innerText = `Stored quality weight: warp ${warpWeight.toFixed(4)} + weft ${weftWeight.toFixed(4)} = ${totalWeight.toFixed(4)} lb/m · estimated ${estimatedTotalPounds.toFixed(2)} lb (${(estimatedTotalPounds * 0.45359237).toFixed(2)} kg) for ${expectedFabricMeters.toFixed(2)} expected fabric meters.`;
    }

    function openMountModal(button) {
        const beamId = button.dataset.id;
        const beamNo = button.dataset.beamNo;
        const currentLoom = button.dataset.loom;
        const remainingMeters = parseFloat(button.dataset.remainingMeters) || 0;
        const widthMultiplier = parseFloat(button.dataset.widthFactor) || 1;
        document.getElementById('formMountBeam').action = sizingBeamAssignUrlTemplate.replace('__BEAM_ID__', encodeURIComponent(beamId));
        document.getElementById('mountBeamName').value = `${button.dataset.beamName || beamNo} (${beamNo})`;
        document.getElementById('mountBeamDetails').textContent = `${button.dataset.quality}\n${parseFloat(button.dataset.warpMeters).toFixed(2)}m warp supplied · ${remainingMeters.toFixed(2)}m warp remaining\n${(remainingMeters * widthMultiplier).toFixed(2)}m expected fabric at factor ${widthMultiplier}`;
        if (currentLoom) document.getElementById('selectMountLoom').value = currentLoom;
        openModal('modalMountBeam');
    }
</script>
@endpush
@include('yarn-qualities.modal')
@endsection
