<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = ['transaction_date', 'type', 'amount', 'fee', 'category_id', 'notes'];

    protected $casts = [
        'transaction_date' => 'date',
        'type' => 'string',
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Calculate GCash service fee based on tiered structures
     */
    public static function calculateFee(float $amount, string $type): float
    {
        if ($type === 'cash-in') {
            // GCash Cash-in fees (typical structure)
            if ($amount <= 5000) {
                return $amount * 0.01; // 1% for amounts up to 5000
            } elseif ($amount <= 10000) {
                return $amount * 0.015; // 1.5% for 5001-10000
            } else {
                return $amount * 0.02; // 2% for amounts over 10000
            }
        } else {
            // GCash Cash-out fees (typical structure)
            if ($amount <= 5000) {
                return $amount * 0.015; // 1.5% for amounts up to 5000
            } elseif ($amount <= 10000) {
                return $amount * 0.02; // 2% for 5001-10000
            } else {
                return $amount * 0.025; // 2.5% for amounts over 10000
            }
        }
    }

    /**
     * Accessor to get formatted net profit/loss for this transaction
     */
    public function getNetAmountAttribute(): float
    {
        if ($this->type === 'cash-in') {
            return $this->amount - $this->fee;
        } else {
            return -($this->amount + $this->fee);
        }
    }
}
