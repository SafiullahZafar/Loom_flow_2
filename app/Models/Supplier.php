<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'phone', 'email', 'address', 'opening_payable', 'status'];

    protected $casts = ['opening_payable' => 'decimal:2'];

    public function bagPurchases(): HasMany
    {
        return $this->hasMany(BagPurchase::class, 'supplier_name', 'name');
    }
}
