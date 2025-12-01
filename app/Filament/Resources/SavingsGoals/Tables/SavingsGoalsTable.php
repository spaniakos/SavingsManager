<?php

namespace App\Filament\Resources\SavingsGoals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SavingsGoalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.goal_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('target_amount')
                    ->label(__('common.target_amount'))
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('current_amount')
                    ->label(__('common.current_amount'))
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('progress')
                    ->label(__('common.progress'))
                    ->formatStateUsing(function ($record) {
                        if ($record->target_amount > 0) {
                            $percentage = ($record->current_amount / $record->target_amount) * 100;

                            return number_format($percentage, 1).'%';
                        }

                        return '0%';
                    })
                    ->badge()
                    ->color(fn ($record) => $record->target_amount > 0 && ($record->current_amount / $record->target_amount) >= 1
                            ? 'success'
                            : 'warning'
                    ),
                TextColumn::make('target_date')
                    ->label(__('common.target_date'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('target_date', 'asc')
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
