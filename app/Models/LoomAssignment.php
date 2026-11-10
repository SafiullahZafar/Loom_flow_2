<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoomAssignment extends Model
{
    protected $fillable = [
        'work_order_id',
        'loom_no',
        'start_date',
        'end_date',
        'change_reason',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}
