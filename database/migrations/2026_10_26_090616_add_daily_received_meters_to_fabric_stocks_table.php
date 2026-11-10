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
        if (! Schema::hasColumn('fabric_stocks', 'daily_received_meters')) {
            Schema::table('fabric_stocks', function (Blueprint $table): void {
                $table->decimal('daily_received_meters', 12, 2)->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('fabric_stocks', 'daily_received_meters')) {
            Schema::table('fabric_stocks', function (Blueprint $table): void {
                $table->dropColumn('daily_received_meters');
            });
        }
    }
};
