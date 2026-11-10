<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartIssuance extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'loom_no',
        'issued_to_person',
        'quantity',
        'unit_price',
        'total_cost',
        'issuance_date',
        'remarks',
    ];

    protected $casts = [
        'issuance_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
