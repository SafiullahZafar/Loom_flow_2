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
        Schema::table('bag_purchases', function (Blueprint $table): void {
            $table->decimal('total_bags', 12, 2)->change();
            $table->decimal('bags_to_sizing', 12, 2)->default(0)->change();
            $table->decimal('bags_to_factory', 12, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_purchases', function (Blueprint $table): void {
            $table->integer('total_bags')->change();
            $table->integer('bags_to_sizing')->default(0)->change();
            $table->integer('bags_to_factory')->default(0)->change();
        });
    }
};
