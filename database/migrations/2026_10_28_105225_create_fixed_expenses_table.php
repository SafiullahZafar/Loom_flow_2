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
        Schema::create('fixed_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('payee', 150)->nullable();
            $table->decimal('default_amount', 14, 2);
            $table->string('frequency', 20)->default('monthly');
            $table->unsignedTinyInteger('due_day')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['is_active', 'frequency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_expenses');
    }
};
