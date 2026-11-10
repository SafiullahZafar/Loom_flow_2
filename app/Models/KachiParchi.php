<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KachiParchi extends Model
{
    protected $fillable = [
        'parchi_no',
        'work_order_id',
        'dispatch_date',
        'dispatched_meters',
        'thaan_count',
        'vehicle_no',
        'notes',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(KachiParchiItem::class, 'kachi_parchi_id');
    }

    public function pakki(): HasOne
    {
        return $this->hasOne(PakkiParchi::class, 'kachi_parchi_id');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ThaanReturn::class, 'kachi_parchi_id');
    }
}
