<?php

namespace App\Filament\Resources\ExpenseCategories\Schemas;

use App\Models\ExpenseSuperCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
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
                TextInput::make('emoji')
                    ->label(__('common.emoji'))
                    ->maxLength(10)
                    ->helperText(__('common.emoji_help'))
                    ->placeholder('🛒'),
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
                Section::make(__('common.save_for_later'))
                    ->schema([
                        TextInput::make('save_for_later_target')
                            ->label(__('common.save_for_later_target'))
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        Select::make('save_for_later_frequency')
                            ->label(__('common.save_for_later_frequency'))
                            ->options([
                                'week' => __('common.week'),
                                'month' => __('common.month'),
                                'quarter' => __('common.quarter'),
                                'year' => __('common.year'),
                            ]),
                        TextInput::make('save_for_later_amount')
                            ->label(__('common.save_for_later_amount'))
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
