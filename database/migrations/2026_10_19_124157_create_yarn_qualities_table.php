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
        Schema::create('yarn_qualities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        $existingQualities = collect();
        foreach ([
            'bag_purchases' => 'quality',
            'bag_transactions' => 'quality',
            'sizing_slips' => 'yarn_quality',
            'sizing_beams' => 'yarn_quality',
        ] as $table => $column) {
            $existingQualities = $existingQualities->merge(DB::table($table)->distinct()->pluck($column));
        }

        foreach ($existingQualities->map(fn (string $name): string => trim($name))->filter()->unique() as $quality) {
            DB::table('yarn_qualities')->insertOrIgnore([
                'name' => $quality,
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
        Schema::dropIfExists('yarn_qualities');
    }
};
