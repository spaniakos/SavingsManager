<?php

namespace App\Filament\Resources\ExpenseCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $userId = Auth::id();

                return $query->forUser($userId);
            })
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.name'))
                    ->formatStateUsing(fn ($record) => $record->getTranslatedName())
                    ->searchable(query: function ($query, $search) {
                        return $query->where('name', 'like', "%{$search}%");
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('name', $direction);
                    }),
                TextColumn::make('name')
                    ->label(__('categories.translation_key'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('expenseSuperCategory.name')
                    ->label(__('common.super_category'))
                    ->formatStateUsing(fn ($record) => $record->expenseSuperCategory?->getTranslatedName() ?? '-')
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('expenseSuperCategory', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),
                IconColumn::make('is_system')
                    ->label(__('categories.system_category'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('categories.created_by'))
                    ->default(__('categories.system'))
                    ->toggleable(),
                TextColumn::make('expenseEntries_count')
                    ->label(__('categories.usage_count'))
                    ->counts('expenseEntries')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_system')
                    ->label(__('categories.type'))
                    ->options([
                        1 => __('categories.system_categories'),
                        0 => __('categories.custom_categories'),
                    ]),
            ])
            ->defaultSort('is_system', 'desc')
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => ! $record->is_system || Auth::user()->isAdmin ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $systemCategories = $records->where('is_system', true);
                            if ($systemCategories->isNotEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title(__('categories.cannot_delete_system'))
                                    ->danger()
                                    ->send();

                                return;
                            }
                            $records->each->delete();
                        }),
                ]),
            ]);
    }
}
