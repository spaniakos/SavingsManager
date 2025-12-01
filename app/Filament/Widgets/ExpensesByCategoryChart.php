<?php

namespace App\Filament\Widgets;

use App\Services\ChartDataService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ExpensesByCategoryChart extends ChartWidget
{
    protected ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('common.expenses_by_category');
    }

    protected function getData(): array
    {
        $chartDataService = app(ChartDataService::class);
        $data = $chartDataService->getExpensesByCategory(null, null, Auth::id());

        return $chartDataService->formatForPieChart($data);
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
