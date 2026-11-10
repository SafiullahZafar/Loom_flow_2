@extends('layouts.app')

@section('title', 'Fixed & Variable Expenses')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-6 items-start">

    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    <aside class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm lg:sticky lg:top-24">

        {{-- Header --}}
        <div class="mb-4 flex items-center gap-3 rounded-2xl bg-zinc-900 px-4 py-3 shadow-md">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-zinc-700">
                <svg class="h-5 w-5 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-extrabold leading-tight text-zinc-50">Expenses</span>
                <span class="block text-[10px] text-zinc-400 mt-0.5">Fixed bills & variable costs</span>
            </div>
        </div>

        <div class="mb-2 px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sections</div>

        <nav class="space-y-1.5" aria-label="Expense sections">

            {{-- Fixed Expense Setup --}}
            <a href="#fixed-setup"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-zinc-100">
                    <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Fixed Expense Setup</span>
                    <span class="block text-[10px] font-normal text-zinc-400">Recurring bills & schedules</span>
                </div>
            </a>

            {{-- Record Variable --}}
            <a href="#variable-entry"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-zinc-100">
                    <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Record Variable Expense</span>
                    <span class="block text-[10px] font-normal text-zinc-400">One-off costs & repairs</span>
                </div>
            </a>

            {{-- Expense History --}}
            <a href="#expense-history"
               class="flex items-center gap-3 w-full rounded-2xl px-3 py-2.5 text-xs font-bold transition-all text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-zinc-100">
                    <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </span>
                <div>
                    <span class="block leading-tight">Expense History</span>
                    <span class="block text-[10px] font-normal text-zinc-400">All posted payments log</span>
                </div>
            </a>

        </nav>

        {{-- This month snapshot --}}
        <div class="mt-4 rounded-2xl bg-zinc-50 border border-zinc-200 p-3 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">This Month</div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Fixed paid</span>
                <span class="font-mono font-bold text-zinc-900">Rs. {{ number_format($monthFixedTotal, 0) }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-500">Variable costs</span>
                <span class="font-mono font-bold text-zinc-600">Rs. {{ number_format($monthVariableTotal, 0) }}</span>
            </div>
            <div class="pt-1.5 border-t border-zinc-200 flex items-center justify-between text-xs">
                <span class="text-zinc-700 font-bold">Total spent</span>
                <span class="font-mono font-black text-zinc-900">Rs. {{ number_format($monthTotal, 0) }}</span>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="mt-5 pt-4 border-t border-zinc-100 space-y-1.5">
            <div class="px-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Quick Actions</div>
            <a href="#fixed-setup"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Fixed Expense
            </a>
            <a href="#variable-entry"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Record Variable Expense
            </a>
            <a href="{{ route('invoices.accounts') }}"
               class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition-colors">
                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Cash & Bank Accounts
            </a>
        </div>
    </aside>

    {{-- ============================================================
         MAIN CONTENT
    ============================================================ --}}
    <div class="min-w-0 space-y-6">

        {{-- Page header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-zinc-200">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Finance · Expense Control</span>
                </div>
                <h1 class="text-2xl font-black text-zinc-900 tracking-tight">Expenses</h1>
                <p class="text-xs text-zinc-400 mt-0.5">Set up recurring fixed bills, record variable costs, and post every payment to its cash or bank account.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="#fixed-setup"
                   class="px-3.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border border-zinc-300 text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Fixed
                </a>
                <a href="#variable-entry"
                   class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 font-extrabold text-xs shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Record Variable
                </a>
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-zinc-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-2xl bg-zinc-100 border border-zinc-400 text-zinc-900 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-zinc-700 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 rounded-2xl bg-zinc-100 border border-zinc-300 text-zinc-800 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-sm mb-1">
                    <svg class="w-5 h-5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Please correct the following:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-zinc-300 bg-zinc-50 p-4 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-600">Total This Month</div>
                <div class="mt-2 font-mono text-xl font-black text-zinc-900">Rs. {{ number_format($monthTotal, 0) }}</div>
                <div class="text-[10px] text-zinc-400 mt-1">All fixed + variable</div>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Fixed Paid</div>
                <div class="mt-2 font-mono text-xl font-black text-zinc-900">Rs. {{ number_format($monthFixedTotal, 0) }}</div>
                <div class="text-[10px] text-zinc-400 mt-1">Recurring bills this month</div>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Variable Costs</div>
                <div class="mt-2 font-mono text-xl font-black text-zinc-900">Rs. {{ number_format($monthVariableTotal, 0) }}</div>
                <div class="text-[10px] text-zinc-400 mt-1">One-off expenses this month</div>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Active Fixed Setups</div>
                <div class="mt-2 font-mono text-xl font-black text-zinc-900">{{ $fixedSetupCount }}</div>
                <div class="text-[10px] text-zinc-400 mt-1">Recurring schedules active</div>
            </div>
        </div>


        {{-- ============================================================
             SECTION 1 — Two forms side by side
        ============================================================ --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- Fixed Expense Setup Form --}}
            <div id="fixed-setup" class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-6 py-4 border-b border-zinc-100 bg-zinc-50">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">Create Fixed Expense Setup</h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Save a repeating bill — rent, electricity, oil budget. Record each payment separately.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('expenses.fixed.store') }}" class="px-6 py-5 space-y-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 mb-1">Expense Name *</label>
                            <input name="name" required maxlength="150" value="{{ old('name') }}"
                                   placeholder="e.g. Factory Rent"
                                   class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 mb-1">Regular Amount (Rs.) *</label>
                            <input name="default_amount" type="number" required min="0.01" step="0.01"
                                   value="{{ old('default_amount') }}" placeholder="e.g. 85000"
                                   class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 mb-1">Repeat Frequency *</label>
                            <select name="frequency" required
                                    class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                                <option value="monthly">Monthly</option>
                                <option value="weekly">Weekly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 mb-1">Due Day of Month</label>
                            <input name="due_day" type="number" min="1" max="31"
                                   value="{{ old('due_day') }}" placeholder="1 – 31"
                                   class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Payee / Supplier</label>
                        <input name="payee" maxlength="150" value="{{ old('payee') }}"
                               placeholder="Landlord or utility company"
                               class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Notes</label>
                        <textarea name="notes" rows="2" placeholder="Optional details"
                                  class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500"></textarea>
                    </div>
                    <div class="pt-3 border-t border-zinc-100 flex justify-end">
                        <button class="px-5 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-extrabold shadow-sm transition-colors">
                            Save Fixed Expense
                        </button>
                    </div>
                </form>
            </div>

            {{-- Variable Expense Form --}}
            <div id="variable-entry" class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-6 py-4 border-b border-zinc-100 bg-zinc-50">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">Record Variable Expense</h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">One-off or changing costs — repairs, transport, or extra machine oil.</p>
                    </div>
                </div>
                @if($activeAccounts->isEmpty())
                    <div class="mx-6 mt-4 p-3 rounded-xl border border-zinc-300 bg-zinc-50 text-xs text-zinc-600 flex items-center gap-2">
                        <svg class="w-4 h-4 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Add an active cash or bank account before recording expenses.
                        <a class="font-bold underline ml-1 text-zinc-900" href="{{ route('invoices.accounts') }}">Open accounts →</a>
                    </div>
                @endif
                <form method="POST" action="{{ route('expenses.variable.store') }}" class="px-6 py-5 space-y-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 mb-1">Expense Name *</label>
                            <input name="title" required maxlength="150" value="{{ old('title') }}"
                                   placeholder="e.g. Loom Repair"
                                   class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 mb-1">Amount (Rs.) *</label>
                            <input name="amount" type="number" required min="0.01" step="0.01"
                                   value="{{ old('amount') }}" placeholder="e.g. 12500"
                                   class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 mb-1">Expense Date *</label>
                            <input name="expense_date" type="date" required
                                   value="{{ old('expense_date', today()->toDateString()) }}"
                                   class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 mb-1">Paid From *</label>
                            <select name="financial_account_id" required @disabled($activeAccounts->isEmpty())
                                    class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500 disabled:opacity-50">
                                <option value="">Choose cash / bank account</option>
                                @foreach($activeAccounts as $account)
                                    <option value="{{ $account->id }}" @selected(old('financial_account_id') == $account->id)>
                                        {{ ucfirst($account->type) }} · {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1">Payee / Supplier</label>
                        <input name="payee" maxlength="150" value="{{ old('payee') }}"
                               placeholder="Who was paid?"
                               class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 mb-1">Reference</label>
                            <input name="reference" maxlength="120" value="{{ old('reference') }}"
                                   placeholder="Receipt or bill number"
                                   class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-600 mb-1">Notes</label>
                            <input name="notes" value="{{ old('notes') }}" placeholder="Optional details"
                                   class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        </div>
                    </div>
                    <div class="pt-3 border-t border-zinc-100 flex justify-end">
                        <button @disabled($activeAccounts->isEmpty())
                                class="px-5 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-extrabold shadow-sm transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                            Record Variable Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>


        {{-- ============================================================
             SECTION 2 — Fixed Expense Setups Grid
        ============================================================ --}}
        <div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">Fixed Expense Setups</h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">These schedules don't auto-pay — record each payment when it's paid</p>
                    </div>
                </div>
                <span class="text-xs text-zinc-400 font-mono">{{ $fixedExpenses->count() }} setups</span>
            </div>

            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($fixedExpenses as $fixedExpense)
                    @php
                        $alreadyPaid  = $fixedExpense->isPaidForCurrentPeriod();
                        $nextDue      = $fixedExpense->nextDueDate();
                        $lastPaid     = $fixedExpense->lastPaidDate();
                        $periodLabel  = $fixedExpense->currentPeriodLabel();
                    @endphp
                    <div class="rounded-2xl border {{ $fixedExpense->is_active ? 'border-zinc-200 bg-white' : 'border-zinc-100 bg-zinc-50 opacity-70' }} p-4 space-y-3 hover:border-zinc-300 transition-colors">

                        {{-- Header row --}}
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold uppercase
                                                 {{ $fixedExpense->is_active ? 'bg-zinc-900 text-zinc-50' : 'bg-zinc-200 text-zinc-500' }}">
                                        {{ $fixedExpense->is_active ? 'Active' : 'Paused' }}
                                    </span>
                                    {{-- Already-paid badge --}}
                                    @if($fixedExpense->is_active && $alreadyPaid)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-100 border border-zinc-300 text-zinc-600">
                                            <svg class="w-2.5 h-2.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Paid · {{ $periodLabel }}
                                        </span>
                                    @elseif($fixedExpense->is_active && !$alreadyPaid)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 border border-amber-200 text-amber-700">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Due · {{ $periodLabel }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="mt-1.5 font-extrabold text-zinc-900 text-sm leading-tight">{{ $fixedExpense->name }}</h3>
                                <p class="mt-0.5 text-[10px] text-zinc-500">
                                    {{ ucfirst($fixedExpense->frequency) }}
                                    {{ $fixedExpense->due_day ? ' · due day '.$fixedExpense->due_day : '' }}
                                    @if($fixedExpense->payee) · {{ $fixedExpense->payee }} @endif
                                </p>
                            </div>
                            <strong class="shrink-0 font-mono text-sm font-black text-zinc-900">
                                Rs. {{ number_format((float) $fixedExpense->default_amount, 0) }}
                            </strong>
                        </div>

                        {{-- Last paid / next due info strip --}}
                        @if($fixedExpense->is_active)
                            <div class="rounded-xl bg-zinc-50 border border-zinc-100 px-3 py-2 grid grid-cols-2 gap-2 text-[10px]">
                                <div>
                                    <span class="block text-zinc-400 uppercase font-bold tracking-wide">Last Paid</span>
                                    <span class="font-mono font-bold text-zinc-700 mt-0.5 block">
                                        {{ $lastPaid ? $lastPaid->format('d M Y') : 'Never' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-zinc-400 uppercase font-bold tracking-wide">Next Due</span>
                                    <span class="font-mono font-bold {{ $alreadyPaid ? 'text-zinc-500' : 'text-amber-700' }} mt-0.5 block">
                                        {{ $alreadyPaid ? $nextDue->format('d M Y') : 'Now / '.$nextDue->format('d M') }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        {{-- Stats + actions --}}
                        <div class="pt-2 border-t border-zinc-100 flex items-center justify-between">
                            <span class="text-[10px] text-zinc-400">{{ $fixedExpense->expenses_count }} payment{{ $fixedExpense->expenses_count != 1 ? 's' : '' }} recorded</span>
                            <div class="flex items-center gap-2">
                                @if($fixedExpense->is_active && $activeAccounts->isNotEmpty())
                                    <button type="button"
                                            data-id="{{ $fixedExpense->id }}"
                                            data-name="{{ $fixedExpense->name }}"
                                            data-amount="{{ $fixedExpense->default_amount }}"
                                            data-payee="{{ $fixedExpense->payee }}"
                                            data-already-paid="{{ $alreadyPaid ? 'true' : 'false' }}"
                                            data-period-label="{{ $periodLabel }}"
                                            data-next-due="{{ $nextDue->format('d M Y') }}"
                                            data-last-paid="{{ $lastPaid ? $lastPaid->format('d M Y') : '' }}"
                                            onclick="openFixedExpensePayment(this)"
                                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition-colors
                                                   {{ $alreadyPaid
                                                       ? 'bg-zinc-100 hover:bg-zinc-200 text-zinc-600 border border-zinc-300'
                                                       : 'bg-zinc-900 hover:bg-zinc-700 text-zinc-50' }}">
                                        {{ $alreadyPaid ? 'Pay again?' : 'Record payment' }}
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('expenses.fixed.status', $fixedExpense) }}"
                                      onsubmit="return confirm('{{ $fixedExpense->is_active ? 'Pause' : 'Activate' }} this fixed expense setup?')">
                                    @csrf @method('PATCH')
                                    <button class="px-3 py-1.5 rounded-lg border border-zinc-300 hover:bg-zinc-100 text-zinc-600 text-[10px] font-bold transition-colors">
                                        {{ $fixedExpense->is_active ? 'Pause' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center rounded-2xl border border-dashed border-zinc-300">
                        <div class="text-zinc-400 text-sm font-medium">No fixed expense setups yet</div>
                        <div class="text-zinc-300 text-xs mt-1">Create one above for rent, electricity, or any recurring bill</div>
                    </div>
                @endforelse
            </div>
        </div>


        {{-- ============================================================
             SECTION 3 — Expense History Table
        ============================================================ --}}
        <div id="expense-history" class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4 border-b border-zinc-100 bg-zinc-50">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-900">
                        <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-zinc-900 leading-tight">Expense History</h2>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Every entry is also recorded as money out in the selected account</p>
                    </div>
                </div>
                {{-- Filter form --}}
                <form method="GET" action="{{ route('expenses.index') }}" class="flex flex-wrap items-center gap-2">
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" aria-label="From date"
                           class="bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-1.5 text-xs text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" aria-label="To date"
                           class="bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-1.5 text-xs text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    <select name="expense_type"
                            class="bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-1.5 text-xs text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        <option value="">All types</option>
                        <option value="fixed" @selected(($filters['expense_type'] ?? '') === 'fixed')>Fixed</option>
                        <option value="variable" @selected(($filters['expense_type'] ?? '') === 'variable')>Variable</option>
                    </select>
                    <button class="px-3 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-bold transition-colors">
                        Filter
                    </button>
                    @if($filters)
                        <a href="{{ route('expenses.index') }}" class="text-xs font-semibold text-zinc-500 hover:text-zinc-900">Clear</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-zinc-500 font-semibold uppercase text-[10px] tracking-wider bg-zinc-50">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Type / Expense</th>
                            <th class="px-4 py-3">Payee</th>
                            <th class="px-4 py-3">Paid From</th>
                            <th class="px-4 py-3">Reference / Notes</th>
                            <th class="px-4 py-3 text-right bg-zinc-900/5 border-l border-zinc-200 font-black text-zinc-800">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($expenses as $expense)
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-zinc-500 whitespace-nowrap">
                                    {{ $expense->expense_date->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase
                                                 {{ $expense->expense_type === 'fixed'
                                                      ? 'bg-zinc-900 text-zinc-50'
                                                      : 'bg-zinc-200 text-zinc-700' }}">
                                        {{ $expense->expense_type }}
                                    </span>
                                    <strong class="mt-1 block text-zinc-900">{{ $expense->title }}</strong>
                                </td>
                                <td class="px-4 py-3 text-zinc-600">{{ $expense->payee ?: '—' }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $expense->financialAccount->name }}</td>
                                <td class="px-4 py-3 text-zinc-500">
                                    {{ $expense->reference ?: '—' }}
                                    @if($expense->notes)
                                        <span class="block text-[10px] text-zinc-400 italic">{{ $expense->notes }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-black text-zinc-900 bg-zinc-50 border-l border-zinc-200">
                                    Rs. {{ number_format((float) $expense->amount, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <div class="text-zinc-400 text-sm font-medium">No expenses match this filter</div>
                                    <div class="text-zinc-300 text-xs mt-1">Record a fixed or variable expense above</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-zinc-100">{{ $expenses->links() }}</div>
        </div>

    </div>{{-- end main --}}
</div>{{-- end grid --}}


{{-- ============================================================
     MODAL: RECORD FIXED EXPENSE PAYMENT
============================================================ --}}
<div id="modalFixedExpensePayment" class="fixed inset-0 z-50 hidden bg-zinc-950/60 backdrop-blur-sm overflow-y-auto p-4 sm:p-6">
    <div class="mx-auto my-8 w-full max-w-xl rounded-3xl border border-zinc-200 bg-white shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-900">
                    <svg class="h-4 w-4 text-zinc-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-base font-extrabold text-zinc-900">Record Fixed Expense Payment</h2>
                    <p id="fixedExpenseModalName" class="text-xs text-zinc-400 mt-0.5"></p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalFixedExpensePayment')"
                    class="text-zinc-400 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Already-paid warning banner (shown/hidden by JS) --}}
        <div id="fixedExpenseAlreadyPaidWarning" class="hidden mx-6 mt-5 rounded-2xl border border-zinc-300 bg-zinc-50 p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-zinc-200">
                    <svg class="h-4 w-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-extrabold text-zinc-900">Already paid this period</p>
                    <p id="fixedExpenseWarningText" class="mt-0.5 text-xs text-zinc-500 leading-5"></p>
                    <p class="mt-2 text-xs text-zinc-500">
                        You can still record another payment — double-check before submitting.
                    </p>
                </div>
            </div>
        </div>

        <form id="fixedExpensePaymentForm" method="POST"
              data-action-template="{{ route('expenses.fixed.record', ['fixedExpense' => 'EXPENSE_ID']) }}"
              class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Amount Paid (Rs.) *</label>
                    <input id="fixedExpensePaymentAmount" name="amount" type="number" required min="0.01" step="0.01"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Payment Date *</label>
                    <input name="expense_date" type="date" required value="{{ today()->toDateString() }}"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Paid From *</label>
                <select name="financial_account_id" required
                        class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    @foreach($activeAccounts as $account)
                        <option value="{{ $account->id }}">{{ ucfirst($account->type) }} · {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1">Payee</label>
                <input id="fixedExpensePaymentPayee" name="payee" maxlength="150"
                       class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Reference</label>
                    <input name="reference" maxlength="120" placeholder="Bill or receipt number"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1">Notes</label>
                    <input name="notes" placeholder="Optional details"
                           class="w-full bg-zinc-50 border border-zinc-300 rounded-xl px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                </div>
            </div>
            <div class="pt-3 border-t border-zinc-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalFixedExpensePayment')"
                        class="px-4 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button id="fixedExpensePaymentSubmit"
                        class="px-5 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-700 text-zinc-50 text-xs font-extrabold shadow-sm transition-colors">
                    Post Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openFixedExpensePayment(button) {
        const expenseId   = button.dataset.id;
        const alreadyPaid = button.dataset.alreadyPaid === 'true';
        const periodLabel = button.dataset.periodLabel;
        const nextDue     = button.dataset.nextDue;
        const lastPaid    = button.dataset.lastPaid;

        // If already paid this period — show confirm popup first
        if (alreadyPaid) {
            const lastPaidMsg = lastPaid ? '\nLast paid: ' + lastPaid + '.' : '';
            const confirmed = window.confirm(
                '⚠️ Already paid this period!\n\n' +
                '"' + button.dataset.name + '" was already paid for ' + periodLabel + '.' +
                lastPaidMsg + '\n' +
                'Next payment is due: ' + nextDue + '.\n\n' +
                'Do you still want to record another payment?'
            );
            if (!confirmed) return; // user said No — stop here, modal never opens
        }

        // Wire the form action
        const paymentForm = document.getElementById('fixedExpensePaymentForm');
        paymentForm.action = paymentForm.dataset.actionTemplate.replace('EXPENSE_ID', expenseId);

        // Set name subtitle
        document.getElementById('fixedExpenseModalName').textContent = button.dataset.name;

        // Pre-fill amount and payee
        document.getElementById('fixedExpensePaymentAmount').value = button.dataset.amount;
        document.getElementById('fixedExpensePaymentPayee').value  = button.dataset.payee || '';

        // Show or hide the already-paid warning banner inside modal
        const warningBox  = document.getElementById('fixedExpenseAlreadyPaidWarning');
        const warningText = document.getElementById('fixedExpenseWarningText');
        const submitBtn   = document.getElementById('fixedExpensePaymentSubmit');

        if (alreadyPaid) {
            const lastPaidStr = lastPaid ? 'Last paid: ' + lastPaid + '. ' : '';
            warningText.textContent = lastPaidStr + 'Next payment due: ' + nextDue + ' (' + periodLabel + ').';
            warningBox.classList.remove('hidden');
            submitBtn.textContent = 'Post Anyway';
            submitBtn.classList.add('bg-zinc-600');
            submitBtn.classList.remove('bg-zinc-900');
        } else {
            warningBox.classList.add('hidden');
            submitBtn.textContent = 'Post Payment';
            submitBtn.classList.add('bg-zinc-900');
            submitBtn.classList.remove('bg-zinc-600');
        }

        openModal('modalFixedExpensePayment');
    }
</script>
@endsection
