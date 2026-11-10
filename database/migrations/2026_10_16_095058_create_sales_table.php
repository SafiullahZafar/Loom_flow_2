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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('slip_no')->unique();
            $table->string('sale_type')->default('open_market'); // open_market, contract
            $table->foreignId('sales_contract_id')->nullable()->constrained('sales_contracts')->nullOnDelete();
            $table->string('buyer_name');
            $table->string('buyer_phone')->nullable();
            $table->string('fabric_quality');
            $table->decimal('meters_sold', 12, 2);
            $table->integer('rolls_sold')->default(1);
            $table->decimal('rate_per_meter', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('net_total', 12, 2);
            $table->string('payment_type')->default('credit'); // cash, credit, bank_transfer
            $table->string('payment_status')->default('paid'); // paid, partial, unpaid
            $table->date('sale_date');
            $table->string('vehicle_no')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
