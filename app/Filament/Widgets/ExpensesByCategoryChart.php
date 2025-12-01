<?php

namespace App\Filament\Widgets;

use App\Services\ChartDataService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ExpensesByCategoryChart extends ChartWidget
{
    protected static ?string $heading = null;
    
    protected int | string | array $columnSpan = 'full';
    
    protected ChartDataService $chartDataService;
    
    public function mount(): void
    {
        $this->chartDataService = app(ChartDataService::class);
    }
    
    public function getHeading(): string
    {
        return __('common.expenses_by_category');
    }

    protected function getData(): array
    {
        $data = $this->chartDataService->getExpensesByCategory(null, null, Auth::id());
        return $this->chartDataService->formatForPieChart($data);
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
            ],
        ];
    }
}
