@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-4">
    <h1 class="text-2xl font-bold text-center mb-6">{{ __('common.expense_super_categories') }}</h1>
    
    <div class="space-y-3">
        @foreach($superCategories as $superCategory)
            <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <span class="text-3xl mr-3">{{ $superCategory->emoji ?? '📂' }}</span>
                        <div class="flex-1">
                            <div class="font-semibold">{{ $superCategory->getTranslatedName() }}</div>
                            <div class="text-sm text-gray-600">
                                {{ __('common.allocation_percentage') }}: {{ number_format($superCategory->allocation_percentage ?? 0, 2) }}%
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('mobile.expense-super-categories.edit', $superCategory->id) }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm">
                        {{ __('common.edit') }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

