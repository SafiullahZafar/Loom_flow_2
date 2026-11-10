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
            $table->string('beam_name')->nullable()->after('beam_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sizing_beams', function (Blueprint $table) {
            $table->dropColumn('beam_name');
        });
    }
};
