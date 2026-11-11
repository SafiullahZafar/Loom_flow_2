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
        Schema::table('bag_transactions', function (Blueprint $table): void {
            $table->unsignedSmallInteger('cones_per_bag')->nullable()->after('quantity_bags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_transactions', function (Blueprint $table): void {
            $table->dropColumn('cones_per_bag');
        });
    }
};
