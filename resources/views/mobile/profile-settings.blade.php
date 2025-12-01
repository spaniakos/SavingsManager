@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-6">
    <h1 class="text-2xl font-bold text-center mb-6">{{ __('common.profile_settings') }}</h1>
    
    <form method="POST" action="{{ route('mobile.profile-settings.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        
        <!-- Seed Capital -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.seed_capital') }}
            </label>
            <input 
                type="number" 
                name="seed_capital" 
                value="{{ old('seed_capital', $user->seed_capital) }}"
                step="0.01"
                min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                required
            >
            @error('seed_capital')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Median Monthly Income -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.median_monthly_income') }}
            </label>
            <input 
                type="number" 
                name="median_monthly_income" 
                value="{{ old('median_monthly_income', $user->median_monthly_income) }}"
                step="0.01"
                min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
            >
            @error('median_monthly_income')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Income Last Verified -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.income_last_verified_at') }}
            </label>
            <input 
                type="date" 
                name="income_last_verified_at" 
                value="{{ old('income_last_verified_at', $user->income_last_verified_at?->format('Y-m-d')) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
            >
            @error('income_last_verified_at')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <button 
            type="submit" 
            class="w-full p-4 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700 transition"
        >
            {{ __('common.save') }}
        </button>
    </form>
</div>
@endsection

