<?php

namespace App\Filament\Widgets;

use App\Services\ChartDataService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class MoMSavingsChart extends ChartWidget
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
        return __('common.mom_comparison');
    }

    protected function getData(): array
    {
        $data = $this->chartDataService->getMoMSavingsComparison(6, Auth::id());
        return $this->chartDataService->formatForMoMChart($data);
    }

    protected function getType(): string
    {
        return 'bar';
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
