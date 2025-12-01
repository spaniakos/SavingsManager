<?php

namespace App\Filament\Widgets;

use App\Services\ChartDataService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class IncomeTrendsChart extends ChartWidget
{
    protected ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('common.income_trends');
    }

    protected function getData(): array
    {
        $chartDataService = app(ChartDataService::class);
        $data = $chartDataService->getIncomeTrends(null, null, Auth::id());

        return $chartDataService->formatForBarChart($data);
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
