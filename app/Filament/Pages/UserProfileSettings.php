<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class UserProfileSettings extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    
    protected string $view = 'filament.pages.user-profile-settings';
    
    public static function getNavigationLabel(): string
    {
        return __('common.settings');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return null;
    }
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $user = Auth::user();
        $this->form->fill([
            'seed_capital' => $user->seed_capital ?? 0,
            'median_monthly_income' => $user->median_monthly_income ?? null,
            'income_last_verified_at' => $user->income_last_verified_at ?? null,
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('common.financial_settings'))
                    ->schema([
                        TextInput::make('seed_capital')
                            ->label(__('common.seed_capital'))
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->helperText(__('common.seed_capital_help')),
                        TextInput::make('median_monthly_income')
                            ->label(__('common.median_monthly_income'))
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->helperText(__('common.median_monthly_income_help')),
                        DatePicker::make('income_last_verified_at')
                            ->label(__('common.income_last_verified_at'))
                            ->displayFormat('d/m/Y')
                            ->helperText(__('common.income_last_verified_at_help')),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();
        
        $user->update($data);
        
        Notification::make()
            ->title(__('common.updated_successfully'))
            ->success()
            ->send();
    }
}
