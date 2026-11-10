<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'party_name',
        'party_type',
        'transaction_date',
        'voucher_no',
        'description',
        'debit',
        'credit',
        'balance',
        'notes',
        'source_type',
        'source_id',
        'source_role',
        'is_migrated_legacy',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];
}
