<?php

namespace App\Filament\Resources\IncomeEntries\Schemas;

use App\Models\IncomeCategory;
use App\Models\Person;
use App\Models\SavingsGoal;
use Carbon\Carbon;
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
                    ->displayFormat('d/m/Y')
                    ->minDate(function ($record) {
                        // For edit, check if entry is from past month (more than 1 month ago)
                        if ($record && $record->date) {
                            $entryMonth = Carbon::parse($record->date)->startOfMonth();
                            $previousMonth = Carbon::now()->subMonth()->startOfMonth();
                            if ($entryMonth->lt($previousMonth)) {
                                // Past month entry - keep original date but disable editing
                                return Carbon::parse($record->date);
                            }
                        }
                        // For create or current/previous month edit
                        $previousMonthCalculated = self::isPreviousMonthCalculated();

                        return $previousMonthCalculated
                            ? Carbon::now()->startOfMonth()
                            : Carbon::now()->subMonth()->startOfMonth();
                    })
                    ->maxDate(now()->endOfMonth())
                    ->helperText(function ($record) {
                        if ($record && $record->date) {
                            $entryMonth = Carbon::parse($record->date)->startOfMonth();
                            $previousMonth = Carbon::now()->subMonth()->startOfMonth();
                            if ($entryMonth->lt($previousMonth)) {
                                return __('common.cannot_edit_past_month_entry');
                            }
                        }
                        $previousMonthCalculated = self::isPreviousMonthCalculated();

                        return $previousMonthCalculated
                            ? __('common.date_current_month_only')
                            : __('common.date_current_or_previous_month');
                    })
                    ->disabled(fn ($record) => $record && $record->date && Carbon::parse($record->date)->startOfMonth()->lt(Carbon::now()->subMonth()->startOfMonth())),
                Select::make('person_id')
                    ->label(__('common.person'))
                    ->options(function () {
                        $userId = Auth::id();
                        if (! $userId) {
                            return [];
                        }

                        return Person::where('user_id', $userId)
                            ->orderBy('fullname')
                            ->get()
                            ->mapWithKeys(function ($person) {
                                return [$person->id => $person->fullname];
                            });
                    })
                    ->searchable()
                    ->nullable(),
                Textarea::make('notes')
                    ->label(__('common.notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    protected static function isPreviousMonthCalculated(): bool
    {
        $userId = Auth::id();
        if (! $userId) {
            return false;
        }

        $previousMonth = Carbon::now()->subMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();

        $allGoals = SavingsGoal::where('user_id', $userId)->get();

        foreach ($allGoals as $goal) {
            if ($goal->last_monthly_calculation_at) {
                $lastCalc = Carbon::parse($goal->last_monthly_calculation_at);
                if ($lastCalc->isAfter($previousMonthEnd)) {
                    return true;
                }
            }
        }

        return false;
    }
}
