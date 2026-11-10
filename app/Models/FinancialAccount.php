<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'account_number', 'opening_balance', 'is_active'];

    protected $casts = ['opening_balance' => 'decimal:2', 'is_active' => 'boolean'];

    public function transactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }
}
