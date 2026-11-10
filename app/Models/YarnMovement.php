<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YarnMovement extends Model
{
    protected $fillable = [
        'work_order_id',
        'component',
        'owner_type',
        'movement_type',
        'weight_kg',
        'bags',
        'yarn_count',
        'lot_no',
        'reference_no',
        'movement_date',
        'notes',
        'destination_type', 'destination_name',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}
