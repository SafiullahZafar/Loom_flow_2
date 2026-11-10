<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_no',
        'buyer_name',
        'customer_id',
        'buyer_phone',
        'fabric_stock_id',
        'fabric_quality',
        'total_meters',
        'rate_per_meter',
        'total_amount',
        'delivered_meters',
        'remaining_meters',
        'start_date',
        'end_date',
        'status',
        'notes',
        'party_contract_no',
        'contract_type',
        'loom_type',
        'buyer_code',
        'buyer_gst',
        'broker_code',
        'broker_name',
        'broker_commission_per_meter',
        'quality_code',
        'contract_quality',
        'contract_quality_width',
        'panna',
        'kp_percentage',
        'kp_days',
        'pp_percentage',
        'pp_days',
        'read',
        'pick',
        'warp_count',
        'weft_count',
        'width',
        'yarn_warp_rate',
        'yarn_weft_rate',
        'conversion_rate',
        'warp_wt_40m',
        'weft_wt_40m',
        'warp_weight_1m',
        'weft_weight_1m',
        'total_weight_1m_lb',
        'total_weight_1m_kg',
        'width_m',
        'gsm',
        'warp_bags',
        'weft_bags',
        'warp_amount_per_mtr',
        'weft_amount_per_mtr',
        'conversion_per_mtr',
        'yarn_value',
        'conv_value',
        'sale_tax_rate',
        'sales_tax_amount',
        'accepted_meters',
        'tax_enabled',
        'delivery_instructions',
        'payment_instructions',
        'quality_instructions',
        'other_instructions',
    ];

    protected $casts = [
        'total_meters' => 'decimal:2',
        'rate_per_meter' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivered_meters' => 'decimal:2',
        'remaining_meters' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'broker_commission_per_meter' => 'decimal:4',
        'contract_quality_width' => 'decimal:3',
        'panna' => 'decimal:3',
        'kp_percentage' => 'decimal:3',
        'kp_days' => 'integer',
        'pp_percentage' => 'decimal:3',
        'pp_days' => 'integer',
        'read' => 'decimal:3',
        'pick' => 'decimal:3',
        'warp_count' => 'decimal:4',
        'weft_count' => 'decimal:4',
        'width' => 'decimal:3',
        'yarn_warp_rate' => 'decimal:4',
        'yarn_weft_rate' => 'decimal:4',
        'conversion_rate' => 'decimal:4',
        'warp_wt_40m' => 'decimal:6',
        'weft_wt_40m' => 'decimal:6',
        'warp_weight_1m' => 'decimal:6',
        'weft_weight_1m' => 'decimal:6',
        'total_weight_1m_lb' => 'decimal:6',
        'total_weight_1m_kg' => 'decimal:6',
        'width_m' => 'decimal:6',
        'gsm' => 'decimal:2',
        'warp_bags' => 'decimal:4',
        'weft_bags' => 'decimal:4',
        'warp_amount_per_mtr' => 'decimal:4',
        'weft_amount_per_mtr' => 'decimal:4',
        'conversion_per_mtr' => 'decimal:4',
        'yarn_value' => 'decimal:2',
        'conv_value' => 'decimal:2',
        'sale_tax_rate' => 'decimal:4',
        'sales_tax_amount' => 'decimal:2',
        'accepted_meters' => 'decimal:2',
        'tax_enabled' => 'boolean',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function fabricStock(): BelongsTo
    {
        return $this->belongsTo(FabricStock::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, float>
     */
    public static function calculateContractValues(array $input): array
    {
        $quantity = (float) $input['total_meters'];
        $read = (float) $input['read'];
        $pick = (float) $input['pick'];
        $warpCount = (float) $input['warp_count'];
        $weftCount = (float) $input['weft_count'];
        $width = (float) $input['width'];
        $warpRate = (float) $input['yarn_warp_rate'];
        $weftRate = (float) $input['yarn_weft_rate'];
        $conversionRate = (float) $input['conversion_rate'];

        $warpWeight = floor((($read * $width / $warpCount / 20 * 1.0936) / 40) * 10000) / 10000;
        $weftWeight = floor((($pick * $width / $weftCount / 20 * 1.0936) / 40) * 10000) / 10000;
        $warpAmount = floor($warpWeight * $warpRate * 10000) / 10000;
        $weftAmount = floor($weftWeight * $weftRate * 10000) / 10000;
        $conversionPerMeter = $pick * $conversionRate;
        $fabricRate = $warpAmount + $weftAmount + $conversionPerMeter;
        $contractValue = $fabricRate * $quantity;
        $salesTaxRate = ! empty($input['tax_enabled']) ? (float) ($input['tax_rate'] ?? 0) : 0;

        return [
            'rate_per_meter' => round($fabricRate, 2),
            'total_amount' => round($contractValue, 2),
            'warp_wt_40m' => $warpWeight * 40,
            'weft_wt_40m' => $weftWeight * 40,
            'warp_weight_1m' => $warpWeight,
            'weft_weight_1m' => $weftWeight,
            'total_weight_1m_lb' => $warpWeight + $weftWeight,
            'total_weight_1m_kg' => ($warpWeight + $weftWeight) / 2.2046,
            'width_m' => $width * 0.0254,
            'gsm' => round((($read / $warpCount) + ($pick / $weftCount)) * 25.25),
            'warp_bags' => round($warpWeight * $quantity / 100, 2),
            'weft_bags' => round($weftWeight * $quantity / 100, 2),
            'warp_amount_per_mtr' => $warpAmount,
            'weft_amount_per_mtr' => $weftAmount,
            'conversion_per_mtr' => $conversionPerMeter,
            'yarn_value' => round(($warpAmount + $weftAmount) * $quantity, 2),
            'conv_value' => round($conversionPerMeter * $quantity, 2),
            'sale_tax_rate' => $salesTaxRate,
            'sales_tax_amount' => round($contractValue * $salesTaxRate, 2),
        ];
    }
}
