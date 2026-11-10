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
        Schema::create('sales_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_no')->unique();
            $table->string('buyer_name');
            $table->string('buyer_phone')->nullable();
            $table->string('fabric_quality');
            $table->decimal('total_meters', 12, 2);
            $table->decimal('rate_per_meter', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('delivered_meters', 12, 2)->default(0);
            $table->decimal('remaining_meters', 12, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_contracts');
    }
};
