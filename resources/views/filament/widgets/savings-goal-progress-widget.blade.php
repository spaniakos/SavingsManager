<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('common.savings_goals') }}
        </x-slot>
        
        @if($this->getSeedCapital() > 0 || $this->getNetWorth() > 0)
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('common.seed_capital') }}</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            €{{ number_format($this->getSeedCapital(), 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('common.net_worth') }}</p>
                        <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                            €{{ number_format($this->getNetWorth(), 2) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="space-y-6">
            @forelse($this->goals as $goal)
                @php
                    $progressData = $this->getProgressData($goal);
                @endphp
                
                @if($progressData)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $goal->name }}
                            </h3>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ number_format($goal->current_amount, 2) }} / {{ number_format($goal->target_amount, 2) }} €
                            </span>
                        </div>
                        
                        <!-- Overall Progress Bar -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">
                                    {{ __('common.progress') }} ({{ __('common.overall') }})
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($progressData['overall_progress'], 1) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-6 overflow-hidden">
                                <div 
                                    class="h-full bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-full transition-all duration-500 flex items-center justify-center"
                                    style="width: {{ min(100, $progressData['overall_progress']) }}%"
                                >
                                    @if($progressData['overall_progress'] > 10)
                                        <span class="text-xs font-semibold text-white">
                                            {{ number_format($progressData['overall_progress'], 1) }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Monthly Progress Bar -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">
                                    {{ __('common.progress') }} ({{ __('common.monthly') }})
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($progressData['monthly_progress'], 1) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-6 overflow-hidden">
                                <div 
                                    class="h-full bg-gradient-to-r from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-full transition-all duration-500 flex items-center justify-center"
                                    style="width: {{ min(100, $progressData['monthly_progress']) }}%"
                                >
                                    @if($progressData['monthly_progress'] > 10)
                                        <span class="text-xs font-semibold text-white">
                                            {{ number_format($progressData['monthly_progress'], 1) }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Progress Details -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('common.monthly_saving_needed') }}
                                </p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($progressData['monthly_saving_needed'], 2) }} €
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('common.months_remaining') }}
                                </p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $progressData['months_remaining'] }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('common.current_month_savings') }}
                                </p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($progressData['current_month_savings'], 2) }} €
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('common.projected_savings') }}
                                </p>
                                <p class="text-sm font-semibold text-green-600 dark:text-green-400">
                                    {{ number_format($progressData['projected_monthly_savings'], 2) }} €
                                </p>
                            </div>
                        </div>
                        
                        <!-- Projection Message -->
                        @if($progressData['projected_monthly_savings'] > 0)
                            <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <p class="text-sm text-green-800 dark:text-green-200">
                                    <strong>{{ __('common.if_no_spending') }}</strong> {{ number_format($progressData['projected_monthly_savings'], 2) }} €
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>{{ __('common.no_savings_goals') }}</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

