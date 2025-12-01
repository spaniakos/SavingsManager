<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('common.financial_settings'))
                    ->components([
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
                Section::make(__('common.language_settings'))
                    ->components([
                        \Filament\Forms\Components\Select::make('language')
                            ->label(__('common.language'))
                            ->options([
                                'en' => 'English',
                                'el' => 'Ελληνικά',
                            ])
                            ->default(app()->getLocale())
                            ->afterStateUpdated(function ($state) {
                                if (in_array($state, ['en', 'el'])) {
                                    \Illuminate\Support\Facades\Cookie::queue('locale', $state, 60 * 24 * 365);
                                    app()->setLocale($state);
                                }
                            }),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        // Remove language from data as it's handled separately
        $language = $data['language'] ?? null;
        unset($data['language']);

        $user->update($data);

        Notification::make()
            ->title(__('common.updated_successfully'))
            ->success()
            ->send();
    }
}
