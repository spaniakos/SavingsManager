@extends('mobile.layout')

@section('content')

<div class="p-4">
    <div class="flex items-center mb-6">
        <a href="{{ route('mobile.expense.index') }}" class="text-2xl mr-3">←</a>
        <h1 class="text-2xl font-bold">{{ $superCategory->getTranslatedName() }}</h1>
    </div>
    
    <!-- Search Bar -->
    <div class="mb-4">
        <input 
            type="text" 
            id="searchInput" 
            placeholder="{{ __('common.search_categories') }}..." 
            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
            onkeyup="filterCategories()"
        >
    </div>
    
    <!-- Categories Grid - 2 per row on mobile, more on larger screens -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" id="categoriesGrid">
        @foreach($categories as $category)
            <a 
                href="{{ route('mobile.expense.create', $category->id) }}" 
                class="category-button bg-white rounded-xl border-2 border-gray-200 hover:border-amber-500 hover:shadow-md transition-all p-4 text-center"
                data-label="{{ strtolower($category->getTranslatedName()) }}"
            >
                <div class="text-4xl mb-2">{{ $category->emoji ?? '📁' }}</div>
                <div class="text-sm font-semibold text-gray-800">{{ $category->getTranslatedName() }}</div>
            </a>
        @endforeach
    </div>
</div>

<script>
function filterCategories() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categories = document.querySelectorAll('.category-button');
    
    categories.forEach(category => {
        const label = category.getAttribute('data-label') || '';
        if (label.includes(searchTerm) || searchTerm === '') {
            category.style.display = 'block';
        } else {
            category.style.display = 'none';
        }
    });
}
</script>
@endsection
