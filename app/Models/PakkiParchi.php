<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PakkiParchi extends Model
{
    protected $fillable = [
        'parchi_no',
        'kachi_parchi_id',
        'invoice_id',
        'received_date',
        'accepted_thaans',
        'accepted_meters',
        'rejected_meters',
        'bill_amount',
        'notes',
    ];

    public function kachi(): BelongsTo
    {
        return $this->belongsTo(KachiParchi::class, 'kachi_parchi_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
