<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('common.save_for_later') }}
        </x-slot>

        @php
            $categories = $this->getSaveForLaterCategories();
        @endphp

        @if($categories->isEmpty())
            <p class="text-gray-500 text-center py-4">{{ __('common.no_save_for_later_categories') }}</p>
        @else
            <div class="space-y-4">
                @foreach($categories as $item)
                    @php
                        $category = $item['category'];
                        $target = $item['target'];
                        $progress = $item['progress'];
                        $remaining = $item['remaining'];
                        $frequency = $item['frequency'];
                        $amount = $item['amount'];
                    @endphp
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold">{{ $category->getTranslatedName() }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ __('common.save_for_later_target') }}: €{{ number_format($target, 2) }}
                                    @if($frequency && $amount)
                                        • {{ __('common.save_for_later_amount') }}: €{{ number_format($amount, 2) }} / {{ __("common.{$frequency}") }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm">
                                    <span class="font-medium">{{ __('common.save_progress') }}:</span>
                                    <span class="text-gray-700">{{ number_format($progress, 1) }}%</span>
                                </p>
                                <p class="text-sm">
                                    <span class="font-medium">{{ __('common.remaining_to_save') }}:</span>
                                    <span class="text-gray-700">€{{ number_format($remaining, 2) }}</span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div 
                                class="h-4 rounded-full bg-blue-500"
                                style="width: {{ min(100, $progress) }}%"
                            ></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

