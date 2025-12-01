<?php

namespace App\Filament\Resources\IncomeEntries\Pages;

use App\Filament\Resources\IncomeEntries\IncomeEntryResource;
use App\Models\SavingsGoal;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateIncomeEntry extends CreateRecord
{
    protected static string $resource = IncomeEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check if previous month was calculated
        $previousMonthCalculated = $this->isPreviousMonthCalculated();
        $minDate = $previousMonthCalculated
            ? Carbon::now()->startOfMonth()
            : Carbon::now()->subMonth()->startOfMonth();
        $maxDate = Carbon::now()->endOfMonth();

        // Validate date is in allowed range
        $entryDate = Carbon::parse($data['date']);

        if ($entryDate->lt($minDate) || $entryDate->gt($maxDate)) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['date' => [$previousMonthCalculated
                    ? __('common.cannot_create_past_month_entry')
                    : __('common.can_only_create_current_or_previous_month')]]
            );
        }

        $data['user_id'] = Auth::id();

        return $data;
    }

    protected function isPreviousMonthCalculated(): bool
    {
        $userId = Auth::id();
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
