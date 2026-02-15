<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_date',
        'type',
        'amount',
        'fee',
        'fee_percentage',
        'fee_amount',
        'gcash_balance_before',
        'gcash_balance_after',
        'cash_balance_before',
        'cash_balance_after',
        'category_id',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'type' => 'string',
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'gcash_balance_before' => 'decimal:2',
        'gcash_balance_after' => 'decimal:2',
        'cash_balance_before' => 'decimal:2',
        'cash_balance_after' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate fee amount based on percentage.
     */
    public function calculateFeeAmount(float $amount, float $feePercentage): float
    {
        return round($amount * ($feePercentage / 100), 2);
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

    /**
     * Calculate balance changes for cash-in transaction.
     */
    public function calculateCashInBalances(float $currentGCashBalance, float $currentCashBalance): array
    {
        $feeAmount = $this->fee_amount ?? $this->calculateFeeAmount($this->amount, $this->fee_percentage);
        
        return [
            'gcash_balance_before' => $currentGCashBalance,
            'gcash_balance_after' => $currentGCashBalance + $this->amount,
            'cash_balance_before' => $currentCashBalance,
            'cash_balance_after' => $currentCashBalance + $feeAmount,
        ];
    }

    /**
     * Calculate balance changes for cash-out transaction.
     */
    public function calculateCashOutBalances(float $currentGCashBalance, float $currentCashBalance): array
    {
        $feeAmount = $this->fee_amount ?? $this->calculateFeeAmount($this->amount, $this->fee_percentage);
        
        return [
            'gcash_balance_before' => $currentGCashBalance,
            'gcash_balance_after' => $currentGCashBalance - $this->amount,
            'cash_balance_before' => $currentCashBalance,
            'cash_balance_after' => $currentCashBalance - ($this->amount - $feeAmount),
        ];
    }
