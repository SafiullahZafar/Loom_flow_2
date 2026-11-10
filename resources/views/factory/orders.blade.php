@extends('layouts.app')
@section('title', 'Factory Orders & Parta')
@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-6">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#15803d]">WEAVING PRODUCTION</span>
            <h1 class="text-2xl sm:text-3xl font-black text-[#133e2b] tracking-tight">Factory Orders, Looms &amp; Parta</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">The master register for own-manufacturing and job-work weaving.</p>
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
                    Existing Fabric Quality
                    <select name="fabric_stock_id" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]" required>
                        @foreach($fabrics as $f)
                            <option value="{{ $f->id }}">{{ $f->quality_name }}</option>
                        @endforeach
                    </select>
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
                <p class="text-[11px] text-stone-400">To create a new quality first, use <a class="underline font-bold text-stone-600 hover:text-[#133e2b]" href="{{ route('fabric.index') }}">Fabric Stock &rarr; New Quality</a>.</p>
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
                    <select required name="fabric_stock_id" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs bg-white focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                        @foreach($fabrics as $f)
                            <option value="{{ $f->id }}">{{ $f->quality_name }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-xs font-bold text-stone-700">
                        Target metres (own: optional)
                        <input name="target_meters" type="number" step="0.01" min="0.01" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                    </label>
                    <label class="text-xs font-bold text-stone-700">
                        Start Date
                        <input name="start_date" type="date" required value="{{ date('Y-m-d') }}" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2.5 text-xs focus:outline-none focus:border-[#133e2b] focus:ring-1 focus:ring-[#133e2b]">
                    </label>
                </div>

                <div id="job-order-fields" class="space-y-3 bg-stone-50 rounded-xl p-3.5 border border-stone-200">
                    <p class="text-[11px] text-stone-500">Fill these for job work. To link an existing Sales Contract, select it and match its fabric quality.</p>
                    <label class="block text-xs font-bold text-stone-700">
                        Existing Sales Contract (optional)
                        <select name="existing_contract_id" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs bg-white">
                            <option value="">Create a new contract below</option>
                            @foreach($existingContracts as $c)
                                <option value="{{ $c->id }}">{{ $c->contract_no }} — {{ $c->buyer_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="text-xs font-bold text-stone-700">
                            New Contract No.
                            <input name="contract_no" placeholder="JC-001" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs">
                        </label>
                        <label class="text-xs font-bold text-stone-700">
                            Customer / Party
                            <input name="party_name" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs">
                        </label>
                    </div>
                    <label class="block text-xs font-bold text-stone-700">
                        Weaving rate per pick (Rs.)
                        <input name="rate_per_pick" type="number" min="0" step="any" placeholder="e.g. 0.50" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs">
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="text-xs font-bold text-stone-700">
                            Taana supplied by
                            <select name="warp_supply" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs bg-white">
                                <option value="customer">Customer</option>
                                <option value="factory">Factory</option>
                                <option value="mixed">Both</option>
                            </select>
                        </label>
                        <label class="text-xs font-bold text-stone-700">
                            Baana supplied by
                            <select name="weft_supply" class="mt-1 w-full border border-[#e2e8e0] rounded-xl p-2 text-xs bg-white">
                                <option value="customer">Customer</option>
                                <option value="factory">Factory</option>
                                <option value="mixed">Both</option>
                            </select>
                        </label>
                    </div>
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
                            <td class="p-3 text-stone-700">{{ $o->party_name ?? 'Factory' }}</td>
                            <td class="p-3 text-stone-700">{{ $o->fabricStock?->quality_name }}</td>
                            <td class="p-3 text-stone-700">{{ $o->target_meters ? number_format($o->target_meters, 0).'m' : 'Open target' }}</td>
                            <td class="p-3 font-semibold text-stone-800">{{ number_format((float) $o->accepted_meters, 2) }}m</td>
                            <td class="p-3 text-stone-700">Rs {{ number_format((float) $o->parta?->cost_per_meter, 2) }}</td>
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
                            <td colspan="8" class="p-6 text-center text-stone-400 italic">No production orders yet. Save Parta and create one above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </section>

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
                            @for($i=1;$i<=24;$i++)
                                <option>{{ sprintf('Loom-%02d',$i) }}</option>
                            @endfor
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
                        @foreach($beams as $b)
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
                            @for($i=1;$i<=24;$i++)
                                <option>{{ sprintf('Loom-%02d',$i) }}</option>
                            @endfor
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

    <!-- SUTAR / TAANA / BAANA MOVEMENT KHATA -->
    <section class="card p-6 space-y-4 shadow-2xs" id="yarn">
        <div class="pb-3 border-b border-[#e2e8e0]">
            <h2 class="text-base font-extrabold text-[#133e2b]">Sutar / Taana / Baana Movement Khata</h2>
            <p class="text-xs text-stone-500">Record customer yarn and order adjustments here. For factory yarn, enter the issue or return once in Bags and link it to this order with its measured kg; the order khata is updated automatically.</p>
        </div>

        <form method="POST" action="{{ route('factory.yarn') }}" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            @csrf
            <label class="text-xs font-bold text-stone-700">
                Order
                <select name="work_order_id" required class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full bg-white">
                    @foreach($activeOrders as $o)
                        <option value="{{ $o->id }}">{{ $o->order_no }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-xs font-bold text-stone-700">
                Material
                <select name="component" class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full bg-white">
                    <option value="warp">Taana / Warp</option>
                    <option value="weft">Baana / Weft</option>
                </select>
            </label>

            <label class="text-xs font-bold text-stone-700">
                Owner
                <select name="owner_type" class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full bg-white">
                    <option value="customer">Customer</option>
                    <option value="factory">Factory (receipts / adjustments)</option>
                </select>
            </label>

            <label class="text-xs font-bold text-stone-700">
                Movement
                <select name="movement_type" class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full bg-white">
                    <option value="receipt">Receipt</option>
                    <option value="issue">Issued / Consumed</option>
                    <option value="return">Returned to Order Stock</option>
                    <option value="adjustment_in">Adjustment (+)</option>
                    <option value="adjustment_out">Adjustment (−)</option>
                </select>
            </label>

            <label class="text-xs font-bold text-stone-700">
                Net weight (kg)
                <input required name="weight_kg" type="number" min=".001" step=".001" class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full">
            </label>

            <label class="text-xs font-bold text-stone-700">
                Bags (optional)
                <input name="bags" type="number" min="0" step=".01" class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full">
            </label>

            <label class="text-xs font-bold text-stone-700">
                Count / Lot
                <input name="yarn_count" placeholder="20/1" class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full">
            </label>

            <label class="text-xs font-bold text-stone-700">
                Lot No
                <input name="lot_no" class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full">
            </label>

            <label class="text-xs font-bold text-stone-700">
                Reference No
                <input name="reference_no" class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full">
            </label>

            <label class="text-xs font-bold text-stone-700">
                Date
                <input type="date" name="movement_date" value="{{ date('Y-m-d') }}" required class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full">
            </label>

            <label class="text-xs font-bold text-stone-700">
                Where is the yarn going?
                <select name="destination_type" class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full bg-white">
                    <option value="factory_store">Factory Store</option>
                    <option value="sizing">Sizing Contractor</option>
                    <option value="loom">Loom</option>
                </select>
            </label>

            <label class="text-xs font-bold text-stone-700">
                Destination Name
                <input name="destination_name" placeholder="Sizing contractor / destination" class="mt-1 border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full">
            </label>

            <div class="sm:col-span-2 lg:col-span-3">
                <input name="notes" placeholder="Notes / Sizing / Weaver details" class="border border-[#e2e8e0] rounded-xl p-2.5 text-xs w-full">
            </div>

            <div class="sm:col-span-2 lg:col-span-1">
                <button class="w-full bg-[#133e2b] hover:bg-[#1b5239] text-white rounded-xl p-2.5 font-bold text-xs shadow-2xs transition-all h-full">
                    Save Movement
                </button>
            </div>
        </form>

        <div class="overflow-x-auto mt-4 pt-4 border-t border-[#e2e8e0]">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-stone-600 mb-2.5">Order-wise yarn balances</h3>
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-stone-600 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-2.5 text-left">Order</th>
                        <th class="p-2.5 text-left">Material</th>
                        <th class="p-2.5 text-left">Owner</th>
                        <th class="p-2.5 text-right">Balance (kg)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($yarnBalances as $y)
                        <tr class="hover:bg-stone-50/60">
                            <td class="p-2.5 font-bold text-stone-900">{{ $activeOrders->firstWhere('id',$y->work_order_id)?->order_no ?? '#'.$y->work_order_id }}</td>
                            <td class="p-2.5 text-stone-700">{{ $y->component }}</td>
                            <td class="p-2.5 text-stone-700">{{ $y->owner_type }}</td>
                            <td class="text-right p-2.5 font-black text-[#133e2b] tabular-nums">{{ number_format((float)$y->balance_kg,3) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-4 text-center text-stone-400 italic">No yarn transactions recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
(function(){
  const type=document.getElementById('order-type'), job=document.getElementById('job-order-fields');
  function update(){job.style.display=type.value==='job'?'block':'none'}; type.addEventListener('change',update); update();
  const f=document.getElementById('parta-form'), display=document.getElementById('parta-preview');
  function preview(){const v=n=>Number(f.elements[n].value)||0;const read=v('read'),pick=v('pick'),width=v('width_inches'),warp=v('warp_count'),weft=v('weft_count');
   if(!read||!pick||!width||!warp||!weft){display.textContent='Enter fabric specification to preview weight and cost.';return;}
   const tr=x=>Math.floor((x+0.000000001)*10000)/10000;
   const a=tr(read*width/warp*1.0936/800),b=tr(pick*width/weft*1.0936/800),kg=(a+b)/2.2046226218;
   const cost=a*v('warp_rate_per_lb')+b*v('weft_rate_per_lb')+pick*v('conversion_rate_per_pick');
   display.textContent='Taana '+a.toFixed(4)+' lb/m · Baana '+b.toFixed(4)+' lb/m · Total '+(kg*1000).toFixed(2)+' grams/m · GSM '+(kg*1000/(width*.0254)).toFixed(2)+' · Cost Rs '+cost.toFixed(2)+'/m'; }
  f.querySelectorAll('input').forEach(el=>el.addEventListener('input',preview));preview();
  const beam=document.getElementById('beam-selector'),out=document.getElementById('settlement-preview');
  const beamForm=beam.closest('form');function settlement(){const op=beam.selectedOptions[0];const supplied=Number(beamForm.elements['remaining_weight_kg'].value)||0;
    const original=Number(beamForm.elements['initial_net_weight_kg'].value)||Number(op?.dataset.weight)||0;
    const kg=supplied||original*(Number(op?.dataset.left)||0)/(Number(op?.dataset.total)||1);
    const rate=Number(op?.dataset.rate)||0;
    out.textContent=kg>0&&rate>0?'Predicted: Rs '+(kg*2.2046226218*rate).toFixed(2)+' ('+kg.toFixed(3)+' kg × '+rate+' Rs/lb). You can enter a different agreed amount.':'Predicted: supply the beam weight and ensure the original contract has a Taana rate; otherwise enter the final agreed amount.';}
  beam.addEventListener('change',settlement);beamForm.querySelectorAll('input').forEach(el=>el.addEventListener('input',settlement));settlement();
})();
</script>
@endsection
