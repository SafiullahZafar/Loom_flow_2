<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $parties = DB::table('sales_contracts')->select('buyer_name', 'buyer_phone')
            ->union(DB::table('sales')->select('buyer_name', 'buyer_phone'))
            ->get();

        foreach ($parties as $party) {
            $name = trim((string) $party->buyer_name);
            if ($name === '' || DB::table('customers')->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->exists()) {
                continue;
            }

            $phone = filled($party->buyer_phone) && ! DB::table('customers')->where('phone', trim($party->buyer_phone))->exists()
                ? trim($party->buyer_phone)
                : null;

            DB::table('customers')->insert([
                'name' => $name,
                'phone' => $phone,
                'status' => 'active',
                'opening_balance' => 0,
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
        // Keep imported customers because invoices and receipts may now reference them.
    }
};
