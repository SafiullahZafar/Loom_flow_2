<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentReceipt extends Model
{
    use HasFactory;

    protected $fillable = ['receipt_no', 'customer_id', 'financial_account_id', 'payment_date', 'amount', 'method', 'reference', 'notes'];

    protected $casts = ['payment_date' => 'date', 'amount' => 'decimal:2'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(InvoicePaymentAllocation::class);
    }
}
