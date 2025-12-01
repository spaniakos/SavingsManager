<?php

namespace App\Filament\Resources\ExpenseEntries\Schemas;

use App\Models\ExpenseCategory;
use App\Models\ExpenseSuperCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ExpenseEntryForm
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
                    ->afterStateUpdated(fn (callable $set) => $set('expense_category_id', null)),
                Select::make('expense_category_id')
                    ->label(__('common.category'))
                    ->options(function (callable $get) {
                        $superCategoryId = $get('expense_super_category_id');
                        if (!$superCategoryId) {
                            return [];
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
                DatePicker::make('date')
                    ->label(__('common.date'))
                    ->required()
                    ->default(now())
                    ->displayFormat('d/m/Y'),
                Textarea::make('notes')
                    ->label(__('common.notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
