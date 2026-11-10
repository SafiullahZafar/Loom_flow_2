@extends('layouts.app')

@section('title', 'Invoices, Customers & Accounts')

@section('content')
<div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
    <aside class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm lg:sticky lg:top-24">

        {{-- Dashboard main link --}}
        <a href="{{ route('invoices.index') }}"
           class="mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 transition-all
                  {{ $page === 'dashboard'
                       ? 'bg-zinc-900 text-zinc-50 shadow-md'
                       : 'bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 hover:text-zinc-900' }}">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                         {{ $page === 'dashboard' ? 'bg-zinc-700' : 'bg-zinc-200' }}">
                <svg class="h-5 w-5 {{ $page === 'dashboard' ? 'text-zinc-100' : 'text-zinc-600' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-extrabold leading-tight">Invoices Dashboard</span>
                <span class="block text-[10px] text-zinc-400 mt-0.5">Receivables & cash position</span>
            </div>
        </a>

        <div class="mb-2 px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sections</div>

        <nav class="space-y-1.5" aria-label="Invoice sections">

            {{-- Invoice Register --}}
            <a href="{{ route('invoices.invoices') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'invoices'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'invoices' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'invoices' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Invoice Register</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Sales & contract billing</span>
                </div>
                @if($page === 'invoices')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Customers --}}
            <a href="{{ route('invoices.customers') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'customers'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'customers' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'customers' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Customers</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Buyer records & balances</span>
                </div>
                @if($page === 'customers')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Payments Received --}}
            <a href="{{ route('invoices.payments') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'payments'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'payments' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'payments' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Payments Received</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Receipts & allocations</span>
                </div>
                @if($page === 'payments')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

            {{-- Cash & Bank --}}
            <a href="{{ route('invoices.accounts') }}"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                      {{ $page === 'accounts'
                           ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                           : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                             {{ $page === 'accounts' ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                    <svg class="h-3.5 w-3.5 {{ $page === 'accounts' ? 'text-zinc-100' : 'text-zinc-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Cash & Bank</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Account balances & entries</span>
                </div>
                @if($page === 'accounts')
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                @endif
            </a>

        </nav>

        {{-- Financial snapshot --}}
        @if(isset($totalDue))
        <div class="mt-4 rounded-2xl bg-zinc-50 border border-zinc-200 p-3 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Financials</div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Invoiced</span>
                <span class="font-mono font-bold text-zinc-900">Rs. {{ number_format($totalInvoiced, 0) }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Received</span>
                <span class="font-mono font-bold text-zinc-600">Rs. {{ number_format($totalReceived, 0) }}</span>
            </div>
            <div class="pt-1.5 border-t border-zinc-200 flex items-center justify-between text-xs">
                <span class="text-zinc-700 font-bold">Outstanding</span>
                <span class="font-mono font-black {{ $totalDue > 0 ? 'text-zinc-900' : 'text-zinc-400' }}">
                    Rs. {{ number_format($totalDue, 0) }}
                </span>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="mt-5 pt-4 border-t border-zinc-100 space-y-1.5">
            <div class="px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Quick Actions</div>
            <a href="{{ route('invoices.invoices') }}#invoice-entry"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Invoice
            </a>
            <a href="{{ route('invoices.payments') }}#payment-entry"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Receive Payment
            </a>
            <a href="{{ route('sales.index') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Sales Module
            </a>
        </div>
    </aside>

    <main class="min-w-0 space-y-6">
        <div class="flex flex-col justify-between gap-4 border-b border-zinc-200 pb-4 sm:flex-row sm:items-end">
            <div><div class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Module 07 · Billing & Receipts</div><h1 class="mt-1 text-2xl font-black tracking-tight text-zinc-900">{{ ['dashboard' => 'Invoices Dashboard', 'invoices' => 'Invoice Register', 'customers' => 'Customers', 'payments' => 'Payments Received', 'accounts' => 'Cash & Bank Accounts'][$page] }}</h1></div>
            <div class="flex flex-wrap gap-2">
                @if(in_array($page, ['dashboard', 'invoices'], true))<a href="{{ route('invoices.invoices') }}#invoice-entry" class="rounded-xl bg-zinc-900 px-4 py-2 text-xs font-bold text-white">Create Invoice</a>@endif
                @if(in_array($page, ['dashboard', 'payments'], true))<a href="{{ route('invoices.payments') }}#payment-entry" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-bold text-zinc-700">Receive Payment</a>@endif
            </div>
        </div>

        @if(session('success'))<div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><p class="font-bold">Please check these entries:</p><ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        @if($page === 'dashboard')
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([['Invoices Issued', $totalInvoiced], ['Payments Allocated', $totalReceived], ['Outstanding Balance', $totalDue], ['Cash / Bank Balance', $totalAccountBalance]] as [$label, $value])
                    <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm"><div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">{{ $label }}</div><div class="mt-2 font-mono text-xl font-black text-zinc-900">Rs. {{ number_format($value, 2) }}</div></div>
                @endforeach
            </section>
            <section class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-zinc-100 bg-zinc-50 px-5 py-4"><div><h2 class="text-sm font-extrabold">Recent Invoices</h2><p class="mt-1 text-[10px] text-zinc-500">Billing linked to sales slips and contracts</p></div><a class="text-xs font-bold text-zinc-600" href="{{ route('invoices.invoices') }}">View register →</a></div>
                @include('invoices.partials.invoice-table', ['rows' => $invoices, 'compact' => true])
            </section>
            <div class="grid gap-4 md:grid-cols-2">
                <a href="{{ route('invoices.customers') }}" class="rounded-2xl border border-zinc-200 bg-white p-5 hover:border-zinc-400"><h2 class="font-extrabold">Customer records</h2><p class="mt-1 text-xs text-zinc-500">Maintain buyers and review invoiced and received totals.</p></a>
                <a href="{{ route('invoices.accounts') }}" class="rounded-2xl border border-zinc-200 bg-white p-5 hover:border-zinc-400"><h2 class="font-extrabold">Cash & bank accounts</h2><p class="mt-1 text-xs text-zinc-500">Record the receiving account, receipts, deposits and withdrawals.</p></a>
            </div>
        @endif

        @if($page === 'invoices')
            <section id="invoice-entry" class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold">Create Invoice</h2><p class="mt-1 text-xs text-zinc-500">Link a sales slip or contract; manual line items are available for contract billing.</p>
                @if($activeCustomers->isEmpty())<p class="mt-3 rounded-xl bg-amber-50 p-3 text-xs text-amber-800">Add a customer first, then create the invoice.</p>@endif
                <form method="POST" action="{{ route('invoices.store') }}" class="mt-4 space-y-4">@csrf
                    <div class="grid gap-3 md:grid-cols-3">
                        <label class="text-xs font-semibold text-zinc-600">Invoice number<input name="invoice_no" required value="{{ old('invoice_no', 'INV-'.now()->format('ymdHis')) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 font-mono text-sm"></label>
                        <label class="text-xs font-semibold text-zinc-600">Customer *<select id="invoiceCustomer" name="customer_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="">Choose customer</option>@foreach($activeCustomers as $customer)<option value="{{ $customer->id }}" data-customer-name="{{ $customer->name }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}{{ $customer->phone ? ' · '.$customer->phone : '' }}</option>@endforeach</select></label>
                        <label class="text-xs font-semibold text-zinc-600">Invoice from sales slip<select id="invoiceSale" name="sale_id" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="">Manual / contract invoice</option>@foreach($availableSales as $sale)<option value="{{ $sale->id }}" data-buyer="{{ $sale->buyer_name }}" data-description="{{ $sale->fabric_quality }} fabric" data-quantity="{{ $sale->meters_sold }}" data-rate="{{ $sale->rate_per_meter }}" data-contract="{{ $sale->sales_contract_id }}">{{ $sale->slip_no }} · {{ $sale->buyer_name }} · {{ number_format($sale->meters_sold, 2) }}m</option>@endforeach</select></label>
                    </div>
                    <div class="grid gap-3 md:grid-cols-3">
                        <label class="text-xs font-semibold text-zinc-600">Related contract<select id="invoiceContract" name="sales_contract_id" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="">No contract link</option>@foreach($contractOptions as $contract)<option value="{{ $contract->id }}" data-buyer="{{ $contract->buyer_name }}" data-description="{{ $contract->fabric_quality }} fabric - Contract {{ $contract->contract_no }}" data-quantity="{{ $contract->invoiceable_meters }}" data-rate="{{ $contract->rate_per_meter }}" @selected(old('sales_contract_id', request('contract_id')) == $contract->id)>{{ $contract->contract_no }} · {{ $contract->buyer_name }} · {{ number_format($contract->invoiceable_meters, 2) }}m delivered, unbilled</option>@endforeach</select></label>
                        <label class="text-xs font-semibold text-zinc-600">Invoice date<input type="date" name="invoice_date" required value="{{ old('invoice_date', today()->toDateString()) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label>
                        <label class="text-xs font-semibold text-zinc-600">Due date<input type="date" name="due_date" value="{{ old('due_date', today()->addDays(30)->toDateString()) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label>
                    </div>
                    <div id="invoiceItems" class="space-y-2">
                        <div class="grid gap-2 rounded-xl bg-zinc-50 p-3 md:grid-cols-[minmax(180px,2fr)_1fr_1fr_1fr_auto] invoice-item-row">
                            <input name="items[0][description]" placeholder="Fabric quality / description" class="invoice-description rounded-lg border border-zinc-300 px-3 py-2 text-xs">
                            <input name="items[0][quantity]" type="number" step="0.001" min="0.001" placeholder="Quantity" class="invoice-quantity rounded-lg border border-zinc-300 px-3 py-2 text-xs">
                            <select name="items[0][unit]" class="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-xs"><option>Meter</option><option>Roll</option><option>Lot</option><option>Other</option></select>
                            <input name="items[0][rate]" type="number" step="0.01" min="0" placeholder="Rate / unit" class="invoice-rate rounded-lg border border-zinc-300 px-3 py-2 text-xs">
                            <button type="button" onclick="removeInvoiceItem(this)" class="rounded-lg px-2 text-xs text-zinc-500 hover:bg-zinc-200">×</button>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-end justify-between gap-3"><button type="button" onclick="addInvoiceItem()" class="rounded-lg border border-zinc-300 px-3 py-2 text-xs font-bold text-zinc-600">+ Add line</button><label class="text-xs font-semibold text-zinc-600">Discount (Rs.)<input name="discount" type="number" step="0.01" min="0" value="0" class="mt-1 block w-48 rounded-xl border border-zinc-300 px-3 py-2 text-right font-mono text-sm"></label></div>
                    <label class="block text-xs font-semibold text-zinc-600">Notes<textarea name="notes" rows="2" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></textarea></label>
                    <div class="flex justify-end"><button @disabled($activeCustomers->isEmpty()) class="rounded-xl bg-zinc-900 px-5 py-2.5 text-xs font-extrabold text-white disabled:cursor-not-allowed disabled:opacity-40">Issue Invoice</button></div>
                </form>
            </section>
            <section class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm"><div class="border-b border-zinc-100 bg-zinc-50 px-5 py-4"><h2 class="text-sm font-extrabold">All Invoices</h2></div>@include('invoices.partials.invoice-table', ['rows' => $invoices, 'compact' => false])<div class="border-t border-zinc-100 px-5 py-4">{{ $invoices->links() }}</div></section>
        @endif

        @if($page === 'customers')
            <section class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm"><h2 class="text-sm font-extrabold">Add Customer</h2><form method="POST" action="{{ route('invoices.customer.store') }}" class="mt-4 grid gap-3 md:grid-cols-2">@csrf
                <label class="text-xs font-semibold text-zinc-600">Customer / party name *<input name="name" required value="{{ old('name') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label>
                <label class="text-xs font-semibold text-zinc-600">Phone<input name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label>
                <label class="text-xs font-semibold text-zinc-600">Email<input name="email" type="email" value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label>
                <label class="text-xs font-semibold text-zinc-600">Opening amount due (Rs.)<input name="opening_balance" type="number" step="0.01" min="0" value="0" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label>
                <label class="text-xs font-semibold text-zinc-600 md:col-span-2">Address<textarea name="address" rows="2" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></textarea></label>
                <div class="md:col-span-2 flex justify-end"><button class="rounded-xl bg-zinc-900 px-5 py-2.5 text-xs font-extrabold text-white">Save Customer</button></div>
            </form></section>
            <section class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm"><div class="border-b border-zinc-100 bg-zinc-50 px-5 py-4"><h2 class="text-sm font-extrabold">Customer Register</h2><p class="mt-1 text-[10px] text-zinc-500">Contract and sales party names are shared with this customer list.</p></div><div class="overflow-x-auto"><table class="w-full min-w-[900px] text-left text-xs"><thead class="bg-white text-[10px] uppercase text-zinc-500"><tr><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Phone / Email</th><th class="px-4 py-3 text-right">Invoices</th><th class="px-4 py-3 text-right">Invoiced</th><th class="px-4 py-3 text-right">Received</th><th class="px-4 py-3 text-right">Customer balance</th></tr></thead><tbody class="divide-y divide-zinc-100">@forelse($customers as $customer)@php($customerBalance = (float) $customer->opening_balance + (float) $customer->invoices_sum_total - (float) $customer->payments_sum_amount)<tr><td class="px-4 py-3 font-bold">{{ $customer->name }}<span class="block text-[10px] font-normal text-zinc-400">{{ $customer->status }}</span></td><td class="px-4 py-3 text-zinc-600">{{ $customer->phone ?: '—' }}<span class="block text-[10px] text-zinc-400">{{ $customer->email }}</span></td><td class="px-4 py-3 text-right">{{ $customer->invoices_count }}</td><td class="px-4 py-3 text-right font-mono">Rs. {{ number_format((float) $customer->invoices_sum_total, 2) }}</td><td class="px-4 py-3 text-right font-mono">Rs. {{ number_format((float) $customer->payments_sum_amount, 2) }}</td><td class="px-4 py-3 text-right font-mono {{ $customerBalance > 0 ? 'text-amber-700' : 'text-emerald-700' }}">Rs. {{ number_format($customerBalance, 2) }}</td></tr>@empty<tr><td colspan="6" class="px-5 py-10 text-center text-zinc-400">No customers yet. Add a buyer above.</td></tr>@endforelse</tbody></table></div><div class="border-t border-zinc-100 px-5 py-4">{{ $customers->links() }}</div></section>
        @endif

        @if($page === 'payments')
            <section id="payment-entry" class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm"><h2 class="text-sm font-extrabold">Receive Customer Payment</h2><p class="mt-1 text-xs text-zinc-500">Post money to cash/bank and allocate it to the selected invoice, oldest open invoices, or keep it as customer credit.</p>
                @if($activeAccounts->isEmpty())<p class="mt-3 rounded-xl bg-amber-50 p-3 text-xs text-amber-800">Create a cash or bank account first in the Cash & Bank section.</p>@endif
                <form method="POST" action="{{ route('invoices.payment.store') }}" class="mt-4 grid gap-3 md:grid-cols-3">@csrf
                    <label class="text-xs font-semibold text-zinc-600">Receipt number<input name="receipt_no" required value="{{ old('receipt_no', 'RCPT-'.now()->format('ymdHis')) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 font-mono text-sm"></label>
                    <label class="text-xs font-semibold text-zinc-600">Customer *<select id="paymentCustomer" name="customer_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="">Choose customer</option>@foreach($activeCustomers as $customer)<option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>@endforeach</select></label>
                    <label class="text-xs font-semibold text-zinc-600">Deposit into *<select id="paymentAccount" name="financial_account_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="">Choose account</option>@foreach($activeAccounts as $account)<option value="{{ $account->id }}" data-account-type="{{ $account->type }}">{{ ucfirst($account->type) }} · {{ $account->name }}</option>@endforeach</select></label>
                    <label class="text-xs font-semibold text-zinc-600">Amount (Rs.)<input name="amount" type="number" step="0.01" min="0.01" required value="{{ old('amount') }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 font-mono text-sm"></label>
                    <label class="text-xs font-semibold text-zinc-600">Payment date<input name="payment_date" type="date" required value="{{ old('payment_date', today()->toDateString()) }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label>
                    <label class="text-xs font-semibold text-zinc-600">Method<select id="paymentMethod" name="method" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="cash">Cash</option><option value="bank_transfer">Bank transfer</option><option value="cheque">Cheque</option><option value="card">Card</option><option value="other">Other</option></select></label>
                    <label class="text-xs font-semibold text-zinc-600">Apply payment<select id="paymentAllocationMode" name="allocation_mode" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="oldest">Oldest open invoices first</option><option value="manual">Allocate across invoices</option><option value="invoice">Choose one invoice</option><option value="credit">Keep as customer credit</option></select></label>
                    <label id="paymentInvoiceWrap" class="hidden text-xs font-semibold text-zinc-600">Invoice to pay<select id="paymentInvoice" name="invoice_id" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="">Choose invoice</option>@foreach($invoiceOptions as $invoice)<option value="{{ $invoice->id }}" data-customer-id="{{ $invoice->customer_id }}" data-due="{{ max(0, (float) $invoice->total - (float) ($invoice->allocations_sum_amount ?? 0)) }}">{{ $invoice->invoice_no }} · {{ $invoice->customer->name }} · due Rs. {{ number_format(max(0, (float) $invoice->total - (float) ($invoice->allocations_sum_amount ?? 0)), 2) }}</option>@endforeach</select></label>
                    <div id="manualAllocationsWrap" class="hidden space-y-2 md:col-span-3"><div class="flex items-center justify-between"><span class="text-xs font-semibold text-zinc-600">Manual invoice allocations</span><button type="button" onclick="addManualAllocation()" class="rounded-lg border border-zinc-300 px-3 py-1.5 text-[11px] font-bold text-zinc-600">+ Add invoice</button></div><div id="manualAllocations" class="space-y-2"><div class="manual-allocation-row grid gap-2 md:grid-cols-[minmax(0,1fr)_200px_auto]"><select name="allocations[0][invoice_id]" class="payment-invoice-choice rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs"><option value="">Choose invoice</option>@foreach($invoiceOptions as $invoice)<option value="{{ $invoice->id }}" data-customer-id="{{ $invoice->customer_id }}">{{ $invoice->invoice_no }} · {{ $invoice->customer->name }} · due Rs. {{ number_format(max(0, (float) $invoice->total - (float) ($invoice->allocations_sum_amount ?? 0)), 2) }}</option>@endforeach</select><input name="allocations[0][amount]" type="number" step="0.01" min="0" placeholder="Allocated amount" class="rounded-xl border border-zinc-300 px-3 py-2 text-xs"><button type="button" onclick="removeManualAllocation(this)" class="rounded-xl px-3 text-zinc-500 hover:bg-zinc-100">×</button></div></div><p class="text-[10px] text-zinc-400">Any unallocated receipt amount stays as customer credit.</p></div>
                    <label class="text-xs font-semibold text-zinc-600">Bank / cheque reference<input name="reference" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label>
                    <label class="text-xs font-semibold text-zinc-600 md:col-span-3">Notes<input name="notes" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label>
                    <div class="md:col-span-3 flex justify-end"><button @disabled($activeAccounts->isEmpty() || $activeCustomers->isEmpty()) class="rounded-xl bg-zinc-900 px-5 py-2.5 text-xs font-extrabold text-white disabled:opacity-40">Save Receipt</button></div>
                </form>
            </section>
            <section class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm"><div class="border-b border-zinc-100 bg-zinc-50 px-5 py-4"><h2 class="text-sm font-extrabold">Payment Receipt History</h2></div><div class="overflow-x-auto"><table class="w-full min-w-[850px] text-left text-xs"><thead class="bg-white text-[10px] uppercase text-zinc-500"><tr><th class="px-4 py-3">Receipt / Date</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Account / Method</th><th class="px-4 py-3">Applied to invoices</th><th class="px-4 py-3 text-right">Received</th></tr></thead><tbody class="divide-y divide-zinc-100">@forelse($payments as $payment)<tr><td class="px-4 py-3 font-mono font-bold">{{ $payment->receipt_no }}<span class="block text-[10px] font-normal text-zinc-400">{{ $payment->payment_date->format('d M Y') }}</span></td><td class="px-4 py-3">{{ $payment->customer->name }}</td><td class="px-4 py-3">{{ $payment->account->name }}<span class="block text-[10px] text-zinc-400">{{ str_replace('_', ' ', ucfirst($payment->method)) }}</span></td><td class="px-4 py-3">@forelse($payment->allocations as $allocation)<span class="mr-1 inline-block rounded bg-zinc-100 px-2 py-1">{{ $allocation->invoice->invoice_no }} · Rs. {{ number_format((float) $allocation->amount, 2) }}</span>@empty<span class="text-zinc-400">Customer credit</span>@endforelse</td><td class="px-4 py-3 text-right font-mono font-bold">Rs. {{ number_format((float) $payment->amount, 2) }}</td></tr>@empty<tr><td colspan="5" class="px-5 py-10 text-center text-zinc-400">No receipts recorded.</td></tr>@endforelse</tbody></table></div><div class="border-t border-zinc-100 px-5 py-4">{{ $payments->links() }}</div></section>
        @endif

        @if($page === 'accounts')
            <div class="grid gap-5 xl:grid-cols-2">
                <section class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm"><h2 class="text-sm font-extrabold">Add Cash / Bank Account</h2><form method="POST" action="{{ route('invoices.account.store') }}" class="mt-4 space-y-3">@csrf
                    <label class="block text-xs font-semibold text-zinc-600">Account name<input name="name" required placeholder="e.g. HBL Current Account" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label>
                    <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold text-zinc-600">Type<select name="type" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="bank">Bank</option><option value="cash">Cash</option></select></label><label class="text-xs font-semibold text-zinc-600">Account / last digits<input name="account_number" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label></div>
                    <label class="block text-xs font-semibold text-zinc-600">Opening balance (Rs.)<input name="opening_balance" type="number" min="0" step="0.01" value="0" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label><button class="rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-bold text-white">Save Account</button>
                </form></section>
                <section class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm"><h2 class="text-sm font-extrabold">Record Deposit / Withdrawal</h2><p class="mt-1 text-xs text-zinc-500">Customer receipts are added automatically. Use this form for other account movements.</p><form method="POST" action="{{ route('invoices.account-transaction.store') }}" class="mt-4 space-y-3">@csrf
                    <label class="block text-xs font-semibold text-zinc-600">Account<select name="financial_account_id" required class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="">Choose account</option>@foreach($activeAccounts as $account)<option value="{{ $account->id }}">{{ ucfirst($account->type) }} · {{ $account->name }}</option>@endforeach</select></label>
                    <div class="grid grid-cols-2 gap-3"><label class="text-xs font-semibold text-zinc-600">Direction<select name="direction" class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm"><option value="in">Money in</option><option value="out">Money out</option></select></label><label class="text-xs font-semibold text-zinc-600">Date<input name="transaction_date" type="date" required value="{{ today()->toDateString() }}" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label></div>
                    <label class="block text-xs font-semibold text-zinc-600">Amount (Rs.)<input name="amount" type="number" min="0.01" step="0.01" required class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label><label class="block text-xs font-semibold text-zinc-600">Description<input name="description" required class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label><label class="block text-xs font-semibold text-zinc-600">Reference<input name="reference" class="mt-1 w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm"></label><button class="rounded-xl bg-zinc-900 px-4 py-2.5 text-xs font-bold text-white">Post Transaction</button>
                </form></section>
            </div>
            <section class="space-y-4">@forelse($accounts as $account)@php($balance = (float) $account->opening_balance + (float) ($account->money_in ?? 0) - (float) ($account->money_out ?? 0))<article class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm"><div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 bg-zinc-50 px-5 py-4"><div><span class="rounded-full bg-zinc-200 px-2 py-1 text-[10px] font-bold uppercase">{{ $account->type }}</span><h2 class="mt-2 font-extrabold">{{ $account->name }}</h2><p class="text-[10px] text-zinc-500">{{ $account->account_number ?: 'Account number not recorded' }}</p></div><div class="text-right"><div class="text-[10px] uppercase tracking-wider text-zinc-500">Current balance</div><div class="font-mono text-xl font-black {{ $balance < 0 ? 'text-red-700' : 'text-zinc-900' }}">Rs. {{ number_format($balance, 2) }}</div></div></div><div class="grid grid-cols-3 gap-2 border-b border-zinc-100 px-5 py-3 text-xs"><div>Opening <strong class="ml-1 font-mono">{{ number_format((float) $account->opening_balance, 2) }}</strong></div><div>Money in <strong class="ml-1 font-mono">{{ number_format((float) ($account->money_in ?? 0), 2) }}</strong></div><div>Money out <strong class="ml-1 font-mono">{{ number_format((float) ($account->money_out ?? 0), 2) }}</strong></div></div><div class="overflow-x-auto"><table class="w-full text-left text-xs"><thead class="text-[10px] uppercase text-zinc-400"><tr><th class="px-5 py-2">Date</th><th class="px-5 py-2">Description</th><th class="px-5 py-2">Reference</th><th class="px-5 py-2 text-right">Direction / Amount</th></tr></thead><tbody class="divide-y divide-zinc-100">@forelse($account->transactions as $transaction)<tr><td class="px-5 py-2">{{ $transaction->transaction_date->format('d M Y') }}</td><td class="px-5 py-2">{{ $transaction->description }}</td><td class="px-5 py-2">{{ $transaction->reference ?: '—' }}</td><td class="px-5 py-2 text-right font-mono {{ $transaction->direction === 'in' ? 'text-emerald-700' : 'text-red-700' }}">{{ $transaction->direction === 'in' ? '+' : '−' }} Rs. {{ number_format((float) $transaction->amount, 2) }}</td></tr>@empty<tr><td colspan="4" class="px-5 py-5 text-center text-zinc-400">No account movements recorded yet.</td></tr>@endforelse</tbody></table></div></article>@empty<div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-8 text-center text-sm text-zinc-500">Add your cash or bank account to start recording receipts and account balances.</div>@endforelse</section>
        @endif
    </main>
</div>

@push('scripts')
<script>
    function addInvoiceItem() {
        const container = document.getElementById('invoiceItems');
        const index = container.querySelectorAll('.invoice-item-row').length;
        const row = container.querySelector('.invoice-item-row').cloneNode(true);
        row.querySelectorAll('input, select').forEach((field) => {
            field.value = '';
            field.name = field.name.replace(/items\[\d+\]/, `items[${index}]`);
        });
        container.appendChild(row);
    }

    function removeInvoiceItem(button) {
        const rows = document.querySelectorAll('.invoice-item-row');
        if (rows.length > 1) button.closest('.invoice-item-row').remove();
    }

    function selectInvoiceCustomer(buyerName) {
        const select = document.getElementById('invoiceCustomer');
        const match = Array.from(select.options).find((option) => option.dataset.customerName?.trim().toLowerCase() === buyerName.trim().toLowerCase());
        if (match) select.value = match.value;
    }

    function fillInvoiceItem(description, quantity, rate) {
        const row = document.querySelector('.invoice-item-row');
        if (!row) return;
        row.querySelector('.invoice-description').value = description || '';
        row.querySelector('.invoice-quantity').value = quantity || '';
        row.querySelector('.invoice-rate').value = rate || '';
    }

    document.getElementById('invoiceSale')?.addEventListener('change', (event) => {
        const option = event.target.selectedOptions[0];
        if (!option.value) return;
        document.getElementById('invoiceContract').value = option.dataset.contract || '';
        selectInvoiceCustomer(option.dataset.buyer || '');
        fillInvoiceItem(option.dataset.description, option.dataset.quantity, option.dataset.rate);
    });

    document.getElementById('invoiceContract')?.addEventListener('change', (event) => {
        const option = event.target.selectedOptions[0];
        if (!option.value) return;
        document.getElementById('invoiceSale').value = '';
        selectInvoiceCustomer(option.dataset.buyer || '');
        fillInvoiceItem(option.dataset.description, option.dataset.quantity, option.dataset.rate);
    });

    if (document.getElementById('invoiceContract')?.value) {
        document.getElementById('invoiceContract').dispatchEvent(new Event('change'));
    }

    function filterPaymentInvoices() {
        const customerId = document.getElementById('paymentCustomer')?.value || '';
        document.querySelectorAll('.payment-invoice-choice, #paymentInvoice').forEach((invoiceSelect) => {
            Array.from(invoiceSelect.options).forEach((option) => {
                if (option.value) option.hidden = customerId !== '' && option.dataset.customerId !== customerId;
            });
            if (invoiceSelect.selectedOptions[0]?.hidden) invoiceSelect.value = '';
        });
    }

    function togglePaymentInvoice() {
        const mode = document.getElementById('paymentAllocationMode')?.value;
        const wrapper = document.getElementById('paymentInvoiceWrap');
        const select = document.getElementById('paymentInvoice');
        const manualWrapper = document.getElementById('manualAllocationsWrap');
        if (!wrapper || !select || !manualWrapper) return;
        wrapper.classList.toggle('hidden', mode !== 'invoice');
        manualWrapper.classList.toggle('hidden', mode !== 'manual');
        select.required = mode === 'invoice';
        manualWrapper.querySelectorAll('select, input').forEach((field) => {
            field.required = mode === 'manual';
        });
    }

    function addManualAllocation() {
        const container = document.getElementById('manualAllocations');
        const index = container.querySelectorAll('.manual-allocation-row').length;
        const row = container.querySelector('.manual-allocation-row').cloneNode(true);
        row.querySelectorAll('select, input').forEach((field) => {
            field.value = '';
            field.name = field.name.replace(/allocations\[\d+\]/, `allocations[${index}]`);
        });
        container.appendChild(row);
        filterPaymentInvoices();
        togglePaymentInvoice();
    }

    function removeManualAllocation(button) {
        const rows = document.querySelectorAll('.manual-allocation-row');
        if (rows.length > 1) button.closest('.manual-allocation-row').remove();
    }

    function matchPaymentMethodToAccount() {
        const account = document.getElementById('paymentAccount');
        const method = document.getElementById('paymentMethod');
        if (!account || !method) return;
        const accountType = account.selectedOptions[0]?.dataset.accountType;
        Array.from(method.options).forEach((option) => {
            option.hidden = accountType === 'cash' ? option.value !== 'cash' : (accountType === 'bank' && option.value === 'cash');
        });
        if (method.selectedOptions[0]?.hidden) method.value = accountType === 'cash' ? 'cash' : 'bank_transfer';
    }

    document.getElementById('paymentCustomer')?.addEventListener('change', filterPaymentInvoices);
    document.getElementById('paymentAccount')?.addEventListener('change', matchPaymentMethodToAccount);
    document.getElementById('paymentAllocationMode')?.addEventListener('change', togglePaymentInvoice);
    filterPaymentInvoices();
    togglePaymentInvoice();
    matchPaymentMethodToAccount();
</script>
@endpush
@endsection
