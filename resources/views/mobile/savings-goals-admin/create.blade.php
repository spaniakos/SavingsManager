@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-4">
    <div class="flex items-center mb-4">
        <a href="{{ route('mobile.savings-goals-admin.index') }}" class="text-2xl mr-3">←</a>
        <h1 class="text-2xl font-bold">{{ __('common.create') }} {{ __('common.savings_goal') }}</h1>
    </div>
    
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
                min="0"
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
                value="{{ old('target_date') }}"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
            >
        </div>
        
        <!-- Joint Goal -->
        <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
            <label class="flex items-center">
                <input 
                    type="checkbox" 
                    name="is_joint" 
                    value="1"
                    {{ old('is_joint') ? 'checked' : '' }}
                    onchange="toggleMembers(this)"
                    class="mr-2"
                >
                <span class="text-sm font-semibold text-gray-700">{{ __('common.joint_goal') }}</span>
            </label>
        </div>
        
        <!-- Members (only shown if joint) -->
        <div id="membersSection" class="bg-white p-4 rounded-xl border-2 border-gray-200" style="display: none;">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('common.members') }}
            </label>
            <select name="members[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg" size="5">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ in_array($user->id, old('members', [])) ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">{{ __('common.select_multiple_help') }}</p>
        </div>
        
        <button type="submit" class="w-full p-4 bg-amber-600 text-white rounded-xl font-semibold">
            {{ __('common.create') }}
        </button>
    </form>
</div>

<script>
function toggleMembers(checkbox) {
    const membersSection = document.getElementById('membersSection');
    membersSection.style.display = checkbox.checked ? 'block' : 'none';
}
</script>
@endsection

