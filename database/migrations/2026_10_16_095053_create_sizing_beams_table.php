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
        Schema::create('sizing_beams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sizing_slip_id')->nullable()->constrained('sizing_slips')->nullOnDelete();
            $table->string('beam_no')->unique();
            $table->string('yarn_quality');
            $table->decimal('total_length_meters', 10, 2);
            $table->decimal('remaining_length_meters', 10, 2);
            $table->string('assigned_loom_no')->nullable()->index();
            $table->string('status')->default('ready_in_store'); // ready_in_store, mounted_on_loom, exhausted
            $table->date('mounted_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizing_beams');
    }
};
