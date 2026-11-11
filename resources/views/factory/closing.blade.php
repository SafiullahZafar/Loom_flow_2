@extends('layouts.app')

@section('title', 'Close Order '.$order->order_no)

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#15803d]">CONTRACT CLOSE REVIEW</span>
            <h1 class="text-2xl sm:text-3xl font-black text-[#133e2b]">Close {{ $order->order_no }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ strtoupper($order->type) }} · {{ $order->customer?->name ?? $order->party_name ?? 'Factory owned' }} · {{ $order->fabricStock?->quality_name }}</p>
        </div>
        <a href="{{ route('factory.orders') }}" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-bold text-stone-700">Back to orders</a>
    </header>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><strong>Resolve before closing:</strong><ul class="mt-2 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            'Target accepted' => $order->target_meters === null ? 'Open target' : number_format((float) $order->target_meters, 2).' m',
            'Provisional loom output' => number_format($summary['provisional_meters'], 2).' m',
            'Checked Thaans' => number_format($summary['checked_meters'], 2).' m',
            'Kachi dispatched' => number_format($summary['dispatched_meters'], 2).' m',
            'Pakki accepted' => number_format($summary['accepted_meters'], 2).' m',
            'Pakki rejected' => number_format($summary['rejected_meters'], 2).' m',
            'Job-work billed' => 'Rs '.number_format($summary['billed_amount'], 2),
            'Still unpaid' => 'Rs '.number_format($summary['unpaid_amount'], 2),
        ] as $label => $value)
            <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                <div class="text-[10px] font-bold uppercase tracking-wide text-stone-500">{{ $label }}</div>
                <div class="mt-1 text-lg font-black text-stone-900">{{ $value }}</div>
            </div>
        @endforeach
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="card space-y-3 p-5">
            <div><h2 class="font-extrabold text-[#133e2b]">Yarn still in custody</h2><p class="text-xs text-stone-500">Customer balance must be returned or settled before this job can close. Factory yarn remains visible for reconciliation.</p></div>
            <div class="overflow-x-auto"><table class="w-full text-xs"><thead><tr class="border-b text-left text-[10px] uppercase text-stone-500"><th class="py-2">Component</th><th class="py-2">Owner</th><th class="py-2 text-right">Balance kg</th></tr></thead><tbody class="divide-y">
                @forelse($yarnBalances as $balance)
                    <tr><td class="py-2 font-bold">{{ $balance->component === 'warp' ? 'Taana' : 'Baana' }}</td><td class="py-2">{{ ucfirst($balance->owner_type) }}</td><td class="py-2 text-right font-mono {{ $balance->owner_type === 'customer' && (float) $balance->balance_kg > 0.001 ? 'font-bold text-red-700' : '' }}">{{ number_format((float) $balance->balance_kg, 3) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="py-3 text-stone-400">No recorded yarn movements.</td></tr>
                @endforelse
            </tbody></table></div>
        </div>

        <div class="card space-y-3 p-5">
            <div><h2 class="font-extrabold text-[#133e2b]">Remaining beams</h2><p class="text-xs text-stone-500">Unmount mounted beams. Return customer beams or settle and reassign them before closing.</p></div>
            <div class="overflow-x-auto"><table class="w-full text-xs"><thead><tr class="border-b text-left text-[10px] uppercase text-stone-500"><th class="py-2">Beam</th><th class="py-2">Loom / status</th><th class="py-2 text-right">Remaining m</th></tr></thead><tbody class="divide-y">
                @forelse($beams as $beam)
                    <tr><td class="py-2 font-bold">{{ $beam->beam_name ?? $beam->beam_no }}</td><td class="py-2">{{ $beam->assigned_loom_no ?? ucfirst(str_replace('_', ' ', $beam->status)) }}</td><td class="py-2 text-right font-mono">{{ number_format((float) $beam->remaining_length_meters, 2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="py-3 text-stone-400">No remaining beams linked to this order.</td></tr>
                @endforelse
            </tbody></table></div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="card space-y-3 p-5">
            <div><h2 class="font-extrabold text-[#133e2b]">Unresolved returned Thaans</h2><p class="text-xs text-stone-500">Returned rejected pieces need a recorded disposition before close.</p></div>
            <ul class="divide-y text-xs">
                @forelse($returnedThaans as $roll)
                    <li class="flex justify-between gap-3 py-2"><span class="font-bold">{{ $roll->roll_no }}</span><span>{{ ucfirst(str_replace('_', ' ', $roll->status)) }} · {{ number_format((float) $roll->length_meters, 2) }} m</span></li>
                @empty
                    <li class="py-2 text-stone-400">No returned Thaans awaiting disposition.</li>
                @endforelse
            </ul>
        </div>

        <div class="card space-y-3 p-5">
            <div><h2 class="font-extrabold text-[#133e2b]">Job-work invoices</h2><p class="text-xs text-stone-500">Operational closure does not record or imply payment.</p></div>
            <ul class="divide-y text-xs">
                @forelse($unpaidInvoices as $row)
                    <li class="flex justify-between gap-3 py-2"><span class="font-bold">{{ $row['invoice']->invoice_no }} · Total Rs {{ number_format((float) $row['invoice']->total, 2) }}</span><span>Paid Rs {{ number_format($row['paid'], 2) }} · Due Rs {{ number_format($row['due'], 2) }}</span></li>
                @empty
                    <li class="py-2 text-stone-400">No job-work invoices recorded.</li>
                @endforelse
            </ul>
        </div>
    </section>

    @if($order->status !== 'closed')
        <section class="card p-5">
            @if(!$canClose)
                <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">Closing is blocked while customer yarn, unresolved returned Thaans, or a relevant remaining beam is still in factory custody. Use Yarn Khata and Beam Transfer / Settlement to record the resolution, then refresh this review.</div>
            @endif
            <form method="POST" action="{{ route('factory.order.close', $order) }}" class="grid gap-3 sm:grid-cols-[1fr_2fr_auto] sm:items-end">
                @csrf
                <label class="text-xs font-bold text-stone-700">Closing date<input name="closed_on" type="date" value="{{ old('closed_on', date('Y-m-d')) }}" class="mt-1 w-full rounded-xl border border-stone-200 p-2.5" required></label>
                <label class="text-xs font-bold text-stone-700">Reason for early close / notes<input name="closure_reason" value="{{ old('closure_reason') }}" placeholder="Required when below target" class="mt-1 w-full rounded-xl border border-stone-200 p-2.5"></label>
                <button @disabled(!$canClose) class="rounded-xl bg-[#133e2b] px-5 py-3 text-xs font-bold text-white disabled:cursor-not-allowed disabled:bg-stone-300">Close order</button>
            </form>
        </section>
    @else
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-900">Order closed on {{ $order->closed_on }}. This review remains available for the financial and material history.</div>
    @endif
</div>
@endsection
