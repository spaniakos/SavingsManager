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
            <input 
                type="date" 
                name="date" 
                value="{{ old('date', $entry->date->format('Y-m-d')) }}"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
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
        
        <!-- Save for Later -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="flex items-center">
                <input 
                    type="checkbox" 
                    name="is_save_for_later" 
                    value="1"
                    {{ old('is_save_for_later', $entry->is_save_for_later) ? 'checked' : '' }}
                    class="mr-2"
                >
                <span class="text-sm font-semibold text-gray-700">{{ __('common.save_for_later') }}</span>
            </label>
        </div>
        
        <button type="submit" class="w-full p-4 bg-amber-600 text-white rounded-xl font-semibold">
            {{ __('common.save') }}
        </button>
    </form>
</div>
@endsection

