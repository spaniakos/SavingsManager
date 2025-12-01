@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-4">
    <h1 class="text-2xl font-bold text-center mb-4">{{ __('common.income_entries') }}</h1>
    
    <!-- Search and Filters -->
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200 space-y-3">
        <form method="GET" action="{{ route('mobile.income-entries.index') }}" id="filterForm">
            <!-- Search -->
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="{{ __('common.search') }}..." 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-3"
            >
            
            <!-- Category Filter -->
            <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-3" onchange="document.getElementById('filterForm').submit()">
                <option value="">{{ __('common.all_categories') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->getTranslatedName() }}
                    </option>
                @endforeach
            </select>
            
            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-2 mb-3">
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="{{ __('common.from') }}" class="px-4 py-2 border border-gray-300 rounded-lg">
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="{{ __('common.to') }}" class="px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <button type="submit" class="w-full p-2 bg-amber-600 text-white rounded-lg">{{ __('common.filter') }}</button>
        </form>
    </div>
    
    <!-- Entries List -->
    <div class="space-y-3">
        @forelse($entries as $entry)
            <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <div class="font-semibold text-lg text-green-600">€{{ number_format($entry->amount, 2) }}</div>
                        <div class="text-sm text-gray-600">{{ $entry->incomeCategory->getTranslatedName() }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->date->format('d/m/Y') }}</div>
                        @if($entry->notes)
                            <div class="text-sm text-gray-600 mt-1">{{ $entry->notes }}</div>
                        @endif
                    </div>
                    @php
                        $entryMonth = \Carbon\Carbon::parse($entry->date)->startOfMonth();
                        $currentMonth = \Carbon\Carbon::now()->startOfMonth();
                        $isPastMonth = $entryMonth->lt($currentMonth);
                    @endphp
                    @if(!$isPastMonth)
                        <div class="flex gap-2">
                            <a href="{{ route('mobile.income-entries.edit', $entry->id) }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm">{{ __('common.edit') }}</a>
                            <form method="POST" action="{{ route('mobile.income-entries.destroy', $entry->id) }}" onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm">{{ __('common.delete') }}</button>
                            </form>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">{{ __('common.past_month_locked') }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-xl border-2 border-gray-200 text-center">
                <p class="text-gray-600">{{ __('common.no_entries_found') }}</p>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($entries->hasPages())
        <div class="mt-4">
            {{ $entries->links() }}
        </div>
    @endif
</div>
@endsection

