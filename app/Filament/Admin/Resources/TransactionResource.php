<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Services\TransactionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Collection;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

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
                            ->live()
                            ->label('Transaction Type'),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->inputMode('decimal')
                            ->minValue(0.01)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                static::updateFeeAmount($set, $get);
                            })
                            ->label('Amount (₱)'),
                        Forms\Components\TextInput::make('fee_percentage')
                            ->required()
                            ->numeric()
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                static::updateFeeAmount($set, $get);
                            })
                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record, Set $set, Get $get) {
                                if (!$state && $record) {
                                    $component->state($record->fee_percentage);
                                } elseif (!$state && Auth::check()) {
                                    $component->state(Auth::user()->default_fee_percentage);
                                }
                                static::updateFeeAmount($set, $get);
                            })
                            ->label('Fee Percentage (%)'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Forms\Components\Section::make('Fee & Balance Preview')
                    ->schema([
                        Forms\Components\TextInput::make('fee_amount')
                            ->label('Fee Amount (₱)')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->helperText('Auto-calculated based on amount and fee percentage')
                            ->live(),
                        
                        Forms\Components\Placeholder::make('balance_preview')
                            ->label('Balance Preview')
                            ->content(function (Get $get) {
                                $user = Auth::user();
                                if (!$user) return 'N/A';
                                
                                $type = $get('type');
                                if (!$type) return 'Please select a transaction type to see preview.';

                                $amount = (float) ($get('amount') ?? 0);
                                if ($amount <= 0) return 'Please enter an amount to see preview.';

                                $feePercentage = (float) ($get('fee_percentage') ?? $user->default_fee_percentage);
                                $feeAmount = round($amount * ($feePercentage / 100), 2);
                                
                                $currentGCash = (float) $user->gcash_balance;
                                $currentCash = (float) $user->cash_balance;
                                
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
            ]);
    }

    public static function updateFeeAmount(Set $set, Get $get): void
    {
        $amount = (float) ($get('amount') ?? 0);
        $feePercentage = (float) ($get('fee_percentage') ?? 0);
        
        if ($amount > 0 && $feePercentage > 0) {
            $fee = round($amount * ($feePercentage / 100), 2);
            $set('fee_amount', $fee);
        } else {
            $set('fee_amount', 0);
        }
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
                Tables\Columns\TextColumn::make('amount')
                    ->money('PHP', locale: 'en_US')
                    ->sortable()
                    ->alignment('right')
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('fee_percentage')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->alignment('right')
                    ->label('Fee %')
                    ->visibleFrom('md'),
                Tables\Columns\TextColumn::make('fee_amount')
                    ->money('PHP', locale: 'en_US')
                    ->sortable()
                    ->alignment('right')
                    ->label('Profit (₱)')
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('gcash_balance_after')
                    ->money('PHP', locale: 'en_US')
                    ->sortable()
                    ->alignment('right')
                    ->label('GCash Balance')
                    ->visibleFrom('lg'),
                Tables\Columns\TextColumn::make('cash_balance_after')
                    ->money('PHP', locale: 'en_US')
                    ->sortable()
                    ->alignment('right')
                    ->label('Cash Balance')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visibleFrom('xl'),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'cash-in' => 'Cash In',
                        'cash-out' => 'Cash Out',
                    ]),
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
                    ExportBulkAction::make()
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                                ->fromTable()
                                ->withFilename('transactions-' . date('Y-m-d')),
                        ])
                        ->label('Export to Excel'),
                    Tables\Actions\BulkAction::make('export_pdf')
                        ->label('Export to PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $pdf = Pdf::loadView('pdf.transactions', [
                                'transactions' => $records->sortByDesc('transaction_date'),
                            ]);

                            return Response::streamDownload(function () use ($pdf) {
                                echo $pdf->stream();
                            }, 'transactions-' . date('Y-m-d') . '.pdf');
                        }),
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

