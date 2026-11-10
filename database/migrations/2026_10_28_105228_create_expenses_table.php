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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_expense_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('financial_account_id')->constrained()->restrictOnDelete();
            $table->string('expense_type', 20);
            $table->string('title', 150);
            $table->date('expense_date');
            $table->decimal('amount', 14, 2);
            $table->string('payee', 150)->nullable();
            $table->string('reference', 120)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['expense_date', 'expense_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
