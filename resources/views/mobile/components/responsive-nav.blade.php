@php
    // Get all navigation items
    $navItems = [
        ['label' => __('common.dashboard'), 'url' => '/admin', 'emoji' => '📊'],
        ['label' => __('common.expense_entries'), 'url' => '/admin/expense-entries', 'emoji' => '💸'],
        ['label' => __('common.income_entries'), 'url' => '/admin/income-entries', 'emoji' => '💰'],
        ['label' => __('common.savings_goals'), 'url' => '/admin/savings-goals', 'emoji' => '🎯'],
        ['label' => __('common.reports'), 'url' => '/admin/reports', 'emoji' => '📈'],
        ['label' => __('common.expense_categories'), 'url' => '/admin/expense-categories', 'emoji' => '📁'],
        ['label' => __('common.income_categories'), 'url' => '/admin/income-categories', 'emoji' => '💼'],
        ['label' => __('common.expense_super_categories'), 'url' => '/admin/expense-super-categories', 'emoji' => '📂'],
        ['label' => __('common.settings'), 'url' => '/admin/user-profile-settings', 'emoji' => '⚙️'],
        ['label' => __('common.data_export'), 'url' => '/admin/data-export', 'emoji' => '📤'],
    ];
    
    $currentPath = request()->path();
@endphp

<div class="responsive-nav-container" id="responsiveNav">
    <!-- Search Bar -->
    <div class="mb-4 p-4 bg-white rounded-lg shadow-sm">
        <input 
            type="text" 
            id="navSearch" 
            placeholder="{{ __('common.search_navigation') }}..." 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
            onkeyup="filterNavigation()"
        >
    </div>
    
    <!-- Navigation Grid - Responsive: 2 cols mobile, 3 tablet, 4 desktop, 5 xl -->
    <div class="nav-grid grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3" id="navGrid">
        @foreach($navItems as $item)
            <a 
                href="{{ $item['url'] }}" 
                class="nav-item block p-4 bg-white rounded-lg border-2 border-gray-200 hover:border-amber-500 hover:shadow-md transition-all text-center {{ str_starts_with($currentPath, str_replace('/admin', '', $item['url'])) || ($currentPath === 'admin' && $item['url'] === '/admin') ? 'border-amber-500 bg-amber-50' : '' }}"
                data-label="{{ strtolower($item['label']) }}"
            >
                <div class="text-3xl mb-2">{{ $item['emoji'] }}</div>
                <div class="text-sm font-medium text-gray-700">{{ $item['label'] }}</div>
            </a>
        @endforeach
    </div>
    
    <!-- Quick Actions -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-3">{{ __('common.quick_actions') }}</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            <a href="/admin/expense-entries/create" class="nav-item block p-4 bg-green-50 rounded-lg border-2 border-green-200 hover:border-green-500 hover:shadow-md transition-all text-center">
                <div class="text-3xl mb-2">➕</div>
                <div class="text-sm font-medium text-gray-700">{{ __('common.add_expense') }}</div>
            </a>
            <a href="/admin/income-entries/create" class="nav-item block p-4 bg-blue-50 rounded-lg border-2 border-blue-200 hover:border-blue-500 hover:shadow-md transition-all text-center">
                <div class="text-3xl mb-2">➕</div>
                <div class="text-sm font-medium text-gray-700">{{ __('common.add_income') }}</div>
            </a>
            <a href="/admin/savings-goals/create" class="nav-item block p-4 bg-purple-50 rounded-lg border-2 border-purple-200 hover:border-purple-500 hover:shadow-md transition-all text-center">
                <div class="text-3xl mb-2">🎯</div>
                <div class="text-sm font-medium text-gray-700">{{ __('common.create_goal') }}</div>
            </a>
        </div>
    </div>
</div>

<script>
function filterNavigation() {
    const searchTerm = document.getElementById('navSearch').value.toLowerCase();
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        const label = item.getAttribute('data-label') || '';
        if (label.includes(searchTerm) || searchTerm === '') {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<style>
.responsive-nav-container {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
}

.nav-item {
    min-height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-decoration: none;
}

/* Responsive grid - 2 columns on mobile */
@media (max-width: 640px) {
    .nav-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

/* 3 columns on tablet */
@media (min-width: 768px) {
    .nav-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

/* 4 columns on desktop */
@media (min-width: 1024px) {
    .nav-grid {
        grid-template-columns: repeat(4, 1fr) !important;
    }
}

/* 5 columns on xl screens */
@media (min-width: 1280px) {
    .nav-grid {
        grid-template-columns: repeat(5, 1fr) !important;
    }
}
</style>
