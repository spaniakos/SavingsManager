@extends('mobile.layout')

@section('content')
<div class="p-4">
    <a href="{{ route('mobile.income.index') }}" class="text-blue-600 mb-4 inline-block">← {{ __('common.back') }}</a>
    <h1 class="text-2xl font-bold mb-6">
        {{ $category->emoji ?? '💰' }} {{ $category->getTranslatedName() }}
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
    
    <form method="POST" action="{{ route('mobile.income.store', $category->id) }}" class="space-y-4">
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
        
        <button type="submit" class="w-full bg-amber-500 text-white py-3 rounded-lg font-semibold text-lg">
            {{ __('common.save') }}
        </button>
    </form>
</div>
@endsection

