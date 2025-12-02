@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-4">
    <div class="flex items-center mb-4">
        <a href="{{ route('mobile.expense-entries.index') }}" class="text-2xl mr-3">←</a>
        <h1 class="text-2xl font-bold">{{ __('common.edit_expense') }}</h1>
    </div>
    
    <form method="POST" action="{{ route('mobile.expense-entries.update', $entry->id) }}" class="space-y-4">
        @csrf
        @method('PUT')
        
        <!-- Category -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.category') }}
            </label>
            <select name="expense_category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $entry->expense_category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->expenseSuperCategory->getTranslatedName() }} → {{ $category->getTranslatedName() }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Amount -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.amount') }}
            </label>
            <input 
                type="number" 
                name="amount" 
                value="{{ old('amount', $entry->amount) }}"
                step="0.01"
                min="0.01"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
        </div>
        
        <!-- Date -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.date') }}
            </label>
            @php
                $entryMonth = \Carbon\Carbon::parse($entry->date)->startOfMonth();
                $previousMonth = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                $isPastMonth = $entryMonth->lt($previousMonth); // More than 1 month ago
            @endphp
            @if($isPastMonth)
                <input 
                    type="date" 
                    name="date" 
                    value="{{ old('date', $entry->date->format('Y-m-d')) }}"
                    required
                    disabled
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                >
                <p class="text-xs text-red-600 mt-1">{{ __('common.cannot_edit_past_month_entry') }}</p>
            @else
                <input 
                    type="date" 
                    name="date" 
                    value="{{ old('date', $entry->date->format('Y-m-d')) }}"
                    required
                    min="{{ $minDate->format('Y-m-d') }}"
                    max="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
                >
                @if($previousMonthCalculated)
                    <p class="text-xs text-gray-500 mt-1">{{ __('common.date_current_month_only') }}</p>
                @else
                    <p class="text-xs text-gray-500 mt-1">{{ __('common.date_current_or_previous_month') }}</p>
                @endif
            @endif
        </div>
        
        <!-- Person -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.person') }}
            </label>
            <select name="person_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                <option value="">{{ __('common.no_person') }}</option>
                @foreach($persons as $person)
                    <option value="{{ $person->id }}" {{ old('person_id', $entry->person_id) == $person->id ? 'selected' : '' }}>
                        {{ $person->fullname }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Notes -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.notes') }}
            </label>
            <textarea 
                name="notes" 
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >{{ old('notes', $entry->notes) }}</textarea>
        </div>
        
        <!-- Save for Later Toggle -->
        <div class="bg-gradient-to-br {{ old('is_save_for_later', $entry->is_save_for_later) ? 'from-purple-100 to-indigo-100 border-purple-400' : 'from-purple-50 to-indigo-50 border-purple-200' }} p-4 rounded-xl border-2">
            <label for="is_save_for_later" class="flex items-center justify-between cursor-pointer">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <input 
                            type="checkbox" 
                            name="is_save_for_later" 
                            id="is_save_for_later"
                            value="1"
                            {{ old('is_save_for_later', $entry->is_save_for_later) ? 'checked' : '' }}
                            class="w-6 h-6 text-purple-600 border-gray-300 rounded focus:ring-purple-500 focus:ring-2"
                            onchange="toggleSaveForLater(this)"
                        >
                        @if(old('is_save_for_later', $entry->is_save_for_later))
                            <div id="save-for-later-icon" class="text-2xl mt-1">💰</div>
                        @else
                            <div id="save-for-later-icon" class="hidden text-2xl mt-1">💰</div>
                        @endif
                    </div>
                    <div>
                        <div class="text-sm font-bold text-purple-900">{{ __('common.save_for_later') }}</div>
                        <div class="text-xs text-purple-700 mt-1">{{ __('common.save_for_later_expense_help') }}</div>
                    </div>
                </div>
                @if(old('is_save_for_later', $entry->is_save_for_later))
                    <div id="save-for-later-badge" class="px-3 py-1 bg-purple-600 text-white text-xs font-semibold rounded-full">
                        {{ __('common.saved') }}
                    </div>
                @else
                    <div id="save-for-later-badge" class="hidden px-3 py-1 bg-purple-600 text-white text-xs font-semibold rounded-full">
                        {{ __('common.saved') }}
                    </div>
                @endif
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
        </script>
        
        <!-- Personal Expense Toggle -->
        <div class="bg-gradient-to-br {{ old('is_personal', $entry->is_personal) ? 'from-blue-100 to-cyan-100 border-blue-400' : 'from-blue-50 to-cyan-50 border-blue-200' }} p-4 rounded-xl border-2">
            <label for="is_personal" class="flex items-center justify-between cursor-pointer">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <input 
                            type="checkbox" 
                            name="is_personal" 
                            id="is_personal"
                            value="1"
                            {{ old('is_personal', $entry->is_personal) ? 'checked' : '' }}
                            class="w-6 h-6 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                            onchange="togglePersonal(this)"
                        >
                        @if(old('is_personal', $entry->is_personal))
                            <div id="personal-icon" class="text-2xl mt-1">👤</div>
                        @else
                            <div id="personal-icon" class="hidden text-2xl mt-1">👤</div>
                        @endif
                    </div>
                    <div>
                        <div class="text-sm font-bold text-blue-900">{{ __('common.personal_expense') }}</div>
                        <div class="text-xs text-blue-700 mt-1">{{ __('common.personal_expense_help') }}</div>
                    </div>
                </div>
                @if(old('is_personal', $entry->is_personal))
                    <div id="personal-badge" class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">
                        {{ __('common.personal') }}
                    </div>
                @else
                    <div id="personal-badge" class="hidden px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">
                        {{ __('common.personal') }}
                    </div>
                @endif
            </label>
        </div>
        
        <script>
            function togglePersonal(checkbox) {
                const icon = document.getElementById('personal-icon');
                const badge = document.getElementById('personal-badge');
                const container = checkbox.closest('.bg-gradient-to-br');
                
                if (checkbox.checked) {
                    icon.classList.remove('hidden');
                    badge.classList.remove('hidden');
                    container.classList.remove('from-blue-50', 'to-cyan-50', 'border-blue-200');
                    container.classList.add('from-blue-100', 'to-cyan-100', 'border-blue-400');
                } else {
                    icon.classList.add('hidden');
                    badge.classList.add('hidden');
                    container.classList.remove('from-blue-100', 'to-cyan-100', 'border-blue-400');
                    container.classList.add('from-blue-50', 'to-cyan-50', 'border-blue-200');
                }
            }
        </script>
        
        <button type="submit" class="w-full p-4 bg-amber-600 text-white rounded-xl font-semibold">
            {{ __('common.save') }}
        </button>
    </form>
</div>
@endsection

