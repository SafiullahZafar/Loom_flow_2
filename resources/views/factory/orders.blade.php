@extends('layouts.app')
@section('title', 'Factory Orders & Parta')
@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-6">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#15803d]">WEAVING PRODUCTION</span>
            <h1 class="text-2xl sm:text-3xl font-black text-[#133e2b] tracking-tight">Factory Orders &amp; Parta</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">Create fabric Parta versions and own or job-work production orders.</p>
        </div>
    </header>

    @include('factory._nav')

    <div class="grid lg:grid-cols-2 gap-6" id="setup">
        
        <!-- SAVE QUALITY PARTA -->
        <section class="card p-6 space-y-4 shadow-2xs">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="font-extrabold text-base text-[#133e2b]">Save Quality Parta</h2>
                <p class="text-xs text-stone-500">Saves a new costing version. Local yarn-weight divisor = 800; old order costing is retained.</p>
            </div>
            
            <form method="POST" action="{{ route('factory.parta') }}" class="space-y-3.5" id="parta-form">
                @csrf
                <label class="block text-xs font-bold text-stone-700">
                    Quality
                    <select name="quality_mode" id="parta-quality-mode" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white" required>
                        <option value="existing">Use existing quality</option>
                        <option value="new">Create a quality</option>
                    </select>
                </label>
                <label class="block text-xs font-bold text-stone-700" id="parta-existing-quality-field">
                    Fabric Quality
                    <select name="fabric_stock_id" id="parta-fabric-stock" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                        @foreach($fabrics as $f)
                            <option value="{{ $f->id }}">{{ $f->quality_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="hidden text-xs font-bold text-stone-700" id="parta-new-quality-field">
                    New Quality Name
                    <input name="quality_name" id="parta-quality-name" maxlength="150" placeholder="e.g. 40x40 Cotton Grey" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs">
                </label>

                <div class="grid grid-cols-2 gap-3">
                    @foreach(['read'=>'Read (ends/in)', 'pick'=>'Pick (picks/in)', 'width_inches'=>'Panna/Width (in)', 'warp_count'=>'Taana Count', 'weft_count'=>'Baana Count', 'warp_rate_per_lb'=>'Taana rate (Rs/lb)', 'weft_rate_per_lb'=>'Baana rate (Rs/lb)', 'conversion_rate_per_pick'=>'Mazdoori per pick (Rs)'] as $field=>$label)
                        <label class="block text-xs font-bold text-stone-700">
                            {{ $label }}
                            <input name="{{ $field }}" type="number" step="any" min="0" required value="{{ old($field, 0) }}" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b] parta-field">
                        </label>
                    @endforeach
                </div>

                <div class="bg-emerald-50/70 border border-emerald-200/60 rounded-xl p-3 text-xs text-[#133e2b] space-y-1 font-medium" id="parta-preview">
                    <strong>Preview:</strong> enter read, pick, counts and width to calculate weight and cost.
                </div>

                <button class="w-full rounded-xl bg-[#133e2b] hover:bg-[#1b5239] text-white font-bold p-3 text-xs shadow-2xs transition-all">
                    Save Parta Version
                </button>
            </form>
        </section>

        <!-- CREATE PRODUCTION ORDER -->
        <section class="card p-6 space-y-4 shadow-2xs">
            <div class="pb-3 border-b border-[#e2e8e0]">
                <h2 class="font-extrabold text-base text-[#133e2b]">Create Production Order</h2>
                <p class="text-xs text-stone-500">One order may run on several looms. Own-manufacturing target can be blank.</p>
            </div>

            <form method="POST" action="{{ route('factory.order.store') }}" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <label class="block text-xs font-bold text-stone-700">
                        Order no.
                        <input required name="order_no" value="{{ old('order_no') }}" placeholder="PO-001" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                    </label>
                    <label class="block text-xs font-bold text-stone-700">
                        Type
                        <select name="type" id="order-type" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                            <option value="own">Own Manufacturing</option>
                            <option value="job">Job Work</option>
                        </select>
                    </label>
                </div>

                <label class="block text-xs font-bold text-stone-700">
                    Fabric Quality
                    <select required name="fabric_stock_id" id="order-fabric-stock" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                        @foreach($fabrics as $f)
                            <option value="{{ $f->id }}">{{ $f->quality_name }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Target metres <span id="target-optional-note">(own: optional)</span>
                        <input name="target_meters" id="order-target-meters" type="number" step="0.01" min="0.01" class="mt-1 w-full border border-[#133e2b] rounded-xl p-2.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#133e2b]">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Start Date
                        <input name="start_date" type="date" required value="{{ date('Y-m-d') }}" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                    </label>
                </div>

                <div id="job-order-fields" class="space-y-3 bg-stone-50 rounded-xl p-3.5 border border-stone-200">
                    <label class="block text-xs font-bold text-stone-700">
                        Link job-work contract
                        <select name="existing_contract_id" id="existing-contract-id" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs bg-white">
                            <option value="">Create a new contract</option>
                            @foreach($existingContracts as $c)
                                <option value="{{ $c->id }}" data-customer-id="{{ $c->customer_id }}" data-fabric-stock-id="{{ $c->fabric_stock_id }}" data-target-meters="{{ $c->total_meters }}" data-rate-per-pick="{{ $c->conversion_rate }}">
                                    {{ $c->contract_no }} - {{ $c->customer?->name ?? $c->buyer_name }} - {{ $c->fabricStock?->quality_name }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <div id="new-contract-fields" class="grid grid-cols-2 gap-3">
                        <label class="text-xs font-bold text-stone-700">
                            New Contract No.
                            <input name="contract_no" id="new-contract-no" placeholder="JC-001" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs">
                        </label>
                        <label class="text-xs font-bold text-stone-700">
                            Customer
                            <select name="customer_id" id="order-customer-id" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs bg-white">
                                <option value="">Choose customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}{{ $customer->phone ? ' - '.$customer->phone : '' }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <label class="block text-xs font-bold text-stone-700">
                        Weaving rate per pick (Rs.)
                        <input name="rate_per_pick" id="order-rate-per-pick" type="number" min="0" step="any" placeholder="e.g. 0.50" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs">
                    </label>
                    <p class="text-[11px] text-stone-500">Linked bag receipts and measured kg appear on the order automatically.</p>
                </div>
                <label class="block text-xs font-bold text-stone-700">
                    Notes
                    <textarea name="notes" rows="2" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs focus:outline-none focus:border-[#133e2b]"></textarea>
                </label>
                
                <button class="w-full bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-3 font-bold text-xs shadow-2xs transition-all">
                    Save Production Order
                </button>
            </form>
        </section>

    </div>

    <!-- PRODUCTION ORDERS TABLE -->
    <section class="card p-6 space-y-4 shadow-2xs" id="orders">
        <div class="flex justify-between items-center pb-3 border-b border-[#e2e8e0]">
            <div>
                <h2 class="text-base font-extrabold text-[#133e2b]">Production Orders</h2>
                <p class="text-xs text-stone-500">Completed target = Pakki accepted metres for job work</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="text-left bg-stone-50 border-b border-stone-200 text-stone-600 font-bold uppercase text-[10px] tracking-wider">
                        <th class="p-3">Order / Type</th>
                        <th class="p-3">Party</th>
                        <th class="p-3">Quality</th>
                        <th class="p-3">Target</th>
                        <th class="p-3">Accepted (job)</th>
                        <th class="p-3">Parta cost/m</th>
                        <th class="p-3">Bags received</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Close</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($orders as $o)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <td class="p-3 font-bold text-stone-900">
                                {{ $o->order_no }}
                                <span class="block text-[10px] font-semibold text-stone-400">{{ strtoupper($o->type) }}</span>
                            </td>
                            <td class="p-3 text-stone-700">{{ $o->customer?->name ?? $o->party_name ?? 'Factory' }}</td>
                            <td class="p-3 text-stone-700">{{ $o->fabricStock?->quality_name }}</td>
                            <td class="p-3 text-stone-700">{{ $o->target_meters ? number_format($o->target_meters, 0).'m' : 'Open target' }}</td>
                            <td class="p-3 font-semibold text-stone-800">{{ number_format((float) $o->accepted_meters, 2) }}m</td>
                            <td class="p-3 text-stone-700">Rs {{ number_format((float) $o->parta?->cost_per_meter, 2) }}</td>
                            <td class="p-3 text-stone-700">
                                @php($bagReceipt = $orderBagReceipts->get($o->id))
                                @if($bagReceipt)
                                    {{ number_format((float) $bagReceipt->bags_received, 2) }} bags
                                    <span class="block text-[10px] text-stone-400">{{ number_format((float) $bagReceipt->weight_received_kg, 3) }} kg</span>
                                @else
                                    <span class="text-stone-400">0 bags received</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $o->status === 'active' ? 'bg-[#e6f4ea] text-[#137333]' : 'bg-stone-100 text-stone-700' }}">
                                    {{ ucfirst($o->status) }}
                                </span>
                            </td>
                            <td class="p-3">
                                @if($o->status !== 'closed')
                                    <a href="{{ route('factory.order.closing', $o) }}" class="inline-flex rounded-lg bg-[#133e2b] px-3 py-2 text-[11px] font-bold text-white hover:bg-[#1b5239]">Review &amp; close</a>
                                @else
                                    <a href="{{ route('factory.order.closing', $o) }}" class="text-[11px] font-bold text-[#15803d]">View close summary</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-6 text-center text-stone-400 italic">No production orders yet. Save Parta and create one above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </section>

</div>

<script>
(function () {
    const partaMode = document.getElementById('parta-quality-mode');
    const existingQualityField = document.getElementById('parta-existing-quality-field');
    const existingQualitySelect = document.getElementById('parta-fabric-stock');
    const newQualityField = document.getElementById('parta-new-quality-field');
    const newQualityName = document.getElementById('parta-quality-name');

    function updatePartaQualityMode() {
        const creatingQuality = partaMode.value === 'new';
        existingQualityField.classList.toggle('hidden', creatingQuality);
        existingQualitySelect.disabled = creatingQuality;
        existingQualitySelect.required = !creatingQuality;
        newQualityField.classList.toggle('hidden', !creatingQuality);
        newQualityName.required = creatingQuality;
        newQualityName.disabled = !creatingQuality;
    }

    partaMode.addEventListener('change', updatePartaQualityMode);
    updatePartaQualityMode();

    const orderType = document.getElementById('order-type');
    const jobFields = document.getElementById('job-order-fields');
    const contractSelect = document.getElementById('existing-contract-id');
    const newContractFields = document.getElementById('new-contract-fields');
    const contractNumber = document.getElementById('new-contract-no');
    const customerSelect = document.getElementById('order-customer-id');
    const fabricSelect = document.getElementById('order-fabric-stock');
    const targetMeters = document.getElementById('order-target-meters');
    const ratePerPick = document.getElementById('order-rate-per-pick');
    let manualTargetMeters = targetMeters.value;
    let manualRatePerPick = ratePerPick.value;
    let manualCustomerId = customerSelect.value;
    let hadLinkedContract = false;

    function updateOrderFields() {
        const isJobWork = orderType.value === 'job';
        const selectedContract = contractSelect.selectedOptions[0];
        const hasExistingContract = isJobWork && Boolean(contractSelect.value);

        jobFields.classList.toggle('hidden', !isJobWork);
        contractSelect.disabled = !isJobWork;
        newContractFields.classList.toggle('hidden', hasExistingContract || !isJobWork);
        contractNumber.disabled = !isJobWork || hasExistingContract;
        contractNumber.required = isJobWork && !hasExistingContract;
        customerSelect.disabled = !isJobWork || hasExistingContract;
        customerSelect.required = isJobWork && !hasExistingContract;
        targetMeters.required = isJobWork;
        targetMeters.readOnly = hasExistingContract;
        ratePerPick.readOnly = hasExistingContract;

        if (hasExistingContract) {
            if (!hadLinkedContract) {
                manualTargetMeters = targetMeters.value;
                manualRatePerPick = ratePerPick.value;
                manualCustomerId = customerSelect.value;
            }
            const contractCustomerId = selectedContract.dataset.customerId || '';
            if (contractCustomerId) {
                customerSelect.value = contractCustomerId;
            }
            fabricSelect.value = selectedContract.dataset.fabricStockId || fabricSelect.value;
            targetMeters.value = selectedContract.dataset.targetMeters || '';
            ratePerPick.value = selectedContract.dataset.ratePerPick || '';
        } else if (hadLinkedContract) {
            targetMeters.value = manualTargetMeters;
            ratePerPick.value = manualRatePerPick;
            customerSelect.value = manualCustomerId;
        }

        hadLinkedContract = hasExistingContract;
    }

    contractSelect.addEventListener('change', updateOrderFields);
    orderType.addEventListener('change', updateOrderFields);
    targetMeters.addEventListener('input', () => {
        manualTargetMeters = targetMeters.value;
    });
    ratePerPick.addEventListener('input', () => {
        manualRatePerPick = ratePerPick.value;
    });
    customerSelect.addEventListener('change', () => {
        manualCustomerId = customerSelect.value;
    });
    updateOrderFields();

    const form = document.getElementById('parta-form');
    const preview = document.getElementById('parta-preview');

    function updatePartaPreview() {
        const value = (name) => Number(form.elements[name].value) || 0;
        const read = value('read');
        const pick = value('pick');
        const width = value('width_inches');
        const warpCount = value('warp_count');
        const weftCount = value('weft_count');

        if (!read || !pick || !width || !warpCount || !weftCount) {
            preview.textContent = 'Enter read, pick, counts and width to calculate weight and cost.';
            return;
        }

        const truncate = (amount, places) => Math.floor((amount + 0.000000001) * (10 ** places)) / (10 ** places);
        const warp = truncate(read * width / warpCount * 1.0936 / 800, 4);
        const weft = truncate(pick * width / weftCount * 1.0936 / 800, 4);
        const kilogramsPerMeter = (warp + weft) / 2.2046226218;
        const paisa = (amount) => truncate(amount, 2);
        const cost = paisa(warp * value('warp_rate_per_lb'))
            + paisa(weft * value('weft_rate_per_lb'))
            + paisa(pick * value('conversion_rate_per_pick'));
        const gsm = (read / warpCount + pick / weftCount) * 25.25;

        preview.textContent = `Taana ${warp.toFixed(4)} lb/m · Baana ${weft.toFixed(4)} lb/m · Total ${paisa(kilogramsPerMeter * 1000).toFixed(2)} grams/m · GSM ${gsm.toFixed(2)} · Cost Rs ${cost.toFixed(2)}/m`;
    }

    form.querySelectorAll('input').forEach((input) => input.addEventListener('input', updatePartaPreview));
    updatePartaPreview();
})();
</script>
@endsection
