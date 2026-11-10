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
        Schema::table('sizing_slips', function (Blueprint $table): void {
            $table->decimal('sizing_gross_weight_pounds', 10, 2)->nullable()->after('sizing_weight_pounds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sizing_slips', function (Blueprint $table): void {
            $table->dropColumn('sizing_gross_weight_pounds');
        });
    }
};
