@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-4">
    <div class="flex items-center mb-4">
        <a href="{{ route('mobile.income-categories.index') }}" class="text-2xl mr-3">←</a>
        <h1 class="text-2xl font-bold">{{ __('common.create') }} {{ __('common.income_category') }}</h1>
    </div>
    
    <form method="POST" action="{{ route('mobile.income-categories.store') }}" class="space-y-4">
        @csrf
        
        <!-- Name -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.name') }} <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="name" 
                value="{{ old('name') }}"
                required
                maxlength="255"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
                placeholder="my_category"
            >
            <p class="text-xs text-gray-500 mt-1">{{ __('categories.translation_key_help') }}</p>
        </div>
        
        <!-- Emoji -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.emoji') }}
            </label>
            <input 
                type="text" 
                name="emoji" 
                value="{{ old('emoji') }}"
                maxlength="10"
                placeholder="💰"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
        </div>
        
        <button type="submit" class="w-full p-4 bg-amber-600 text-white rounded-xl font-semibold">
            {{ __('common.create') }}
        </button>
    </form>
</div>
@endsection

