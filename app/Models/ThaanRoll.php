<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThaanRoll extends Model
{
    protected $fillable = [
        'roll_no',
        'work_order_id',
        'fabric_stock_id',
        'loom_no',
        'checked_on',
        'length_meters',
        'grade',
        'status',
        'owner_type',
        'notes',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function fabricStock(): BelongsTo
    {
        return $this->belongsTo(FabricStock::class, 'fabric_stock_id');
    }

    public function dispatchItems(): HasMany
    {
        return $this->hasMany(KachiParchiItem::class, 'thaan_roll_id');
    }
}
