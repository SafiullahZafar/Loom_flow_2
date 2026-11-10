<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricStockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'fabric_stock_id',
        'movement_date',
        'previous_stock_meters',
        'received_meters',
        'adjustment_meters',
        'dispatch_meters',
        'closing_stock_meters',
        'rolls_received',
        'area',
        'remarks',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'previous_stock_meters' => 'decimal:2',
        'received_meters' => 'decimal:2',
        'adjustment_meters' => 'decimal:2',
        'dispatch_meters' => 'decimal:2',
        'closing_stock_meters' => 'decimal:2',
        'rolls_received' => 'integer',
    ];

    public function fabricStock(): BelongsTo
    {
        return $this->belongsTo(FabricStock::class);
    }
}
