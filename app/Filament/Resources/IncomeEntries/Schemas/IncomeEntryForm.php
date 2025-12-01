<?php

namespace App\Filament\Resources\IncomeEntries\Schemas;

use App\Models\IncomeCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class IncomeEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('income_category_id')
                    ->label(__('common.income_category'))
                    ->options(function () {
                        $userId = Auth::id();
                        return IncomeCategory::forUser($userId)
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
