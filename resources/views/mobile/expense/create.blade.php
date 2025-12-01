@extends('mobile.layout')

@section('content')
<div class="p-4">
    <a href="{{ route('mobile.expense.categories', $category->expense_super_category_id) }}" class="text-blue-600 mb-4 inline-block">← {{ __('common.back') }}</a>
    <h1 class="text-2xl font-bold mb-6">
        {{ $category->emoji ?? '📝' }} {{ $category->getTranslatedName() }}
    </h1>
    
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ route('mobile.expense.store', $category->id) }}" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.amount') }}</label>
            <input type="number" name="amount" step="0.01" min="0.01" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                   placeholder="0.00">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.date') }}</label>
            <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                   min="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}"
                   max="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            <p class="text-xs text-gray-500 mt-1">{{ __('common.date_current_month_only') }}</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.notes') }}</label>
            <textarea name="notes" rows="3"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                      placeholder="{{ __('common.notes_placeholder') }}"></textarea>
        </div>
        
        <!-- Save for Later Toggle -->
        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 p-4 rounded-xl border-2 border-purple-200">
            <label for="is_save_for_later" class="flex items-center justify-between cursor-pointer">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <input type="checkbox" name="is_save_for_later" id="is_save_for_later" value="1"
                               class="w-6 h-6 text-purple-600 border-gray-300 rounded focus:ring-purple-500 focus:ring-2"
                               onchange="toggleSaveForLater(this)">
                        <div id="save-for-later-icon" class="hidden text-2xl mt-1">💰</div>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-purple-900">{{ __('common.save_for_later') }}</div>
                        <div class="text-xs text-purple-700 mt-1">{{ __('common.save_for_later_expense_help') }}</div>
                    </div>
                </div>
                <div id="save-for-later-badge" class="hidden px-3 py-1 bg-purple-600 text-white text-xs font-semibold rounded-full">
                    {{ __('common.saved') }}
                </div>
            </label>
        </div>
        
        <script>
            function toggleSaveForLater(checkbox) {
                const icon = document.getElementById('save-for-later-icon');
                const badge = document.getElementById('save-for-later-badge');
                const container = checkbox.closest('.bg-gradient-to-br');
                
                if (checkbox.checked) {
                    icon.classList.remove('hidden');
                    badge.classList.remove('hidden');
                    container.classList.remove('from-purple-50', 'to-indigo-50', 'border-purple-200');
                    container.classList.add('from-purple-100', 'to-indigo-100', 'border-purple-400');
                } else {
                    icon.classList.add('hidden');
                    badge.classList.add('hidden');
                    container.classList.remove('from-purple-100', 'to-indigo-100', 'border-purple-400');
                    container.classList.add('from-purple-50', 'to-indigo-50', 'border-purple-200');
                }
            }
            
            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                const checkbox = document.getElementById('is_save_for_later');
                if (checkbox) {
                    toggleSaveForLater(checkbox);
                }
            });
        </script>
        
        <button type="submit" class="w-full bg-amber-500 text-white py-3 rounded-lg font-semibold text-lg">
            {{ __('common.save') }}
        </button>
    </form>
</div>
@endsection

