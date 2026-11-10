<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ledgers', function (Blueprint $table): void {
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('source_role', 40)->nullable();
            $table->boolean('is_migrated_legacy')->default(false);
            $table->decimal('legacy_debit', 14, 2)->nullable();
            $table->decimal('legacy_credit', 14, 2)->nullable();
            $table->decimal('legacy_balance', 14, 2)->nullable();
            $table->unique(['source_type', 'source_id', 'source_role'], 'ledgers_source_line_unique');
        });

        $writeLine = static function (
            string $sourceType,
            int $sourceId,
            string $sourceRole,
            string $partyName,
            string $partyType,
            string $date,
            ?string $voucher,
            string $description,
            float $debit,
            float $credit,
            ?string $notes = null
        ): void {
            $source = ['source_type' => $sourceType, 'source_id' => $sourceId, 'source_role' => $sourceRole];
            $values = [
                'party_name' => $partyName,
                'party_type' => $partyType,
                'transaction_date' => $date,
                'voucher_no' => $voucher,
                'description' => $description,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => 0,
                'notes' => $notes,
                'updated_at' => now(),
            ];
            $existingLine = DB::table('ledgers')->where($source)->first();
            if ($existingLine) {
                DB::table('ledgers')->where('id', $existingLine->id)->update($values);
            } else {
                DB::table('ledgers')->insert([...$source, ...$values, 'created_at' => now()]);
            }
        };

        foreach (DB::table('bag_purchases')->get() as $purchase) {
            $legacySupplierLine = DB::table('ledgers')
                ->where('party_type', 'yarn_supplier')
                ->where('party_name', $purchase->supplier_name)
                ->where('voucher_no', $purchase->challan_no)
                ->whereNull('source_type')
                ->orderBy('id')
                ->first();
            if ($legacySupplierLine) {
                DB::table('ledgers')->where('id', $legacySupplierLine->id)->update([
                    'source_type' => 'bag_purchase', 'source_id' => $purchase->id, 'source_role' => 'supplier', 'is_migrated_legacy' => true,
                    'legacy_debit' => $legacySupplierLine->debit, 'legacy_credit' => $legacySupplierLine->credit, 'legacy_balance' => $legacySupplierLine->balance,
                ]);
            }
            $amount = (float) $purchase->total_amount;
            $writeLine('bag_purchase', $purchase->id, 'supplier', $purchase->supplier_name, 'yarn_supplier', $purchase->purchase_date, $purchase->challan_no,
                "Yarn Purchase: {$purchase->total_bags} bags ({$purchase->quality}) @ Rs. {$purchase->rate_per_bag}", 0, $amount, $purchase->notes);
            $writeLine('bag_purchase', $purchase->id, 'inventory', 'Yarn Inventory', 'inventory', $purchase->purchase_date, $purchase->challan_no,
                "Yarn purchased: {$purchase->total_bags} bags ({$purchase->quality})", $amount, 0, $purchase->notes);
        }

        foreach (DB::table('sales')->get() as $sale) {
            $legacyBuyerLine = DB::table('ledgers')
                ->where('party_type', 'fabric_buyer')
                ->where('voucher_no', $sale->slip_no)
                ->whereNull('source_type')
                ->orderBy('id')
                ->first();
            if ($legacyBuyerLine) {
                DB::table('ledgers')->where('id', $legacyBuyerLine->id)->update([
                    'source_type' => 'sale', 'source_id' => $sale->id, 'source_role' => 'buyer', 'is_migrated_legacy' => true,
                    'legacy_debit' => $legacyBuyerLine->debit, 'legacy_credit' => $legacyBuyerLine->credit, 'legacy_balance' => $legacyBuyerLine->balance,
                ]);
            }
            $isPaid = $sale->payment_status === 'paid';
            $writeLine('sale', $sale->id, 'buyer', $sale->buyer_name, 'fabric_buyer', $sale->sale_date, $sale->slip_no,
                "Fabric Sale: {$sale->meters_sold}m {$sale->fabric_quality} ({$sale->sale_type})", $isPaid ? 0 : (float) $sale->net_total, 0,
                "Payment Type: {$sale->payment_type}, Status: {$sale->payment_status}");
            if ($isPaid) {
                $writeLine('sale', $sale->id, 'cash', 'Cash / Bank Clearing', 'cash_bank', $sale->sale_date, $sale->slip_no,
                    'Payment received for fabric sale '.$sale->slip_no, (float) $sale->net_total, 0, 'Legacy paid sale');
            }
            $writeLine('sale', $sale->id, 'revenue', 'Fabric Sales Revenue', 'income', $sale->sale_date, $sale->slip_no,
                "Fabric Sale: {$sale->meters_sold}m {$sale->fabric_quality} ({$sale->sale_type})", 0, (float) $sale->net_total, null);
        }

        foreach (DB::table('customers')->get() as $customer) {
            if ((float) $customer->opening_balance <= 0) {
                continue;
            }
            $date = substr($customer->created_at, 0, 10);
            $voucher = 'OPEN-CUS-'.$customer->id;
            $writeLine('customer_opening_balance', $customer->id, 'customer', $customer->name, 'fabric_buyer', $date, $voucher, 'Customer opening receivable balance', (float) $customer->opening_balance, 0);
            $writeLine('customer_opening_balance', $customer->id, 'equity', 'Opening Balance Equity', 'equity', $date, $voucher, 'Opening customer balance', 0, (float) $customer->opening_balance);
        }

        foreach (DB::table('financial_accounts')->get() as $account) {
            if ((float) $account->opening_balance <= 0) {
                continue;
            }
            $date = substr($account->created_at, 0, 10);
            $voucher = 'OPEN-ACC-'.$account->id;
            $writeLine('account_opening_balance', $account->id, 'account', $account->name, 'cash_bank', $date, $voucher, 'Cash/bank opening balance', (float) $account->opening_balance, 0, $account->account_number);
            $writeLine('account_opening_balance', $account->id, 'equity', 'Opening Balance Equity', 'equity', $date, $voucher, 'Opening cash/bank balance', 0, (float) $account->opening_balance, $account->account_number);
        }

        foreach (DB::table('expenses')->join('financial_accounts', 'financial_accounts.id', '=', 'expenses.financial_account_id')
            ->select('expenses.*', 'financial_accounts.name as account_name')->get() as $expense) {
            $voucher = $expense->reference ?: 'EXP-'.$expense->id;
            $party = filled($expense->payee) ? $expense->payee : 'Expense: '.$expense->title;
            $writeLine('expense', $expense->id, 'expense', $party, 'expense', $expense->expense_date, $voucher, ucfirst($expense->expense_type).' expense: '.$expense->title, (float) $expense->amount, 0, $expense->notes);
            $writeLine('expense', $expense->id, 'account', $expense->account_name, 'cash_bank', $expense->expense_date, $voucher, 'Payment for '.$expense->title, 0, (float) $expense->amount, $expense->notes);
        }

        foreach (DB::table('invoices')->join('customers', 'customers.id', '=', 'invoices.customer_id')->whereNull('invoices.sale_id')->select('invoices.*', 'customers.name as customer_name')->get() as $invoice) {
            $writeLine('invoice', $invoice->id, 'customer', $invoice->customer_name, 'fabric_buyer', $invoice->invoice_date, $invoice->invoice_no, 'Invoice issued to customer', (float) $invoice->total, 0, $invoice->notes);
            $writeLine('invoice', $invoice->id, 'revenue', 'Fabric Sales Revenue', 'income', $invoice->invoice_date, $invoice->invoice_no, 'Revenue from customer invoice', 0, (float) $invoice->total, $invoice->notes);
        }

        foreach (DB::table('payment_receipts')->join('customers', 'customers.id', '=', 'payment_receipts.customer_id')->join('financial_accounts', 'financial_accounts.id', '=', 'payment_receipts.financial_account_id')->select('payment_receipts.*', 'customers.name as customer_name', 'financial_accounts.name as account_name')->get() as $payment) {
            $voucher = $payment->receipt_no;
            $writeLine('payment_receipt', $payment->id, 'account', $payment->account_name, 'cash_bank', $payment->payment_date, $voucher, 'Customer payment received into '.$payment->account_name, (float) $payment->amount, 0, $payment->notes);
            $writeLine('payment_receipt', $payment->id, 'customer', $payment->customer_name, 'fabric_buyer', $payment->payment_date, $voucher, 'Customer receipt applied / held as credit', 0, (float) $payment->amount, $payment->notes);
        }

        foreach (DB::table('account_transactions')->join('financial_accounts', 'financial_accounts.id', '=', 'account_transactions.financial_account_id')->whereNull('account_transactions.expense_id')->whereNull('account_transactions.payment_receipt_id')->select('account_transactions.*', 'financial_accounts.name as account_name')->get() as $transaction) {
            $isMoneyIn = $transaction->direction === 'in';
            $voucher = $transaction->reference ?: 'ACC-'.$transaction->id;
            $writeLine('account_transaction', $transaction->id, 'account', $transaction->account_name, 'cash_bank', $transaction->transaction_date, $voucher, $transaction->description, $isMoneyIn ? (float) $transaction->amount : 0, $isMoneyIn ? 0 : (float) $transaction->amount);
            $writeLine('account_transaction', $transaction->id, 'counterpart', $isMoneyIn ? 'Other Income / Deposit' : 'Other Expense / Withdrawal', $isMoneyIn ? 'income' : 'expense', $transaction->transaction_date, $voucher, $transaction->description, $isMoneyIn ? 0 : (float) $transaction->amount, $isMoneyIn ? (float) $transaction->amount : 0);
        }

        foreach (DB::table('sizing_slips')->whereNotNull('sizing_bill_amount')->where('sizing_bill_amount', '>', 0)->get() as $slip) {
            $writeLine('sizing_slip', $slip->id, 'sizing_mill', $slip->sizing_party, 'sizing_mill', $slip->slip_date, $slip->slip_no, 'Sizing service bill for '.$slip->slip_no, 0, (float) $slip->sizing_bill_amount, $slip->notes);
            $writeLine('sizing_slip', $slip->id, 'expense', 'Sizing Service Expense', 'expense', $slip->slip_date, $slip->slip_no, 'Sizing service expense for '.$slip->slip_no, (float) $slip->sizing_bill_amount, 0, $slip->notes);
        }

        foreach (DB::table('parts')->get() as $part) {
            $issuedValue = (float) DB::table('part_issuances')->where('part_id', $part->id)->sum('total_cost');
            $openingValue = round(((int) $part->stock_quantity * (float) $part->unit_price) + $issuedValue, 2);
            if ($openingValue > 0) {
                $date = substr($part->created_at, 0, 10);
                $voucher = 'PART-OPEN-'.$part->id;
                $writeLine('part_opening_inventory', $part->id, 'inventory', 'Parts Inventory', 'inventory', $date, $voucher, 'Opening parts inventory: '.$part->part_name, $openingValue, 0, $part->part_code);
                $writeLine('part_opening_inventory', $part->id, 'equity', 'Opening Inventory Equity', 'equity', $date, $voucher, 'Opening value for parts inventory', 0, $openingValue, $part->part_code);
            }
        }

        foreach (DB::table('part_issuances')->join('parts', 'parts.id', '=', 'part_issuances.part_id')->select('part_issuances.*', 'parts.part_name')->get() as $issuance) {
            if ((float) $issuance->total_cost <= 0) {
                continue;
            }
            $voucher = 'PART-ISSUE-'.$issuance->id;
            $writeLine('part_issuance', $issuance->id, 'expense', 'Factory Parts Expense', 'expense', $issuance->issuance_date, $voucher, 'Parts issued to '.$issuance->loom_no.': '.$issuance->part_name, (float) $issuance->total_cost, 0, $issuance->remarks);
            $writeLine('part_issuance', $issuance->id, 'inventory', 'Parts Inventory', 'inventory', $issuance->issuance_date, $voucher, 'Parts issued from inventory: '.$issuance->part_name, 0, (float) $issuance->total_cost, $issuance->remarks);
        }

        foreach (DB::table('ledgers')->select('party_name', 'party_type')->distinct()->get() as $party) {
            $balance = 0.0;
            foreach (DB::table('ledgers')->where('party_name', $party->party_name)->where('party_type', $party->party_type)->orderBy('transaction_date')->orderBy('id')->get() as $entry) {
                $balance = round($balance + (float) $entry->debit - (float) $entry->credit, 2);
                DB::table('ledgers')->where('id', $entry->id)->update(['balance' => $balance]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('ledgers')->whereNotNull('source_type')->where('is_migrated_legacy', false)->delete();
        DB::table('ledgers')->where('is_migrated_legacy', true)->update([
            'source_type' => null,
            'source_id' => null,
            'source_role' => null,
            'is_migrated_legacy' => false,
            'debit' => DB::raw('legacy_debit'),
            'credit' => DB::raw('legacy_credit'),
            'balance' => DB::raw('legacy_balance'),
            'legacy_debit' => null,
            'legacy_credit' => null,
            'legacy_balance' => null,
        ]);

        Schema::table('ledgers', function (Blueprint $table): void {
            $table->dropUnique('ledgers_source_line_unique');
            $table->dropColumn(['source_type', 'source_id', 'source_role', 'is_migrated_legacy', 'legacy_debit', 'legacy_credit', 'legacy_balance']);
        });
    }
};
