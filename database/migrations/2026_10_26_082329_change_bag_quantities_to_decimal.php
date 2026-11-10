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
        Schema::table('sizing_slips', function (Blueprint $table) {
            $table->decimal('bags_allocated', 12, 2)->change();
            $table->decimal('bags_used', 12, 2)->default(0)->change();
            $table->decimal('bags_returned_to_factory', 12, 2)->default(0)->change();
            $table->decimal('bags_held_with_sizing', 12, 2)->default(0)->change();
        });

        Schema::table('bag_transactions', function (Blueprint $table) {
            $table->decimal('quantity_bags', 12, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sizing_slips', function (Blueprint $table) {
            $table->integer('bags_allocated')->change();
            $table->integer('bags_used')->default(0)->change();
            $table->integer('bags_returned_to_factory')->default(0)->change();
            $table->integer('bags_held_with_sizing')->default(0)->change();
        });

        Schema::table('bag_transactions', function (Blueprint $table) {
            $table->integer('quantity_bags')->change();
        });
    }
};
