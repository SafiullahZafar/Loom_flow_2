    <!-- LOOMS & BEAMS -->
    <div class="grid lg:grid-cols-2 gap-6">
        
        <!-- ASSIGN LOOM -->
        <section class="card p-6 space-y-4 shadow-2xs" id="looms">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="text-base font-extrabold text-[#133e2b]">Assign Loom to Order</h2>
                <p class="text-xs text-stone-500">A loom runs one contract on any given day. Switching is allowed from a later date, after recording the beam balance.</p>
            </div>

            <form method="POST" action="{{ route('factory.loom.assign') }}" class="space-y-3.5">
                @csrf
                <label class="block text-xs font-bold text-stone-700">
                    Order
                    <select name="work_order_id" required class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white">
                        @foreach($activeOrders as $o)
                            <option value="{{ $o->id }}">{{ $o->order_no }} — {{ $o->fabricStock?->quality_name }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Loom
                        <select name="loom_no" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white">
                            @foreach($looms as $loom)
                                <option value="{{ $loom }}">{{ $loom }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Effective from
                        <input type="date" required name="start_date" value="{{ date('Y-m-d') }}" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs">
                    </label>
                </div>

                <input name="change_reason" placeholder="Reason for switch (optional)" class="border border-[#e2e8e0] rounded-xl p-2.5 w-full text-xs">
                
                <button class="bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-3 font-bold text-xs w-full shadow-2xs transition-all">
                    Save Loom Allocation
                </button>
            </form>

            <div class="overflow-auto max-h-52 border border-stone-200 rounded-xl mt-3">
                <table class="text-xs w-full">
                    <thead class="bg-stone-50 text-[10px] font-bold uppercase text-stone-500 border-b border-stone-200">
                        <tr>
                            <th class="text-left p-2.5">Loom</th>
                            <th class="text-left p-2.5">Running order</th>
                            <th class="p-2.5 text-right">From</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($assignments as $a)
                            <tr class="hover:bg-stone-50/60">
                                <td class="p-2.5 font-bold text-stone-900">{{ $a->loom_no }}</td>
                                <td class="p-2.5 text-stone-700">{{ $a->order?->order_no }} &middot; {{ $a->order?->fabricStock?->quality_name }}</td>
                                <td class="p-2.5 text-right text-stone-500">{{ $a->start_date }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-3 text-center text-stone-400 italic">No active loom allocations</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- BEAM TRANSFER / SETTLEMENT -->
        <section class="card p-6 space-y-4 shadow-2xs" id="beams">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="text-base font-extrabold text-[#133e2b]">Beam Transfer / Settlement</h2>
                <p class="text-xs text-stone-500">Beam keeps its original order during loom transfers. Change its order only through Settle &amp; Reassign.</p>
            </div>

            <form method="POST" action="{{ route('factory.beam') }}" class="space-y-3.5">
                @csrf
                <label class="block text-xs font-bold text-stone-700">
                    Beam
                    <select id="beam-selector" name="sizing_beam_id" required class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white">
                        @foreach($transferBeams as $b)
                            <option value="{{ $b->id }}" data-weight="{{ $b->initial_net_weight_kg }}" data-total="{{ $b->total_length_meters }}" data-left="{{ $b->remaining_length_meters }}" data-rate="{{ $b->workOrder?->contract?->yarn_warp_rate ?? $b->workOrder?->parta?->warp_rate_per_lb ?? 0 }}">
                                {{ $b->beam_name ?? $b->beam_no }} &middot; Left {{ number_format((float)$b->remaining_length_meters,2) }}m &middot; {{ $b->assigned_loom_no ?? 'Store' }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-xs font-bold text-stone-700">
                    Action
                    <select name="action" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white">
                        <option value="mount">Mount / Transfer to another loom (same order)</option>
                        <option value="unmount">Unmount to Store</option>
                        <option value="settle_reassign">Settle customer yarn and reassign to another order</option>
                        <option value="return_customer">Return customer beam and remaining yarn</option>
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Target order
                        <select name="work_order_id" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white">
                            <option value="">Keep original order</option>
                            @foreach($activeOrders as $o)
                                <option value="{{ $o->id }}">{{ $o->order_no }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Target Loom
                        <select name="loom_no" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white">
                            <option value="">Unmount / no loom</option>
                            @foreach($looms as $loom)
                                <option value="{{ $loom }}">{{ $loom }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Original Beam Weight (kg, if known)
                        <input type="number" step=".001" name="initial_net_weight_kg" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Remaining Yarn (kg, optional override)
                        <input type="number" step=".001" name="remaining_weight_kg" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs">
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Agreed settlement (Rs, optional when predicted)
                        <input name="agreed_settlement" type="number" step=".01" min="0" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Date
                        <input type="date" name="event_date" required value="{{ date('Y-m-d') }}" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs">
                    </label>
                </div>

                <textarea name="notes" placeholder="Notes" rows="2" class="w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b]"></textarea>

                <p id="settlement-preview" class="p-3 bg-emerald-50/70 border border-emerald-200/60 rounded-xl text-xs font-semibold text-[#133e2b]">
                    Predicted settlement: select a beam and weight
                </p>

                <button class="bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-3 font-bold text-xs w-full shadow-2xs transition-all">
                    Record Beam Action
                </button>
            </form>

            <p class="text-[11px] text-stone-400">Predicted settlement = remaining warp kg &times; 2.20462 &times; original contract Taana rate per lb. Final amount may be overridden.</p>

            <div class="max-h-44 overflow-auto text-xs border border-stone-200 rounded-xl p-2 divide-y divide-stone-100">
                @foreach($recentBeams as $e)
                    <p class="py-2 text-stone-600">
                        <strong class="text-stone-900">{{ $e->event_date }}</strong> &middot; {{ $e->beam?->beam_no }} &middot; {{ $e->action }} &middot; {{ number_format((float)$e->remaining_length_meters,2) }}m left
                        @if($e->agreed_settlement !== null)
                            &middot; Rs {{ number_format((float)$e->agreed_settlement,2) }} agreed (predicted Rs {{ number_format((float)$e->predicted_settlement,2) }})
                        @endif
                    </p>
                @endforeach
            </div>
        </section>

    </div>


<script>
(function () {
    const beam = document.getElementById('beam-selector');
    const output = document.getElementById('settlement-preview');
    if (!beam || !output) {
        return;
    }

    const form = beam.closest('form');
    const remainingWeight = form.elements['remaining_weight_kg'];
    const originalWeight = form.elements['initial_net_weight_kg'];

    function updateSettlementPreview() {
        const option = beam.selectedOptions[0];
        const suppliedWeight = Number(remainingWeight.value) || 0;
        const original = Number(originalWeight.value) || Number(option?.dataset.weight) || 0;
        const remainingKg = suppliedWeight || original * (Number(option?.dataset.left) || 0) / (Number(option?.dataset.total) || 1);
        const rate = Number(option?.dataset.rate) || 0;

        output.textContent = remainingKg > 0 && rate > 0
            ? `Predicted settlement: Rs ${(remainingKg * 2.2046226218 * rate).toFixed(2)} (${remainingKg.toFixed(3)} kg × Rs ${rate}/lb).`
            : 'Enter the remaining yarn weight and contract Taana rate to estimate settlement.';
    }

    beam.addEventListener('change', updateSettlementPreview);
    form.querySelectorAll('input').forEach((input) => input.addEventListener('input', updateSettlementPreview));
    updateSettlementPreview();
})();
</script>