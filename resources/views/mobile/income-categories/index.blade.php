@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ __('common.income_categories') }}</h1>
        <a href="{{ route('mobile.income-categories.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">
            {{ __('common.create') }}
        </a>
    </div>
    
    <!-- Search -->
    <div class="mb-4">
        <input 
            type="text" 
            id="searchInput" 
            placeholder="{{ __('common.search') }}..." 
            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl"
            onkeyup="filterCategories()"
        >
    </div>
    
    <div class="space-y-2">
        @foreach($categories as $category)
            <div class="bg-white p-4 rounded-xl border-2 border-gray-200 category-item" data-label="{{ strtolower($category->getTranslatedName()) }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <span class="text-2xl mr-3">{{ $category->emoji ?? '💰' }}</span>
                        <div class="font-semibold">{{ $category->getTranslatedName() }}</div>
                        @if($category->is_system)
                            <span class="ml-2 text-xs text-gray-500">{{ __('common.system') }}</span>
                        @endif
                    </div>
                    @if(!$category->is_system)
                        <div class="flex gap-2">
                            <a href="{{ route('mobile.income-categories.edit', $category->id) }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm">
                                {{ __('common.edit') }}
                            </a>
                            <form method="POST" action="{{ route('mobile.income-categories.destroy', $category->id) }}" onsubmit="return confirm('{{ __('common.confirm_delete') }}')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm">
                                    {{ __('common.delete') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
function filterCategories() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const items = document.querySelectorAll('.category-item');
    
    items.forEach(item => {
        const label = item.getAttribute('data-label') || '';
        if (label.includes(searchTerm) || searchTerm === '') {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endsection

