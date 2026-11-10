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
        Schema::table('productions', function (Blueprint $table) {
            $table->foreignId('fabric_stock_id')->nullable()->after('sizing_beam_id')->constrained()->nullOnDelete();
        });

        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->foreignId('fabric_stock_id')->nullable()->after('buyer_phone')->constrained()->nullOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('fabric_stock_id')->nullable()->after('sales_contract_id')->constrained()->nullOnDelete();
        });

        foreach (['productions', 'sales_contracts', 'sales'] as $tableName) {
            DB::table($tableName)->orderBy('id')->chunkById(200, function ($records) use ($tableName): void {
                foreach ($records as $record) {
                    $fabricStockId = DB::table('fabric_stocks')
                        ->where('quality_name', $record->fabric_quality)
                        ->value('id');

                    if ($fabricStockId !== null) {
                        DB::table($tableName)->where('id', $record->id)->update([
                            'fabric_stock_id' => $fabricStockId,
                        ]);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fabric_stock_id');
        });

        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fabric_stock_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fabric_stock_id');
        });
    }
};
