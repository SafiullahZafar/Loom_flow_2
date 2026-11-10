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
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('part_name');
            $table->string('part_code')->unique();
            $table->string('category')->default('Mechanical'); // Gripper, Rapier, Sensor, Heald & Dropper, Mechanical, Consumables
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_alert_threshold')->default(5);
            $table->string('unit')->default('pcs');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('rack_location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
