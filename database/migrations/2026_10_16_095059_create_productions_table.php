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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->date('production_date');
            $table->string('shift')->default('Day'); // Day, Night
            $table->string('loom_no')->index();
            $table->string('operator_name');
            $table->foreignId('sizing_beam_id')->nullable()->constrained('sizing_beams')->nullOnDelete();
            $table->string('fabric_quality');
            $table->decimal('start_meter', 10, 2)->default(0);
            $table->decimal('end_meter', 10, 2)->default(0);
            $table->decimal('net_meters_produced', 10, 2);
            $table->decimal('beam_meters_consumed', 10, 2);
            $table->decimal('leakage_waste_meters', 10, 2)->default(0);
            $table->decimal('leakage_percentage', 5, 2)->default(0);
            $table->integer('picks_per_inch')->default(60);
            $table->integer('rpm')->default(350);
            $table->decimal('efficiency_percentage', 5, 2)->default(85.00);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
