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
        Schema::table('sizing_beams', function (Blueprint $table) {
            $table->foreignId('fabric_stock_id')->nullable()->after('sizing_slip_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sizing_beams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fabric_stock_id');
        });
    }
};
