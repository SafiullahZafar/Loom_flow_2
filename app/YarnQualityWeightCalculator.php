<?php

namespace App;

class YarnQualityWeightCalculator
{
    /**
     * @return array{warp_weight_per_meter_lb: float, weft_weight_per_meter_lb: float, total_weight_per_meter_lb: float}
     */
    public function calculate(float $read, float $pick, float $width, float $warpCount, float $weftCount): array
    {
        $truncateFourDecimals = static fn (float $value): float => floor(($value + 0.000000001) * 10000) / 10000;
        $warpWeightPerMeter = $truncateFourDecimals($read * $width / $warpCount / 20 * 1.0936 / 40);
        $weftWeightPerMeter = $truncateFourDecimals($pick * $width / $weftCount / 20 * 1.0936 / 40);

        return [
            'warp_weight_per_meter_lb' => $warpWeightPerMeter,
            'weft_weight_per_meter_lb' => $weftWeightPerMeter,
            'total_weight_per_meter_lb' => $warpWeightPerMeter + $weftWeightPerMeter,
        ];
    }
}
