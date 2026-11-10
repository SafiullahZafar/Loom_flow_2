<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Customer;
use App\Models\FinancialAccount;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentReceipt;
use App\Models\Sale;
use App\Models\SalesContract;
use App\Models\WorkOrder;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        return $this->renderPage('dashboard', $this->invoiceQuery()->latest('invoice_date')->latest('id')->limit(5)->get(), collect(), collect(), collect());
    }

    public function invoices(): View
    {
        return $this->renderPage('invoices', $this->invoiceQuery()->latest('invoice_date')->latest('id')->paginate(15), collect(), collect(), collect());
    }

    public function customers(): View
    {
        return $this->renderPage('customers', collect(), Customer::withCount('invoices')->withSum('invoices', 'total')->withSum('payments', 'amount')->orderBy('name')->paginate(20), collect(), collect());
    }

    public function payments(): View
    {
        return $this->renderPage('payments', collect(), collect(), PaymentReceipt::with(['customer', 'account', 'allocations.invoice'])->latest('payment_date')->latest('id')->paginate(20), collect());
    }

    public function accounts(): View
    {
        $accounts = FinancialAccount::withSum(['transactions as money_in' => fn ($query) => $query->where('direction', 'in')], 'amount')
            ->withSum(['transactions as money_out' => fn ($query) => $query->where('direction', 'out')], 'amount')
            ->with(['transactions' => fn ($query) => $query->latest('transaction_date')->latest('id')->limit(8)])
            ->orderBy('type')->orderBy('name')->get();

        return $this->renderPage('accounts', collect(), collect(), collect(), $accounts);
    }

    /**
     * @param  Collection<int, Invoice>|LengthAwarePaginator  $invoices
     * @param  Collection<int, Customer>|LengthAwarePaginator  $customers
     * @param  Collection<int, PaymentReceipt>|LengthAwarePaginator  $payments
     * @param  Collection<int, FinancialAccount>  $accounts
     */
    private function renderPage(string $page, Collection|LengthAwarePaginator $invoices, Collection|LengthAwarePaginator $customers, Collection|LengthAwarePaginator $payments, Collection $accounts): View
    {
        $invoiceOptions = Invoice::with(['customer'])->withSum('allocations', 'amount')
            ->where('status', 'issued')->orderBy('invoice_date')->orderBy('id')->get()
            ->filter(fn (Invoice $invoice): bool => (float) $invoice->total > (float) ($invoice->allocations_sum_amount ?? 0))
            ->values();
        $activeCustomers = Customer::where('status', 'active')->orderBy('name')->get();
        $activeAccounts = FinancialAccount::where('is_active', true)->orderBy('name')->get();
        $availableSales = Sale::with('contract')->whereDoesntHave('invoice')->latest('sale_date')->get();
        $billedContractMeters = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->whereNotNull('invoices.sales_contract_id')
            ->where('invoice_items.unit', 'Meter')
            ->groupBy('invoices.sales_contract_id')
            ->selectRaw('invoices.sales_contract_id, SUM(invoice_items.quantity) as billed_meters')
            ->pluck('billed_meters', 'sales_contract_id');
        $contractOptions = SalesContract::latest('start_date')->get();
        foreach ($contractOptions as $contractOption) {
            $contractOption->invoiceable_meters = max(0, (float) $contractOption->delivered_meters - (float) $billedContractMeters->get($contractOption->id, 0));
        }
        $totalInvoiced = (float) Invoice::where('status', 'issued')->sum('total');
        $totalReceived = (float) PaymentReceipt::sum('amount');
        $totalDue = max(0, $totalInvoiced - $totalReceived + (float) Customer::sum('opening_balance'));
        $totalAccountBalance = (float) FinancialAccount::sum('opening_balance')
            + (float) AccountTransaction::where('direction', 'in')->sum('amount')
            - (float) AccountTransaction::where('direction', 'out')->sum('amount');

        return view('invoices.index', compact(
            'page', 'invoices', 'customers', 'payments', 'accounts', 'invoiceOptions', 'activeCustomers',
            'activeAccounts', 'availableSales', 'contractOptions', 'totalInvoiced', 'totalReceived', 'totalDue', 'totalAccountBalance'
        ));
    }

    private function invoiceQuery(): Builder
    {
        return Invoice::with(['customer', 'contract', 'sale', 'items'])->withSum('allocations', 'amount');
    }

    public function storeCustomer(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:50|unique:customers,phone',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:1000',
            'opening_balance' => 'nullable|numeric|min:0|max:999999999999',
        ]);
        $validated['name'] = trim($validated['name']);
        if (Customer::whereRaw('LOWER(name) = ?', [mb_strtolower($validated['name'])])->exists()) {
            throw ValidationException::withMessages(['name' => 'This customer already exists. Select the existing customer instead of creating a duplicate.']);
        }
        $validated['phone'] = filled($validated['phone'] ?? null) ? trim($validated['phone']) : null;
        $validated['opening_balance'] = round((float) ($validated['opening_balance'] ?? 0), 2);
        $customer = Customer::create($validated);
        if ((float) $customer->opening_balance > 0) {
            $ledgerPostingService->post('customer_opening_balance', $customer->id, [
                ['role' => 'customer', 'customer_id' => $customer->id, 'party_name' => $customer->name, 'party_type' => 'fabric_buyer', 'transaction_date' => $customer->created_at->toDateString(), 'voucher_no' => 'OPEN-CUS-'.$customer->id, 'description' => 'Customer opening receivable balance', 'debit' => (float) $customer->opening_balance, 'credit' => 0, 'notes' => null],
                ['role' => 'equity', 'party_name' => 'Opening Balance Equity', 'party_type' => 'equity', 'transaction_date' => $customer->created_at->toDateString(), 'voucher_no' => 'OPEN-CUS-'.$customer->id, 'description' => 'Opening customer balance', 'debit' => 0, 'credit' => (float) $customer->opening_balance, 'notes' => null],
            ]);
        }

        return back()->with('success', 'Customer saved. They can now be selected on invoices and payment receipts.');
    }

    public function storeInvoice(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|max:100|unique:invoices,invoice_no',
            'customer_id' => 'required|exists:customers,id',
            'sales_contract_id' => 'nullable|exists:sales_contracts,id',
            'sale_id' => 'nullable|exists:sales,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'discount' => 'nullable|numeric|min:0|max:999999999999',
            'items' => 'required_without:sale_id|array|min:1|max:50',
            'items.*.description' => 'required_without:sale_id|string|max:200',
            'items.*.quantity' => 'required_without:sale_id|numeric|gt:0|max:100000000',
            'items.*.unit' => 'required_without:sale_id|string|max:30',
            'items.*.rate' => 'required_without:sale_id|numeric|min:0|max:999999999999',
            'notes' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($validated, $ledgerPostingService): void {
            $customer = Customer::whereKey($validated['customer_id'])->lockForUpdate()->firstOrFail();
            $sale = isset($validated['sale_id']) ? Sale::with('contract')->whereKey($validated['sale_id'])->lockForUpdate()->firstOrFail() : null;
            $contract = isset($validated['sales_contract_id']) ? SalesContract::whereKey($validated['sales_contract_id'])->lockForUpdate()->firstOrFail() : null;
            $items = [];
            $saleId = null;

            if ($sale) {
                if (Invoice::where('sale_id', $sale->id)->exists()) {
                    throw ValidationException::withMessages(['sale_id' => 'This sales slip already has an invoice.']);
                }
                if ($sale->customer_id && (int) $sale->customer_id !== (int) $customer->id) {
                    throw ValidationException::withMessages(['customer_id' => 'Choose the customer linked to the selected sales slip.']);
                }
                $sale->customer_id = $customer->id;
                $sale->save();
                if ($contract && (int) $sale->sales_contract_id !== (int) $contract->id) {
                    throw ValidationException::withMessages(['sales_contract_id' => 'The selected contract does not belong to this sales slip.']);
                }
                $contract ??= $sale->contract;
                if ($contract && $contract->customer_id && (int) $contract->customer_id !== (int) $customer->id) {
                    throw ValidationException::withMessages(['customer_id' => 'The sales slip and contract belong to a different customer.']);
                }
                if ($contract && ! $contract->customer_id) {
                    $contract->customer_id = $customer->id;
                    $contract->buyer_name = $customer->name;
                    $contract->buyer_phone = $customer->phone;
                    $contract->save();
                }
                $items[] = ['description' => $sale->fabric_quality.' fabric', 'quantity' => (float) $sale->meters_sold,
                    'unit' => 'Meter', 'rate' => (float) $sale->rate_per_meter, 'amount' => (float) $sale->subtotal];
                $subtotal = (float) $sale->subtotal;
                $discount = (float) $sale->discount;
                $saleId = $sale->id;
            } else {
                if ($contract && $contract->customer_id && (int) $contract->customer_id !== (int) $customer->id) {
                    throw ValidationException::withMessages(['customer_id' => 'Choose the customer linked to the selected contract.']);
                }
                if ($contract && ! $contract->customer_id) {
                    $contract->customer_id = $customer->id;
                    $contract->buyer_name = $customer->name;
                    $contract->buyer_phone = $customer->phone;
                    $contract->save();
                }
                foreach ($validated['items'] as $item) {
                    $quantity = round((float) $item['quantity'], 3);
                    $rate = round((float) $item['rate'], 2);
                    $items[] = ['description' => trim($item['description']), 'quantity' => $quantity, 'unit' => trim($item['unit']),
                        'rate' => $rate, 'amount' => round($quantity * $rate, 2)];
                }
                $subtotal = round(array_sum(array_column($items, 'amount')), 2);
                $discount = round((float) ($validated['discount'] ?? 0), 2);
            }

            if ($contract && WorkOrder::where('sales_contract_id', $contract->id)->where('type', 'job')->exists()) {
                throw ValidationException::withMessages(['sales_contract_id' => 'Job-work invoices are created automatically from Pakki accepted metres.']);
            }
            if ($contract) {
                $alreadyBilledMeters = (float) InvoiceItem::where('unit', 'Meter')
                    ->whereHas('invoice', fn ($query) => $query->where('sales_contract_id', $contract->id))
                    ->sum('quantity');
                $invoiceMeters = array_sum(array_map(
                    fn (array $item): float => $item['unit'] === 'Meter' ? (float) $item['quantity'] : 0,
                    $items
                ));
                $availableMeters = max(0, (float) $contract->delivered_meters - $alreadyBilledMeters);
                if ($invoiceMeters > $availableMeters) {
                    throw ValidationException::withMessages(['items' => "This contract has only {$availableMeters} delivered meter(s) left to invoice."]);
                }
            }

            if ($discount >= $subtotal) {
                throw ValidationException::withMessages(['discount' => 'Discount must be less than the invoice subtotal.']);
            }

            $invoice = Invoice::create([
                'invoice_no' => trim($validated['invoice_no']), 'customer_id' => $customer->id,
                'sales_contract_id' => $contract?->id, 'sale_id' => $saleId,
                'invoice_date' => $validated['invoice_date'], 'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotal, 'discount' => $discount, 'total' => round($subtotal - $discount, 2),
                'status' => 'issued', 'notes' => $validated['notes'] ?? null,
            ]);
            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            if ($saleId === null) {
                $ledgerPostingService->post('invoice', $invoice->id, [
                    ['role' => 'customer', 'customer_id' => $customer->id, 'party_name' => $customer->name, 'party_type' => 'fabric_buyer', 'transaction_date' => $invoice->invoice_date, 'voucher_no' => $invoice->invoice_no, 'description' => 'Invoice issued to customer', 'debit' => (float) $invoice->total, 'credit' => 0, 'notes' => $invoice->notes],
                    ['role' => 'revenue', 'party_name' => 'Fabric Sales Revenue', 'party_type' => 'income', 'transaction_date' => $invoice->invoice_date, 'voucher_no' => $invoice->invoice_no, 'description' => 'Revenue from customer invoice', 'debit' => 0, 'credit' => (float) $invoice->total, 'notes' => $invoice->notes],
                ]);
            }
        });

        return redirect()->route('invoices.invoices')->with('success', 'Invoice created and linked to its customer and selected sales/contract record.');
    }

    public function storePayment(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate([
            'receipt_no' => 'required|string|max:100|unique:payment_receipts,receipt_no',
            'customer_id' => 'required|exists:customers,id', 'financial_account_id' => 'required|exists:financial_accounts,id',
            'payment_date' => 'required|date', 'amount' => 'required|numeric|gt:0|max:999999999999',
            'method' => 'required|in:cash,bank_transfer,cheque,card,other', 'reference' => 'nullable|string|max:120',
            'allocation_mode' => 'required|in:oldest,manual,invoice,credit',
            'invoice_id' => 'nullable|required_if:allocation_mode,invoice|exists:invoices,id',
            'allocations' => 'nullable|required_if:allocation_mode,manual|array|min:1|max:50',
            'allocations.*.invoice_id' => 'required_if:allocation_mode,manual|integer|distinct|exists:invoices,id',
            'allocations.*.amount' => 'required_if:allocation_mode,manual|numeric|min:0|max:999999999999',
            'notes' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($validated, $ledgerPostingService): void {
            $customer = Customer::whereKey($validated['customer_id'])->lockForUpdate()->firstOrFail();
            $account = FinancialAccount::whereKey($validated['financial_account_id'])->where('is_active', true)->lockForUpdate()->firstOrFail();
            $allowedMethods = $account->type === 'cash' ? ['cash'] : ['bank_transfer', 'cheque', 'card', 'other'];
            if (! in_array($validated['method'], $allowedMethods, true)) {
                throw ValidationException::withMessages(['method' => 'Choose a payment method that matches the receiving cash or bank account.']);
            }
            $amount = round((float) $validated['amount'], 2);
            $remaining = $amount;
            $allocations = [];
            if ($validated['allocation_mode'] === 'invoice') {
                $invoice = Invoice::whereKey($validated['invoice_id'])->where('customer_id', $customer->id)->where('status', 'issued')->lockForUpdate()->first();
                if (! $invoice) {
                    throw ValidationException::withMessages(['invoice_id' => 'Choose an issued invoice belonging to this customer.']);
                }
                $due = round((float) $invoice->total - (float) $invoice->allocations()->sum('amount'), 2);
                if ($due <= 0) {
                    throw ValidationException::withMessages(['invoice_id' => 'This invoice has no remaining balance.']);
                }
                $allocated = min($remaining, max(0, $due));
                if ($allocated > 0) {
                    $allocations[] = ['invoice_id' => $invoice->id, 'amount' => $allocated];
                    $remaining = round($remaining - $allocated, 2);
                }
            } elseif ($validated['allocation_mode'] === 'manual') {
                foreach ($validated['allocations'] as $row) {
                    $invoice = Invoice::whereKey($row['invoice_id'])->where('customer_id', $customer->id)
                        ->where('status', 'issued')->lockForUpdate()->first();
                    if (! $invoice) {
                        throw ValidationException::withMessages(['allocations' => 'Each invoice allocation must belong to this customer and be issued.']);
                    }
                    $allocationAmount = round((float) $row['amount'], 2);
                    $due = round((float) $invoice->total - (float) $invoice->allocations()->sum('amount'), 2);
                    if ($allocationAmount > $due || $allocationAmount > $remaining) {
                        throw ValidationException::withMessages(['allocations' => 'An allocation cannot exceed the invoice balance or the unallocated receipt amount.']);
                    }
                    if ($allocationAmount > 0) {
                        $allocations[] = ['invoice_id' => $invoice->id, 'amount' => $allocationAmount];
                        $remaining = round($remaining - $allocationAmount, 2);
                    }
                }
            } elseif ($validated['allocation_mode'] === 'oldest') {
                $openInvoices = Invoice::where('customer_id', $customer->id)->where('status', 'issued')
                    ->withSum('allocations', 'amount')->orderBy('invoice_date')->orderBy('id')->lockForUpdate()->get();
                foreach ($openInvoices as $invoice) {
                    $due = round((float) $invoice->total - (float) ($invoice->allocations_sum_amount ?? 0), 2);
                    $allocated = min($remaining, max(0, $due));
                    if ($allocated > 0) {
                        $allocations[] = ['invoice_id' => $invoice->id, 'amount' => $allocated];
                        $remaining = round($remaining - $allocated, 2);
                    }
                    if ($remaining <= 0) {
                        break;
                    }
                }
            }

            $payment = PaymentReceipt::create([
                'receipt_no' => trim($validated['receipt_no']), 'customer_id' => $customer->id,
                'financial_account_id' => $account->id, 'payment_date' => $validated['payment_date'],
                'amount' => $amount, 'method' => $validated['method'], 'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
            foreach ($allocations as $allocation) {
                $payment->allocations()->create($allocation);
                $invoice = Invoice::withSum('allocations', 'amount')->findOrFail($allocation['invoice_id']);
                if ($invoice->sale_id) {
                    $invoiceDue = max(0, (float) $invoice->total - (float) $invoice->allocations_sum_amount);
                    Sale::whereKey($invoice->sale_id)->update([
                        'payment_status' => $invoiceDue <= 0 ? 'paid' : ($invoiceDue < (float) $invoice->total ? 'partial' : 'unpaid'),
                    ]);
                }
            }
            AccountTransaction::create([
                'financial_account_id' => $account->id, 'payment_receipt_id' => $payment->id,
                'transaction_date' => $payment->payment_date, 'direction' => 'in', 'amount' => $amount,
                'description' => 'Customer receipt from '.$customer->name, 'reference' => $payment->reference ?: $payment->receipt_no,
            ]);
            $ledgerPostingService->post('payment_receipt', $payment->id, [
                ['role' => 'account', 'party_name' => $account->name, 'party_type' => 'cash_bank', 'transaction_date' => $payment->payment_date, 'voucher_no' => $payment->receipt_no, 'description' => 'Customer payment received into '.$account->name, 'debit' => $amount, 'credit' => 0, 'notes' => $payment->notes],
                ['role' => 'customer', 'customer_id' => $customer->id, 'party_name' => $customer->name, 'party_type' => 'fabric_buyer', 'transaction_date' => $payment->payment_date, 'voucher_no' => $payment->receipt_no, 'description' => 'Customer receipt applied / held as credit', 'debit' => 0, 'credit' => $amount, 'notes' => $payment->notes],
            ]);
        });

        return redirect()->route('invoices.payments')->with('success', 'Payment received and posted to the selected account. It was allocated to invoices; any excess remains as customer credit.');
    }

    public function storeAccount(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate(['name' => 'required|string|max:120', 'type' => 'required|in:cash,bank',
            'account_number' => 'nullable|string|max:80', 'opening_balance' => 'nullable|numeric|min:0|max:999999999999']);
        $validated['opening_balance'] = round((float) ($validated['opening_balance'] ?? 0), 2);
        $account = FinancialAccount::create($validated);
        if ((float) $account->opening_balance > 0) {
            $ledgerPostingService->post('account_opening_balance', $account->id, [
                ['role' => 'account', 'party_name' => $account->name, 'party_type' => 'cash_bank', 'transaction_date' => $account->created_at->toDateString(), 'voucher_no' => 'OPEN-ACC-'.$account->id, 'description' => 'Cash/bank opening balance', 'debit' => (float) $account->opening_balance, 'credit' => 0, 'notes' => $account->account_number],
                ['role' => 'equity', 'party_name' => 'Opening Balance Equity', 'party_type' => 'equity', 'transaction_date' => $account->created_at->toDateString(), 'voucher_no' => 'OPEN-ACC-'.$account->id, 'description' => 'Opening cash/bank balance', 'debit' => 0, 'credit' => (float) $account->opening_balance, 'notes' => $account->account_number],
            ]);
        }

        return redirect()->route('invoices.accounts')->with('success', 'Cash/bank account added.');
    }

    public function storeAccountTransaction(Request $request, LedgerPostingService $ledgerPostingService): RedirectResponse
    {
        $validated = $request->validate(['financial_account_id' => 'required|exists:financial_accounts,id,is_active,1',
            'transaction_date' => 'required|date', 'direction' => 'required|in:in,out',
            'amount' => 'required|numeric|gt:0|max:999999999999', 'description' => 'required|string|max:200',
            'reference' => 'nullable|string|max:120']);
        DB::transaction(function () use ($validated, $ledgerPostingService): void {
            $account = FinancialAccount::whereKey($validated['financial_account_id'])->where('is_active', true)->lockForUpdate()->firstOrFail();
            $amount = round((float) $validated['amount'], 2);
            $transaction = AccountTransaction::create([...$validated, 'amount' => $amount]);
            $isMoneyIn = $transaction->direction === 'in';
            $ledgerPostingService->post('account_transaction', $transaction->id, [
                ['role' => 'account', 'party_name' => $account->name, 'party_type' => 'cash_bank', 'transaction_date' => $transaction->transaction_date, 'voucher_no' => $transaction->reference ?: 'ACC-'.$transaction->id, 'description' => $transaction->description, 'debit' => $isMoneyIn ? $amount : 0, 'credit' => $isMoneyIn ? 0 : $amount, 'notes' => null],
                ['role' => 'counterpart', 'party_name' => $isMoneyIn ? 'Other Income / Deposit' : 'Other Expense / Withdrawal', 'party_type' => $isMoneyIn ? 'income' : 'expense', 'transaction_date' => $transaction->transaction_date, 'voucher_no' => $transaction->reference ?: 'ACC-'.$transaction->id, 'description' => $transaction->description, 'debit' => $isMoneyIn ? 0 : $amount, 'credit' => $isMoneyIn ? $amount : 0, 'notes' => null],
            ]);

        });

        return back()->with('success', 'Account transaction saved and included in the balance.');
    }
}
