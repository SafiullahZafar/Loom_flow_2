<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bag_transactions', function (Blueprint $table): void {
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->string('owner_type', 12)->default('factory');
            $table->decimal('net_weight_kg', 14, 3)->nullable();
        });
        Schema::table('sizing_slips', function (Blueprint $table): void {
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
        });
        Schema::table('yarn_movements', function (Blueprint $table): void {
            $table->string('destination_type', 20)->default('factory_store');
            $table->string('destination_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('yarn_movements', fn (Blueprint $table) => $table->dropColumn(['destination_type', 'destination_name']));
        Schema::table('sizing_slips', fn (Blueprint $table) => $table->dropConstrainedForeignId('work_order_id'));
        Schema::table('bag_transactions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('work_order_id');
            $table->dropColumn(['owner_type', 'net_weight_kg']);
        });
    }
};
