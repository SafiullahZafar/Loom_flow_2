<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePaymentAllocation extends Model
{
    use HasFactory;

    protected $fillable = ['payment_receipt_id', 'invoice_id', 'amount'];

    protected $casts = ['amount' => 'decimal:2'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(PaymentReceipt::class, 'payment_receipt_id');
    }
}
