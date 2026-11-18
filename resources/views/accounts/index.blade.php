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
        <article class="group rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div><div class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-emerald-800">Customer receivables</div><div class="mt-3 text-2xl font-black tabular-nums tracking-tight text-emerald-950">Rs. {{ number_format($receivables, 2) }}</div></div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800" aria-hidden="true"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m12-13a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm2 5a4 4 0 0 1 4 4v4"/></svg></div>
            </div>
            <p class="mt-3 border-t border-emerald-100 pt-3 text-xs font-medium text-zinc-500">Outstanding customer balances</p>
        </article>
        <article class="group rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div><div class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-amber-800">Supplier payables</div><div class="mt-3 text-2xl font-black tabular-nums tracking-tight text-amber-950">Rs. {{ number_format($payables, 2) }}</div></div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-800" aria-hidden="true"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01m8-4v.01m0 3v.01"/></svg></div>
            </div>
            <p class="mt-3 border-t border-amber-100 pt-3 text-xs font-medium text-zinc-500">Outstanding supplier and mill balances</p>
        </article>
        <article class="group rounded-2xl border border-sky-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div><div class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-sky-800">Open invoices</div><div class="mt-3 text-2xl font-black tabular-nums tracking-tight text-sky-950">Rs. {{ number_format($openInvoiceBalance, 2) }}</div></div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-800" aria-hidden="true"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6"/></svg></div>
            </div>
            <p class="mt-3 border-t border-sky-100 pt-3 text-xs font-medium text-zinc-500">{{ $openInvoiceCount }} invoice{{ $openInvoiceCount === 1 ? '' : 's' }} with a balance due</p>
        </article>
        <article class="group rounded-2xl border border-violet-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div><div class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-violet-800">Received this month</div><div class="mt-3 text-2xl font-black tabular-nums tracking-tight text-violet-950">Rs. {{ number_format($monthReceipts, 2) }}</div></div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-800" aria-hidden="true"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4m-4 8h16"/></svg></div>
            </div>
            <p class="mt-3 border-t border-violet-100 pt-3 text-xs font-medium text-zinc-500">Customer payments recorded this month</p>
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

    <section id="record-party-payment" class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-sm font-extrabold text-zinc-900">Record customer or supplier payment</h2>
            <p class="mt-1 text-xs text-zinc-500">Choose the party account and record money received from a customer or paid to a supplier.</p>
        </div>
        @if($activeFinancialAccounts->isEmpty())
            <p class="rounded-xl border border-dashed border-amber-300 bg-amber-50 px-4 py-3 text-xs text-amber-900">Add an active cash or bank account before recording a party payment.</p>
        @elseif($customerAccounts->isEmpty() && $supplierAccounts->isEmpty())
            <p class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 px-4 py-3 text-xs text-zinc-600">Create a customer or supplier account above before recording a payment.</p>
        @else
            <form id="accounts-party-payment-form" method="POST" action="{{ old('payment_kind', $supplierAccounts->isNotEmpty() ? 'paid' : 'received') === 'received' ? route('invoices.payment.store') : route('accounts.supplier-payment.store') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">
                @csrf
                <label class="text-[11px] font-bold text-zinc-600">Payment direction
                    <select id="accounts-payment-kind" name="payment_kind" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs text-zinc-900">
                        @if($supplierAccounts->isNotEmpty())<option value="paid" @selected(old('payment_kind', $supplierAccounts->isNotEmpty() ? 'paid' : 'received') === 'paid')>Pay supplier</option>@endif
                        @if($customerAccounts->isNotEmpty())<option value="received" @selected(old('payment_kind', $supplierAccounts->isNotEmpty() ? 'paid' : 'received') === 'received')>Receive from customer</option>@endif
                    </select>
                </label>
                <label id="accounts-supplier-field" class="text-[11px] font-bold text-zinc-600 sm:col-span-2">Supplier account
                    <select name="supplier_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs text-zinc-900">
                        <option value="">Choose supplier</option>
                        @foreach($supplierAccounts as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }} · {{ ucfirst(str_replace('_', ' ', $supplier->type)) }}</option>
                        @endforeach
                    </select>
                </label>
                <label id="accounts-customer-field" class="hidden text-[11px] font-bold text-zinc-600 sm:col-span-2">Customer account
                    <select name="customer_id" required disabled class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs text-zinc-900">
                        <option value="">Choose customer</option>
                        @foreach($customerAccounts as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-[11px] font-bold text-zinc-600 sm:col-span-2">
                    <span id="accounts-financial-account-label">Pay from</span>
                    <select name="financial_account_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs text-zinc-900">
                        <option value="">Choose cash or bank account</option>
                        @foreach($activeFinancialAccounts as $account)
                            <option value="{{ $account->id }}" data-account-type="{{ $account->type }}" @selected(old('financial_account_id') == $account->id)>{{ $account->name }} · {{ ucfirst($account->type) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-[11px] font-bold text-zinc-600">Date
                    <input type="date" name="payment_date" required value="{{ old('payment_date', now()->toDateString()) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs text-zinc-900">
                </label>
                <label class="text-[11px] font-bold text-zinc-600">Amount (PKR)
                    <input type="number" name="amount" min="0.01" step="0.01" required value="{{ old('amount') }}" placeholder="0.00" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs font-mono text-zinc-900">
                </label>
                <label id="accounts-receipt-number-field" class="hidden text-[11px] font-bold text-zinc-600 sm:col-span-2">Receipt number
                    <input name="receipt_no" maxlength="100" disabled value="{{ old('receipt_no', 'RCPT-'.now()->format('ymdHis').'-'.strtoupper(\Illuminate\Support\Str::random(4))) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs text-zinc-900">
                </label>
                <label id="accounts-payment-method-field" class="hidden text-[11px] font-bold text-zinc-600">Method
                    <select name="method" disabled class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-xs text-zinc-900">
                        <option value="cash" @selected(old('method') === 'cash')>Cash</option><option value="bank_transfer" @selected(old('method', 'bank_transfer') === 'bank_transfer')>Bank transfer</option><option value="cheque" @selected(old('method') === 'cheque')>Cheque</option><option value="card" @selected(old('method') === 'card')>Card</option><option value="other" @selected(old('method') === 'other')>Other</option>
                    </select>
                </label>
                <label class="text-[11px] font-bold text-zinc-600 sm:col-span-2">Reference
                    <input name="reference" maxlength="120" value="{{ old('reference') }}" placeholder="Payment reference or cheque number" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs text-zinc-900">
                </label>
                <label class="text-[11px] font-bold text-zinc-600 sm:col-span-3">Notes
                    <input name="notes" maxlength="1000" value="{{ old('notes') }}" placeholder="Optional payment notes" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-xs text-zinc-900">
                </label>
                <input type="hidden" name="allocation_mode" value="oldest" disabled>
                <input type="hidden" name="return_to_account" value="1" disabled>
                <div class="flex items-end xl:col-span-1"><button id="accounts-payment-submit" type="submit" class="w-full rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-extrabold text-white shadow-sm hover:bg-zinc-700">Record supplier payment</button></div>
            </form>
            <script>
                (() => {
                    const form = document.getElementById('accounts-party-payment-form');
                    const kind = document.getElementById('accounts-payment-kind');
                    const supplierField = document.getElementById('accounts-supplier-field');
                    const customerField = document.getElementById('accounts-customer-field');
                    const receiptField = document.getElementById('accounts-receipt-number-field');
                    const methodField = document.getElementById('accounts-payment-method-field');
                    const submit = document.getElementById('accounts-payment-submit');
                    const accountLabel = document.getElementById('accounts-financial-account-label');
                    const accountSelect = form.querySelector('[name="financial_account_id"]');
                    const methodSelect = methodField.querySelector('select');
                    const toggleField = (field, enabled) => {
                        field.classList.toggle('hidden', !enabled);
                        field.querySelectorAll('input, select').forEach((input) => {
                            input.disabled = !enabled;
                            if (['customer_id', 'supplier_id', 'receipt_no', 'method'].includes(input.name)) {
                                input.required = enabled;
                            }
                        });
                    };
                    const updatePaymentForm = () => {
                        const isCustomerReceipt = kind.value === 'received';
                        form.action = isCustomerReceipt ? @json(route('invoices.payment.store')) : @json(route('accounts.supplier-payment.store'));
                        toggleField(supplierField, !isCustomerReceipt);
                        toggleField(customerField, isCustomerReceipt);
                        toggleField(receiptField, isCustomerReceipt);
                        toggleField(methodField, isCustomerReceipt);
                        form.querySelector('[name="allocation_mode"]').disabled = !isCustomerReceipt;
                        form.querySelector('[name="return_to_account"]').disabled = !isCustomerReceipt;
                        accountLabel.textContent = isCustomerReceipt ? 'Receive into' : 'Pay from';
                        submit.textContent = isCustomerReceipt ? 'Record customer receipt' : 'Record supplier payment';
                        updatePaymentMethod();
                    };
                    const updatePaymentMethod = () => {
                        if (kind.value !== 'received') {
                            return;
                        }
                        const accountType = accountSelect.selectedOptions[0]?.dataset.accountType;
                        if (accountType === 'cash') {
                            methodSelect.value = 'cash';
                        } else if (accountType === 'bank' && methodSelect.value === 'cash') {
                            methodSelect.value = 'bank_transfer';
                        }
                    };
                    kind.addEventListener('change', updatePaymentForm);
                    accountSelect.addEventListener('change', updatePaymentMethod);
                    updatePaymentForm();
                })();
            </script>
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
