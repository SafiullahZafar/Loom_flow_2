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
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->foreignId('expense_id')->nullable()->unique()->after('payment_receipt_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expense_id');
        });
    }
};
