<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SizingBeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'sizing_slip_id',
        'fabric_stock_id',
        'beam_no',
        'beam_name',
        'yarn_quality',
        'total_length_meters',
        'remaining_length_meters',
        'assigned_loom_no',
        'status',
        'mounted_date',
        'notes',
        'work_order_id',
        'initial_net_weight_kg',
    ];

    protected $casts = [
        'total_length_meters' => 'decimal:2',
        'remaining_length_meters' => 'decimal:2',
        'mounted_date' => 'date',
    ];

    public function sizingSlip(): BelongsTo
    {
        return $this->belongsTo(SizingSlip::class);
    }

    public function fabricStock(): BelongsTo
    {
        return $this->belongsTo(FabricStock::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }
}
