<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factory_worker_wages', function (Blueprint $table): void {
            $table->id();
            $table->string('worker_name', 150);
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->string('loom_no', 50)->nullable();
            $table->date('wage_date');
            $table->string('basis', 20); // metre, day, fixed
            $table->decimal('quantity', 14, 3)->default(1);
            $table->decimal('rate', 14, 4);
            $table->decimal('gross_amount', 14, 2);
            $table->decimal('advance_adjustment', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('balance_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['worker_name', 'wage_date']);
        });
        Schema::create('loom_stoppages', function (Blueprint $table): void {
            $table->id();
            $table->string('loom_no', 50);
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->date('stopped_on');
            $table->unsignedInteger('downtime_minutes');
            $table->string('reason', 150);
            $table->decimal('repair_cost', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loom_stoppages');
        Schema::dropIfExists('factory_worker_wages');
    }
};
