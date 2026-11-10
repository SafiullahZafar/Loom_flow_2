<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    protected $fillable = [
        'order_no',
        'type',
        'sales_contract_id',
        'customer_id',
        'fabric_stock_id',
        'parta_record_id',
        'party_name',
        'target_meters',
        'accepted_meters',
        'rate_per_pick',
        'warp_supply',
        'weft_supply',
        'status',
        'start_date',
        'closed_on',
        'closure_reason',
        'notes',
    ];

    public function fabricStock(): BelongsTo
    {
        return $this->belongsTo(FabricStock::class, 'fabric_stock_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(SalesContract::class, 'sales_contract_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function parta(): BelongsTo
    {
        return $this->belongsTo(PartaRecord::class, 'parta_record_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LoomAssignment::class, 'work_order_id');
    }

    public function rolls(): HasMany
    {
        return $this->hasMany(ThaanRoll::class, 'work_order_id');
    }

    public function kachis(): HasMany
    {
        return $this->hasMany(KachiParchi::class, 'work_order_id');
    }

    public function yarnMovements(): HasMany
    {
        return $this->hasMany(YarnMovement::class, 'work_order_id');
    }
}
