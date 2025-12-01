<x-filament-panels::page>
    @include('mobile.components.responsive-nav')
    
    <form wire:submit="save" class="mt-6">
        {{ $this->form }}
        
        <div class="flex justify-end gap-4 mt-6">
            <x-filament::button type="submit" color="primary">
                {{ __('common.save') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
