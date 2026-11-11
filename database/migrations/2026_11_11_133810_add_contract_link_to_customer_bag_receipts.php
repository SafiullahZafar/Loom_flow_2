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
        Schema::table('bag_transactions', function (Blueprint $table): void {
            $table->foreignId('sales_contract_id')->nullable()->after('work_order_id')->constrained()->nullOnDelete();
            $table->string('component', 10)->nullable()->after('owner_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_transactions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('sales_contract_id');
            $table->dropColumn('component');
        });
    }
};
