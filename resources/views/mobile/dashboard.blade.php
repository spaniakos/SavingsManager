@extends('mobile.layout')

@section('content')
@php
    use App\Models\IncomeEntry;
    use App\Models\ExpenseEntry;
    use App\Models\SavingsGoal;
    use App\Services\SavingsCalculatorService;
    use App\Services\ReportService;
    use Carbon\Carbon;
    
    $user = auth()->user();
    $now = Carbon::now();
    $startOfMonth = $now->copy()->startOfMonth();
    $endOfMonth = $now->copy()->endOfMonth();
    
    $calculator = app(SavingsCalculatorService::class);
    $reportService = app(ReportService::class);
    
    // Current month stats
    $currentIncome = IncomeEntry::where('user_id', $user->id)
        ->whereBetween('date', [$startOfMonth, $endOfMonth])
        ->sum('amount');
    
    $currentExpenses = ExpenseEntry::where('user_id', $user->id)
        ->whereBetween('date', [$startOfMonth, $endOfMonth])
        ->sum('amount');
    
    $currentSavings = $currentIncome - $currentExpenses;
    
    // Projected savings using simplified formula: current income + max(0, (median income - current income))
    $medianIncome = $user->median_monthly_income ?? 0;
    $projectedSavings = $currentIncome + max(0, ($medianIncome - $currentIncome));
    
    // Days remaining in month (rounded)
    $daysRemaining = round($now->diffInDays($endOfMonth) + 1);
    
    // Total saved (seed + savings goals + save for later)
    $totalSaved = $reportService->calculateTotalSaved($user);
    
    // Active savings goals
    $activeGoals = $user->savingsGoals()
        ->where(function($q) use ($now) {
            $q->whereNull('target_date')
              ->orWhere('target_date', '>=', $now);
        })
        ->get();
    
    // All goals for monthly calculation check
    $allGoals = $user->savingsGoals()->get();
    
    // Expenses by super category this month
    $expensesBySuperCategory = ExpenseEntry::where('expense_entries.user_id', $user->id)
        ->whereBetween('expense_entries.date', [$startOfMonth, $endOfMonth])
        ->join('expense_categories', 'expense_entries.expense_category_id', '=', 'expense_categories.id')
        ->join('expense_super_categories', 'expense_categories.expense_super_category_id', '=', 'expense_super_categories.id')
        ->selectRaw('expense_super_categories.id, expense_super_categories.name, expense_super_categories.emoji, expense_super_categories.allocation_percentage, SUM(expense_entries.amount) as total')
        ->groupBy('expense_super_categories.id', 'expense_super_categories.name', 'expense_super_categories.emoji', 'expense_super_categories.allocation_percentage')
        ->get();
    
    // Calculate progress for each super category
    $medianIncome = $user->median_monthly_income ?? 0;
    $superCategoryProgress = $expensesBySuperCategory->map(function($item) use ($medianIncome) {
        $allowance = $medianIncome * ($item->allocation_percentage / 100);
        $percentage = $allowance > 0 ? min(100, round(($item->total / $allowance) * 100, 1)) : 0;
        return [
            'name' => $item->name,
            'emoji' => $item->emoji,
            'current' => $item->total,
            'allowance' => $allowance,
            'percentage' => $percentage
        ];
    });
    
    // Calculate savings goals progress
    $totalSavingsCurrent = $activeGoals->sum('current_amount');
    $totalSavingsTarget = $activeGoals->sum('target_amount');
    $savingsProgressPercentage = $totalSavingsTarget > 0 ? min(100, round(($totalSavingsCurrent / $totalSavingsTarget) * 100, 1)) : 0;
    
    // Income trend (last 6 months)
    $incomeTrend = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = $now->copy()->subMonths($i);
        $monthIncome = IncomeEntry::where('user_id', $user->id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->sum('amount');
        $incomeTrend[] = [
            'month' => $month->format('M Y'),
            'amount' => $monthIncome
        ];
    }
    
    // Savings trend (last 6 months)
    $savingsTrend = [];
    $previousMonthSavings = null;
    for ($i = 5; $i >= 0; $i--) {
        $month = $now->copy()->subMonths($i);
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        $monthIncome = IncomeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');
        $monthExpenses = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');
        $monthSavings = $monthIncome - $monthExpenses;
        $savingsTrend[] = [
            'month' => $month->format('M Y'),
            'amount' => $monthSavings
        ];
        // Store previous month savings for multiplier calculation
        if ($i == 1) {
            $previousMonthSavings = $monthSavings;
        }
    }
    
    // Calculate multiplier (current month vs previous month)
    $savingsMultiplier = null;
    if ($previousMonthSavings !== null && $previousMonthSavings > 0) {
        $savingsMultiplier = round($currentSavings / $previousMonthSavings, 1);
    } elseif ($previousMonthSavings !== null && $previousMonthSavings < 0 && $currentSavings > 0) {
        // If previous month was negative and current is positive, show as improvement
        $savingsMultiplier = '∞';
    }
@endphp

<div class="p-4 space-y-6">
    <!-- Header -->
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('common.dashboard') }}</h1>
        <p class="text-sm text-gray-600 mt-1">{{ $now->format('F Y') }}</p>
    </div>
    
    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border-2 border-green-200">
            <div class="text-xs text-green-700 mb-1">{{ __('common.income') }}</div>
            <div class="text-2xl font-bold text-green-800">€{{ number_format($currentIncome, 2) }}</div>
            <div class="text-xs text-green-600 mt-1">{{ __('common.this_month') }}</div>
        </div>
        
        <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-xl border-2 border-red-200">
            <div class="text-xs text-red-700 mb-1">{{ __('common.expenses') }}</div>
            <div class="text-2xl font-bold text-red-800">€{{ number_format($currentExpenses, 2) }}</div>
            <div class="text-xs text-red-600 mt-1">{{ __('common.this_month') }}</div>
        </div>
        
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border-2 border-blue-200">
            <div class="text-xs text-blue-700 mb-1">{{ __('common.current_savings') }}</div>
            <div class="text-2xl font-bold text-blue-800">€{{ number_format($currentSavings, 2) }}</div>
            <div class="text-xs text-blue-600 mt-1">{{ __('common.this_month') }}</div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-xl border-2 border-purple-200">
            <div class="text-xs text-purple-700 mb-1">{{ __('common.total_saved') }}</div>
            <div class="text-2xl font-bold text-purple-800">€{{ number_format($totalSaved, 2) }}</div>
            <div class="text-xs text-purple-600 mt-1">{{ __('common.all_time') }}</div>
        </div>
    </div>
    
    <!-- Projection Card -->
    <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-5 rounded-xl border-2 border-amber-300">
        <div class="flex items-center justify-between mb-3">
            <div>
                <div class="text-sm font-semibold text-amber-900">{{ __('common.projected_month_end') }}</div>
                <div class="text-3xl font-bold text-amber-900 mt-1">€{{ number_format($projectedSavings, 2) }}</div>
            </div>
            <div class="text-4xl">📈</div>
        </div>
        <div class="text-xs text-amber-700 mt-2">
            @if($projectedSavings > 0)
                {{ __('common.great_job') }} {{ __('common.days_remaining', ['days' => $daysRemaining]) }} {{ __('common.and_you_have') }} €{{ number_format($projectedSavings, 2) }} {{ __('common.projected') }}!
            @else
                {{ __('common.watch_spending') }} {{ __('common.days_remaining', ['days' => $daysRemaining]) }} {{ __('common.left_this_month') }}.
            @endif
        </div>
    </div>
    
    <!-- Monthly Calculation Button -->
    @php
        $previousMonth = $now->copy()->subMonth();
        $previousMonthStart = $previousMonth->copy()->startOfMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();
        
        // Check if previous month has been calculated
        // We check ALL goals because if any goal was calculated for that month, we shouldn't show the button
        // If last_monthly_calculation_at is after the end of previous month, it means previous month was calculated
        $previousMonthCalculated = false;
        foreach ($allGoals as $goal) {
            if ($goal->last_monthly_calculation_at) {
                $lastCalc = Carbon::parse($goal->last_monthly_calculation_at);
                // If calculation was done after the previous month ended, it means previous month was calculated
                if ($lastCalc->isAfter($previousMonthEnd)) {
                    $previousMonthCalculated = true;
                    break;
                }
            }
        }
    @endphp
    @if(!$previousMonthCalculated)
    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-5 rounded-xl border-2 border-indigo-300">
        <form method="POST" action="{{ route('mobile.monthly-calculation.calculate') }}" onsubmit="return confirm('{{ __('common.confirm_monthly_calculation', ['month' => $previousMonth->format('F Y')]) }}')">
            @csrf
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-indigo-900">{{ __('common.calculate_previous_month') }}</div>
                    <div class="text-xs text-indigo-700 mt-1">{{ $previousMonth->format('F Y') }}</div>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold text-sm">
                    {{ __('common.calculate') }}
                </button>
            </div>
        </form>
    </div>
    @endif
    
    <!-- Super Category Progress Bars -->
    @if($superCategoryProgress->count() > 0)
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <h3 class="text-lg font-semibold mb-4">{{ __('common.expenses_by_category') }}</h3>
        <div class="space-y-4">
            @foreach($superCategoryProgress as $progress)
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        @if($progress['emoji'])
                            <span class="text-lg">{{ $progress['emoji'] }}</span>
                        @endif
                        <span class="text-sm font-semibold">{{ __("categories.expense_super.{$progress['name']}") }}</span>
                    </div>
                    <div class="text-xs text-gray-600">
                        €{{ number_format($progress['current'], 2) }} / €{{ number_format($progress['allowance'], 2) }} ({{ $progress['percentage'] }}%)
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="h-3 rounded-full {{ $progress['percentage'] > 100 ? 'bg-red-500' : ($progress['percentage'] > 80 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                         style="width: {{ min(100, $progress['percentage']) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Savings Goals Progress Bar -->
    @if($activeGoals->count() > 0 && $totalSavingsTarget > 0)
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <h3 class="text-lg font-semibold mb-4">{{ __('common.savings_goals_progress') }}</h3>
        <div>
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-semibold">{{ __('common.total_savings') }}</span>
                <div class="text-xs text-gray-600">
                    €{{ number_format($totalSavingsCurrent, 2) }} / €{{ number_format($totalSavingsTarget, 2) }} ({{ $savingsProgressPercentage }}%)
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="h-3 rounded-full bg-gradient-to-r from-amber-400 to-amber-600" 
                     style="width: {{ min(100, $savingsProgressPercentage) }}%"></div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Expenses by Category Chart -->
    @if($expensesBySuperCategory->count() > 0)
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <h3 class="text-lg font-semibold mb-4">{{ __('common.expenses_by_category') }}</h3>
        <div class="chart-container">
            <canvas id="expensesChart"></canvas>
        </div>
    </div>
    @endif
    
    <!-- Income Trend Chart -->
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <h3 class="text-lg font-semibold mb-4">{{ __('common.income_trend') }}</h3>
        <div class="chart-container">
            <canvas id="incomeTrendChart"></canvas>
        </div>
    </div>
    
    <!-- Savings Trend Chart -->
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">{{ __('common.savings_trend') }}</h3>
            @if($savingsMultiplier !== null)
                <div class="text-sm text-blue-600 font-semibold">
                    {{ __('common.saved') }} <{{ $savingsMultiplier }}X> {{ __('common.this_month') }}
                </div>
            @endif
        </div>
        <div class="chart-container">
            <canvas id="savingsTrendChart"></canvas>
        </div>
    </div>
    
    <!-- Active Goals -->
    @if($activeGoals->count() > 0)
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <h3 class="text-lg font-semibold mb-4">{{ __('common.active_goals') }}</h3>
        <div class="space-y-3">
            @foreach($activeGoals as $goal)
                @php
                    $progress = $calculator->getProgressData($goal);
                @endphp
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="font-semibold text-gray-800">{{ $goal->name }}</div>
                            <div class="text-xs text-gray-600">€{{ number_format($goal->current_amount, 2) }} / €{{ number_format($goal->target_amount, 2) }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-amber-600">{{ number_format($progress['overall_progress'], 0) }}%</div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-2 rounded-full" style="width: {{ min(100, $progress['overall_progress']) }}%"></div>
                    </div>
                    @if($progress['months_remaining'] > 0)
                        <div class="text-xs text-gray-600">
                            {{ __('common.monthly_needed') }}: €{{ number_format($progress['monthly_saving_needed'], 2) }} | 
                            {{ __('common.months_left') }}: {{ $progress['months_remaining'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Expenses by Category Chart
@if($expensesBySuperCategory->count() > 0)
@php
    $expensesTotal = $expensesBySuperCategory->sum('total');
    $expensesLabels = $expensesBySuperCategory->map(function($item) use ($expensesTotal) {
        $percentage = $expensesTotal > 0 ? round(($item->total / $expensesTotal) * 100, 1) : 0;
        $emoji = $item->emoji ? $item->emoji . ' ' : '';
        return $emoji . __("categories.expense_super.{$item->name}") . ': €' . number_format($item->total, 2) . ' (' . $percentage . '%)';
    })->toArray();
@endphp
const expensesCtx = document.getElementById('expensesChart').getContext('2d');
new Chart(expensesCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($expensesLabels) !!},
        datasets: [{
            data: {!! json_encode($expensesBySuperCategory->pluck('total')->toArray()) !!},
            backgroundColor: [
                '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6', '#10b981', '#ec4899'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 8,
                    font: { size: 10 },
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return label + ': €' + value.toFixed(2) + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
@endif

// Income Trend Chart
const incomeCtx = document.getElementById('incomeTrendChart').getContext('2d');
new Chart(incomeCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($incomeTrend, 'month')) !!},
        datasets: [{
            label: '{{ __('common.income') }}',
            data: {!! json_encode(array_column($incomeTrend, 'amount')) !!},
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '€' + value.toFixed(0);
                    }
                }
            }
        }
    }
});

// Savings Trend Chart
const savingsCtx = document.getElementById('savingsTrendChart').getContext('2d');
new Chart(savingsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($savingsTrend, 'month')) !!},
        datasets: [{
            label: '{{ __('common.savings') }}',
            data: {!! json_encode(array_column($savingsTrend, 'amount')) !!},
            backgroundColor: function(context) {
                const value = context.parsed.y;
                return value >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)';
            },
            borderColor: function(context) {
                const value = context.parsed.y;
                return value >= 0 ? '#10b981' : '#ef4444';
            },
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                ticks: {
                    callback: function(value) {
                        return '€' + value.toFixed(0);
                    }
                }
            }
        }
    }
});
</script>
@endsection
