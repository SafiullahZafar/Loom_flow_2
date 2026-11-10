<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FixedExpense;
use App\Services\LedgerPostingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'expense_type' => 'nullable|in:fixed,variable',
        ]);
        $expenses = Expense::with(['financialAccount', 'fixedExpense'])
            ->when($filters['from'] ?? null, fn ($query, string $date) => $query->whereDate('expense_date', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, string $date) => $query->whereDate('expense_date', '<=', $date))
            ->when($filters['expense_type'] ?? null, fn ($query, string $type) => $query->where('expense_type', $type))
            ->latest('expense_date')->latest('id')->paginate(20)->withQueryString();
        $fixedExpenses = FixedExpense::withCount('expenses')
            ->with(['expenses' => function ($query): void {
                // Load only expenses in the current week — used for period checks across all frequencies.
                // The model methods do the final filtering in PHP, so loading the last 90 days
                // covers weekly, monthly, quarterly without over-fetching.
                $query->where('expense_date', '>=', now()->subDays(90)->toDateString())
                    ->latest('expense_date')
                    ->latest('id');
            }])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();
        $activeAccounts = FinancialAccount::where('is_active', true)->orderBy('type')->orderBy('name')->get();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $monthTotal = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');
        $monthFixedTotal = Expense::where('expense_type', 'fixed')->whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');
        $monthVariableTotal = Expense::where('expense_type', 'variable')->whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');
        $fixedSetupCount = FixedExpense::where('is_active', true)->count();

        return view('expenses.index', compact(
            'expenses',
            'fixedExpenses',
            'activeAccounts',
            'filters',
            'monthTotal',
            'monthFixedTotal',
            'monthVariableTotal',
            'fixedSetupCount'
        ));
    }

    public function storeFixedExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'payee' => 'nullable|string|max:150',
            'default_amount' => 'required|numeric|gt:0|max:999999999999',
            'frequency' => 'required|in:weekly,monthly,quarterly,yearly',
            'due_day' => 'nullable|integer|min:1|max:31',
            'notes' => 'nullable|string',
        ]);
        $validated['default_amount'] = round((float) $validated['default_amount'], 2);
        FixedExpense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Fixed expense setup saved. Record each payment when it is paid.');
    }

    public function storeVariableExpense(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'amount' => 'required|numeric|gt:0|max:999999999999',
            'expense_date' => 'required|date',
            'financial_account_id' => 'required|exists:financial_accounts,id,is_active,1',
            'payee' => 'nullable|string|max:150',
            'reference' => 'nullable|string|max:120',
            'notes' => 'nullable|string',
        ]);
        $this->recordExpense('variable', $validated['title'], null, $validated, $ledgerPostingService);

        return redirect()->route('expenses.index')->with('success', 'Variable expense recorded and deducted from the selected account.');
    }

    public function recordFixedExpense(Request $request, FixedExpense $fixedExpense, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        if (! $fixedExpense->is_active) {
            return redirect()->route('expenses.index')->with('error', 'This fixed expense setup is inactive. Activate it before recording a payment.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|gt:0|max:999999999999',
            'expense_date' => 'required|date',
            'financial_account_id' => 'required|exists:financial_accounts,id,is_active,1',
            'payee' => 'nullable|string|max:150',
            'reference' => 'nullable|string|max:120',
            'notes' => 'nullable|string',
        ]);
        $this->recordExpense('fixed', $fixedExpense->name, $fixedExpense, $validated, $ledgerPostingService);

        return redirect()->route('expenses.index')->with('success', 'Fixed expense payment recorded and deducted from the selected account.');
    }

    public function toggleFixedExpense(FixedExpense $fixedExpense): RedirectResponse
    {
        $fixedExpense->update(['is_active' => ! $fixedExpense->is_active]);

        return redirect()->route('expenses.index')->with('success', $fixedExpense->is_active
            ? 'Fixed expense setup activated.'
            : 'Fixed expense setup paused. Existing expense history was kept.');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function recordExpense(string $type, string $title, ?FixedExpense $fixedExpense, array $validated, LedgerPostingService $ledgerPostingService): void
    {
        DB::transaction(function () use ($type, $title, $fixedExpense, $validated, $ledgerPostingService): void {
            $account = FinancialAccount::whereKey($validated['financial_account_id'])
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();
            $amount = round((float) $validated['amount'], 2);
            $expense = Expense::create([
                'fixed_expense_id' => $fixedExpense?->id,
                'financial_account_id' => $account->id,
                'expense_type' => $type,
                'title' => $title,
                'expense_date' => $validated['expense_date'],
                'amount' => $amount,
                'payee' => $validated['payee'] ?? $fixedExpense?->payee,
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
            AccountTransaction::create([
                'financial_account_id' => $account->id,
                'expense_id' => $expense->id,
                'transaction_date' => $expense->expense_date,
                'direction' => 'out',
                'amount' => $amount,
                'description' => ucfirst($type).' expense: '.$title,
                'reference' => $expense->reference ?: 'EXP-'.$expense->id,
            ]);
            $ledgerPostingService->post('expense', $expense->id, [
                [
                    'role' => 'expense',
                    'party_name' => filled($expense->payee) ? $expense->payee : 'Expense: '.$expense->title,
                    'party_type' => 'expense',
                    'transaction_date' => $expense->expense_date,
                    'voucher_no' => $expense->reference ?: 'EXP-'.$expense->id,
                    'description' => ucfirst($type).' expense: '.$expense->title,
                    'debit' => $amount,
                    'credit' => 0,
                    'notes' => $expense->notes,
                ],
                [
                    'role' => 'account',
                    'party_name' => $account->name,
                    'party_type' => 'cash_bank',
                    'transaction_date' => $expense->expense_date,
                    'voucher_no' => $expense->reference ?: 'EXP-'.$expense->id,
                    'description' => 'Payment for '.$expense->title,
                    'debit' => 0,
                    'credit' => $amount,
                    'notes' => $expense->notes,
                ],
            ]);
        });
    }
}
