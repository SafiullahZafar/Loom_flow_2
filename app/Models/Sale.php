<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'slip_no',
        'sale_type',
        'sales_contract_id',
        'fabric_stock_id',
        'buyer_name',
        'buyer_phone',
        'fabric_quality',
        'meters_sold',
        'rolls_sold',
        'rate_per_meter',
        'subtotal',
        'discount',
        'net_total',
        'payment_type',
        'payment_status',
        'sale_date',
        'vehicle_no',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'meters_sold' => 'decimal:2',
        'rolls_sold' => 'integer',
        'rate_per_meter' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'net_total' => 'decimal:2',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(SalesContract::class, 'sales_contract_id');
    }

    public function fabricStock(): BelongsTo
    {
        return $this->belongsTo(FabricStock::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
