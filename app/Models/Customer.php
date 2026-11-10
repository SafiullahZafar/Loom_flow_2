<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'address', 'opening_balance', 'status'];

    protected $casts = ['opening_balance' => 'decimal:2'];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function allocations(): HasManyThrough
    {
        return $this->hasManyThrough(InvoicePaymentAllocation::class, Invoice::class);
    }
}
