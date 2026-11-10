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
        Schema::create('bag_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type'); // purchase_factory, purchase_sizing, issue_to_sizing, return_from_sizing, issue_to_loom
            $table->string('reference_no')->nullable()->index();
            $table->string('quality');
            $table->integer('quantity_bags');
            $table->string('source')->default('Factory Store');
            $table->string('destination')->default('Sizing Person');
            $table->string('party_name')->nullable();
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_transactions');
    }
};
