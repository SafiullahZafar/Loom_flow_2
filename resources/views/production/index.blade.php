@extends('layouts.app')

@section('title', 'Loom-wise Production & Leakage Analysis')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-6 items-start">

    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    <aside class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm lg:sticky lg:top-24">

        {{-- Dashboard main link --}}
        <a href="{{ route('production.index') }}"
           class="mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 transition-all
                  {{ $page === 'dashboard'
                       ? 'bg-zinc-900 text-zinc-50 shadow-md'
                       : 'bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 hover:text-zinc-900' }}">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                         {{ $page === 'dashboard' ? 'bg-zinc-700' : 'bg-zinc-200' }}">
                <svg class="h-5 w-5 {{ $page === 'dashboard' ? 'text-zinc-100' : 'text-zinc-600' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-extrabold leading-tight">Production Dashboard</span>
                <span class="block text-[10px] text-zinc-400 mt-0.5">Overview & efficiency stats</span>
            </div>
        </a>

        <div class="mb-2 px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sections</div>

        <nav class="space-y-1.5" aria-label="Production sections">

            {{-- Loom-wise Summary --}}
            <a href="{{ route('production.loom-summary') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'loom-summary'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'loom-summary' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'loom-summary' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Loom-wise Summary</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Output, leakage & efficiency per loom</span>
                </div>
                @if($page === 'loom-summary')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Production Leakage --}}
            <a href="{{ route('production.leakage') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'leakage' ? 'bg-zinc-900 text-zinc-50 shadow-sm' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $page === 'leakage' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'leakage' ? 'text-zinc-100' : 'text-zinc-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M5.06 19h13.88c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.33 16c-.77 1.33.19 3 1.73 3z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Production Leakage</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Expected vs actual by beam</span>
                </div>
                @if($page === 'leakage')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Production History --}}
            <a href="{{ route('production.history') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'history'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'history' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'history' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Production History</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Shift-by-shift daily log</span>
                </div>
                @if($page === 'history')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

        </nav>

        {{-- Production stats snapshot --}}
        @if(isset($totalFabricProduced))
        <div class="mt-4 rounded-2xl bg-zinc-50 border border-zinc-200 p-3 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Floor Snapshot</div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Fabric Made</span>
                <span class="font-mono font-bold text-zinc-900">{{ number_format($totalFabricProduced, 0) }}m</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Avg Efficiency</span>
                <span class="font-mono font-bold text-zinc-800">{{ $avgEfficiency }}%</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Avg Leakage</span>
                <span class="font-mono font-bold text-zinc-600">{{ $overallLeakagePercentage }}%</span>
            </div>
            <div class="pt-1.5 border-t border-zinc-200 flex items-center justify-between text-xs">
                <span class="text-zinc-700 font-bold">Active Looms</span>
                <span class="font-mono font-black text-zinc-900">{{ $loomWiseStats->count() }} / 24</span>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="mt-5 pt-4 border-t border-zinc-100 space-y-1.5">
            <div class="px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Quick Actions</div>
            <button type="button" onclick="openModal('modalRecordProduction')"
                    class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Record Production
            </button>
            <a href="{{ route('sizing.index') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Active Beams
            </a>
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

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3"><div><h2 class="font-extrabold text-zinc-900">Production Entries</h2><p class="mt-1 text-xs text-zinc-500">Enter fabric quality, Gazana per roll, and roll quantity for each loom.</p></div><button type="button" onclick="openModal('modalRollRegister')" class="rounded-xl bg-zinc-900 px-4 py-2 text-xs font-bold text-white">Production entry</button></div>
            <div class="mt-4 grid grid-cols-2 gap-3"><label class="text-xs font-semibold">Filter date<input id="rollFilterDate" type="date" class="mt-1 w-full rounded-xl border px-3 py-2" oninput="filterRollRegister()"></label><label class="text-xs font-semibold">Filter quality<input id="rollFilterQuality" type="search" placeholder="Fabric quality" class="mt-1 w-full rounded-xl border px-3 py-2" oninput="filterRollRegister()"></label></div>
            <div class="mt-4 overflow-x-auto"><table class="w-full min-w-[1000px] text-left text-xs"><thead class="bg-zinc-50 text-[10px] uppercase text-zinc-500"><tr><th class="px-3 py-2">Date</th><th class="px-3 py-2">Fabric quality</th><th class="px-3 py-2">Loom</th><th class="px-3 py-2 text-right">Gazana / roll</th><th class="px-3 py-2 text-right">Roll qty</th><th class="px-3 py-2 text-right">Total Gazana</th><th class="px-3 py-2 text-right">Total meters</th><th class="px-3 py-2 text-right">Warp used</th></tr></thead><tbody class="divide-y divide-zinc-100">@forelse($productions as $production)<tr data-roll-row data-date="{{ $production->production_date->format('Y-m-d') }}" data-quality="{{ strtolower($production->fabric_quality) }}"><td class="px-3 py-2">{{ $production->production_date->format('d-m-Y') }}</td><td class="px-3 py-2">{{ $production->fabric_quality }}</td><td class="px-3 py-2 font-mono">{{ $production->loom_no }}</td><td class="px-3 py-2 text-right font-mono">{{ $production->gazana_per_roll !== null ? number_format($production->gazana_per_roll, 2) : '—' }}</td><td class="px-3 py-2 text-right font-mono">{{ $production->roll_quantity ?? '—' }}</td><td class="px-3 py-2 text-right font-mono font-bold">{{ $production->total_gazana !== null ? number_format($production->total_gazana, 2) : '—' }}</td><td class="px-3 py-2 text-right font-mono">{{ number_format($production->net_meters_produced, 2) }}</td><td class="px-3 py-2 text-right font-mono">{{ number_format($production->beam_meters_consumed, 2) }}m</td></tr>@empty<tr><td colspan="8" class="px-3 py-6 text-center text-zinc-400">No production entries yet.</td></tr>@endforelse</tbody></table></div>
        </section>
        <div id="modalRollRegister" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-zinc-950/60 p-4"><div class="mx-auto my-8 w-full max-w-5xl overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl"><div class="mb-4 flex items-center justify-between"><div><h3 class="font-extrabold">Production entry</h3><p class="text-xs text-zinc-500">Choose the loom and linked fabric quality, then enter Gazana per roll and roll quantity.</p></div><button type="button" onclick="closeModal('modalRollRegister')">×</button></div>
            <form method="POST" action="{{ route('production.store') }}" class="space-y-5">
    @csrf
    <div class="grid gap-4 sm:grid-cols-[220px_minmax(0,1fr)]">
        <label class="block text-xs font-bold text-zinc-600">Production date
            <input name="production_date" type="date" required value="{{ date('Y-m-d') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm">
        </label>
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-3 text-xs leading-5 text-blue-900">
            <strong>Total Gazana</strong> = Gazana per roll × roll quantity; production meters = total Gazana × 0.9144. For a mounted beam, warp use is calculated from production meters and its sizing width factor.
        </div>
    </div>
    @if($fabricQualities->isEmpty())
        <a href="{{ route('fabric.index') }}" class="block rounded-xl bg-amber-50 p-3 text-xs font-semibold text-amber-800 underline">Add a fabric quality in Fabric Stock first.</a>
    @endif
    <div class="flex items-center justify-between gap-3 border-b pb-2">
        <div><h4 class="text-sm font-extrabold text-zinc-900">Loom production rows</h4><p class="mt-1 text-xs text-zinc-500">The linked Fabric Stock quality fills automatically for each mounted loom.</p></div>
        <button type="button" onclick="addRollRegisterRow()" class="shrink-0 rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs font-bold hover:bg-zinc-50">+ Add loom</button>
    </div>
    <div id="rollRegisterRows" class="space-y-3">
        <div class="roll-register-row rounded-2xl border border-zinc-200 bg-zinc-50/70 p-3" data-row-index="0">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
                <label class="block text-xs font-bold text-zinc-600">Loom
                    <select name="rolls[0][loom_no]" required onchange="updateRollRegisterLoom(this)" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-sm">
                        <option value="">Choose loom</option>
                        @foreach($looms as $loom)
                            <option value="{{ $loom }}" data-running="{{ $mountedBeams->get($loom) ? '1' : '0' }}" data-width-multiplier="{{ $mountedBeams->get($loom)?->sizingSlip?->width_multiplier ?? 0 }}" data-fabric-stock-id="{{ $loomQualityIds[$loom] ?? '' }}">{{ $loom }}{{ $mountedBeams->get($loom) ? ' - Running' : '' }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-xs font-bold text-zinc-600">Fabric quality
                    <select name="rolls[0][fabric_stock_id]" required data-quality-select onchange="updateRollRegisterRow(this)" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-sm">
                        <option value="">Choose linked quality</option>
                        @foreach($fabricQualities as $quality)
                            <option value="{{ $quality->id }}">{{ $quality->quality_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-xs font-bold text-zinc-600">Gazana per roll
                    <input name="rolls[0][gazana_per_roll]" required type="number" min="0.01" step="0.01" placeholder="e.g. 100" oninput="updateRollRegisterRow(this)" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-right font-mono text-base">
                </label>
                <label class="block text-xs font-bold text-zinc-600">Roll quantity
                    <input name="rolls[0][quantity]" required type="number" min="1" step="1" placeholder="e.g. 3" oninput="updateRollRegisterRow(this)" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-right font-mono text-base">
                </label>
                <label class="block text-xs font-bold text-zinc-600">Total Gazana
                    <input name="rolls[0][total_gazana]" readonly placeholder="0.00" class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-100 px-3 py-3 text-right font-mono text-base font-bold text-zinc-800">
                </label>
                <div class="flex items-end"><button type="button" onclick="removeRollRegisterRow(this)" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-sm font-bold text-zinc-600 hover:bg-red-50 hover:text-red-700">Remove loom row</button></div>
            </div>
        </div>
    </div>
    <div class="grid gap-2 rounded-xl bg-zinc-100 p-3 text-sm sm:grid-cols-2">
        <span>Total production: <strong id="rollRegisterGazanaTotal" class="font-mono">0.00 Gazana</strong></span>
        <span>Fabric stock addition: <strong id="rollRegisterMeterTotal" class="font-mono">0.00m</strong></span>
    </div>
    <label class="block text-xs font-bold text-zinc-600">Remarks<input name="remarks" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm"></label>
    <div class="flex justify-end gap-2 border-t pt-4"><button type="button" onclick="closeModal('modalRollRegister')" class="rounded-xl bg-zinc-100 px-4 py-3 text-xs font-bold">Cancel</button><button class="rounded-xl bg-zinc-900 px-5 py-3 text-xs font-bold text-white">Save production</button></div>
</form>
        </div></div>
        <script>
            let rollRegisterIndex = 1;

            function addRollRegisterRow() {
                const rowsContainer = document.getElementById('rollRegisterRows');
                const rowIndex = rollRegisterIndex++;
                const row = rowsContainer.querySelector('.roll-register-row').cloneNode(true);
                row.dataset.rowIndex = String(rowIndex);
                row.querySelectorAll('[name]').forEach((field) => {
                    field.name = field.name.replace(/\[0\]/g, `[${rowIndex}]`);
                    if (field instanceof HTMLInputElement) { field.value = ''; }
                    if (field instanceof HTMLSelectElement) { field.selectedIndex = 0; }
                });
                row.querySelector('[name$="[loom_no]"]').onchange = function () { updateRollRegisterLoom(this); };
                rowsContainer.appendChild(row);
                updateRollRegisterRow(row.querySelector('[name$="[quantity]"]'));
            }
            function updateRollRegisterLoom(select) {
                const row = select.closest('.roll-register-row');
                const option = select.selectedOptions[0];
                const qualitySelect = row.querySelector('[data-quality-select]');
                const qualityId = option?.dataset.fabricStockId || '';
                qualitySelect.value = [...qualitySelect.options].some((quality) => quality.value === qualityId) ? qualityId : '';
                qualitySelect.disabled = Boolean(qualityId);
                qualitySelect.title = qualityId ? 'Using the quality linked to this loom.' : 'No quality is linked to this loom. Link one in Fabric Stock.';
                updateRollRegisterRow(select);
            }
            function removeRollRegisterRow(button) {
                const rowsContainer = document.getElementById('rollRegisterRows');
                if (rowsContainer.querySelectorAll('.roll-register-row').length <= 1) { return; }
                button.closest('.roll-register-row').remove();
                updateRollRegisterTotal();
            }
            function updateRollRegisterRow(input) { const row = input.closest('.roll-register-row'); const gazana = parseFloat(row.querySelector('[name$="[gazana_per_roll]"]').value) || 0; const quantity = parseInt(row.querySelector('[name$="[quantity]"]').value, 10) || 0; const totalGazana = gazana * quantity; row.querySelector('[name$="[total_gazana]"]').value = totalGazana ? totalGazana.toFixed(2) : ''; updateRollRegisterTotal(); }
            function updateRollRegisterTotal() { const rows = [...document.querySelectorAll('.roll-register-row')]; const totalGazana = rows.reduce((sum, row) => sum + (parseFloat(row.querySelector('[name$="[total_gazana]"]').value) || 0), 0); document.getElementById('rollRegisterGazanaTotal').textContent = `${totalGazana.toFixed(2)} Gazana`; document.getElementById('rollRegisterMeterTotal').textContent = `${(totalGazana * 0.9144).toFixed(2)}m`; }
            function filterRollRegister() { const date = document.getElementById('rollFilterDate').value; const quality = document.getElementById('rollFilterQuality').value.toLowerCase(); document.querySelectorAll('[data-roll-row]').forEach((row) => { row.hidden = (date && row.dataset.date !== date) || (quality && !row.dataset.quality.includes(quality)); }); }
        </script>

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-zinc-200">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Module 06</span>
                </div>
                <h1 class="text-2xl font-black text-zinc-900 tracking-tight">
                    {{ ['dashboard' => 'Production Dashboard', 'loom-summary' => 'Loom-wise Production Summary', 'leakage' => 'Production Leakage', 'history' => 'Production History Log'][$page] }}
                </h1>
                <p class="text-xs text-zinc-400 mt-0.5">
                    {{ ['dashboard' => 'Shift production, beam consumption, leakage waste, and efficiency overview.', 'loom-summary' => 'Per-loom breakdown of fabric output, beam consumed, and leakage loss.', 'leakage' => 'Sizing beam expected meters compared with actual woven meters.', 'history' => 'Complete shift-by-shift production log with meter counters and leakage data.'][$page] }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" onclick="openModal('modalRecordProduction')"
                        class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 font-extrabold text-xs shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Record Loom Production
                </button>
                <a href="{{ route('sizing.index') }}"
                   class="px-3.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border border-zinc-300 text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Active Beams
                </a>
            </div>
        </div>

        <?php if ($page === 'leakage') { ?>
            <section class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Expected Production</div>
                        <div class="mt-1 font-mono text-2xl font-black text-zinc-900">{{ number_format($expectedTotal, 2) }}m</div>
                        <p class="mt-1 text-xs text-zinc-400">Total sizing warp × width factor across all slips</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Actual Production</div>
                        <div class="mt-1 font-mono text-2xl font-black text-zinc-900">{{ number_format($actualTotal, 2) }}m</div>
                        <p class="mt-1 text-xs text-zinc-400">Sum of recorded woven meters</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Expected Capacity Not Yet Woven</div>
                        <div class="mt-1 font-mono text-2xl font-black text-zinc-900">{{ number_format($gapTotal, 2) }}m</div>
                        <p class="mt-1 text-xs text-zinc-400">Total expected cloth minus actual woven cloth</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-300 bg-zinc-100 p-4 shadow-sm">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700">Final Leakage on Exhausted Beams</div>
                        <div class="mt-1 font-mono text-2xl font-black text-zinc-900">{{ number_format($finalLeakageTotal, 2) }}m</div>
                        <p class="mt-1 text-xs text-zinc-600">Expected minus actual for finished beams</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
                    <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
                        <h2 class="text-sm font-extrabold text-zinc-900">Production by Sizing Slip</h2>
                        <p class="mt-1 text-[11px] text-zinc-500">All beams and looms linked to a slip are combined. Expected cloth uses only warp recorded as consumed, so unused beams do not inflate the expectation.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                        <thead class="bg-white text-[10px] font-semibold uppercase tracking-wider text-zinc-500"><tr class="border-b border-zinc-200"><th class="px-4 py-3">Sizing Slip</th><th class="px-4 py-3">Yarn Quality</th><th class="px-4 py-3 text-right">Beams Used / Total</th><th class="px-4 py-3 text-right">Looms</th><th class="px-4 py-3 text-right">Expected Total</th><th class="px-4 py-3 text-right">Actual Woven</th><th class="px-4 py-3 text-right">Recorded Leakage</th></tr></thead>
                            <tbody class="divide-y divide-zinc-100">
                                @forelse($sizingSlipProductionRows as $slipProduction)
                                    <tr class="hover:bg-zinc-50"><td class="px-4 py-3 font-mono font-bold text-zinc-900">{{ $slipProduction->slip_no }}</td><td class="px-4 py-3 text-zinc-600">{{ $slipProduction->yarn_quality }}</td><td class="px-4 py-3 text-right font-mono">{{ $slipProduction->used_beam_count }} / {{ $slipProduction->total_beams }}</td><td class="px-4 py-3 text-right font-mono">{{ $slipProduction->loom_count }}</td><td class="px-4 py-3 text-right font-mono">{{ number_format($slipProduction->expected_total_meters, 2) }}m<span class="block text-[10px] text-zinc-400">all beams × width factor</span></td><td class="px-4 py-3 text-right font-mono font-bold">{{ number_format($slipProduction->actual_woven_meters, 2) }}m</td><td class="px-4 py-3 text-right font-mono font-bold text-amber-700">{{ number_format($slipProduction->recorded_leakage_meters, 2) }}m</td></tr>
                                @empty
                                    <tr><td colspan="7" class="px-4 py-10 text-center text-zinc-400">No sizing slips have production records yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
                    <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
                        <h2 class="text-sm font-extrabold text-zinc-900">Expected vs Actual Production by Beam</h2>
                        <p class="mt-1 text-[11px] text-zinc-500">Gap = expected beam meters − actual woven meters. For beams still running, the gap is provisional; it becomes final leakage when the beam is exhausted.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-white text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                                <tr class="border-b border-zinc-200">
                                    <th class="px-4 py-3">Beam / Sizing Slip</th>
                                    <th class="px-4 py-3">Loom</th>
                                    <th class="px-4 py-3 text-right">Expected</th>
                                    <th class="px-4 py-3 text-right">Actual Woven</th>
                                    <th class="px-4 py-3 text-right">Gap / Leakage</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($beamLeakageRows as $beam)
                                    @php
                                        $expectedMeters = (float) $beam->total_length_meters * (float) ($beam->sizingSlip?->width_multiplier ?? 1);
                                        $actualMeters = (float) ($beam->productions_sum_net_meters_produced ?? 0);
                                        $productionGap = max(0, $expectedMeters - $actualMeters);
                                        $isFinished = $beam->status === 'exhausted';
                                    @endphp
                                    <tr class="hover:bg-zinc-50">
                                        <td class="px-4 py-3">
                                            <span class="font-mono font-bold text-zinc-900">{{ $beam->beam_no }}</span>
                                            <span class="block text-[10px] text-zinc-400">{{ $beam->sizingSlip?->slip_no ?? 'No sizing slip reference' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-zinc-600">{{ $beam->assigned_loom_no ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right font-mono font-semibold text-zinc-700">{{ number_format($expectedMeters, 2) }}m<span class="block text-[10px] font-normal text-zinc-400">remaining capacity: {{ number_format((float) $beam->remaining_length_meters * (float) ($beam->sizingSlip?->width_multiplier ?? 1), 2) }}m</span></td>
                                        <td class="px-4 py-3 text-right font-mono font-semibold text-zinc-900">{{ number_format($actualMeters, 2) }}m</td>
                                        <td class="px-4 py-3 text-right font-mono font-bold {{ $isFinished ? 'text-amber-700' : 'text-zinc-600' }}">{{ number_format($productionGap, 2) }}m</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $isFinished ? 'bg-amber-50 text-amber-800' : 'bg-zinc-100 text-zinc-600' }}">{{ $isFinished ? 'Final leakage' : 'In progress · provisional gap' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        <?php } ?>

        {{-- KPI Cards (dashboard only) --}}
        @if($page === 'dashboard')
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Total Fabric Made</span><span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-xl font-black text-zinc-900 mt-1 font-mono">{{ number_format($totalFabricProduced, 0) }}<span class="text-xs font-normal text-zinc-400 ml-1">m</span></div>
                <div class="text-[11px] text-zinc-400 mt-1">Net cloth on looms</div>
            </div>
            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Beam Length Taken</span><span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-xl font-black text-zinc-900 mt-1 font-mono">{{ number_format($totalBeamConsumed, 0) }}<span class="text-xs font-normal text-zinc-400 ml-1">m</span></div>
                <div class="text-[11px] text-zinc-400 mt-1">Fed into looms</div>
            </div>
            <div class="rounded-2xl p-4 border border-zinc-400 bg-zinc-100 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700 flex items-center justify-between">
                    <span>Warp Leakage / Loss</span>
                    <span class="w-2 h-2 rounded-full bg-zinc-700 animate-pulse"></span>
                </div>
                <div class="text-xl font-black text-zinc-900 mt-1 font-mono">{{ number_format($totalLeakageMeters, 0) }}<span class="text-xs font-normal text-zinc-400 ml-1">m</span></div>
                <div class="text-[11px] text-zinc-600 font-bold mt-1">{{ $overallLeakagePercentage }}% avg leakage</div>
            </div>
            <div class="rounded-2xl p-4 border border-zinc-300 bg-zinc-50 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-600 flex items-center justify-between">
                    <span>Floor Efficiency</span><span class="w-2 h-2 rounded-full bg-zinc-500"></span>
                </div>
                <div class="text-xl font-black text-zinc-900 mt-1 font-mono">{{ $avgEfficiency }}%</div>
                <div class="text-[11px] text-zinc-400 mt-1">RPM & picks / inch</div>
            </div>
            <div class="rounded-2xl p-4 border border-zinc-200 bg-white shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 flex items-center justify-between">
                    <span>Active Looms</span><span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                </div>
                <div class="text-xl font-black text-zinc-900 mt-1">{{ $loomWiseStats->count() }}<span class="text-xs font-normal text-zinc-400 ml-1">of 24</span></div>
                <div class="text-[11px] text-zinc-400 mt-1">With mounted beams</div>
            </div>
        </div>
        @endif


        {{-- ============================================================
             SECTION 1 — Loom-wise Summary Cards
        ============================================================ --}}
        @if(in_array($page, ['dashboard', 'loom-summary'], true))
        <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">Loom-wise Production & Leakage Summary</h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Output, beam consumption, and leakage % for every active loom</p>
                    </div>
                </div>
                @if($page === 'dashboard')
                    <a href="{{ route('production.loom-summary') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
                @else
                    <span class="text-xs text-zinc-400 font-mono">{{ $loomWiseStats->count() }} looms</span>
                @endif
            </div>

            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @forelse($loomWiseStats as $stat)
                    @php
                        $leakagePct = $stat->total_expected_fabric > 0
                            ? round(($stat->total_leakage / $stat->total_expected_fabric) * 100, 1)
                            : 0;
                        $efficiency = round($stat->avg_eff, 1);
                        $effClass = $efficiency >= 85 ? 'text-zinc-900' : ($efficiency >= 70 ? 'text-zinc-600' : 'text-zinc-400');
                    @endphp
                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 space-y-3 hover:border-zinc-300 transition-colors">

                        {{-- Loom header --}}
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-black text-zinc-50 text-sm px-3 py-1 rounded-xl bg-zinc-900">
                                {{ $stat->loom_no }}
                            </span>
                            <span class="text-[10px] font-bold text-zinc-400 bg-white border border-zinc-200 px-2 py-0.5 rounded-lg">
                                {{ $stat->shift_count }} shift{{ $stat->shift_count != 1 ? 's' : '' }}
                            </span>
                        </div>

                        {{-- Stats rows --}}
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500">Fabric Made</span>
                                <strong class="font-mono text-zinc-900 text-sm">{{ number_format($stat->total_produced, 0) }}m</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500">Beam Taken</span>
                                <strong class="font-mono text-zinc-600">{{ number_format($stat->total_beam, 0) }}m</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500">Leakage Loss</span>
                                <strong class="font-mono text-zinc-500">{{ number_format($stat->total_leakage, 0) }}m
                                    <span class="text-[10px] font-normal text-zinc-400">({{ $leakagePct }}%)</span>
                                </strong>
                            </div>
                        </div>

                        {{-- Efficiency bar --}}
                        <div class="pt-2 border-t border-zinc-200">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] text-zinc-400 uppercase font-bold">Efficiency</span>
                                <strong class="{{ $effClass }} font-mono text-sm">{{ $efficiency }}%</strong>
                            </div>
                            <div class="w-full bg-zinc-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-zinc-800 h-1.5 rounded-full transition-all"
                                     style="width: {{ min(100, $efficiency) }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 py-12 text-center">
                        <div class="text-zinc-400 text-sm font-medium">No loom production recorded yet</div>
                        <div class="text-zinc-300 text-xs mt-1">Click "+ Record Loom Production" to log your first shift</div>
                    </div>
                @endforelse
            </div>
        </div>
        @endif


        {{-- ============================================================
             SECTION 2 — Shift Production History Table
        ============================================================ --}}
        @if(in_array($page, ['dashboard', 'history'], true))
        <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

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
                            {{ $page === 'dashboard' ? 'Recent Shift Production' : 'Daily Shift Production Log' }}
                        </h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Meter counters, beam consumed, leakage loss and efficiency per shift</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($page === 'dashboard')
                        <a href="{{ route('production.history') }}" class="text-[11px] font-bold text-zinc-500 hover:text-zinc-900 transition-colors">View all &rarr;</a>
                    @else
                        {{-- Filters for history page --}}
                        <form method="GET" action="{{ route('production.history') }}" class="flex items-center gap-2">
                            <select name="loom_no" onchange="this.form.submit()"
                                    class="bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-1.5 text-xs text-zinc-900">
                                <option value="">All Looms</option>
                                @foreach($looms as $loom)
                                    <option value="{{ $loom }}" {{ request('loom_no') == $loom ? 'selected' : '' }}>{{ $loom }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="date" value="{{ request('date') }}"
                                   onchange="this.form.submit()"
                                   class="bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-1.5 text-xs text-zinc-900">
                            @if(request('loom_no') || request('date'))
                                <a href="{{ route('production.history') }}"
                                   class="px-2.5 py-1.5 rounded-xl bg-zinc-100 text-zinc-500 hover:text-zinc-900 text-xs">Clear</a>
                            @endif
                        </form>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] tracking-wider bg-zinc-50">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Shift</th>
                            <th class="px-4 py-3">Loom #</th>
                            <th class="px-4 py-3">Operator</th>
                            <th class="px-4 py-3">Fabric Variety</th>
                            <th class="px-4 py-3 text-center font-mono">Counters</th>
                            <th class="px-4 py-3 text-right bg-zinc-900/5 border-x border-zinc-200 font-black text-zinc-800">Net Produced</th>
                            <th class="px-4 py-3 text-right">Est. / Short</th>
                            <th class="px-4 py-3 text-right">Beam Taken</th>
                            <th class="px-4 py-3 text-right bg-zinc-100/70 border-x border-zinc-200 text-zinc-600">Shift Leakage<br><span class="font-normal normal-case">Consumed − woven</span></th>
                            <th class="px-4 py-3 text-center">Efficiency</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($productions as $p)
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-zinc-500">{{ $p->production_date->format('d M, Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                                 {{ $p->shift === 'Day' ? 'bg-zinc-200 text-zinc-700' : 'bg-zinc-900 text-zinc-50' }}">
                                        {{ $p->shift }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-mono font-black text-zinc-50 px-2.5 py-1 rounded-lg bg-zinc-900 text-xs">
                                        {{ $p->loom_no }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-700 font-medium">{{ $p->operator_name }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $p->fabric_quality }}</td>
                                <td class="px-4 py-3 font-mono text-center text-zinc-500 text-[11px]">
                                    {{ number_format($p->start_meter, 0) }} &rarr; {{ number_format($p->end_meter, 0) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-black text-zinc-900 text-sm bg-zinc-50 border-x border-zinc-200">
                                    +{{ number_format($p->net_meters_produced, 0) }}m
                                </td>
                                @php
                                    $expectedShiftMeters = $p->picks_per_inch > 0 ? ($p->rpm * 720) / ($p->picks_per_inch * 39.37) : 0;
                                    $shiftShortageMeters = max(0, $expectedShiftMeters - $p->net_meters_produced);
                                @endphp
                                <td class="px-4 py-3 text-right font-mono text-zinc-600">
                                    {{ number_format($expectedShiftMeters, 0) }}m est.<br>
                                    <span class="text-[10px] text-amber-700">{{ number_format($shiftShortageMeters, 0) }}m short</span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-zinc-600">
                                    {{ number_format($p->beam_meters_consumed, 0) }}m
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-zinc-500 bg-zinc-50 border-x border-zinc-200">
                                    <span class="font-bold">{{ number_format($p->leakage_waste_meters, 0) }}m</span>
                                    <span class="text-[10px] text-zinc-400 ml-0.5">({{ $p->leakage_percentage }}%)</span>
                                </td>
                                <td class="px-4 py-3 text-center font-mono font-black text-zinc-800">
                                    {{ $p->efficiency_percentage }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center">
                                    <div class="text-zinc-400 text-sm font-medium">No production entries match the criteria</div>
                                    <div class="text-zinc-300 text-xs mt-1">Click "+ Record Loom Production" to log your first shift</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($page === 'history')
                <div class="px-6 py-4 border-t border-zinc-100">{{ $productions->links() }}</div>
            @endif
        </div>
        @endif

    </div>{{-- end main --}}
</div>{{-- end grid --}}


{{-- ============================================================
     MODAL: RECORD LOOM PRODUCTION
============================================================ --}}
<div id="modalRecordProduction" class="fixed inset-0 z-50 hidden bg-zinc-950/60 backdrop-blur-sm overflow-y-auto p-4 sm:p-6">
    <div class="mx-auto my-8 w-full max-w-2xl overflow-y-auto rounded-3xl border border-zinc-200 bg-white shadow-2xl">
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-zinc-100 bg-white/95 px-6 py-4 backdrop-blur">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-zinc-900">
                    <svg class="h-5 w-5 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </span>
                <div>
                    <h3 class="text-base font-extrabold text-zinc-900">Record Loom Shift Production</h3>
                    <p class="text-xs text-zinc-400">Output, beam consumed, leakage & efficiency — calculated live</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalRecordProduction')" class="text-zinc-400 hover:text-zinc-900 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('production.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Production Date *</label>
                    <input type="date" name="production_date" required value="{{ date('Y-m-d') }}"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Shift *</label>
                    <select name="shift" required
                            class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        <option value="Day">Day Shift</option>
                        <option value="Night">Night Shift</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Loom # *</label>
                    <select name="loom_no" id="selectProdLoom" required
                            class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        @foreach($looms as $loom)
                            <option value="{{ $loom }}">{{ $loom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Operator / Weaver Name *</label>
                    <input type="text" name="operator_name" required placeholder="e.g. Tariq Mehmood"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Mounted Sizing Beam *</label>
                    <select name="sizing_beam_id" id="selectProdBeam" required onchange="handleBeamSelected()"
                            class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        <option value="">— Choose Mounted Beam —</option>
                        @foreach($activeBeams as $bm)
                            <option value="{{ $bm->id }}"
                                    data-quality="{{ $bm->yarn_quality }}"
                                    data-remaining="{{ $bm->remaining_length_meters }}"
                                    data-total="{{ $bm->total_length_meters }}"
                                    data-width-multiplier="{{ $bm->sizingSlip?->width_multiplier ?? 1 }}"
                                    data-produced="{{ $bm->productions_sum_net_meters_produced ?? 0 }}"
                                    data-ends="{{ $bm->sizingSlip?->total_ends ?? '' }}"
                                    data-weight-pounds="{{ $bm->sizingSlip?->sizing_weight_pounds ?? '' }}"
                                    data-loom="{{ $bm->assigned_loom_no }}">
                                {{ $bm->beam_no }} ({{ $bm->yarn_quality }}) — {{ number_format($bm->remaining_length_meters) }}m left [{{ $bm->assigned_loom_no ?? 'Ready' }}]
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Fabric Quality Being Woven *</label>
                <select id="inpProdQuality" name="fabric_stock_id" required
                        class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    <option value="">Select fabric quality from Fabric Stock</option>
                    @foreach($fabricQualities as $fabricQuality)
                        <option value="{{ $fabricQuality->id }}">{{ $fabricQuality->quality_name }}</option>
                    @endforeach
                </select>
                @if($fabricQualities->isEmpty())
                    <a href="{{ route('fabric.index') }}" class="mt-1 inline-block text-[10px] font-semibold text-zinc-500 underline">Add a fabric quality in Fabric Stock first</a>
                @endif
            </div>

            {{-- Meter counters panel --}}
            <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-600 block">📊 Meter Counters & Beam Consumption</span>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-500 mb-1">Starting Meter *</label>
                        <input type="number" step="0.01" id="inpStartMeter" name="start_meter"
                               required value="0" min="0" oninput="calculateProductionLeakage()"
                               class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-1">Ending Meter *</label>
                        <input type="number" step="0.01" id="inpEndMeter" name="end_meter"
                               required min="1" placeholder="320" oninput="calculateProductionLeakage()"
                               class="w-full bg-white border border-zinc-400 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-600">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-1">Beam Consumed (m) *</label>
                        <input type="number" step="0.01" id="inpBeamConsumed" name="beam_meters_consumed"
                               required min="1" placeholder="332" oninput="calculateProductionLeakage()"
                               class="w-full bg-white border border-zinc-400 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-600">
                    </div>
                </div>

                {{-- Live calculation panel --}}
                <div class="grid grid-cols-3 gap-3 p-3.5 rounded-xl bg-white border border-zinc-200 text-center">
                    <div>
                        <span class="text-[10px] text-zinc-500 uppercase font-semibold block mb-1">Net Fabric Made</span>
                        <strong id="displayNetProduced" class="text-zinc-900 text-lg font-black font-mono">0m</strong>
                    </div>
                    <div class="border-x border-zinc-100">
                        <span class="text-[10px] text-zinc-600 uppercase font-semibold block mb-1">Production Shortage</span>
                        <strong id="displayLeakageMeters" class="text-zinc-600 text-lg font-black font-mono">0m (0%)</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-zinc-500 uppercase font-semibold block mb-1">Loom Efficiency</span>
                        <strong id="displayEfficiency" class="text-zinc-800 text-lg font-black font-mono">0%</strong>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-white border border-zinc-200 text-center">
                    <div>
                        <span class="text-[10px] text-zinc-500 uppercase font-semibold block mb-1">Estimated 12h Output</span>
                        <strong id="displayExpectedOutput" class="text-zinc-800 text-base font-black font-mono">0m</strong>
                    </div>
                    <div class="border-l border-zinc-100">
                        <span class="text-[10px] text-zinc-500 uppercase font-semibold block mb-1">Output Shortage</span>
                        <strong id="displayOutputShortage" class="text-amber-700 text-base font-black font-mono">0m</strong>
                    </div>
                </div>
                <p id="displayBeamSummary" class="text-[11px] text-zinc-500">Select a sizing beam to see its set length and gate pass totals.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Loom Speed (RPM) *</label>
                    <input type="number" id="inpRpm" name="rpm" required value="350" min="100" max="800"
                           oninput="calculateProductionLeakage()"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Picks Per Inch (PPI) *</label>
                    <input type="number" id="inpPpi" name="picks_per_inch" required value="60" min="20" max="200"
                           oninput="calculateProductionLeakage()"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Shift Notes / Stoppages Remarks</label>
                <input type="text" name="remarks" placeholder="e.g. 15 min warp stop, clean run"
                       class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>

            <div class="pt-3 border-t border-zinc-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalRecordProduction')"
                        class="px-4 py-2 rounded-xl bg-zinc-100 text-zinc-600 text-xs font-semibold">Cancel</button>
                <button type="submit" id="btnSubmitProduction"
                        class="px-6 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-black shadow-sm opacity-50 cursor-not-allowed"
                        disabled>
                    Save Shift Production
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function handleBeamSelected() {
        const select = document.getElementById('selectProdBeam');
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            if (opt.dataset.loom && opt.dataset.loom !== 'Ready') {
                document.getElementById('selectProdLoom').value = opt.dataset.loom;
            }
            const warpBeamLength = parseFloat(opt.dataset.total) || 0;
            const widthMultiplier = parseFloat(opt.dataset.widthMultiplier) || 1;
            const beamLength = warpBeamLength * widthMultiplier;
            const produced = parseFloat(opt.dataset.produced) || 0;
            const productionVariance = beamLength - produced;
            const ends = opt.dataset.ends || 'not recorded';
            const weightLb = parseFloat(opt.dataset.weightPounds) || 0;
            const weightKg = weightLb * 0.45359237;
            document.getElementById('displayBeamSummary').innerText = `${warpBeamLength.toFixed(2)}m warp length × ${widthMultiplier} width(s) = ${beamLength.toFixed(2)}m expected fabric · ${produced.toFixed(2)}m woven so far · ${ends} ends · ${weightLb ? `${weightKg.toFixed(2)} kg net yarn bill (${weightLb.toFixed(2)} lb)` : 'net yarn weight not recorded'}`;
        } else {
            document.getElementById('displayBeamSummary').innerText = 'Select a sizing beam to see its set length and gate pass totals.';
        }
        calculateProductionLeakage();
    }

    function calculateProductionLeakage() {
        const start        = parseFloat(document.getElementById('inpStartMeter').value)    || 0;
        const end          = parseFloat(document.getElementById('inpEndMeter').value)       || 0;
        const beamConsumed = parseFloat(document.getElementById('inpBeamConsumed').value)   || 0;
        const selectedBeam = document.getElementById('selectProdBeam');
        const selectedOption = selectedBeam.options[selectedBeam.selectedIndex];
        const widthMultiplier = parseFloat(selectedOption?.dataset.widthMultiplier) || 1;
        const rpm          = parseFloat(document.getElementById('inpRpm').value)            || 350;
        const ppi          = parseFloat(document.getElementById('inpPpi').value)            || 60;

        const netProduced  = Math.max(0, end - start);
        const expectedFabric = beamConsumed * widthMultiplier;
        const leakage      = Math.max(0, expectedFabric - netProduced);
        const leakagePct   = expectedFabric > 0 ? ((leakage / expectedFabric) * 100).toFixed(1) : '0.0';
        const theoretical  = (rpm * 60 * 12) / (ppi * 39.37);
        const efficiency   = theoretical > 0 ? Math.min(99.9, (netProduced / theoretical) * 100).toFixed(1) : '0.0';
        const outputShortage = Math.max(0, theoretical - netProduced);

        document.getElementById('displayNetProduced').innerText    = netProduced.toFixed(0) + 'm';
        document.getElementById('displayLeakageMeters').innerText  = leakage.toFixed(0) + 'm (' + leakagePct + '%)';
        document.getElementById('displayEfficiency').innerText     = efficiency + '%';
        document.getElementById('displayExpectedOutput').innerText = theoretical.toFixed(0) + 'm';
        document.getElementById('displayOutputShortage').innerText = outputShortage.toFixed(0) + 'm';

        const btn = document.getElementById('btnSubmitProduction');
        if (end > start && beamConsumed > 0) {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
</script>
@endpush
@endsection
