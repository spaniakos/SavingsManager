<x-filament-panels::page>
    @include('mobile.components.responsive-nav')
    
    <div class="space-y-6 mt-6">
        <form wire:submit.prevent="exportToCsv">
            {{ $this->form }}

            <div class="mt-4 flex gap-2">
                <x-filament::button type="submit" name="action" value="csv">
                    {{ __('common.export_csv') }}
                </x-filament::button>
                <x-filament::button type="submit" name="action" value="json" color="gray">
                    {{ __('common.export_json') }}
                </x-filament::button>
            </div>
        </form>

        <x-filament::section>
            <x-slot name="heading">
                {{ __('common.export_instructions') }}
            </x-slot>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('common.export_instructions_text') }}
            </p>
        </x-filament::section>
    </div>
</x-filament-panels::page>

