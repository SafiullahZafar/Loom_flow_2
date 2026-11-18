@extends('layouts.app')

@section('title', 'Accounts Overview')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <header class="flex flex-col gap-4 border-b border-zinc-200 pb-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-zinc-500">Finance overview</span>
            <h1 class="mt-1 text-2xl font-black tracking-tight text-zinc-900 sm:text-3xl">Accounts</h1>
            <p class="mt-1 text-sm text-zinc-500">Customer balances, supplier payables, invoices and received payments in one place.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-zinc-700">
                <span aria-hidden="true">＋</span> Create invoice or receipt
            </a>
            <a href="{{ route('ledgers.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-xs font-bold text-zinc-700 transition hover:bg-zinc-50">
                General ledger <span aria-hidden="true">↗</span>
            </a>
        </div>
    </header>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Account summaries">
        <article class="rounded-2xl border border-emerald-200 border-l-4 border-l-emerald-500 bg-emerald-50/70 p-5 shadow-sm">
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-800">Customer receivables</div>
            <div class="mt-2 text-2xl font-black tabular-nums text-emerald-950">Rs. {{ number_format($receivables, 2) }}</div>
            <p class="mt-1 text-xs text-emerald-800/75">Outstanding customer balances</p>
        </article>
        <article class="rounded-2xl border border-amber-200 border-l-4 border-l-amber-500 bg-amber-50/70 p-5 shadow-sm">
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-amber-800">Supplier payables</div>
            <div class="mt-2 text-2xl font-black tabular-nums text-amber-950">Rs. {{ number_format($payables, 2) }}</div>
            <p class="mt-1 text-xs text-amber-800/75">Outstanding supplier and mill balances</p>
        </article>
        <article class="rounded-2xl border border-sky-200 border-l-4 border-l-sky-500 bg-sky-50/70 p-5 shadow-sm">
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-sky-800">Open invoices</div>
            <div class="mt-2 text-2xl font-black tabular-nums text-sky-950">Rs. {{ number_format($openInvoiceBalance, 2) }}</div>
            <p class="mt-1 text-xs text-sky-800/75">{{ $openInvoiceCount }} invoice{{ $openInvoiceCount === 1 ? '' : 's' }} with a balance due</p>
        </article>
        <article class="rounded-2xl border border-violet-200 border-l-4 border-l-violet-500 bg-violet-50/70 p-5 shadow-sm">
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-violet-800">Received this month</div>
            <div class="mt-2 text-2xl font-black tabular-nums text-violet-950">Rs. {{ number_format($monthReceipts, 2) }}</div>
            <p class="mt-1 text-xs text-violet-800/75">Customer payments recorded this month</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-2" aria-label="Create party accounts">
        <form method="POST" action="{{ route('invoices.customer.store') }}" class="space-y-3 rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6">
            @csrf
            <div><h2 class="text-sm font-extrabold text-zinc-900">Add customer account</h2><p class="mt-1 text-xs text-zinc-500">Create the customer and their separate ledger account together.</p></div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="text-[11px] font-bold text-zinc-600 sm:col-span-2">Customer name<input name="name" required maxlength="120" value="{{ old('name') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600">Phone<input name="phone" maxlength="40" value="{{ old('phone') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600">Email<input name="email" type="email" maxlength="120" value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600">Opening balance (PKR)<input name="opening_balance" type="number" min="0" step="0.01" value="{{ old('opening_balance', 0) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600">Address<input name="address" maxlength="500" value="{{ old('address') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
            </div>
            <button class="rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-extrabold text-white hover:bg-zinc-700">Create customer account</button>
        </form>
        <form method="POST" action="{{ route('accounts.suppliers.store') }}" class="space-y-3 rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6">
            @csrf
            <div><h2 class="text-sm font-extrabold text-zinc-900">Add supplier account</h2><p class="mt-1 text-xs text-zinc-500">Each supplier or mill gets a separate payable ledger account.</p></div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="text-[11px] font-bold text-zinc-600 sm:col-span-2">Supplier name<input name="name" required maxlength="150" value="{{ old('name') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600">Account type<select name="type" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs"><option value="yarn_supplier">Yarn supplier</option><option value="sizing_mill" @selected(old('type') === 'sizing_mill')>Sizing mill</option><option value="parts_vendor" @selected(old('type') === 'parts_vendor')>Parts vendor</option></select></label>
                <label class="text-[11px] font-bold text-zinc-600">Opening payable (PKR)<input name="opening_payable" type="number" min="0" step="0.01" value="{{ old('opening_payable', 0) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600">Phone<input name="phone" maxlength="60" value="{{ old('phone') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600">Email<input name="email" type="email" maxlength="150" value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
                <label class="text-[11px] font-bold text-zinc-600 sm:col-span-2">Address<input name="address" maxlength="1000" value="{{ old('address') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs"></label>
            </div>
            <button class="rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-extrabold text-white hover:bg-zinc-700">Create supplier account</button>
        </form>
    </section>

    <section id="record-supplier-payment" class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-sm font-extrabold text-zinc-900">Record supplier payment</h2>
            <p class="mt-1 text-xs text-zinc-500">Save a payment against an existing supplier ledger and cash or bank account.</p>
        </div>
        @if($supplierAccounts->isEmpty())
            <p class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 px-4 py-3 text-xs text-zinc-600">Supplier accounts appear here after a purchase or supplier transaction is posted.</p>
        @elseif($activeFinancialAccounts->isEmpty())
            <p class="rounded-xl border border-dashed border-amber-300 bg-amber-50 px-4 py-3 text-xs text-amber-900">Add an active cash or bank account in Invoices → Accounts before recording a supplier payment.</p>
        @else
            <form method="POST" action="{{ route('accounts.supplier-payment.store') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">
                @csrf
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-3">Supplier account
                    <select name="supplier_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs text-zinc-900 focus:border-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-200">
                        <option value="">Choose supplier account</option>
                        @foreach($supplierAccounts as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }} · {{ ucfirst(str_replace('_', ' ', $supplier->type)) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-2">Pay from
                    <select name="financial_account_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs text-zinc-900 focus:border-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-200">
                        <option value="">Select cash or bank account</option>
                        @foreach($activeFinancialAccounts as $account)
                            <option value="{{ $account->id }}" @selected(old('financial_account_id') == $account->id)>{{ $account->name }} · {{ ucfirst($account->type) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-[11px] font-bold text-zinc-600">Payment date
                    <input type="date" name="payment_date" required value="{{ old('payment_date', now()->toDateString()) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs text-zinc-900 focus:border-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-200">
                </label>
                <label class="text-[11px] font-bold text-zinc-600">Amount (PKR)
                    <input type="number" name="amount" min="0.01" step="0.01" required value="{{ old('amount') }}" placeholder="0.00" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs font-mono text-zinc-900 focus:border-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-200">
                </label>
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-2">Reference
                    <input name="reference" maxlength="120" value="{{ old('reference') }}" placeholder="Payment reference or cheque number" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs text-zinc-900 focus:border-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-200">
                </label>
                <label class="text-[11px] font-bold text-zinc-600 xl:col-span-3">Notes
                    <input name="notes" maxlength="1000" value="{{ old('notes') }}" placeholder="Optional payment notes" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs text-zinc-900 focus:border-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-200">
                </label>
                <div class="flex items-end xl:col-span-1">
                    <button type="submit" class="w-full rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-extrabold text-white shadow-sm hover:bg-zinc-700">Save payment</button>
                </div>
            </form>
        @endif
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <article class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-extrabold text-zinc-900">Customer accounts</h2>
                    <p class="mt-1 text-xs text-zinc-500">Receivable balances from the customer ledgers.</p>
                </div>
                <a href="{{ route('invoices.customers') }}" class="shrink-0 text-xs font-bold text-zinc-600 hover:text-zinc-900">All customers ↗</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-xs">
                    <thead class="bg-zinc-50 text-[10px] font-bold uppercase tracking-wider text-zinc-500">
                        <tr><th class="px-5 py-3">Customer</th><th class="px-4 py-3 text-right">Invoices</th><th class="px-5 py-3 text-right">Balance due</th></tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($customerAccounts as $customer)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('accounts.customers.show', $customer) }}" class="font-bold text-zinc-900 hover:underline">{{ $customer->name }}</a>
                                    <span class="mt-0.5 block text-[10px] text-zinc-500">{{ $customer->phone ?: 'Customer account' }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-zinc-600">{{ $customer->invoices_count }}</td>
                                <td class="px-5 py-3.5 text-right font-mono font-extrabold {{ $customer->receivable_balance > 0 ? 'text-emerald-800' : 'text-zinc-500' }}">Rs. {{ number_format($customer->receivable_balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-8 text-center text-zinc-500">No customer accounts yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-extrabold text-zinc-900">Supplier accounts</h2>
                    <p class="mt-1 text-xs text-zinc-500">Payables from yarn, sizing-mill and parts ledgers.</p>
                </div>
                <a href="{{ route('ledgers.index', ['ledger' => 'parties']) }}" class="shrink-0 text-xs font-bold text-zinc-600 hover:text-zinc-900">Party ledgers ↗</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-xs">
                    <thead class="bg-zinc-50 text-[10px] font-bold uppercase tracking-wider text-zinc-500">
                        <tr><th class="px-5 py-3">Supplier / party</th><th class="px-4 py-3">Account type</th><th class="px-5 py-3 text-right">Payable</th></tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($supplierAccounts as $supplier)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('accounts.suppliers.show', $supplier) }}" class="font-bold text-zinc-900 hover:underline">{{ $supplier->name }}</a>
                                    <span class="mt-0.5 block text-[10px] text-zinc-500">{{ $supplier->last_activity ? \Illuminate\Support\Carbon::parse($supplier->last_activity)->format('d M Y') : 'No activity date' }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-zinc-600">{{ ['yarn_supplier' => 'Yarn supplier', 'sizing_mill' => 'Sizing mill', 'parts_vendor' => 'Parts vendor'][$supplier->type] ?? ucfirst(str_replace('_', ' ', $supplier->type)) }}</td>
                                <td class="px-5 py-3.5 text-right font-mono font-extrabold {{ $supplier->payable_balance > 0 ? 'text-amber-800' : 'text-zinc-500' }}">Rs. {{ number_format(max(0, (float) $supplier->payable_balance), 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-8 text-center text-zinc-500">No supplier balances have been posted yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>


</div>
@endsection
