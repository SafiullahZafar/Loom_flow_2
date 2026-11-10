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
        Schema::create('bag_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('challan_no')->index();
            $table->string('supplier_name');
            $table->string('quality'); // e.g. 30/1 PC, 40/1 Combed
            $table->integer('total_bags');
            $table->integer('bags_to_sizing')->default(0);
            $table->integer('bags_to_factory')->default(0);
            $table->decimal('rate_per_bag', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->date('purchase_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_purchases');
    }
};
