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
        Schema::table('sizing_slips', function (Blueprint $table) {
            $table->unsignedSmallInteger('back_beam_count')->nullable()->after('total_beams');
            $table->decimal('width_multiplier', 6, 3)->default(1)->after('total_length_meters');
        });

        Schema::table('productions', function (Blueprint $table) {
            $table->decimal('expected_fabric_meters', 12, 2)->default(0)->after('beam_meters_consumed');
        });

        DB::table('productions')->orderBy('id')->chunkById(200, function ($productions): void {
            foreach ($productions as $production) {
                $widthMultiplier = 1;
                if ($production->sizing_beam_id) {
                    $widthMultiplier = DB::table('sizing_beams')
                        ->join('sizing_slips', 'sizing_slips.id', '=', 'sizing_beams.sizing_slip_id')
                        ->where('sizing_beams.id', $production->sizing_beam_id)
                        ->value('sizing_slips.width_multiplier') ?? 1;
                }

                DB::table('productions')->where('id', $production->id)->update([
                    'expected_fabric_meters' => round($production->beam_meters_consumed * $widthMultiplier, 2),
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sizing_slips', function (Blueprint $table) {
            $table->dropColumn(['back_beam_count', 'width_multiplier']);
        });

        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn('expected_fabric_meters');
        });
    }
};
