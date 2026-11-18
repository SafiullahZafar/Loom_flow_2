@extends('layouts.app')

@section('title', $accountParty->name.' Account')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <header class="flex flex-col gap-4 border-b border-zinc-200 pb-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('accounts.index') }}" class="text-xs font-bold text-zinc-500 hover:text-zinc-900">← All accounts</a>
            <span class="mt-3 block text-[10px] font-extrabold uppercase tracking-[0.18em] text-zinc-500">{{ ucfirst($accountType) }} account</span>
            <h1 class="mt-1 text-2xl font-black tracking-tight text-zinc-900 sm:text-3xl">{{ $accountParty->name }}</h1>
            <p class="mt-1 text-sm text-zinc-500">{{ $accountParty->phone ?: 'No phone recorded' }}{{ $accountParty->email ? ' · '.$accountParty->email : '' }}</p>
        </div>
        <article class="rounded-2xl border {{ $accountType === 'customer' ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} px-5 py-4">
            <div class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-600">{{ $accountType === 'customer' ? 'Receivable balance' : 'Payable balance' }}</div>
            <div class="mt-1 text-2xl font-black tabular-nums text-zinc-900">Rs. {{ number_format(max(0, $accountBalance), 2) }}</div>
        </article>
    </header>

    @if($accountType === 'customer')
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <article class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
                <div class="border-b border-zinc-100 px-5 py-4"><h2 class="text-sm font-extrabold text-zinc-900">Invoices</h2><p class="mt-1 text-xs text-zinc-500">Invoices billed to this customer and remaining amounts.</p></div>
                <div class="overflow-x-auto"><table class="w-full min-w-[600px] text-left text-xs">
                    <thead class="bg-zinc-50 text-[10px] font-bold uppercase tracking-wider text-zinc-500"><tr><th class="px-5 py-3">Invoice</th><th class="px-4 py-3">Date</th><th class="px-4 py-3 text-right">Total</th><th class="px-5 py-3 text-right">Due</th></tr></thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($invoices as $invoice)
                            <tr><td class="px-5 py-3.5 font-bold text-zinc-900">{{ $invoice->invoice_no }}</td><td class="px-4 py-3.5 text-zinc-600">{{ $invoice->invoice_date?->format('d M Y') }}</td><td class="px-4 py-3.5 text-right font-mono">Rs. {{ number_format((float) $invoice->total, 2) }}</td><td class="px-5 py-3.5 text-right font-mono font-bold">Rs. {{ number_format($invoice->outstanding_amount, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-zinc-500">No invoices for this customer yet.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </article>
            <article class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
                <div class="border-b border-zinc-100 px-5 py-4"><h2 class="text-sm font-extrabold text-zinc-900">Payments received</h2><p class="mt-1 text-xs text-zinc-500">Receipts posted to this customer account.</p></div>
                <div class="divide-y divide-zinc-100">
                    @forelse($receipts as $receipt)
                        <div class="flex items-center justify-between gap-3 px-5 py-3.5"><div><p class="text-xs font-bold text-zinc-900">{{ $receipt->receipt_no }}</p><p class="mt-1 text-[11px] text-zinc-500">{{ $receipt->payment_date?->format('d M Y') }} · {{ $receipt->account?->name ?? 'Account' }}</p></div><span class="font-mono text-xs font-extrabold text-emerald-800">Rs. {{ number_format((float) $receipt->amount, 2) }}</span></div>
                    @empty
                        <p class="px-5 py-8 text-center text-xs text-zinc-500">No receipts for this customer yet.</p>
                    @endforelse
                </div>
            </article>
        </section>
    @else
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <article class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
                <div class="border-b border-zinc-100 px-5 py-4"><h2 class="text-sm font-extrabold text-zinc-900">Yarn purchases</h2><p class="mt-1 text-xs text-zinc-500">Purchases registered against this supplier.</p></div>
                <div class="overflow-x-auto"><table class="w-full min-w-[520px] text-left text-xs"><thead class="bg-zinc-50 text-[10px] font-bold uppercase tracking-wider text-zinc-500"><tr><th class="px-5 py-3">Challan</th><th class="px-4 py-3">Date</th><th class="px-4 py-3 text-right">Bags</th><th class="px-5 py-3 text-right">Amount</th></tr></thead><tbody class="divide-y divide-zinc-100">
                    @forelse($supplierPurchases as $purchase)
                        <tr><td class="px-5 py-3.5 font-bold text-zinc-900">{{ $purchase->challan_no }}</td><td class="px-4 py-3.5 text-zinc-600">{{ $purchase->purchase_date?->format('d M Y') }}</td><td class="px-4 py-3.5 text-right font-mono">{{ number_format((float) $purchase->total_bags, 2) }}</td><td class="px-5 py-3.5 text-right font-mono font-bold">Rs. {{ number_format((float) $purchase->total_amount, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-zinc-500">No purchases for this supplier yet.</td></tr>
                    @endforelse
                </tbody></table></div>
            </article>
            <article class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
                <div class="border-b border-zinc-100 px-5 py-4"><h2 class="text-sm font-extrabold text-zinc-900">Payments made</h2><p class="mt-1 text-xs text-zinc-500">Payments deducted from your cash or bank accounts.</p></div>
                <div class="divide-y divide-zinc-100">
                    @forelse($supplierPayments as $payment)
                        <div class="flex items-center justify-between gap-3 px-5 py-3.5"><div><p class="text-xs font-bold text-zinc-900">{{ $payment->reference ?: 'Supplier payment' }}</p><p class="mt-1 text-[11px] text-zinc-500">{{ $payment->transaction_date->format('d M Y') }} · {{ $payment->account?->name ?? 'Account' }}</p></div><span class="font-mono text-xs font-extrabold text-amber-800">Rs. {{ number_format((float) $payment->amount, 2) }}</span></div>
                    @empty
                        <p class="px-5 py-8 text-center text-xs text-zinc-500">No payments to this supplier yet.</p>
                    @endforelse
                </div>
            </article>
        </section>
    @endif

    <article class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-100 px-5 py-4"><h2 class="text-sm font-extrabold text-zinc-900">Account ledger</h2><p class="mt-1 text-xs text-zinc-500">Latest transactions recorded for {{ $accountParty->name }}.</p></div>
        <div class="overflow-x-auto"><table class="w-full min-w-[760px] text-left text-xs"><thead class="bg-zinc-50 text-[10px] font-bold uppercase tracking-wider text-zinc-500"><tr><th class="px-5 py-3">Date</th><th class="px-4 py-3">Voucher</th><th class="px-4 py-3">Description</th><th class="px-4 py-3 text-right">Debit</th><th class="px-5 py-3 text-right">Credit</th></tr></thead><tbody class="divide-y divide-zinc-100">
            @forelse($ledgerEntries as $entry)
                <tr><td class="whitespace-nowrap px-5 py-3.5 text-zinc-600">{{ $entry->transaction_date->format('d M Y') }}</td><td class="px-4 py-3.5 font-mono text-zinc-700">{{ $entry->voucher_no ?: '—' }}</td><td class="px-4 py-3.5 text-zinc-700">{{ $entry->description }}</td><td class="px-4 py-3.5 text-right font-mono">{{ (float) $entry->debit > 0 ? 'Rs. '.number_format((float) $entry->debit, 2) : '—' }}</td><td class="px-5 py-3.5 text-right font-mono">{{ (float) $entry->credit > 0 ? 'Rs. '.number_format((float) $entry->credit, 2) : '—' }}</td></tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-zinc-500">No ledger entries yet. New invoices, receipts, purchases and payments will appear here.</td></tr>
            @endforelse
        </tbody></table></div>
    </article>
</div>
@endsection
