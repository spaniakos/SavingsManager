<?php

namespace App\Filament\Widgets;

use App\Models\SavingsGoal;
use App\Services\SavingsCalculatorService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SavingsGoalProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.savings-goal-progress-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected SavingsCalculatorService $calculator;
    
    public function mount(): void
    {
        $this->calculator = app(SavingsCalculatorService::class);
    }
    
    public function getGoalsProperty()
    {
        $userId = Auth::id();
        
        return SavingsGoal::where('user_id', $userId)
            ->orWhereHas('members', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->get();
    }
    
    public function getProgressData(SavingsGoal $goal): array
    {
        return $this->calculator->getProgressData($goal, Auth::id());
    }
    
    protected static ?int $sort = 1;
}
