<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Onboarding extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $title = 'Initial Setup';
    protected static string $view = 'filament.admin.pages.onboarding';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        
        if ($user && $user->onboarded) {
            redirect()->route('filament.admin.pages.dashboard');
        }

        $this->form->fill([
            'gcash_balance' => $user?->gcash_balance ?? 0,
            'cash_balance' => $user?->cash_balance ?? 0,
            'default_fee_percentage' => $user?->default_fee_percentage ?? 2.0,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Welcome to GCash Tracker')
                    ->description('Let\'s set up your initial balances. Enter the amount of money you currently have in GCash and physical cash.')
                    ->schema([
                        Forms\Components\TextInput::make('gcash_balance')
                            ->label('GCash Balance (₱)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('₱')
                            ->helperText('Your current GCash account balance'),
                        
                        Forms\Components\TextInput::make('cash_balance')
                            ->label('Physical Cash Balance (₱)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('₱')
                            ->helperText('Physical cash you have on hand'),
                        
                        Forms\Components\TextInput::make('default_fee_percentage')
                            ->label('Default Fee Percentage (%)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(2.0)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Your standard transaction fee percentage'),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        $data = $this->form->getState();

        $user->update([
            'gcash_balance' => (float) $data['gcash_balance'],
            'cash_balance' => (float) $data['cash_balance'],
            'default_fee_percentage' => (float) $data['default_fee_percentage'],
            'onboarded' => true,
        ]);

        \Filament\Notifications\Notification::make()
            ->success()
            ->title('Setup Complete!')
            ->body('Your initial balances have been saved. You can now start tracking transactions.')
            ->send();

        redirect()->route('filament.admin.pages.dashboard');
    }
}
