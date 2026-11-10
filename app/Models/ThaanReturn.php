<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThaanReturn extends Model
{
    protected $fillable = [
        'kachi_parchi_id',
        'thaan_roll_id',
        'returned_on',
    ];

    public function kachi(): BelongsTo
    {
        return $this->belongsTo(KachiParchi::class, 'kachi_parchi_id');
    }

    public function roll(): BelongsTo
    {
        return $this->belongsTo(ThaanRoll::class, 'thaan_roll_id');
    }
}
