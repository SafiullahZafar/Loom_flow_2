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
        Schema::table('productions', function (Blueprint $table): void {
            $table->decimal('gazana_per_roll', 12, 2)->nullable();
            $table->unsignedInteger('roll_quantity')->nullable();
            $table->decimal('total_gazana', 14, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productions', function (Blueprint $table): void {
            $table->dropColumn(['gazana_per_roll', 'roll_quantity', 'total_gazana']);
        });
    }
};
