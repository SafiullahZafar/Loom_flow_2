<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['financial_account_id', 'payment_receipt_id', 'expense_id', 'transaction_date', 'direction', 'amount', 'description', 'reference'];

    protected $casts = ['transaction_date' => 'date', 'amount' => 'decimal:2'];

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(PaymentReceipt::class, 'payment_receipt_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
