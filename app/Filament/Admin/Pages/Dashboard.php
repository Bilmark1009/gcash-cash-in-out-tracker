<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\CashFlowChart;
use App\Filament\Admin\Widgets\RecentTransactions;
use App\Filament\Admin\Resources\TransactionResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTransaction')
                ->label('New Transaction')
                ->url(fn (): string => TransactionResource::getUrl('create'))
                ->icon('heroicon-o-plus-circle')
                ->color('success'),
        ];
    }

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
