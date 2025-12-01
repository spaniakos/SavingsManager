<?php

namespace App\Filament\Resources\RecurringExpenses\Tables;

use App\Services\RecurringExpenseService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RecurringExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->columns([
                TextColumn::make('expenseCategory.getTranslatedName')
                    ->label(__('common.category'))
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('common.amount'))
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('frequency')
                    ->label(__('common.frequency'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __("common.{$state}")),
                TextColumn::make('start_date')
                    ->label(__('common.start_date'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(__('common.end_date'))
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('next_due_date')
                    ->label(__('common.next_due_date'))
                    ->getStateUsing(function ($record) {
                        $service = app(RecurringExpenseService::class);
                        $nextDue = $service->calculateNextDueDate($record);
                        return $nextDue ? $nextDue->format('d/m/Y') : '-';
                    })
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('common.is_active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('last_generated_at')
                    ->label(__('common.last_generated_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label(__('common.is_active'))
                    ->options([
                        1 => __('common.yes'),
                        0 => __('common.no'),
                    ]),
                SelectFilter::make('frequency')
                    ->label(__('common.frequency'))
                    ->options([
                        'week' => __('common.week'),
                        'month' => __('common.month'),
                        'quarter' => __('common.quarter'),
                        'year' => __('common.year'),
                    ]),
            ])
            ->defaultSort('start_date', 'desc')
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
