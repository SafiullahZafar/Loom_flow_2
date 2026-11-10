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
        foreach (['sales_contracts', 'work_orders', 'sales', 'ledgers'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('customer_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            });
        }

        $customerIdsByName = DB::table('customers')->get(['id', 'name'])
            ->groupBy(fn (object $customer): string => mb_strtolower(trim((string) $customer->name)))
            ->filter(fn ($customers): bool => $customers->count() === 1)
            ->map(fn ($customers): int => (int) $customers->first()->id);

        foreach (DB::table('sales_contracts')->whereNull('customer_id')->orderBy('id')->get(['id', 'buyer_name']) as $contract) {
            $customerId = $customerIdsByName->get(mb_strtolower(trim((string) $contract->buyer_name)));
            if ($customerId) {
                DB::table('sales_contracts')->where('id', $contract->id)->update(['customer_id' => $customerId]);
            }
        }

        foreach (DB::table('work_orders')->whereNull('customer_id')->orderBy('id')->get(['id', 'party_name', 'sales_contract_id']) as $order) {
            $customerId = $order->sales_contract_id
                ? DB::table('sales_contracts')->where('id', $order->sales_contract_id)->value('customer_id')
                : null;
            $customerId ??= $customerIdsByName->get(mb_strtolower(trim((string) $order->party_name)));
            if ($customerId) {
                DB::table('work_orders')->where('id', $order->id)->update(['customer_id' => $customerId]);
            }
        }

        foreach (DB::table('sales')->whereNull('customer_id')->orderBy('id')->get(['id', 'buyer_name', 'sales_contract_id']) as $sale) {
            $customerId = $sale->sales_contract_id
                ? DB::table('sales_contracts')->where('id', $sale->sales_contract_id)->value('customer_id')
                : null;
            $customerId ??= $customerIdsByName->get(mb_strtolower(trim((string) $sale->buyer_name)));
            if ($customerId) {
                DB::table('sales')->where('id', $sale->id)->update(['customer_id' => $customerId]);
            }
        }

        foreach (DB::table('ledgers')->where('party_type', 'fabric_buyer')->whereNull('customer_id')->where('source_type', 'invoice')->get(['id', 'source_id']) as $ledger) {
            $customerId = DB::table('invoices')->where('id', $ledger->source_id)->value('customer_id');
            if ($customerId) {
                DB::table('ledgers')->where('id', $ledger->id)->update(['customer_id' => $customerId]);
            }
        }

        foreach (DB::table('ledgers')->where('party_type', 'fabric_buyer')->whereNull('customer_id')->where('source_type', 'payment_receipt')->get(['id', 'source_id']) as $ledger) {
            $customerId = DB::table('payment_receipts')->where('id', $ledger->source_id)->value('customer_id');
            if ($customerId) {
                DB::table('ledgers')->where('id', $ledger->id)->update(['customer_id' => $customerId]);
            }
        }

        foreach (DB::table('ledgers')->where('party_type', 'fabric_buyer')->whereNull('customer_id')->where('source_type', 'customer_opening_balance')->get(['id', 'source_id']) as $ledger) {
            if (DB::table('customers')->where('id', $ledger->source_id)->exists()) {
                DB::table('ledgers')->where('id', $ledger->id)->update(['customer_id' => $ledger->source_id]);
            }
        }

        foreach (DB::table('ledgers')->where('party_type', 'fabric_buyer')->whereNull('customer_id')->orderBy('id')->get(['id', 'party_name']) as $ledger) {
            $customerId = $customerIdsByName->get(mb_strtolower(trim((string) $ledger->party_name)));
            if ($customerId) {
                DB::table('ledgers')->where('id', $ledger->id)->update(['customer_id' => $customerId]);
            }
        }

        foreach (DB::table('ledgers')->whereNotNull('customer_id')->distinct()->pluck('customer_id') as $customerId) {
            $balance = 0.0;
            foreach (DB::table('ledgers')->where('customer_id', $customerId)->where('party_type', 'fabric_buyer')
                ->orderBy('transaction_date')->orderBy('id')->get(['id', 'debit', 'credit']) as $entry) {
                $balance = round($balance + (float) $entry->debit - (float) $entry->credit, 2);
                DB::table('ledgers')->where('id', $entry->id)->update(['balance' => $balance]);
            }
        }

        Schema::table('ledgers', function (Blueprint $table): void {
            $table->index(['customer_id', 'party_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledgers', function (Blueprint $table): void {
            $table->dropIndex(['customer_id', 'party_type']);
        });

        foreach (['ledgers', 'sales', 'work_orders', 'sales_contracts'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('customer_id');
            });
        }
    }
};
