<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabric_stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fabric_stock_id')->constrained()->cascadeOnDelete();
            $table->date('movement_date');
            $table->decimal('previous_stock_meters', 14, 2)->default(0);
            $table->decimal('received_meters', 14, 2)->default(0);
            $table->decimal('adjustment_meters', 14, 2)->default(0);
            $table->decimal('dispatch_meters', 14, 2)->default(0);
            $table->decimal('closing_stock_meters', 14, 2)->default(0);
            $table->unsignedInteger('rolls_received')->default(0);
            $table->string('area', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['fabric_stock_id', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabric_stock_movements');
    }
};
