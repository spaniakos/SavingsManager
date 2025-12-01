<x-filament-panels::page>
    @include('mobile.components.responsive-nav')
    
    <div class="space-y-6 mt-6">
        <form wire:submit.prevent="generateReport">
            {{ $this->form }}

            <x-filament::button type="submit" class="mt-4">
                {{ __('common.generate_report') }}
            </x-filament::button>
        </form>

        @if($this->reportData)
            <div class="mt-8 space-y-6">
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('common.report_results') }}
                    </x-slot>

                    @if(isset($this->reportData['month']))
                        <!-- Monthly Report -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.total_income') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($this->reportData['total_income'] ?? 0, 2) }} €
                                    </p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.total_expenses') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($this->reportData['total_expenses'] ?? 0, 2) }} €
                                    </p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.net_savings') }}</p>
                                    <p class="text-2xl font-bold {{ ($this->reportData['net_savings'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} dark:text-white">
                                        {{ number_format($this->reportData['net_savings'] ?? 0, 2) }} €
                                    </p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.total_saved') }}</p>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($this->reportData['total_saved'] ?? 0, 2) }} €
                                    </p>
                                </div>
                            </div>

                            @if(isset($this->reportData['income_by_category']) && count($this->reportData['income_by_category']) > 0)
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold mb-3">{{ __('common.income_by_category') }}</h3>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-100 dark:bg-gray-800">
                                                <tr>
                                                    <th class="px-4 py-2">{{ __('common.category') }}</th>
                                                    <th class="px-4 py-2 text-right">{{ __('common.amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($this->reportData['income_by_category'] as $category => $amount)
                                                    <tr class="border-b dark:border-gray-700">
                                                        <td class="px-4 py-2">{{ $category }}</td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($amount, 2) }} €</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if(isset($this->reportData['expenses_by_category']) && count($this->reportData['expenses_by_category']) > 0)
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold mb-3">{{ __('common.expenses_by_category') }}</h3>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-100 dark:bg-gray-800">
                                                <tr>
                                                    <th class="px-4 py-2">{{ __('common.category') }}</th>
                                                    <th class="px-4 py-2 text-right">{{ __('common.amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($this->reportData['expenses_by_category'] as $category => $amount)
                                                    <tr class="border-b dark:border-gray-700">
                                                        <td class="px-4 py-2">{{ $category }}</td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($amount, 2) }} €</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif(isset($this->reportData['goals']))
                        <!-- Savings Report -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.total_target') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($this->reportData['total_target'] ?? 0, 2) }} €
                                    </p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.total_current') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($this->reportData['total_current'] ?? 0, 2) }} €
                                    </p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.total_progress') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($this->reportData['total_progress'] ?? 0, 1) }}%
                                    </p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.total_saved') }}</p>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($this->reportData['total_saved'] ?? 0, 2) }} €
                                    </p>
                                </div>
                            </div>

                            @foreach($this->reportData['goals'] as $goal)
                                <div class="mt-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <h4 class="font-semibold">{{ $goal['name'] }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ number_format($goal['current_amount'], 2) }} / {{ number_format($goal['target_amount'], 2) }} €
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('common.progress') }}: {{ number_format($goal['progress_percentage'], 1) }}%
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @elseif(isset($this->reportData['expenses_by_category']))
                        <!-- Category Report -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.total_expenses') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($this->reportData['total_expenses'] ?? 0, 2) }} €
                                    </p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.total_saved') }}</p>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($this->reportData['total_saved'] ?? 0, 2) }} €
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-lg font-semibold mb-3">{{ __('common.expenses_by_category') }}</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-gray-100 dark:bg-gray-800">
                                            <tr>
                                                <th class="px-4 py-2">{{ __('common.category') }}</th>
                                                <th class="px-4 py-2 text-right">{{ __('common.amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($this->reportData['expenses_by_category'] as $category => $amount)
                                                <tr class="border-b dark:border-gray-700">
                                                    <td class="px-4 py-2">{{ $category }}</td>
                                                    <td class="px-4 py-2 text-right">{{ number_format($amount, 2) }} €</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </x-filament::section>
            </div>
        @endif
    </div>
</x-filament-panels::page>
