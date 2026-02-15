<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalanceHistory extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'balance_type',
        'amount_before',
        'amount_after',
        'change_amount',
        'reason',
    ];

    protected $casts = [
        'amount_before' => 'decimal:2',
        'amount_after' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this balance history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction associated with this balance history.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
