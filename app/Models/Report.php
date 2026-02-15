<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = ['name', 'period', 'start_date', 'end_date', 'total_cash_in', 'total_cash_out', 'total_fees', 'net_profit', 'user_id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_cash_in' => 'decimal:2',
        'total_cash_out' => 'decimal:2', 
        'total_fees' => 'decimal:2',
        'net_profit' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a report from transactions
     */
    public static function generateReport($period, $startDate, $endDate, $userId)
    {
        $transactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->get();

        $totalCashIn = $transactions->where('type', 'cash-in')->sum('amount');
        $totalCashOut = $transactions->where('type', 'cash-out')->sum('amount');
        $totalFees = $transactions->sum('fee');
        $netProfit = $totalCashIn - $totalCashOut - $totalFees;

        return self::create([
            'name' => ucfirst($period) . ' Report - ' . $startDate->format('M d, Y'),
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_cash_in' => $totalCashIn,
            'total_cash_out' => $totalCashOut,
            'total_fees' => $totalFees,
            'net_profit' => $netProfit,
            'user_id' => $userId,
        ]);
    }
}
