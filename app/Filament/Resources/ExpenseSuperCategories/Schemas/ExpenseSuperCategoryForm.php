<?php

namespace App\Filament\Resources\ExpenseSuperCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ExpenseSuperCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('common.name'))
                    ->required()
                    ->maxLength(255)
                    ->helperText(__('categories.translation_key_help'))
                    ->disabled(fn ($record) => $record && $record->is_system)
                    ->dehydrated(),
                TextInput::make('emoji')
                    ->label(__('common.emoji'))
                    ->maxLength(10)
                    ->helperText(__('common.emoji_help'))
                    ->placeholder('🏠'),
                TextInput::make('allocation_percentage')
                    ->label(__('common.allocation_percentage'))
                    ->numeric()
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->helperText(__('common.allocation_percentage_help'))
                    ->required(),
                Textarea::make('translation_help')
                    ->label(__('categories.translation_info'))
                    ->helperText(__('categories.translation_instructions'))
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record) => !$record || !$record->is_system)
                    ->columnSpanFull(),
            ]);
    }
}
