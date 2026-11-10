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
        Schema::table('fabric_stocks', function (Blueprint $table) {
            $table->decimal('read', 10, 4)->nullable();
            $table->decimal('pick', 10, 4)->nullable();
            $table->decimal('warp_count', 10, 4)->nullable();
            $table->decimal('weft_count', 10, 4)->nullable();
            $table->decimal('warp_weight_per_meter_lb', 12, 4)->nullable();
            $table->decimal('weft_weight_per_meter_lb', 12, 4)->nullable();
            $table->decimal('total_weight_per_meter_lb', 12, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fabric_stocks', function (Blueprint $table) {
            $table->dropColumn([
                'read',
                'pick',
                'warp_count',
                'weft_count',
                'warp_weight_per_meter_lb',
                'weft_weight_per_meter_lb',
                'total_weight_per_meter_lb',
            ]);
        });
    }
};
