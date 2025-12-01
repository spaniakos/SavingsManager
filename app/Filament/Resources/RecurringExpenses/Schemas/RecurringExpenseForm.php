<?php

namespace App\Filament\Resources\RecurringExpenses\Schemas;

use App\Models\ExpenseCategory;
use App\Models\ExpenseSuperCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class RecurringExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->reactive()
                    ->dehydrated(false) // Don't save this field
                    ->afterStateUpdated(fn (callable $set) => $set('expense_category_id', null)),
                Select::make('expense_category_id')
                    ->label(__('common.category'))
                    ->options(function (callable $get) {
                        $superCategoryId = $get('expense_super_category_id');
                        if (!$superCategoryId) {
                            // If no super category selected, show all categories
                            $userId = Auth::id();
                            return ExpenseCategory::forUser($userId)
                                ->with('expenseSuperCategory')
                                ->get()
                                ->mapWithKeys(function ($category) {
                                    $superName = $category->expenseSuperCategory?->getTranslatedName() ?? '';
                                    return [$category->id => ($superName ? $superName . ' - ' : '') . $category->getTranslatedName()];
                                });
                        }
                        $userId = Auth::id();
                        return ExpenseCategory::forUser($userId)
                            ->where('expense_super_category_id', $superCategoryId)
                            ->get()
                            ->mapWithKeys(function ($category) {
                                return [$category->id => $category->getTranslatedName()];
                            });
                    })
                    ->searchable()
                    ->required(),
                TextInput::make('amount')
                    ->label(__('common.amount'))
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01),
                Select::make('frequency')
                    ->label(__('common.frequency'))
                    ->options([
                        'week' => __('common.week'),
                        'month' => __('common.month'),
                        'quarter' => __('common.quarter'),
                        'year' => __('common.year'),
                    ])
                    ->required()
                    ->default('month'),
                DatePicker::make('start_date')
                    ->label(__('common.start_date'))
                    ->required()
                    ->default(now())
                    ->displayFormat('d/m/Y'),
                DatePicker::make('end_date')
                    ->label(__('common.end_date'))
                    ->displayFormat('d/m/Y'),
                Toggle::make('is_active')
                    ->label(__('common.is_active'))
                    ->default(true),
                Textarea::make('notes')
                    ->label(__('common.notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
