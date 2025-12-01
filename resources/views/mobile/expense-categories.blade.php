@extends('mobile.layout')

@section('content')
<div class="p-4">
    <h1 class="text-2xl font-bold text-center mb-6">{{ __('common.expense_categories') }}</h1>
    
    <div class="space-y-3">
        @foreach($categories as $category)
            <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
                <div class="flex items-center">
                    <span class="text-3xl mr-3">{{ $category->emoji ?? '📁' }}</span>
                    <div class="flex-1">
                        <div class="font-semibold">{{ $category->getTranslatedName() }}</div>
                        <div class="text-sm text-gray-600">{{ $category->expenseSuperCategory->getTranslatedName() }}</div>
                    </div>
                    @if($category->is_system)
                        <span class="text-xs text-gray-500">{{ __('common.system') }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

