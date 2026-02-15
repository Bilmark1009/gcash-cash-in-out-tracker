<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use App\Services\ProfitService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected ?string $heading = 'Financial Summary';

    protected function getStats(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [];
        }

        $monthStart = Carbon::now()->startOfMonth();
        
        $totalCashIn = $user->transactions()
            ->where('type', 'cash-in')
            ->whereDate('transaction_date', '>=', $monthStart)
            ->sum('amount');

        $totalCashOut = $user->transactions()
            ->where('type', 'cash-out')
            ->whereDate('transaction_date', '>=', $monthStart)
            ->sum('amount');

        $totalFees = $user->transactions()
            ->whereDate('transaction_date', '>=', $monthStart)
            ->sum('fee_amount');

        $profitService = new ProfitService();
        $totalProfit = $profitService->getTotalProfit($user);

        return [
            Stat::make('Total Cash In (This Month)', '₱' . number_format($totalCashIn, 2))
                ->description('Cash inflows')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-arrow-up-circle'),
            Stat::make('Total Cash Out (This Month)', '₱' . number_format($totalCashOut, 2))
                ->description('Cash outflows')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->icon('heroicon-o-arrow-down-circle'),
            Stat::make('Profit This Month', '₱' . number_format($totalFees, 2))
                ->description('From transaction fees')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('warning')
                ->icon('heroicon-o-bolt'),
            Stat::make('Total Cumulative Profit', '₱' . number_format($totalProfit, 2))
                ->description('All time profit')
                ->descriptionIcon('heroicon-m-trending-up')
                ->color('info')
                ->icon('heroicon-o-chart-bar'),
            Stat::make('GCash Balance', '₱' . number_format($user->gcash_balance, 2))
                ->description('Available in GCash')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary')
                ->icon('heroicon-o-wallet'),
            Stat::make('Cash Balance', '₱' . number_format($user->cash_balance, 2))
                ->description('Physical cash on hand')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
