<x-filament-panels::page>
    @include('mobile.components.responsive-nav')
    
    <div class="space-y-6 mt-6">
        <form wire:submit.prevent="generateReport">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{ $this->form }}
            </div>

            <x-filament::button type="submit" class="mt-4">
                {{ __('common.generate_report') }}
            </x-filament::button>
        </form>

        @if($this->reportData)
            <div class="mt-8 space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg border-2 border-green-200 dark:border-green-800">
                        <p class="text-sm text-green-700 dark:text-green-300 mb-1">{{ __('common.total_income') }}</p>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                            €{{ number_format($this->reportData['summary']['total_income'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-lg border-2 border-red-200 dark:border-red-800">
                        <p class="text-sm text-red-700 dark:text-red-300 mb-1">{{ __('common.total_expenses') }}</p>
                        <p class="text-2xl font-bold text-red-900 dark:text-red-100">
                            €{{ number_format($this->reportData['summary']['total_expenses'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg border-2 border-blue-200 dark:border-blue-800">
                        <p class="text-sm text-blue-700 dark:text-blue-300 mb-1">{{ __('common.net_savings') }}</p>
                        <p class="text-2xl font-bold {{ ($this->reportData['summary']['net_savings'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            €{{ number_format($this->reportData['summary']['net_savings'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg border-2 border-purple-200 dark:border-purple-800">
                        <p class="text-sm text-purple-700 dark:text-purple-300 mb-1">{{ __('common.savings_rate') }}</p>
                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                            {{ number_format($this->reportData['summary']['savings_rate'] ?? 0, 1) }}%
                        </p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-lg border-2 border-amber-200 dark:border-amber-800">
                        <p class="text-sm text-amber-700 dark:text-amber-300 mb-1">{{ __('common.total_saved') }}</p>
                        <p class="text-2xl font-bold text-amber-900 dark:text-amber-100">
                            €{{ number_format($this->reportData['summary']['total_saved'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>

                <!-- Period Info -->
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('common.period') }}: 
                        <span class="font-semibold">{{ $this->reportData['period']['start_formatted'] }}</span> 
                        {{ __('common.to') }} 
                        <span class="font-semibold">{{ $this->reportData['period']['end_formatted'] }}</span>
                    </p>
                </div>

                <!-- Income Analysis -->
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('common.income_analysis') }}
                    </x-slot>

                    @if(!empty($this->reportData['income']['hierarchical']))
                        @php
                            $totalIncome = $this->reportData['summary']['total_income'];
                            $breakdownType = $this->reportData['breakdown_type'] ?? 'super_category';
                        @endphp
                        
                        <div class="space-y-4">
                            @foreach($this->reportData['income']['hierarchical'] as $category)
                                <!-- Category Card -->
                                <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    <!-- Category Header -->
                                    <div class="bg-gray-100 dark:bg-gray-700 p-3 border-b-2 border-gray-200 dark:border-gray-600">
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center gap-2">
                                                @if($category['emoji']) <span class="text-lg">{{ $category['emoji'] }}</span> @endif
                                                <span class="font-bold text-lg text-gray-900 dark:text-white">{{ $category['name'] }}</span>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-lg text-gray-900 dark:text-white">€{{ number_format($category['total'], 2) }}</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $totalIncome > 0 ? number_format(($category['total'] / $totalIncome) * 100, 1) : 0 }}% | 
                                                    {{ $category['count'] }} {{ __('common.items') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Items (if breakdown type is item) -->
                                    @if($breakdownType === 'item' && !empty($category['items']))
                                        <div class="bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700">
                                            <div class="pl-8 pr-4 pb-2 pt-2">
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-xs">
                                                        <thead class="bg-gray-100 dark:bg-gray-800">
                                                            <tr>
                                                                <th class="px-2 py-1 text-left">{{ __('common.date') }}</th>
                                                                <th class="px-2 py-1 text-right">{{ __('common.amount') }}</th>
                                                                <th class="px-2 py-1 text-left">{{ __('common.notes') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($category['items'] as $item)
                                                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                                                    <td class="px-2 py-1 text-gray-900 dark:text-white">
                                                                        <span class="ml-4 text-gray-500 dark:text-gray-400">└─</span>
                                                                        {{ $item['date'] }}
                                                                    </td>
                                                                    <td class="px-2 py-1 text-right font-semibold text-gray-900 dark:text-white">
                                                                        €{{ number_format($item['amount'], 2) }}
                                                                    </td>
                                                                    <td class="px-2 py-1 text-gray-600 dark:text-gray-400">{{ $item['notes'] ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>

                <!-- Expenses Analysis -->
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('common.expenses_analysis') }}
                    </x-slot>

                    @if(!empty($this->reportData['expenses']['hierarchical']))
                        @php
                            $totalExpenses = $this->reportData['summary']['total_expenses'];
                            $breakdownType = $this->reportData['breakdown_type'] ?? 'super_category';
                        @endphp
                        
                        @foreach($this->reportData['expenses']['hierarchical'] as $superCategory)
                            <div class="mb-6">
                                <!-- Super Category Header -->
                                <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-t-lg border-b-2 border-gray-200 dark:border-gray-600">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            @if($superCategory['emoji']) <span class="text-lg">{{ $superCategory['emoji'] }}</span> @endif
                                            <span class="font-bold text-lg text-gray-900 dark:text-white">{{ $superCategory['name'] }}</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-lg text-gray-900 dark:text-white">€{{ number_format($superCategory['total'], 2) }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $totalExpenses > 0 ? number_format(($superCategory['total'] / $totalExpenses) * 100, 1) : 0 }}% | 
                                                {{ $superCategory['count'] }} {{ __('common.items') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Categories (only if breakdown type is category or item) -->
                                @if(($breakdownType === 'category' || $breakdownType === 'item') && !empty($superCategory['categories']))
                                    <div class="bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 rounded-b-lg">
                                        @foreach($superCategory['categories'] as $category)
                                            <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                                <!-- Category Row -->
                                                <div class="p-3 bg-white dark:bg-gray-800">
                                                    <div class="flex justify-between items-center">
                                                        <div class="flex items-center gap-2">
                                                            <span class="ml-4 dark:text-gray-400">└─</span>
                                                            @if($category['emoji']) <span>{{ $category['emoji'] }}</span> @endif
                                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $category['name'] }}</span>
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="font-semibold text-gray-900 dark:text-white">€{{ number_format($category['total'], 2) }}</div>
                                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                                {{ $totalExpenses > 0 ? number_format(($category['total'] / $totalExpenses) * 100, 1) : 0 }}% | 
                                                                {{ $category['count'] }} {{ __('common.items') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Items (if breakdown type is item) -->
                                                @if($breakdownType === 'item' && !empty($category['items']))
                                                    <div class="pl-8 pr-4 pb-2">
                                                        <div class="overflow-x-auto">
                                                            <table class="w-full text-xs">
                                                                <thead class="bg-gray-100 dark:bg-gray-800">
                                                                    <tr>
                                                                        <th class="px-2 py-1 text-left">{{ __('common.date') }}</th>
                                                                        <th class="px-2 py-1 text-right">{{ __('common.amount') }}</th>
                                                                        <th class="px-2 py-1 text-left">{{ __('common.notes') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($category['items'] as $item)
                                                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                                                            <td class="px-2 py-1 text-gray-900 dark:text-white">
                                                                                <span class="ml-4 text-gray-500 dark:text-gray-400">└─</span>
                                                                                {{ $item['date'] }}
                                                                            </td>
                                                                            <td class="px-2 py-1 text-right font-semibold text-gray-900 dark:text-white">
                                                                                €{{ number_format($item['amount'], 2) }}
                                                                                @if($item['is_save_for_later'])
                                                                                    <span class="ml-1 text-xs text-purple-600 dark:text-purple-400">({{ __('common.save_for_later') }})</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="px-2 py-1 text-gray-600 dark:text-gray-400">{{ $item['notes'] ?? '-' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </x-filament::section>

                <!-- Goal Progression -->
                @if(!empty($this->reportData['goals_progress']))
                    <x-filament::section>
                        <x-slot name="heading">
                            {{ __('common.goal_progression') }}
                        </x-slot>

                        <div class="space-y-4">
                            @foreach($this->reportData['goals_progress'] as $goal)
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-semibold text-lg">{{ $goal['name'] }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                €{{ number_format($goal['current_amount'] ?? 0, 2) }} / €{{ number_format($goal['target_amount'], 2) }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                                {{ number_format($goal['progress'] ?? 0, 1) }}%
                                            </p>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-3 rounded-full" 
                                             style="width: {{ min(100, $goal['progress'] ?? 0) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-filament::section>
                @endif
            </div>
        @endif
    </div>
</x-filament-panels::page>
