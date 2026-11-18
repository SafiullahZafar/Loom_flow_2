<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Customer;
use App\Models\FinancialAccount;
use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\PaymentReceipt;
use App\Models\Supplier;
use App\Services\LedgerPostingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccountsController extends Controller
{
    public function index(): View
    {
        $customerLedgerBalances = Ledger::query()
            ->where('party_type', 'fabric_buyer')
            ->selectRaw('customer_id, party_name, SUM(debit - credit) as balance')
            ->groupBy('customer_id', 'party_name')
            ->get()
            ->groupBy(fn (object $balance): string => $balance->customer_id
                ? 'customer:'.$balance->customer_id
                : 'name:'.mb_strtolower(trim($balance->party_name)))
            ->map(fn (Collection $balances): float => (float) $balances->sum('balance'));

        $customerAccounts = Customer::query()
            ->withCount('invoices')
            ->orderBy('name')
            ->get()
            ->map(function (Customer $customer) use ($customerLedgerBalances): Customer {
                $balanceKey = $customerLedgerBalances->has('customer:'.$customer->id)
                    ? 'customer:'.$customer->id
                    : 'name:'.mb_strtolower(trim($customer->name));

                $customer->setAttribute('receivable_balance', round((float) $customerLedgerBalances->get($balanceKey, 0), 2));

                return $customer;
            })
            ->sortByDesc('receivable_balance')
            ->values();

        $supplierBalances = Ledger::query()
            ->whereIn('party_type', ['yarn_supplier', 'sizing_mill', 'parts_vendor'])
            ->selectRaw('party_name, party_type, SUM(credit - debit) as payable_balance, MAX(transaction_date) as last_activity')
            ->groupBy('party_name', 'party_type')
            ->get()
            ->keyBy(fn (object $balance): string => $balance->party_type.'|'.mb_strtolower(trim($balance->party_name)));

        $supplierAccounts = Supplier::query()
            ->where('status', 'active')
            ->withCount('bagPurchases')
            ->orderBy('name')
            ->get()
            ->map(function (Supplier $supplier) use ($supplierBalances): Supplier {
                $balance = $supplierBalances->get($supplier->type.'|'.mb_strtolower(trim($supplier->name)));
                $supplier->setAttribute('payable_balance', round((float) ($balance->payable_balance ?? 0), 2));
                $supplier->setAttribute('last_activity', $balance->last_activity ?? null);

                return $supplier;
            })
            ->sortByDesc('payable_balance')
            ->values();

        $openInvoices = Invoice::query()
            ->with('customer')
            ->withSum('allocations as received_total', 'amount')
            ->where('status', 'issued')
            ->latest('invoice_date')
            ->latest('id')
            ->get()
            ->map(function (Invoice $invoice): Invoice {
                $invoice->setAttribute('outstanding_amount', max(0, round((float) $invoice->total - (float) ($invoice->received_total ?? 0), 2)));

                return $invoice;
            })
            ->filter(fn (Invoice $invoice): bool => $invoice->outstanding_amount > 0)
            ->values();

        return view('accounts.index', [
            'customerAccounts' => $customerAccounts,
            'supplierAccounts' => $supplierAccounts,
            'receivables' => $customerAccounts->sum(fn (Customer $customer): float => max(0, (float) $customer->receivable_balance)),
            'payables' => $supplierAccounts->sum(fn (object $supplier): float => max(0, (float) $supplier->payable_balance)),
            'openInvoiceBalance' => $openInvoices->sum('outstanding_amount'),
            'openInvoiceCount' => $openInvoices->count(),
            'monthReceipts' => (float) PaymentReceipt::query()
                ->whereBetween('payment_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->sum('amount'),
            'activeFinancialAccounts' => FinancialAccount::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function showCustomer(Customer $customer): View
    {
        $ledgerEntries = Ledger::query()
            ->where('party_type', 'fabric_buyer')
            ->where(function ($query) use ($customer): void {
                $query->where('customer_id', $customer->id)
                    ->orWhere(function ($legacyQuery) use ($customer): void {
                        $legacyQuery->whereNull('customer_id')->where('party_name', $customer->name);
                    });
            })
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();
        $runningBalance = 0.0;
        $ledgerEntries->each(function (Ledger $entry) use (&$runningBalance): void {
            $runningBalance += (float) $entry->debit - (float) $entry->credit;
            $entry->setAttribute('running_balance', round($runningBalance, 2));
        });
        $accountBalance = (float) Ledger::query()
            ->where('party_type', 'fabric_buyer')
            ->where(function ($query) use ($customer): void {
                $query->where('customer_id', $customer->id)
                    ->orWhere(function ($legacyQuery) use ($customer): void {
                        $legacyQuery->whereNull('customer_id')->where('party_name', $customer->name);
                    });
            })
            ->sum(DB::raw('debit - credit'));

        $invoices = Invoice::query()
            ->withSum('allocations as received_total', 'amount')
            ->where('customer_id', $customer->id)
            ->latest('invoice_date')
            ->latest('id')
            ->limit(30)
            ->get()
            ->map(function (Invoice $invoice): Invoice {
                $invoice->setAttribute('outstanding_amount', max(0, round((float) $invoice->total - (float) ($invoice->received_total ?? 0), 2)));

                return $invoice;
            });

        $receipts = PaymentReceipt::query()
            ->with(['account', 'allocations.invoice'])
            ->where('customer_id', $customer->id)
            ->latest('payment_date')
            ->latest('id')
            ->limit(30)
            ->get();

        return view('accounts.show', [
            'accountParty' => $customer,
            'accountType' => 'customer',
            'accountBalance' => $accountBalance,
            'ledgerEntries' => $ledgerEntries,
            'invoices' => $invoices,
            'receipts' => $receipts,
            'activeFinancialAccounts' => FinancialAccount::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function showSupplier(Supplier $supplier): View
    {
        $ledgerEntries = Ledger::query()
            ->where('party_type', $supplier->type)
            ->where('party_name', $supplier->name)
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();
        $runningBalance = 0.0;
        $ledgerEntries->each(function (Ledger $entry) use (&$runningBalance): void {
            $runningBalance += (float) $entry->credit - (float) $entry->debit;
            $entry->setAttribute('running_balance', round($runningBalance, 2));
        });
        $accountBalance = (float) Ledger::query()
            ->where('party_type', $supplier->type)
            ->where('party_name', $supplier->name)
            ->sum(DB::raw('credit - debit'));

        return view('accounts.show', [
            'accountParty' => $supplier,
            'accountType' => 'supplier',
            'accountBalance' => $accountBalance,
            'ledgerEntries' => $ledgerEntries,
            'supplierPurchases' => $supplier->bagPurchases()->latest('purchase_date')->limit(30)->get(),
            'supplierPayments' => AccountTransaction::query()
                ->with('account')
                ->where('party_type', $supplier->type)
                ->where('party_name', $supplier->name)
                ->where('direction', 'out')
                ->latest('transaction_date')
                ->latest('id')
                ->limit(30)
                ->get(),
            'activeFinancialAccounts' => FinancialAccount::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeSupplier(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:yarn_supplier,sizing_mill,parts_vendor',
            'phone' => 'nullable|string|max:60',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:1000',
            'opening_payable' => 'nullable|numeric|min:0|max:999999999999',
        ]);
        $validated['name'] = trim($validated['name']);

        if (Supplier::query()->where('type', $validated['type'])->whereRaw('LOWER(name) = ?', [mb_strtolower($validated['name'])])->exists()) {
            throw ValidationException::withMessages(['name' => 'A supplier account with this name and type already exists.']);
        }

        DB::transaction(function () use ($validated, $ledgerPostingService): void {
            $supplier = Supplier::query()->create([
                ...$validated,
                'opening_payable' => round((float) ($validated['opening_payable'] ?? 0), 2),
            ]);
            $openingPayable = (float) $supplier->opening_payable;

            if ($openingPayable > 0) {
                $ledgerPostingService->post('supplier_opening_balance', $supplier->id, [
                    [
                        'role' => 'supplier',
                        'party_name' => $supplier->name,
                        'party_type' => $supplier->type,
                        'transaction_date' => $supplier->created_at->toDateString(),
                        'voucher_no' => 'OPEN-SUP-'.$supplier->id,
                        'description' => 'Supplier opening payable balance',
                        'debit' => 0,
                        'credit' => $openingPayable,
                        'notes' => null,
                    ],
                    [
                        'role' => 'equity',
                        'party_name' => 'Opening Balance Equity',
                        'party_type' => 'equity',
                        'transaction_date' => $supplier->created_at->toDateString(),
                        'voucher_no' => 'OPEN-SUP-'.$supplier->id,
                        'description' => 'Opening supplier balance',
                        'debit' => $openingPayable,
                        'credit' => 0,
                        'notes' => null,
                    ],
                ]);
            }
        });

        return redirect()->route('accounts.index')->with('success', 'Supplier account created. You can now record purchases and payments against it.');
    }

    public function storeSupplierPayment(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'financial_account_id' => 'required|exists:financial_accounts,id,is_active,1',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|gt:0|max:999999999999',
            'reference' => 'nullable|string|max:120',
            'notes' => 'nullable|string|max:1000',
        ]);
        DB::transaction(function () use ($validated, $ledgerPostingService): void {
            $supplier = Supplier::query()->whereKey($validated['supplier_id'])->where('status', 'active')->lockForUpdate()->firstOrFail();
            $account = FinancialAccount::query()
                ->whereKey($validated['financial_account_id'])
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();
            $amount = round((float) $validated['amount'], 2);
            $transaction = AccountTransaction::query()->create([
                'financial_account_id' => $account->id,
                'party_name' => $supplier->name,
                'party_type' => $supplier->type,
                'notes' => $validated['notes'] ?? null,
                'transaction_date' => $validated['payment_date'],
                'direction' => 'out',
                'amount' => $amount,
                'description' => 'Payment to '.$supplier->name,
                'reference' => $validated['reference'] ?? null,
            ]);

            $ledgerPostingService->post('account_transaction', $transaction->id, [
                [
                    'role' => 'party',
                    'party_name' => $supplier->name,
                    'party_type' => $supplier->type,
                    'transaction_date' => $transaction->transaction_date,
                    'voucher_no' => $transaction->reference ?: 'SUP-PAY-'.$transaction->id,
                    'description' => 'Payment made to '.$supplier->name,
                    'debit' => $amount,
                    'credit' => 0,
                    'notes' => $transaction->notes,
                ],
                [
                    'role' => 'bank',
                    'party_name' => $account->name,
                    'party_type' => 'cash_bank',
                    'transaction_date' => $transaction->transaction_date,
                    'voucher_no' => $transaction->reference ?: 'SUP-PAY-'.$transaction->id,
                    'description' => 'Supplier payment from '.$account->name,
                    'debit' => 0,
                    'credit' => $amount,
                    'notes' => $transaction->notes,
                ],
            ]);
        });

        return redirect()->route('accounts.suppliers.show', Supplier::findOrFail($validated['supplier_id']))
            ->with('success', 'Supplier payment recorded and posted to the party and cash/bank ledgers.');
    }
}
