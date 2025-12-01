@extends('mobile.layout')

@section('content')
<div class="p-4">
    <a href="{{ route('mobile.expense.categories', $category->expense_super_category_id) }}" class="text-blue-600 mb-4 inline-block">← {{ __('common.back') }}</a>
    <h1 class="text-2xl font-bold mb-6">
        {{ $category->emoji ?? '📝' }} {{ $category->getTranslatedName() }}
    </h1>
    
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
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.notes') }}</label>
            <textarea name="notes" rows="3"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                      placeholder="{{ __('common.notes_placeholder') }}"></textarea>
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" name="is_save_for_later" id="is_save_for_later" value="1"
                   class="w-5 h-5 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
            <label for="is_save_for_later" class="ml-2 text-sm text-gray-700">
                {{ __('common.save_for_later') }}
            </label>
        </div>
        
        <button type="submit" class="w-full bg-amber-500 text-white py-3 rounded-lg font-semibold text-lg">
            {{ __('common.save') }}
        </button>
    </form>
</div>
@endsection

