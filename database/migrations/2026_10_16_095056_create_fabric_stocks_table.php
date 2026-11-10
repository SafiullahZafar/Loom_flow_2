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
        Schema::create('fabric_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('quality_name')->unique();
            $table->string('construction')->nullable(); // e.g. 40x40 / 133x72
            $table->decimal('width_inches', 6, 2)->default(63.0);
            $table->decimal('total_meters_produced', 12, 2)->default(0);
            $table->integer('total_rolls_in_stock')->default(0);
            $table->decimal('current_stock_meters', 12, 2)->default(0);
            $table->decimal('committed_contract_meters', 12, 2)->default(0);
            $table->decimal('available_open_meters', 12, 2)->default(0);
            $table->decimal('unit_cost_meter', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabric_stocks');
    }
};
