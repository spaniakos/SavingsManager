@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-6">
    <h1 class="text-2xl font-bold text-center mb-6">{{ __('common.settings') }}</h1>
    
    <!-- Theme Toggle -->
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <h2 class="text-lg font-semibold mb-4">{{ __('common.theme') }}</h2>
        <div class="flex items-center justify-between">
            <span class="text-gray-700">{{ __('common.dark_mode') }}</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="themeToggle" class="sr-only peer" onchange="toggleTheme()">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
            </label>
        </div>
    </div>
    
    <!-- Language -->
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <h2 class="text-lg font-semibold mb-4">{{ __('common.language') }}</h2>
        <form method="POST" action="{{ route('language.switch') }}">
            @csrf
            <select name="locale" onchange="this.form.submit()" class="w-full p-2 border border-gray-300 rounded-lg">
                <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                <option value="el" {{ app()->getLocale() === 'el' ? 'selected' : '' }}>Ελληνικά</option>
            </select>
        </form>
    </div>
    
    <!-- Person Management -->
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <h2 class="text-lg font-semibold mb-4">{{ __('common.person_management') }}</h2>
        <div class="space-y-2">
            <a href="{{ route('mobile.persons.index') }}" class="block p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">👥</span>
                        <span class="font-medium">{{ __('common.persons') }}</span>
                    </div>
                    <span class="text-gray-500">→</span>
                </div>
            </a>
            <a href="{{ route('mobile.persons.create') }}" class="block p-3 bg-amber-50 rounded-lg border border-amber-200 hover:bg-amber-100 transition">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">➕</span>
                    <span class="font-medium">{{ __('common.create_person') }}</span>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
        <h2 class="text-lg font-semibold mb-4">{{ __('common.quick_links') }}</h2>
        <div class="space-y-2">
            <a href="{{ route('mobile.profile-settings') }}" class="block p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">👤</span>
                    <span class="font-medium">{{ __('common.profile_settings') }}</span>
                </div>
            </a>
            <a href="{{ route('mobile.expense-entries.index') }}" class="block p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">💸</span>
                    <span class="font-medium">{{ __('common.expense_entries') }}</span>
                </div>
            </a>
            <a href="{{ route('mobile.income-entries.index') }}" class="block p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">💰</span>
                    <span class="font-medium">{{ __('common.income_entries') }}</span>
                </div>
            </a>
            <a href="{{ route('mobile.expense-super-categories.index') }}" class="block p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">📂</span>
                    <span class="font-medium">{{ __('common.expense_super_categories') }}</span>
                </div>
            </a>
            <a href="{{ route('mobile.expense-categories.index') }}" class="block p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">📁</span>
                    <span class="font-medium">{{ __('common.expense_categories') }}</span>
                </div>
            </a>
            <a href="{{ route('mobile.income-categories.index') }}" class="block p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">💼</span>
                    <span class="font-medium">{{ __('common.income_categories') }}</span>
                </div>
            </a>
            <a href="{{ route('mobile.savings-goals-admin.index') }}" class="block p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🎯</span>
                    <span class="font-medium">{{ __('common.savings_goals') }}</span>
                </div>
            </a>
            <a href="{{ route('mobile.reports.index') }}" class="block p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">📈</span>
                    <span class="font-medium">{{ __('common.reports') }}</span>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Sign Out -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full p-4 bg-red-50 text-red-700 rounded-xl border-2 border-red-200 font-semibold hover:bg-red-100 transition">
            <div class="flex items-center justify-center">
                <span class="text-2xl mr-2">🚪</span>
                <span>{{ __('common.sign_out') }}</span>
            </div>
        </button>
    </form>
</div>

<script>
function toggleTheme() {
    const checkbox = document.getElementById('themeToggle');
    const isDark = checkbox.checked;
    const body = document.body;
    
    if (isDark) {
        body.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        body.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
}

// Load saved theme on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    const checkbox = document.getElementById('themeToggle');
    const body = document.body;
    
    if (savedTheme === 'dark') {
        checkbox.checked = true;
        body.classList.add('dark');
    } else {
        checkbox.checked = false;
        body.classList.remove('dark');
    }
});
</script>
@endsection
