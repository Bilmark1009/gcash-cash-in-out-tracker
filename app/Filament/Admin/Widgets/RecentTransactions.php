<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactions extends BaseWidget
{
    protected static ?string $heading = 'Recent Transactions';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query()->latest('transaction_date')->take(5))
            ->columns([
                TextColumn::make('transaction_date')
                    ->date('M d, Y')
                    ->label('Date')
                    ->sortable(),
                BadgeColumn::make('type')
                    ->formatStateUsing(fn(string $state): string => ucfirst(str_replace('-', ' ', $state)))
                    ->colors([
                        'success' => 'cash-in',
                        'danger' => 'cash-out',
                    ]),
                TextColumn::make('amount')
                    ->money('PHP', locale: 'en_US')
                    ->alignment('right'),
                TextColumn::make('fee')
                    ->money('PHP', locale: 'en_US')
                    ->alignment('right'),
            ]);
    }
}
