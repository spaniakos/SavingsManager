@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-4">
    <div class="flex items-center mb-4">
        <a href="{{ route('mobile.savings-goals-admin.index') }}" class="text-2xl mr-3">←</a>
        <h1 class="text-2xl font-bold">{{ __('common.create') }} {{ __('common.savings_goal') }}</h1>
    </div>
    
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <form method="POST" action="{{ route('mobile.savings-goals-admin.store') }}" class="space-y-4">
        @csrf
        
        <!-- Name -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.goal_name') }} <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="name" 
                value="{{ old('name') }}"
                required
                maxlength="255"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
        </div>
        
        <!-- Target Amount -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.target_amount') }} <span class="text-red-500">*</span>
            </label>
            <input 
                type="number" 
                name="target_amount" 
                value="{{ old('target_amount') }}"
                step="0.01"
                min="0.01"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
        </div>
        
        <!-- Current Amount -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.current_amount') }}
            </label>
            <input 
                type="number" 
                name="current_amount" 
                value="{{ old('current_amount', 0) }}"
                step="0.01"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
        </div>
        
        <!-- Initial Checkpoint -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.initial_checkpoint') }}
            </label>
            <input 
                type="number" 
                name="initial_checkpoint" 
                value="{{ old('initial_checkpoint', 0) }}"
                step="0.01"
                min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
        </div>
        
        <!-- Start Date -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.start_date') }} <span class="text-red-500">*</span>
            </label>
            <input 
                type="date" 
                name="start_date" 
                value="{{ old('start_date', now()->format('Y-m-d')) }}"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
        </div>
        
        <!-- Target Date -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.target_date') }} <span class="text-red-500">*</span>
            </label>
            <input 
                type="date" 
                name="target_date" 
                value="{{ old('target_date', now()->addMonth()->format('Y-m-d')) }}"
                required
                min="{{ old('start_date', now()->format('Y-m-d')) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
        </div>
        
        <button type="submit" class="w-full p-4 bg-amber-600 text-white rounded-xl font-semibold">
            {{ __('common.create') }}
        </button>
    </form>
</div>

<script>
    // Update target_date min when start_date changes
    document.querySelector('input[name="start_date"]')?.addEventListener('change', function(e) {
        const targetDateInput = document.querySelector('input[name="target_date"]');
        if (targetDateInput && e.target.value) {
            targetDateInput.min = e.target.value;
            if (targetDateInput.value && targetDateInput.value < e.target.value) {
                targetDateInput.value = e.target.value;
            }
        }
    });
</script>
@endsection

