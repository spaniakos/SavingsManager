@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-4">
    <div class="flex items-center mb-4">
        <a href="{{ route('mobile.income-entries.index') }}" class="text-2xl mr-3">←</a>
        <h1 class="text-2xl font-bold">{{ __('common.edit_income') }}</h1>
    </div>
    
    <form method="POST" action="{{ route('mobile.income-entries.update', $entry->id) }}" class="space-y-4">
        @csrf
        @method('PUT')
        
        <!-- Category -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.category') }}
            </label>
            <select name="income_category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $entry->income_category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->getTranslatedName() }}
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
        
        <button type="submit" class="w-full p-4 bg-amber-600 text-white rounded-xl font-semibold">
            {{ __('common.save') }}
        </button>
    </form>
</div>
@endsection

