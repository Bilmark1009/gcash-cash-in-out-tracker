<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'business_name',
        'gcash_balance',
        'cash_balance',
        'default_fee_percentage',
        'onboarded',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gcash_balance' => 'decimal:2',
            'cash_balance' => 'decimal:2',
            'default_fee_percentage' => 'decimal:2',
        ];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function balanceHistory(): HasMany
    {
        return $this->hasMany(BalanceHistory::class);
    }

    public function profitTracking()
    {
        return $this->hasOne(ProfitTracking::class);
    }

    /**
     * Get or create profit tracking for this user.
     */
    public function getOrCreateProfitTracking(): ProfitTracking
    {
        return $this->profitTracking()->firstOrCreate(
            ['user_id' => $this->id],
            [
                'total_profit' => 0,
                'profit_from_cash_in' => 0,
                'profit_from_cash_out' => 0,
                'transaction_count' => 0,
            ]
        );
    }

    /**
     * Get current GCash balance.
     */
    public function getGCashBalance(): float
    {
        return (float) $this->gcash_balance;
    }

    /**
     * Get current Cash balance.
     */
    public function getCashBalance(): float
    {
        return (float) $this->cash_balance;
    }

    /**
     * Get total profit.
     */
    public function getTotalProfit(): float
    {
        $profitTracking = $this->getOrCreateProfitTracking();
        return (float) $profitTracking->total_profit;
    }

    /**
     * Update GCash balance.
     */
    public function updateGCashBalance(float $amount, ?Transaction $transaction = null, string $reason = 'Balance Update'): void
    {
        $oldBalance = $this->gcash_balance;
        $this->gcash_balance += $amount;
        $this->save();

        // Record balance history
        BalanceHistory::create([
            'user_id' => $this->id,
            'transaction_id' => $transaction?->id,
            'balance_type' => 'gcash',
            'amount_before' => $oldBalance,
            'amount_after' => $this->gcash_balance,
            'change_amount' => $amount,
            'reason' => $reason,
        ]);
    }

    /**
     * Update Cash balance.
     */
    public function updateCashBalance(float $amount, ?Transaction $transaction = null, string $reason = 'Balance Update'): void
    {
        $oldBalance = $this->cash_balance;
        $this->cash_balance += $amount;
        $this->save();

        // Record balance history
        BalanceHistory::create([
            'user_id' => $this->id,
            'transaction_id' => $transaction?->id,
            'balance_type' => 'cash',
            'amount_before' => $oldBalance,
            'amount_after' => $this->cash_balance,
            'change_amount' => $amount,
            'reason' => $reason,
        ]);
    }

    /**
     * Add profit.
     */
    public function addProfit(float $amount, string $type, ?Transaction $transaction = null): void
    {
        $profitTracking = $this->getOrCreateProfitTracking();
        $profitTracking->addProfit($type, $amount);

        // Record balance history for profit
        BalanceHistory::create([
            'user_id' => $this->id,
            'transaction_id' => $transaction?->id,
            'balance_type' => 'profit',
            'amount_before' => $profitTracking->total_profit - $amount,
            'amount_after' => $profitTracking->total_profit,
            'change_amount' => $amount,
            'reason' => "Profit from $type transaction",
        ]);
    }
}
