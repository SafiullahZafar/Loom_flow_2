<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThaanDisposition extends Model
{
    protected $fillable = [
        'thaan_roll_id',
        'from_work_order_id',
        'to_work_order_id',
        'action',
        'settlement_amount',
        'approval_reference',
        'action_date',
        'notes',
    ];

    public function roll(): BelongsTo
    {
        return $this->belongsTo(ThaanRoll::class, 'thaan_roll_id');
    }
}
