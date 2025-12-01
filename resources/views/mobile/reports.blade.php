@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-6">
    <h1 class="text-2xl font-bold text-center mb-6">{{ __('common.reports') }}</h1>
    
    <!-- Report Form -->
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <form method="GET" action="{{ route('mobile.reports.index') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.from') }}</label>
                <input type="date" name="start_date" value="{{ $startDate }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.to') }}</label>
                <input type="date" name="end_date" value="{{ $endDate }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.breakdown_type') }}</label>
                <select name="breakdown_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="super_category" {{ $breakdownType === 'super_category' ? 'selected' : '' }}>
                        {{ __('common.per_super_category') }}
                    </option>
                    <option value="category" {{ $breakdownType === 'category' ? 'selected' : '' }}>
                        {{ __('common.per_category') }}
                    </option>
                    <option value="item" {{ $breakdownType === 'item' ? 'selected' : '' }}>
                        {{ __('common.per_item') }}
                    </option>
                </select>
            </div>
            
            <input type="hidden" name="generate" value="1">
            <button type="submit" class="w-full bg-amber-600 text-white py-3 rounded-lg font-semibold">
                {{ __('common.generate_report') }}
            </button>
        </form>
    </div>

    @if($reportData)
        <!-- Summary Cards -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border-2 border-green-200">
                <p class="text-xs text-green-700 mb-1">{{ __('common.total_income') }}</p>
                <p class="text-xl font-bold text-green-900">
                    €{{ number_format($reportData['summary']['total_income'] ?? 0, 2) }}
                </p>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-xl border-2 border-red-200">
                <p class="text-xs text-red-700 mb-1">{{ __('common.total_expenses') }}</p>
                <p class="text-xl font-bold text-red-900">
                    €{{ number_format($reportData['summary']['total_expenses'] ?? 0, 2) }}
                </p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border-2 border-blue-200">
                <p class="text-xs text-blue-700 mb-1">{{ __('common.net_savings') }}</p>
                <p class="text-xl font-bold {{ ($reportData['summary']['net_savings'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    €{{ number_format($reportData['summary']['net_savings'] ?? 0, 2) }}
                </p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-xl border-2 border-purple-200">
                <p class="text-xs text-purple-700 mb-1">{{ __('common.savings_rate') }}</p>
                <p class="text-xl font-bold text-purple-900">
                    {{ number_format($reportData['summary']['savings_rate'] ?? 0, 1) }}%
                </p>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-4 rounded-xl border-2 border-amber-200">
            <p class="text-xs text-amber-700 mb-1">{{ __('common.total_saved') }}</p>
            <p class="text-xl font-bold text-amber-900">
                €{{ number_format($reportData['summary']['total_saved'] ?? 0, 2) }}
            </p>
        </div>

        <!-- Period Info -->
        <div class="bg-gray-50 p-3 rounded-lg">
            <p class="text-sm text-gray-600">
                {{ __('common.period') }}: 
                <span class="font-semibold">{{ $reportData['period']['start_formatted'] }}</span> 
                {{ __('common.to') }} 
                <span class="font-semibold">{{ $reportData['period']['end_formatted'] }}</span>
            </p>
        </div>

        <!-- Export Buttons -->
        <div class="grid grid-cols-2 gap-3">
            <form method="GET" action="{{ route('mobile.reports.export-pdf') }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <input type="hidden" name="breakdown_type" value="{{ $breakdownType }}">
                <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold">
                    📄 {{ __('common.export_pdf') }}
                </button>
            </form>
            <a href="{{ route('mobile.reports.index', ['start_date' => $startDate, 'end_date' => $endDate, 'breakdown_type' => $breakdownType, 'generate' => 1]) }}" 
               class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold text-center">
                🔄 {{ __('common.refresh') }}
            </a>
        </div>

        <!-- Income Analysis -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">{{ __('common.income_analysis') }}</h2>
            
            @if(!empty($reportData['income']['hierarchical']))
                @php
                    $totalIncome = $reportData['summary']['total_income'];
                @endphp
                
                <div class="space-y-4">
                    @foreach($reportData['income']['hierarchical'] as $category)
                        <!-- Category Card -->
                        <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                            <!-- Category Header -->
                            <div class="bg-gray-100 p-3 border-b-2 border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        @if($category['emoji']) <span class="text-lg">{{ $category['emoji'] }}</span> @endif
                                        <span class="font-bold text-base text-gray-800">{{ $category['name'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-base text-gray-800">€{{ number_format($category['total'], 2) }}</div>
                                        <div class="text-xs text-gray-600">
                                            {{ $totalIncome > 0 ? number_format(($category['total'] / $totalIncome) * 100, 1) : 0 }}% | 
                                            {{ $category['count'] }} {{ __('common.items') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items (if breakdown type is item) -->
                            @if($breakdownType === 'item' && !empty($category['items']))
                                <div class="bg-white">
                                    <div class="pl-6 pr-2 pb-2 pt-2 space-y-1">
                                        @foreach($category['items'] as $item)
                                            <div class="p-1.5 bg-gray-100 rounded text-xs">
                                                <div class="flex justify-between items-center">
                                                    <div class="flex-1">
                                                        <span class="text-gray-400">└─</span>
                                                        <span class="font-medium text-gray-800">{{ $item['date'] }}</span>
                                                        @if($item['notes'])
                                                            <span class="text-gray-500 ml-1">- {{ $item['notes'] }}</span>
                                                        @endif
                                                    </div>
                                                    <span class="font-semibold ml-2 text-gray-800">€{{ number_format($item['amount'], 2) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Expenses Analysis -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">{{ __('common.expenses_analysis') }}</h2>
            
            @if(!empty($reportData['expenses']['hierarchical']))
                @php
                    $totalExpenses = $reportData['summary']['total_expenses'];
                @endphp
                
                <div class="space-y-4">
                    @foreach($reportData['expenses']['hierarchical'] as $superCategory)
                        <!-- Super Category Card -->
                        <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                            <!-- Super Category Header -->
                            <div class="bg-gray-100 p-3 border-b-2 border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        @if($superCategory['emoji']) <span class="text-lg">{{ $superCategory['emoji'] }}</span> @endif
                                        <span class="font-bold text-base text-gray-800">{{ $superCategory['name'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-base text-gray-800">€{{ number_format($superCategory['total'], 2) }}</div>
                                        <div class="text-xs text-gray-600">
                                            {{ $totalExpenses > 0 ? number_format(($superCategory['total'] / $totalExpenses) * 100, 1) : 0 }}% | 
                                            {{ $superCategory['count'] }} {{ __('common.items') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Categories (only if breakdown type is category or item) -->
                            @if(($breakdownType === 'category' || $breakdownType === 'item') && !empty($superCategory['categories']))
                                <div class="bg-white">
                                    @foreach($superCategory['categories'] as $category)
                                        <div class="border-b border-gray-200 last:border-b-0">
                                            <!-- Category Row -->
                                            <div class="p-2 bg-white">
                                                <div class="flex justify-between items-center">
                                                    <div class="flex items-center gap-2 flex-1">
                                                        <span class="text-gray-400">└─</span>
                                                        @if($category['emoji']) <span>{{ $category['emoji'] }}</span> @endif
                                                        <span class="font-semibold text-sm text-gray-800">{{ $category['name'] }}</span>
                                                        <span class="text-xs text-gray-500">({{ $category['count'] }})</span>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="font-semibold text-sm text-gray-800">€{{ number_format($category['total'], 2) }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $totalExpenses > 0 ? number_format(($category['total'] / $totalExpenses) * 100, 1) : 0 }}%
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Items (if breakdown type is item) -->
                                            @if($breakdownType === 'item' && !empty($category['items']))
                                                <div class="pl-6 pr-2 pb-2 space-y-1">
                                                    @foreach($category['items'] as $item)
                                                        <div class="p-1.5 bg-gray-100 rounded text-xs">
                                                            <div class="flex justify-between items-center">
                                                                <div class="flex-1">
                                                                    <span class="text-gray-400">└─</span>
                                                                    <span class="font-medium text-gray-800">{{ $item['date'] }}</span>
                                                                    @if($item['is_save_for_later'])
                                                                        <span class="ml-1 text-xs text-purple-600">({{ __('common.save_for_later') }})</span>
                                                                    @endif
                                                                    @if($item['notes'])
                                                                        <span class="text-gray-500 ml-1">- {{ $item['notes'] }}</span>
                                                                    @endif
                                                                </div>
                                                                <span class="font-semibold ml-2 text-gray-800">€{{ number_format($item['amount'], 2) }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Goal Progression -->
        @if(!empty($reportData['goals_progress']))
            <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
                <h2 class="text-lg font-semibold mb-4">{{ __('common.goal_progression') }}</h2>
                <div class="space-y-4">
                    @foreach($reportData['goals_progress'] as $goal)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-semibold">{{ $goal['name'] }}</h4>
                                    <p class="text-sm text-gray-600">
                                        €{{ number_format($goal['current_amount'] ?? 0, 2) }} / €{{ number_format($goal['target_amount'], 2) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-amber-600">
                                        {{ number_format($goal['progress'] ?? 0, 1) }}%
                                    </p>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-2 rounded-full" 
                                     style="width: {{ min(100, $goal['progress'] ?? 0) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
