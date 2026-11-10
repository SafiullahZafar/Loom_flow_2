<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parta_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fabric_stock_id')->constrained()->restrictOnDelete();
            $table->integer('version')->default(1);
            $table->decimal('read', 12, 3);
            $table->decimal('pick', 12, 3);
            $table->decimal('width_inches', 12, 3);
            $table->decimal('warp_count', 12, 4);
            $table->decimal('weft_count', 12, 4);
            $table->decimal('warp_rate_per_lb', 14, 4)->default(0);
            $table->decimal('weft_rate_per_lb', 14, 4)->default(0);
            $table->decimal('conversion_rate_per_pick', 14, 4)->default(0);
            $table->decimal('warp_lb_per_meter', 14, 6);
            $table->decimal('weft_lb_per_meter', 14, 6);
            $table->decimal('total_lb_per_meter', 14, 6);
            $table->decimal('kg_per_meter', 14, 6);
            $table->decimal('grams_per_meter', 14, 3);
            $table->decimal('gsm', 14, 3);
            $table->decimal('cost_per_meter', 14, 4);
            $table->timestamps();
            $table->unique(['fabric_stock_id', 'version']);
        });

        Schema::create('work_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_no')->unique();
            $table->string('type', 12); // own, job
            $table->foreignId('sales_contract_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('fabric_stock_id')->constrained()->restrictOnDelete();
            $table->foreignId('parta_record_id')->nullable()->constrained()->nullOnDelete();
            $table->string('party_name')->nullable();
            $table->decimal('target_meters', 14, 2)->nullable();
            $table->decimal('accepted_meters', 14, 2)->default(0);
            $table->decimal('rate_per_pick', 14, 4)->default(0);
            $table->string('warp_supply', 15)->default('factory'); // customer, factory, mixed
            $table->string('weft_supply', 15)->default('factory');
            $table->string('status', 20)->default('active'); // active, fulfilled, closed
            $table->date('start_date');
            $table->date('closed_on')->nullable();
            $table->text('closure_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('loom_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->restrictOnDelete();
            $table->string('loom_no', 50);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('change_reason')->nullable();
            $table->timestamps();
            $table->index(['loom_no', 'start_date', 'end_date']);
        });

        Schema::create('thaan_rolls', function (Blueprint $table): void {
            $table->id();
            $table->string('roll_no')->unique();
            $table->foreignId('work_order_id')->constrained()->restrictOnDelete();
            $table->foreignId('fabric_stock_id')->constrained()->restrictOnDelete();
            $table->string('loom_no', 50);
            $table->date('checked_on');
            $table->decimal('length_meters', 14, 2);
            $table->string('grade', 12)->default('A');
            $table->string('status', 25)->default('ready'); // ready, dispatched, returned_hold, returned_ready, disposed, accepted
            $table->string('owner_type', 12)->default('factory');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['work_order_id', 'status']);
        });

        Schema::create('kachi_parchis', function (Blueprint $table): void {
            $table->id();
            $table->string('parchi_no')->unique();
            $table->foreignId('work_order_id')->constrained()->restrictOnDelete();
            $table->date('dispatch_date');
            $table->decimal('dispatched_meters', 14, 2)->default(0);
            $table->unsignedInteger('thaan_count')->default(0);
            $table->string('vehicle_no')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('kachi_parchi_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kachi_parchi_id')->constrained()->cascadeOnDelete();
            $table->foreignId('thaan_roll_id')->constrained()->restrictOnDelete();
            $table->decimal('length_meters', 14, 2);
            $table->timestamps();
            $table->unique(['kachi_parchi_id', 'thaan_roll_id']);
        });

        Schema::create('pakki_parchis', function (Blueprint $table): void {
            $table->id();
            $table->string('parchi_no')->unique();
            $table->foreignId('kachi_parchi_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('invoice_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->date('received_date');
            $table->unsignedInteger('accepted_thaans');
            $table->decimal('accepted_meters', 14, 2);
            $table->decimal('rejected_meters', 14, 2);
            $table->decimal('bill_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('thaan_returns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kachi_parchi_id')->constrained()->restrictOnDelete();
            $table->foreignId('thaan_roll_id')->constrained()->restrictOnDelete();
            $table->date('returned_on');
            $table->timestamps();
            $table->unique(['kachi_parchi_id', 'thaan_roll_id']);
        });

        Schema::create('thaan_dispositions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('thaan_roll_id')->constrained()->restrictOnDelete();
            $table->foreignId('from_work_order_id')->constrained('work_orders')->restrictOnDelete();
            $table->foreignId('to_work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->string('action', 30); // resend_a, return_customer, settle_keep, sell_approved, carry_forward
            $table->decimal('settlement_amount', 14, 2)->nullable();
            $table->string('approval_reference')->nullable();
            $table->date('action_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('yarn_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->restrictOnDelete();
            $table->string('component', 10); // warp/weft
            $table->string('owner_type', 12); // customer/factory
            $table->string('movement_type', 20); // receipt, issue, return, adjustment_in, adjustment_out
            $table->decimal('weight_kg', 14, 3);
            $table->decimal('bags', 14, 2)->nullable();
            $table->string('yarn_count', 80)->nullable();
            $table->string('lot_no', 100)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->date('movement_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['work_order_id', 'component', 'owner_type']);
        });

        Schema::create('beam_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sizing_beam_id')->constrained()->restrictOnDelete();
            $table->foreignId('from_work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignId('to_work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->string('from_loom', 50)->nullable();
            $table->string('to_loom', 50)->nullable();
            $table->string('action', 20); // mount, unmount, settle_reassign
            $table->decimal('remaining_length_meters', 14, 2);
            $table->decimal('estimated_weight_kg', 14, 3)->nullable();
            $table->decimal('predicted_settlement', 14, 2)->nullable();
            $table->decimal('agreed_settlement', 14, 2)->nullable();
            $table->date('event_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('sizing_beams', function (Blueprint $table): void {
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->decimal('initial_net_weight_kg', 14, 3)->nullable();
        });
        Schema::table('productions', function (Blueprint $table): void {
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignId('loom_assignment_id')->nullable()->constrained('loom_assignments')->nullOnDelete();
            $table->boolean('is_provisional')->default(true);
        });
        Schema::table('sales_contracts', function (Blueprint $table): void {
            $table->decimal('accepted_meters', 14, 2)->default(0);
            $table->boolean('tax_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('sales_contracts', fn (Blueprint $table) => $table->dropColumn(['accepted_meters', 'tax_enabled']));
        Schema::table('productions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('work_order_id');
            $table->dropConstrainedForeignId('loom_assignment_id');
            $table->dropColumn('is_provisional');
        });
        Schema::table('sizing_beams', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('work_order_id');
            $table->dropColumn('initial_net_weight_kg');
        });
        foreach (['beam_events', 'yarn_movements', 'thaan_dispositions', 'thaan_returns', 'pakki_parchis', 'kachi_parchi_items', 'kachi_parchis', 'thaan_rolls', 'loom_assignments', 'work_orders', 'parta_records'] as $name) {
            Schema::dropIfExists($name);
        }
    }
};
