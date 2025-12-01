<x-filament-widgets::widget>
    <div class="flex items-center justify-end gap-2">
        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.language') }}:</span>
        <div class="flex gap-1">
            <form method="POST" action="{{ route('filament.admin.pages.language-switch') }}" class="inline">
                @csrf
                <input type="hidden" name="locale" value="en">
                <button type="submit" class="px-3 py-1 text-sm rounded {{ app()->getLocale() === 'en' ? 'bg-primary-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    English
                </button>
            </form>
            <form method="POST" action="{{ route('filament.admin.pages.language-switch') }}" class="inline">
                @csrf
                <input type="hidden" name="locale" value="el">
                <button type="submit" class="px-3 py-1 text-sm rounded {{ app()->getLocale() === 'el' ? 'bg-primary-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    Ελληνικά
                </button>
            </form>
        </div>
    </div>
</x-filament-widgets::widget>

