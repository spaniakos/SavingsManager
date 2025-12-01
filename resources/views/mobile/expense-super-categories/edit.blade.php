@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-4">
    <div class="flex items-center mb-4">
        <a href="{{ route('mobile.expense-super-categories.index') }}" class="text-2xl mr-3">←</a>
        <h1 class="text-2xl font-bold">{{ __('common.edit') }} {{ $superCategory->getTranslatedName() }}</h1>
    </div>
    
    <form method="POST" action="{{ route('mobile.expense-super-categories.update', $superCategory->id) }}" class="space-y-4">
        @csrf
        @method('PUT')
        
        <!-- Emoji -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.emoji') }}
            </label>
            <input 
                type="text" 
                name="emoji" 
                value="{{ old('emoji', $superCategory->emoji) }}"
                maxlength="10"
                placeholder="🏠"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
        </div>
        
        <!-- Allocation Percentage -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.allocation_percentage') }}
            </label>
            <input 
                type="number" 
                name="allocation_percentage" 
                value="{{ old('allocation_percentage', $superCategory->allocation_percentage) }}"
                step="0.01"
                min="0"
                max="100"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
            <p class="text-xs text-gray-500 mt-1">{{ __('common.allocation_percentage_help') }}</p>
        </div>
        
        <button type="submit" class="w-full p-4 bg-amber-600 text-white rounded-xl font-semibold">
            {{ __('common.save') }}
        </button>
    </form>
</div>
@endsection

