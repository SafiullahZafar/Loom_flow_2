<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 50)->nullable()->unique();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('financial_accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('type', 20);
            $table->string('account_number', 80)->nullable();
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->string('invoice_no', 100)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('sales_contract_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->unique()->constrained('sales')->nullOnDelete();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 14, 2);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->string('status')->default('issued');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description', 200);
            $table->decimal('quantity', 14, 3);
            $table->string('unit', 30)->default('Meter');
            $table->decimal('rate', 14, 2);
            $table->decimal('amount', 14, 2);
            $table->timestamps();
        });

        Schema::create('payment_receipts', function (Blueprint $table): void {
            $table->id();
            $table->string('receipt_no', 100)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('financial_account_id')->constrained()->restrictOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 14, 2);
            $table->string('method', 40);
            $table->string('reference', 120)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_payment_allocations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->timestamps();
            $table->unique(['payment_receipt_id', 'invoice_id']);
        });

        Schema::create('account_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('financial_account_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_receipt_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->date('transaction_date');
            $table->string('direction', 10);
            $table->decimal('amount', 14, 2);
            $table->string('description', 200);
            $table->string('reference', 120)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
        Schema::dropIfExists('invoice_payment_allocations');
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('financial_accounts');
        Schema::dropIfExists('customers');
    }
};
