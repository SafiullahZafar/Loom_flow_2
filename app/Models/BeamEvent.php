<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeamEvent extends Model
{
    protected $fillable = [
        'sizing_beam_id',
        'from_work_order_id',
        'to_work_order_id',
        'from_loom',
        'to_loom',
        'action',
        'remaining_length_meters',
        'estimated_weight_kg',
        'predicted_settlement',
        'agreed_settlement',
        'event_date',
        'notes',
    ];

    public function beam(): BelongsTo
    {
        return $this->belongsTo(SizingBeam::class, 'sizing_beam_id');
    }
}
