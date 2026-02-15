<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfitTracking extends Model
{
    protected $fillable = [
        'user_id',
        'total_profit',
        'profit_from_cash_in',
        'profit_from_cash_out',
        'transaction_count',
    ];

    protected $casts = [
        'total_profit' => 'decimal:2',
        'profit_from_cash_in' => 'decimal:2',
        'profit_from_cash_out' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this profit tracking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Add profit from a transaction.
     */
    public function addProfit(string $type, float $amount): void
    {
        $this->total_profit += $amount;
        
        if ($type === 'cash-in') {
            $this->profit_from_cash_in += $amount;
        } elseif ($type === 'cash-out') {
            $this->profit_from_cash_out += $amount;
        }
        
        $this->transaction_count++;
        $this->save();
    }

    /**
     * Get average profit per transaction.
     */
    public function getAverageProfitPerTransaction(): float
    {
        if ($this->transaction_count === 0) {
            return 0;
        }
        
        return $this->total_profit / $this->transaction_count;
    }
}
