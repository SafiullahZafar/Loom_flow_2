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
        Schema::table('fabric_stock_movements', function (Blueprint $table) {
            $table->decimal('returned_meters', 14, 2)->default(0)->after('dispatch_meters');
            $table->unsignedInteger('rolls_returned')->default(0)->after('rolls_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fabric_stock_movements', function (Blueprint $table) {
            $table->dropColumn(['returned_meters', 'rolls_returned']);
        });
    }
};
