<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class BalanceService
{
    /**
     * Get current balances for a user.
     */
    public function getCurrentBalances(User $user): array
    {
        return [
            'gcash_balance' => (float) $user->gcash_balance,
            'cash_balance' => (float) $user->cash_balance,
            'total_balance' => (float) ($user->gcash_balance + $user->cash_balance),
        ];
    }

    /**
     * Get balance at a specific date.
     */
    public function getBalanceAtDate(User $user, Carbon $date): array
    {
        $transactionsBeforeDate = $user->transactions()
            ->where('transaction_date', '<', $date)
            ->orderBy('transaction_date', 'desc')
            ->first();

        if ($transactionsBeforeDate) {
            return [
                'gcash_balance' => (float) $transactionsBeforeDate->gcash_balance_after,
                'cash_balance' => (float) $transactionsBeforeDate->cash_balance_after,
                'total_balance' => (float) ($transactionsBeforeDate->gcash_balance_after + $transactionsBeforeDate->cash_balance_after),
            ];
        }

        // No transactions before date, return initial balances
        return [
            'gcash_balance' => 0,
            'cash_balance' => 0,
            'total_balance' => 0,
        ];
    }

    /**
     * Get balance history for a period.
     */
    public function getBalanceHistory(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = $user->balanceHistory();

        if ($startDate) {
            $query = $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query = $query->where('created_at', '<=', $endDate);
        }

        return $query->orderBy('created_at')->get()->toArray();
    }

    /**
     * Get balance trend over time.
     */
    public function getBalanceTrend(User $user, string $type = 'gcash', ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $endDate = $endDate ?? now();
        $startDate = $startDate ?? $endDate->copy()->subDays(30);

        $balanceType = $type === 'gcash' ? 'gcash' : 'cash';
        $field = $balanceType . '_balance_after';

        return $user->transactions()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw("DATE(transaction_date) as date, MAX($field) as balance")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn($item) => [$item->date => (float) $item->balance])
            ->toArray();
    }

    /**
     * Check if balance is low (for alerts).
     */
    public function isBalanceLow(User $user, float $threshold = 1000): bool
    {
        return $user->gcash_balance < $threshold || $user->cash_balance < $threshold;
    }

    /**
     * Get low balance alerts.
     */
    public function getLowBalanceAlerts(User $user, float $gcashThreshold = 1000, float $cashThreshold = 1000): array
    {
        $alerts = [];

        if ($user->gcash_balance < $gcashThreshold) {
            $alerts[] = [
                'type' => 'gcash',
                'message' => "GCash balance is low: ₱" . number_format($user->gcash_balance, 2),
                'balance' => (float) $user->gcash_balance,
                'threshold' => $gcashThreshold,
            ];
        }

        if ($user->cash_balance < $cashThreshold) {
            $alerts[] = [
                'type' => 'cash',
                'message' => "Cash balance is low: ₱" . number_format($user->cash_balance, 2),
                'balance' => (float) $user->cash_balance,
                'threshold' => $cashThreshold,
            ];
        }

        return $alerts;
    }

    /**
     * Get balance statistics.
     */
    public function getBalanceStats(User $user): array
    {
        $transactions = $user->transactions()->orderBy('transaction_date')->get();

        $gcashMax = $transactions->max('gcash_balance_after') ?? 0;
        $gcashMin = $transactions->min('gcash_balance_after') ?? 0;
        $cashMax = $transactions->max('cash_balance_after') ?? 0;
        $cashMin = $transactions->min('cash_balance_after') ?? 0;

        return [
            'gcash' => [
                'current' => (float) $user->gcash_balance,
                'max' => (float) $gcashMax,
                'min' => (float) $gcashMin,
                'average' => $transactions->count() > 0 ? (float) $transactions->avg('gcash_balance_after') : 0,
            ],
            'cash' => [
                'current' => (float) $user->cash_balance,
                'max' => (float) $cashMax,
                'min' => (float) $cashMin,
                'average' => $transactions->count() > 0 ? (float) $transactions->avg('cash_balance_after') : 0,
            ],
        ];
    }
}
