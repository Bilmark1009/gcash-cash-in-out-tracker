<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\CashFlowChart;
use App\Filament\Admin\Widgets\RecentTransactions;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            CashFlowChart::class, 
            RecentTransactions::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 3;
    }
}
