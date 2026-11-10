<?php

namespace App\Services;

use App\Models\FabricStock;
use App\Models\PartaRecord;
use App\YarnQualityWeightCalculator;

class PartaService
{
    public function __construct(private readonly YarnQualityWeightCalculator $calculator) {}

    /** @param array<string, numeric-string|float|int> $values */
    public function calculate(array $values): array
    {
        $read = (float) $values['read'];
        $pick = (float) $values['pick'];
        $width = (float) $values['width_inches'];
        $warpCount = (float) $values['warp_count'];
        $weftCount = (float) $values['weft_count'];
        $warpRate = (float) $values['warp_rate_per_lb'];
        $weftRate = (float) $values['weft_rate_per_lb'];
        $perPick = (float) $values['conversion_rate_per_pick'];
        $w = $this->calculator->calculate($read, $pick, $width, $warpCount, $weftCount);
        $lbs = $w['total_weight_per_meter_lb'];
        $kg = $lbs / 2.2046226218;

        return [
            'warp_lb_per_meter' => $w['warp_weight_per_meter_lb'],
            'weft_lb_per_meter' => $w['weft_weight_per_meter_lb'],
            'total_lb_per_meter' => $lbs,
            'kg_per_meter' => round($kg, 6),
            'grams_per_meter' => round($kg * 1000, 3),
            'gsm' => round(($kg * 1000) / ($width * 0.0254), 3),
            'cost_per_meter' => round($w['warp_weight_per_meter_lb'] * $warpRate + $w['weft_weight_per_meter_lb'] * $weftRate + $pick * $perPick, 4),
        ];
    }

    public function save(FabricStock $fabric, array $values): PartaRecord
    {
        $version = (int) PartaRecord::where('fabric_stock_id', $fabric->id)->max('version') + 1;
        $calculated = $this->calculate($values);
        $record = PartaRecord::create([
            ...$values,
            ...$calculated,
            'fabric_stock_id' => $fabric->id,
            'version' => $version,
        ]);
        $fabric->update([
            'read' => $values['read'], 'pick' => $values['pick'],
            'width_inches' => $values['width_inches'],
            'warp_count' => $values['warp_count'], 'weft_count' => $values['weft_count'],
            'warp_weight_per_meter_lb' => $calculated['warp_lb_per_meter'],
            'weft_weight_per_meter_lb' => $calculated['weft_lb_per_meter'],
            'total_weight_per_meter_lb' => $calculated['total_lb_per_meter'],
            'unit_cost_meter' => $calculated['cost_per_meter'],
        ]);
        return $record;
    }
}
