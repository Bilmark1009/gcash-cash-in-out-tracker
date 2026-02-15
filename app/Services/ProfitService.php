<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class ProfitService
{
    /**
     * Get total profit for a user.
     */
    public function getTotalProfit(User $user): float
    {
        $profitTracking = $user->profitTracking;
        return $profitTracking ? (float) $profitTracking->total_profit : 0;
    }

    /**
     * Get profit for a specific period.
     */
    public function getProfitByPeriod(User $user, Carbon $startDate, Carbon $endDate): float
    {
        return (float) $user->transactions()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('fee_amount');
    }

    /**
     * Get profit by transaction type.
     */
    public function getProfitByType(User $user, string $type): float
    {
        $profitTracking = $user->profitTracking;
        
        if (!$profitTracking) {
            return 0;
        }

        $fieldName = 'profit_from_' . str_replace('-', '_', $type);
        return (float) ($profitTracking->{$fieldName} ?? 0);
    }

    /**
     * Get profit statistics.
     */
    public function getProfitStats(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $profitTracking = $user->getOrCreateProfitTracking();

        $query = $user->transactions();
        
        if ($startDate) {
            $query = $query->where('transaction_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query = $query->where('transaction_date', '<=', $endDate);
        }

        $totalFee = $query->sum('fee_amount');
        $cashInProfit = $query->where('type', 'cash-in')->sum('fee_amount');
        $cashOutProfit = $query->where('type', 'cash-out')->sum('fee_amount');
        $transactionCount = $query->count();

        return [
            'total_profit' => (float) $totalFee,
            'cash_in_profit' => (float) $cashInProfit,
            'cash_out_profit' => (float) $cashOutProfit,
            'transaction_count' => $transactionCount,
            'average_profit_per_transaction' => $transactionCount > 0 ? round($totalFee / $transactionCount, 2) : 0,
            'all_time_profit' => (float) $profitTracking->total_profit,
        ];
    }

    /**
     * Get profit trend over time.
     */
    public function getProfitTrend(User $user, string $period = 'daily', ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $endDate = $endDate ?? now();
        $startDate = $startDate ?? $endDate->copy()->subDays(30);

        $query = $user->transactions()
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($period === 'daily') {
            return $query->selectRaw('DATE(transaction_date) as date, SUM(fee_amount) as profit')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->mapWithKeys(fn($item) => [$item->date => (float) $item->profit])
                ->toArray();
        } elseif ($period === 'weekly') {
            return $query->selectRaw('WEEK(transaction_date) as week, SUM(fee_amount) as profit')
                ->groupBy('week')
                ->orderBy('week')
                ->get()
                ->mapWithKeys(fn($item) => ['week_' . $item->week => (float) $item->profit])
                ->toArray();
        } else {
            // monthly
            return $query->selectRaw('MONTH(transaction_date) as month, SUM(fee_amount) as profit')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(fn($item) => ['month_' . $item->month => (float) $item->profit])
                ->toArray();
        }
    }
}
