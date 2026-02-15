<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected ?string $heading = 'Financial Summary';

    protected function getStats(): array
    {
        $totalCashIn = Transaction::where('type', 'cash-in')
            ->whereDate('transaction_date', '>=', Carbon::now()->startOfMonth())
            ->sum('amount');

        $totalCashOut = Transaction::where('type', 'cash-out')
            ->whereDate('transaction_date', '>=', Carbon::now()->startOfMonth())
            ->sum('amount');

        $totalFees = Transaction::whereDate('transaction_date', '>=', Carbon::now()->startOfMonth())
            ->sum('fee');

        $netProfit = $totalCashIn - $totalCashOut - $totalFees;

        return [
            Stat::make('Total Cash In', '₱' . number_format($totalCashIn, 2))
                ->description('This month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-arrow-up-circle'),
            Stat::make('Total Cash Out', '₱' . number_format($totalCashOut, 2))
                ->description('This month')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->icon('heroicon-o-arrow-down-circle'),
            Stat::make('Total Fees', '₱' . number_format($totalFees, 2))
                ->description('This month')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('warning')
                ->icon('heroicon-o-bolt'),
            Stat::make('Net Profit/Loss', '₱' . number_format($netProfit, 2))
                ->description(($netProfit >= 0 ? 'Profit' : 'Loss') . ' this month')
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netProfit >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
