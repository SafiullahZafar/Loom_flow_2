@extends('layouts.app')

@section('title', 'Ledgers & Audit Statements')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-6 items-start">

    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    <aside class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm lg:sticky lg:top-24">

        {{-- General Ledger — main dashboard link --}}
        <a href="{{ route('ledgers.index') }}"
           class="mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 transition-all
                  {{ $activeLedger === 'all'
                       ? 'bg-zinc-900 text-zinc-50 shadow-md'
                       : 'bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 hover:text-zinc-900' }}">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                         {{ $activeLedger === 'all' ? 'bg-zinc-700' : 'bg-zinc-200' }}">
                <svg class="h-5 w-5 {{ $activeLedger === 'all' ? 'text-zinc-100' : 'text-zinc-600' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-extrabold leading-tight">General Ledger</span>
                <span class="block text-[10px] text-zinc-400 mt-0.5">All posted entries</span>
            </div>
        </a>

        <div class="mb-2 px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sub-Ledgers</div>

        <nav class="space-y-1.5" aria-label="Ledger sections">

            @php
                $navItems = [
                    'parties'  => ['label' => 'Party Ledgers',     'desc' => 'Suppliers, mills & buyers',  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    'expenses' => ['label' => 'Expense Ledger',    'desc' => 'Fixed & variable costs',     'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                    'cash_bank'=> ['label' => 'Cash & Bank',       'desc' => 'Account movements',          'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                    'inventory'=> ['label' => 'Inventory Ledger',  'desc' => 'Stock value entries',        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    'income'   => ['label' => 'Income Ledger',     'desc' => 'Sales & revenue entries',    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    'equity'   => ['label' => 'Equity Ledger',     'desc' => 'Capital & equity accounts',  'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp

            @foreach($navItems as $key => $item)
                <a href="{{ route('ledgers.index', ['ledger' => $key]) }}"
                   class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all
                          {{ $activeLedger === $key
                               ? 'bg-zinc-900 text-zinc-50 shadow-sm'
                               : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                                 {{ $activeLedger === $key ? 'bg-zinc-700' : 'bg-zinc-100' }}">
                        <svg class="h-3.5 w-3.5 {{ $activeLedger === $key ? 'text-zinc-100' : 'text-zinc-500' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                    </span>
                    <div>
                        <span class="block leading-tight">{{ $item['label'] }}</span>
                        <span class="block text-[10px] font-normal text-zinc-400">{{ $item['desc'] }}</span>
                    </div>
                    @if($activeLedger === $key)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-zinc-400 shrink-0"></span>
                    @endif
                </a>
            @endforeach

        </nav>

        {{-- Totals snapshot --}}
        <div class="mt-4 rounded-2xl bg-zinc-50 border border-zinc-200 p-3 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">
                {{ $activeLedgerTitle }} Totals
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Total Debit</span>
                <span class="font-mono font-bold text-zinc-900">Rs. {{ number_format($totalDebit, 0) }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Total Credit</span>
                <span class="font-mono font-bold text-zinc-600">Rs. {{ number_format($totalCredit, 0) }}</span>
            </div>
            <div class="pt-1.5 border-t border-zinc-200 flex items-center justify-between text-xs">
                <span class="text-zinc-700 font-bold">Difference</span>
                <span class="font-mono font-black {{ $ledgerDifference == 0 ? 'text-zinc-400' : 'text-zinc-900' }}">
                    Rs. {{ number_format(abs($ledgerDifference), 0) }}
                </span>
            </div>
        </div>

        {{-- Quick links --}}
        <div class="mt-5 pt-4 border-t border-zinc-100 space-y-1.5">
            <div class="px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Quick Links</div>
            <a href="{{ route('sales.index') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Sales Module
            </a>
            <a href="{{ route('invoices.index') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a2 2 0 01.707.293l5.414 5.414A2 2 0 0119 9v10a2 2 0 01-2 2z"/>
                </svg>
                Invoices Module
            </a>
            <a href="{{ route('expenses.index') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Expenses Module
            </a>
        </div>
    </aside>

    {{-- ============================================================
         MAIN CONTENT
    ============================================================ --}}
    <div class="min-w-0 space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-zinc-200">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Accounts & Ledgers</span>
                </div>
                <h1 class="text-2xl font-black text-zinc-900 tracking-tight">{{ $activeLedgerTitle }}</h1>
                <p class="text-xs text-zinc-400 mt-0.5">
                    @if($activeLedger === 'all')
                        All posted entries across parties, expenses, cash & bank, inventory, income, and equity.
                    @elseif($activeLedger === 'parties')
                        Yarn suppliers, sizing mills, and fabric buyers — payables and receivables.
                    @elseif($activeLedger === 'expenses')
                        Fixed recurring bills and variable costs posted to accounts.
                    @elseif($activeLedger === 'cash_bank')
                        Cash and bank account movements — deposits, withdrawals, and receipts.
                    @elseif($activeLedger === 'inventory')
                        Fabric stock, yarn bags, and parts — value entries.
                    @elseif($activeLedger === 'income')
                        Sales receipts and revenue entries.
                    @else
                        Capital and equity account postings.
                    @endif
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border border-zinc-300 text-xs font-bold transition-colors flex items-center gap-1.5 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Back to Hub
            </a>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-zinc-300 bg-zinc-50 p-4 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-600">
                    {{ $activeLedger === 'all' ? 'All Ledger Debits' : $activeLedgerTitle.' Debits' }}
                </div>
                <div class="text-2xl font-black text-zinc-900 mt-1 font-mono">Rs. {{ number_format($totalDebit, 2) }}</div>
                <div class="text-[11px] text-zinc-400 mt-1">Receivables, assets & expenses</div>
            </div>
            <div class="rounded-2xl border border-zinc-400 bg-zinc-100 p-4 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-700">
                    {{ $activeLedger === 'all' ? 'All Ledger Credits' : $activeLedgerTitle.' Credits' }}
                </div>
                <div class="text-2xl font-black text-zinc-900 mt-1 font-mono">Rs. {{ number_format($totalCredit, 2) }}</div>
                <div class="text-[11px] text-zinc-500 mt-1">Payables, income & reductions</div>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Debit / Credit Difference</div>
                <div class="text-2xl font-black text-zinc-900 mt-1 font-mono">
                    Rs. {{ number_format(abs($ledgerDifference), 2) }}
                </div>
                <div class="text-[11px] text-zinc-400 mt-1">
                    {{ $ledgerDifference == 0 ? '✓ Balanced' : 'Imbalance — check entries' }}
                </div>
            </div>
        </div>


        {{-- ============================================================
             LEDGER TABLE CARD
        ============================================================ --}}
        <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

            {{-- Card header + filter --}}
            <div class="flex flex-col gap-4 px-6 py-4 border-b border-zinc-100 bg-zinc-50 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">{{ $activeLedgerTitle }} Entries</h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Source-linked postings with running balances per party or account</p>
                    </div>
                </div>

                {{-- Filter form --}}
                <form method="GET" action="{{ route('ledgers.index') }}" class="flex flex-wrap items-center gap-2">
                    {{-- Keep active ledger type --}}
                    @if($activeLedger !== 'all')
                        <input type="hidden" name="ledger" value="{{ $activeLedger }}">
                    @endif

                    <select name="party_name" onchange="this.form.submit()"
                            class="bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-1.5 text-xs text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        <option value="">All Parties</option>
                        @foreach($parties as $p)
                            <option value="{{ $p->party_name }}"
                                    {{ ($filters['party_name'] ?? '') == $p->party_name ? 'selected' : '' }}>
                                {{ $p->party_name }} ({{ str_replace('_', ' ', $p->party_type) }})
                            </option>
                        @endforeach
                    </select>

                    @if($activeLedger === 'all' || $activeLedger === 'parties')
                        <select name="party_type" onchange="this.form.submit()"
                                class="bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-1.5 text-xs text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                            <option value="">All Types</option>
                            <option value="yarn_supplier"  {{ ($filters['party_type'] ?? '') === 'yarn_supplier'  ? 'selected' : '' }}>Yarn Supplier</option>
                            <option value="sizing_mill"    {{ ($filters['party_type'] ?? '') === 'sizing_mill'    ? 'selected' : '' }}>Sizing Mill</option>
                            <option value="fabric_buyer"   {{ ($filters['party_type'] ?? '') === 'fabric_buyer'   ? 'selected' : '' }}>Fabric Buyer</option>
                            <option value="expense"        {{ ($filters['party_type'] ?? '') === 'expense'        ? 'selected' : '' }}>Expense</option>
                            <option value="cash_bank"      {{ ($filters['party_type'] ?? '') === 'cash_bank'      ? 'selected' : '' }}>Cash / Bank</option>
                            <option value="inventory"      {{ ($filters['party_type'] ?? '') === 'inventory'      ? 'selected' : '' }}>Inventory</option>
                            <option value="income"         {{ ($filters['party_type'] ?? '') === 'income'         ? 'selected' : '' }}>Income</option>
                            <option value="equity"         {{ ($filters['party_type'] ?? '') === 'equity'         ? 'selected' : '' }}>Equity</option>
                        </select>
                    @endif

                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" aria-label="From date"
                           class="bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-1.5 text-xs text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" aria-label="To date"
                           class="bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-1.5 text-xs text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">

                    <button type="submit"
                            class="px-3 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-bold transition-colors">
                        Filter
                    </button>

                    @if(($filters['party_name'] ?? null) || ($filters['party_type'] ?? null) || ($filters['from'] ?? null) || ($filters['to'] ?? null))
                        <a href="{{ route('ledgers.index', $activeLedger === 'all' ? [] : ['ledger' => $activeLedger]) }}"
                           class="px-2.5 py-1.5 rounded-xl bg-zinc-100 text-zinc-500 hover:text-zinc-900 text-xs font-semibold transition-colors">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] tracking-wider bg-zinc-50">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Voucher #</th>
                            <th class="px-4 py-3">Party / Account Name</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3 text-right text-zinc-700">Debit (Rs.)</th>
                            <th class="px-4 py-3 text-right text-zinc-700">Credit (Rs.)</th>
                            <th class="px-4 py-3 text-right font-black text-zinc-900 bg-zinc-900/5 border-l border-zinc-200">Balance (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($ledgers as $l)
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-zinc-500 whitespace-nowrap">
                                    {{ $l->transaction_date->format('d M, Y') }}
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-zinc-900">
                                    {{ $l->voucher_no ?? '—' }}
                                </td>
                                <td class="px-4 py-3 font-bold text-zinc-800">{{ $l->party_name }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-zinc-100 text-zinc-600 border border-zinc-200">
                                        {{ str_replace('_', ' ', $l->party_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-600">{{ $l->description }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-zinc-900">
                                    {{ $l->debit > 0 ? number_format($l->debit, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-zinc-600">
                                    {{ $l->credit > 0 ? number_format($l->credit, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-black text-zinc-900 bg-zinc-50 border-l border-zinc-200">
                                    {{ number_format($l->balance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <div class="text-zinc-400 text-sm font-medium">No ledger entries match your filter</div>
                                    <div class="text-zinc-300 text-xs mt-1">Entries are posted automatically from sales, expenses, and account transactions</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-zinc-100">{{ $ledgers->links() }}</div>
        </div>

    </div>{{-- end main --}}
</div>{{-- end grid --}}
@endsection
