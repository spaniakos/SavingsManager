<?php

namespace App\Filament\Widgets;

use App\Services\ChartDataService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class IncomeTrendsChart extends ChartWidget
{
    protected ?string $heading = null;
    
    protected int | string | array $columnSpan = 'full';
    
    protected ChartDataService $chartDataService;
    
    public function mount(): void
    {
        $this->chartDataService = app(ChartDataService::class);
    }
    
    public function getHeading(): string
    {
        return __('common.income_trends');
    }

    protected function getData(): array
    {
        $data = $this->chartDataService->getIncomeTrends(null, null, Auth::id());
        return $this->chartDataService->formatForBarChart($data);
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
