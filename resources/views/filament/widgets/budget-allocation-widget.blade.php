<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('common.budget_allocation') }}
        </x-slot>

        <div class="space-y-6">
            @foreach($this->getAllocationStatus() as $status)
                @php
                    $superCategory = $status['super_category'];
                    $allowance = $status['allowance'];
                    $spent = $status['spent'];
                    $remaining = $status['remaining'];
                    $spentPercentage = $status['spent_percentage'];
                    $isOverBudget = $status['is_over_budget'];
                @endphp
                
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $superCategory->getTranslatedName() }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ __('common.allocation_percentage') }}: {{ number_format($status['allocation_percentage'], 2) }}%
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm">
                                <span class="font-medium">{{ __('common.allowance') }}:</span>
                                <span class="text-gray-700">€{{ number_format($allowance, 2) }}</span>
                            </p>
                            <p class="text-sm">
                                <span class="font-medium">{{ __('common.spent') }}:</span>
                                <span class="{{ $isOverBudget ? 'text-red-600' : 'text-gray-700' }}">
                                    €{{ number_format($spent, 2) }}
                                </span>
                            </p>
                            <p class="text-sm">
                                <span class="font-medium">{{ __('common.remaining_allowance') }}:</span>
                                <span class="{{ $remaining > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    €{{ number_format($remaining, 2) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div 
                            class="h-4 rounded-full {{ $isOverBudget ? 'bg-red-500' : ($spentPercentage < 80 ? 'bg-green-500' : 'bg-yellow-500') }}"
                            style="width: {{ min(100, $spentPercentage) }}%"
                        ></div>
                    </div>
                    <p class="text-xs text-gray-500 text-center">
                        {{ number_format($spentPercentage, 1) }}% {{ __('common.spent') }}
                    </p>
                </div>
            @endforeach
            
            @if(count($this->getEncouragementMessages()) > 0)
                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-semibold text-green-800 mb-2">{{ __('common.positive_reinforcement') }}</h4>
                    <ul class="space-y-1 text-sm text-green-700">
                        @foreach($this->getEncouragementMessages() as $message)
                            <li>• {{ $message['message'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

