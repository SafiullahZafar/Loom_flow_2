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
        Schema::table('account_transactions', function (Blueprint $table): void {
            $table->string('party_name', 150)->nullable();
            $table->string('party_type', 40)->nullable();
            $table->text('notes')->nullable();
            $table->index(['party_type', 'party_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_transactions', function (Blueprint $table): void {
            $table->dropIndex(['party_type', 'party_name']);
            $table->dropColumn(['party_name', 'party_type', 'notes']);
        });
    }
};
