<?php

namespace App\Filament\Widgets;

use App\Services\BudgetAllocationService;
use App\Services\PositiveReinforcementService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BudgetAllocationWidget extends Widget
{
    protected string $view = 'filament.widgets.budget-allocation-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected BudgetAllocationService $budgetService;
    protected PositiveReinforcementService $reinforcementService;
    
    public function mount(): void
    {
        $this->budgetService = app(BudgetAllocationService::class);
        $this->reinforcementService = app(PositiveReinforcementService::class);
    }
    
    public function getAllocationStatus()
    {
        $user = Auth::user();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        return $this->budgetService->getAllocationStatus($user, $startDate, $endDate);
    }
    
    public function getEncouragementMessages()
    {
        $user = Auth::user();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        return $this->reinforcementService->getEncouragementMessages($user, $startDate, $endDate);
    }
    
    public function getDaysRemaining()
    {
        return $this->reinforcementService->getDaysRemainingInMonth();
    }
    
    protected static ?int $sort = 2;
}

