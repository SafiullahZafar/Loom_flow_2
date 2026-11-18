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
            <div class="mt-1 text-2xl font-black tabular-nums text-zinc-900">Rs. {{ number_format($accountBalance, 2) }}</div>
        </article>
    </header>

    <section class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-sm font-extrabold text-zinc-900">{{ $accountType === 'customer' ? 'Record customer payment' : 'Record supplier payment' }}</h2>
            <p class="mt-1 text-xs text-zinc-500">{{ $accountType === 'customer' ? 'Record money received from this customer. The payment will reduce their receivable balance.' : 'Record money paid to this supplier. The payment will reduce their payable balance.' }}</p>
        </div>
        @if($activeFinancialAccounts->isEmpty())
            <p class="rounded-xl border border-dashed border-amber-300 bg-amber-50 px-4 py-3 text-xs text-amber-900">Add an active cash or bank account before recording a payment.</p>
        @elseif($accountType === 'customer')
            <form method="POST" action="{{ route('invoices.payment.store') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $accountParty->id }}">
                <input type="hidden" name="allocation_mode" value="oldest">
                <input type="hidden" name="return_to_account" value="1">
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-2">Receipt number
                    <input name="receipt_no" required maxlength="100" value="{{ old('receipt_no', 'RCPT-'.now()->format('ymdHis').'-'.strtoupper(\Illuminate\Support\Str::random(4))) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs">
                </label>
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-2">Receive into
                    <select name="financial_account_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs">
                        <option value="">Choose cash or bank account</option>
                        @foreach($activeFinancialAccounts as $account)
                            <option value="{{ $account->id }}" @selected(old('financial_account_id') == $account->id)>{{ $account->name }} · {{ ucfirst($account->type) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-[11px] font-bold text-zinc-600">Payment date<input name="payment_date" type="date" required value="{{ old('payment_date', now()->toDateString()) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600">Amount (PKR)<input name="amount" type="number" min="0.01" step="0.01" required value="{{ old('amount') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600">Method
                    <select name="method" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs">
                        <option value="cash" @selected(old('method') === 'cash')>Cash</option><option value="bank_transfer" @selected(old('method') === 'bank_transfer')>Bank transfer</option><option value="cheque" @selected(old('method') === 'cheque')>Cheque</option><option value="card" @selected(old('method') === 'card')>Card</option><option value="other" @selected(old('method') === 'other')>Other</option>
                    </select>
                </label>
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-2">Reference<input name="reference" maxlength="120" value="{{ old('reference') }}" placeholder="Transfer reference, cheque number, etc." class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-3">Notes<input name="notes" maxlength="2000" value="{{ old('notes') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <div class="flex items-end xl:col-span-1"><button type="submit" class="w-full rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-extrabold text-white hover:bg-zinc-700">Record received payment</button></div>
            </form>
        @else
            <form method="POST" action="{{ route('accounts.supplier-payment.store') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">
                @csrf
                <input type="hidden" name="supplier_id" value="{{ $accountParty->id }}">
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-2">Pay from
                    <select name="financial_account_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs">
                        <option value="">Choose cash or bank account</option>
                        @foreach($activeFinancialAccounts as $account)
                            <option value="{{ $account->id }}" @selected(old('financial_account_id') == $account->id)>{{ $account->name }} · {{ ucfirst($account->type) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-[11px] font-bold text-zinc-600">Payment date<input name="payment_date" type="date" required value="{{ old('payment_date', now()->toDateString()) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600">Amount (PKR)<input name="amount" type="number" min="0.01" step="0.01" required value="{{ old('amount') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-2">Reference<input name="reference" maxlength="120" value="{{ old('reference') }}" placeholder="Payment reference or cheque number" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-5">Notes<input name="notes" maxlength="1000" value="{{ old('notes') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <div class="flex items-end xl:col-span-1"><button type="submit" class="w-full rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-extrabold text-white hover:bg-zinc-700">Record payment made</button></div>
            </form>
        @endif
    </section>

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
        <div class="overflow-x-auto"><table class="w-full min-w-[920px] text-left text-xs"><thead class="bg-zinc-50 text-[10px] font-bold uppercase tracking-wider text-zinc-500"><tr><th class="px-5 py-3">Date</th><th class="px-4 py-3">Voucher</th><th class="px-4 py-3">Description</th><th class="px-4 py-3 text-right">Debit</th><th class="px-4 py-3 text-right">Credit</th><th class="px-5 py-3 text-right">Running balance</th></tr></thead><tbody class="divide-y divide-zinc-100">
            @forelse($ledgerEntries as $entry)
                <tr><td class="whitespace-nowrap px-5 py-3.5 text-zinc-600">{{ $entry->transaction_date->format('d M Y') }}</td><td class="px-4 py-3.5 font-mono text-zinc-700">{{ $entry->voucher_no ?: '—' }}</td><td class="px-4 py-3.5 text-zinc-700">{{ $entry->description }}</td><td class="px-4 py-3.5 text-right font-mono">{{ (float) $entry->debit > 0 ? 'Rs. '.number_format((float) $entry->debit, 2) : '—' }}</td><td class="px-4 py-3.5 text-right font-mono">{{ (float) $entry->credit > 0 ? 'Rs. '.number_format((float) $entry->credit, 2) : '—' }}</td><td class="px-5 py-3.5 text-right font-mono font-extrabold">Rs. {{ number_format($entry->running_balance, 2) }}</td></tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-zinc-500">No ledger entries yet. New invoices, receipts, purchases and payments will appear here.</td></tr>
            @endforelse
        </tbody></table></div>
    </article>
</div>
@endsection
