<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('type', 40)->default('yarn_supplier');
            $table->string('phone', 60)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->decimal('opening_payable', 14, 2)->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->unique(['name', 'type']);
            $table->index(['type', 'status']);
        });

        $supplierParties = DB::table('ledgers')
            ->whereIn('party_type', ['yarn_supplier', 'sizing_mill', 'parts_vendor'])
            ->selectRaw('party_type as type, party_name as name')
            ->union(DB::table('bag_purchases')->selectRaw("'yarn_supplier' as type, supplier_name as name"))
            ->get();

        foreach ($supplierParties as $party) {
            $name = trim((string) $party->name);
            if ($name === '' || DB::table('suppliers')
                ->where('type', $party->type)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                ->exists()) {
                continue;
            }

            DB::table('suppliers')->insert([
                'name' => $name,
                'type' => $party->type,
                'opening_payable' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
