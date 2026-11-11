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
            $table->unsignedSmallInteger('cones_per_bag')->nullable()->after('total_bags');
            $table->foreignId('expense_id')->nullable()->after('total_amount')->constrained('expenses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_purchases', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('expense_id');
            $table->dropColumn('cones_per_bag');
        });
    }
};
