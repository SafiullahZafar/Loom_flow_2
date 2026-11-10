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
        Schema::create('sizing_slips', function (Blueprint $table) {
            $table->id();
            $table->string('slip_no')->unique();
            $table->string('sizing_party');
            $table->string('yarn_quality');
            $table->integer('bags_allocated');
            $table->integer('bags_used')->default(0);
            $table->integer('bags_returned_to_factory')->default(0);
            $table->integer('bags_held_with_sizing')->default(0);
            $table->integer('total_beams')->default(1);
            $table->decimal('total_length_meters', 10, 2)->default(0);
            $table->date('slip_date');
            $table->string('status')->default('completed'); // in_process, completed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizing_slips');
    }
};
