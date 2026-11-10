<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartaRecord extends Model
{
    protected $fillable = [
        'fabric_stock_id',
        'version',
        'read',
        'pick',
        'width_inches',
        'warp_count',
        'weft_count',
        'warp_rate_per_lb',
        'weft_rate_per_lb',
        'conversion_rate_per_pick',
        'warp_lb_per_meter',
        'weft_lb_per_meter',
        'total_lb_per_meter',
        'kg_per_meter',
        'grams_per_meter',
        'gsm',
        'cost_per_meter',
    ];

    public function fabricStock(): BelongsTo
    {
        return $this->belongsTo(FabricStock::class, 'fabric_stock_id');
    }
}
