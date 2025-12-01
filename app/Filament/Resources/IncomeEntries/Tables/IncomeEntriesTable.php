<?php

namespace App\Filament\Resources\IncomeEntries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class IncomeEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->columns([
                TextColumn::make('incomeCategory.name')
                    ->label(__('common.category'))
                    ->formatStateUsing(fn ($record) => $record->incomeCategory?->getTranslatedName() ?? '-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('common.amount'))
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('date')
                    ->label(__('common.date'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('notes')
                    ->label(__('common.notes'))
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('income_category_id')
                    ->label(__('common.category'))
                    ->relationship('incomeCategory', 'name')
                    ->searchable(),
            ])
            ->defaultSort('date', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
