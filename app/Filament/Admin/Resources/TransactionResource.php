<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Services\TransactionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\DatePicker::make('transaction_date')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->label('Date'),
                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'cash-in' => 'Cash In',
                                'cash-out' => 'Cash Out',
                            ])
                            ->native(false)
                            ->reactive()
                            ->label('Transaction Type'),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->inputMode('decimal')
                            ->minValue(0.01)
                            ->reactive()
                            ->live(onBlur: true)
                            ->label('Amount (₱)'),
                        Forms\Components\TextInput::make('fee_percentage')
                            ->required()
                            ->numeric()
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->reactive()
                            ->live(onBlur: true)
                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                if (!$state && $record) {
                                    $component->state($record->fee_percentage);
                                } elseif (!$state && Auth::check()) {
                                    $component->state(Auth::user()->default_fee_percentage);
                                }
                            })
                            ->label('Fee Percentage (%)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Fee & Balance Preview')
                    ->schema([
                        Forms\Components\TextInput::make('fee_amount')
                            ->label('Fee Amount (₱)')
                            ->numeric()
                            ->readOnly()
                            ->disabled()
                            ->helperText('Auto-calculated based on amount and fee percentage')
                            ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                                $amount = $get('amount');
                                $feePercentage = $get('fee_percentage');
                                if ($amount && $feePercentage) {
                                    $fee = round($amount * ($feePercentage / 100), 2);
                                    $set('fee_amount', $fee);
                                }
                            })
                            ->live(onBlur: true),
                        
                        Forms\Components\Placeholder::make('balance_preview')
                            ->label('Balance Preview')
                            ->content(function ($get) {
                                $user = Auth::user();
                                if (!$user) return 'N/A';
                                
                                $type = $get('type');
                                $amount = (float) ($get('amount') ?? 0);
                                $feePercentage = (float) ($get('fee_percentage') ?? $user->default_fee_percentage);
                                $feeAmount = round($amount * ($feePercentage / 100), 2);
                                
                                $currentGCash = $user->gcash_balance;
                                $currentCash = $user->cash_balance;
                                
                                if ($type === 'cash-in') {
                                    $newGCash = $currentGCash + $amount;
                                    $newCash = $currentCash + $feeAmount;
                                } else {
                                    $newGCash = $currentGCash - $amount;
                                    $newCash = $currentCash - ($amount - $feeAmount);
                                }
                                
                                return view('filament.balance-preview', [
                                    'currentGCash' => $currentGCash,
                                    'currentCash' => $currentCash,
                                    'newGCash' => $newGCash,
                                    'newCash' => $newCash,
                                ]);
                            }),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Additional Info')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->preload()
                            ->label('Category'),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull()
                            ->label('Notes / Remarks'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date('M d, Y')
                    ->sortable()
                    ->label('Date'),
                Tables\Columns\BadgeColumn::make('type')
                    ->formatStateUsing(fn(string $state): string => ucfirst(str_replace('-', ' ', $state)))
                    ->colors([
                        'success' => 'cash-in',
                        'danger' => 'cash-out',
                    ])
                    ->sortable()
                    ->label('Type'),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->label('Category'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('PHP', locale: 'en_US')
                    ->sortable()
                    ->alignment('right')
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('fee_percentage')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->alignment('right')
                    ->label('Fee %'),
                Tables\Columns\TextColumn::make('fee_amount')
                    ->money('PHP', locale: 'en_US')
                    ->sortable()
                    ->alignment('right')
                    ->label('Profit (₱)'),
                Tables\Columns\TextColumn::make('gcash_balance_after')
                    ->money('PHP', locale: 'en_US')
                    ->sortable()
                    ->alignment('right')
                    ->label('GCash Balance'),
                Tables\Columns\TextColumn::make('cash_balance_after')
                    ->money('PHP', locale: 'en_US')
                    ->sortable()
                    ->alignment('right')
                    ->label('Cash Balance')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'cash-in' => 'Cash In',
                        'cash-out' => 'Cash Out',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}

