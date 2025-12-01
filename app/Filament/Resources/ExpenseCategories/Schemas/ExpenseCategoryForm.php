<?php

namespace App\Filament\Resources\ExpenseCategories\Schemas;

use App\Models\ExpenseSuperCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryForm
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
                Select::make('expense_super_category_id')
                    ->label(__('common.super_category'))
                    ->options(function () {
                        $userId = Auth::id();
                        return ExpenseSuperCategory::forUser($userId)
                            ->get()
                            ->mapWithKeys(function ($superCategory) {
                                return [$superCategory->id => $superCategory->getTranslatedName()];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->disabled(fn ($record) => $record && $record->is_system),
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
