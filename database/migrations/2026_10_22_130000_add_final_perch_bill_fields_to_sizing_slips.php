<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sizing_slips', function (Blueprint $table): void {
            $table->string('slip_type', 20)->default('raw_perch')->after('slip_no');
            $table->unsignedInteger('wet_cone_bags')->default(0);
            $table->decimal('wet_cone_weight_kg', 12, 2)->default(0);
            $table->unsignedInteger('empty_bag_count')->default(0);
            $table->decimal('empty_bag_weight_kg', 12, 2)->default(0);
            $table->unsignedInteger('paper_bag_count')->default(0);
            $table->decimal('paper_bag_weight_kg', 12, 2)->default(0);
            $table->unsignedInteger('sound_cone_count')->default(0);
            $table->decimal('sound_cone_weight_kg', 12, 2)->default(0);
            $table->unsignedInteger('carton_count')->default(0);
            $table->decimal('carton_weight_kg', 12, 2)->default(0);
            $table->decimal('returned_materials_weight_kg', 12, 2)->default(0);
            $table->decimal('sizing_rate_per_kg', 12, 2)->nullable();
            $table->decimal('sizing_bill_amount', 14, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sizing_slips', function (Blueprint $table): void {
            $table->dropColumn([
                'slip_type',
                'wet_cone_bags', 'wet_cone_weight_kg',
                'empty_bag_count', 'empty_bag_weight_kg',
                'paper_bag_count', 'paper_bag_weight_kg',
                'sound_cone_count', 'sound_cone_weight_kg',
                'carton_count', 'carton_weight_kg',
                'returned_materials_weight_kg', 'sizing_rate_per_kg', 'sizing_bill_amount',
            ]);
        });
    }
};
