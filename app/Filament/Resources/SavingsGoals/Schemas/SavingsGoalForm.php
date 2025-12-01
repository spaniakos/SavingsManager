<?php

namespace App\Filament\Resources\SavingsGoals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SavingsGoalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('common.goal_name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('target_amount')
                    ->label(__('common.target_amount'))
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01),
                TextInput::make('current_amount')
                    ->label(__('common.current_amount'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('€')
                    ->step(0.01),
                TextInput::make('initial_checkpoint')
                    ->label(__('common.initial_checkpoint'))
                    ->numeric()
                    ->default(0)
                    ->prefix('€')
                    ->step(0.01)
                    ->helperText(__('common.initial_checkpoint_help')),
                DatePicker::make('start_date')
                    ->label(__('common.start_date'))
                    ->required()
                    ->default(now())
                    ->displayFormat('d/m/Y'),
                DatePicker::make('target_date')
                    ->label(__('common.target_date'))
                    ->required()
                    ->displayFormat('d/m/Y'),
                Textarea::make('notes')
                    ->label(__('common.notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
