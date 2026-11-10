<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_name',
        'part_code',
        'category',
        'stock_quantity',
        'min_alert_threshold',
        'unit',
        'unit_price',
        'rack_location',
        'notes',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'min_alert_threshold' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function issuances(): HasMany
    {
        return $this->hasMany(PartIssuance::class);
    }
}
