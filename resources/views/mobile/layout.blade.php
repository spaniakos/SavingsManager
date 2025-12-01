<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Savings Manager') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            padding-bottom: 80px; /* Space for bottom nav */
        }
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 12px 0;
            z-index: 50;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
        .bottom-nav-item {
            flex: 1;
            text-align: center;
            padding: 8px;
            text-decoration: none;
            color: #6b7280;
            font-size: 12px;
        }
        .bottom-nav-item.active {
            color: #f59e0b;
        }
        .bottom-nav-item svg {
            width: 24px;
            height: 24px;
            margin: 0 auto 4px;
        }
        .category-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 120px;
            padding: 16px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .category-button:hover {
            border-color: #f59e0b;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .category-button:active {
            background: #f3f4f6;
            transform: scale(0.98);
        }
        .category-emoji {
            font-size: 32px;
            margin-bottom: 8px;
        }
        
        /* Dark mode support */
        body.dark {
            background: #111827;
            color: #f9fafb;
        }
        body.dark .bg-white {
            background: #1f2937;
            color: #f9fafb;
        }
        body.dark .bg-gray-50 {
            background: #111827;
        }
        body.dark .text-gray-800,
        body.dark .text-gray-700 {
            color: #f9fafb;
        }
        body.dark .text-gray-600 {
            color: #d1d5db;
        }
        body.dark .border-gray-200 {
            border-color: #374151;
        }
        body.dark .bottom-nav {
            background: #1f2937;
            border-top-color: #374151;
        }
        body.dark .category-button {
            background: #1f2937;
            border-color: #374151;
            color: #f9fafb;
        }
        body.dark input[type="text"],
        body.dark input[type="number"],
        body.dark input[type="date"],
        body.dark input[type="email"],
        body.dark textarea,
        body.dark select {
            background: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }
        body.dark input[type="text"]:focus,
        body.dark input[type="number"]:focus,
        body.dark input[type="date"]:focus,
        body.dark input[type="email"]:focus,
        body.dark textarea:focus,
        body.dark select:focus {
            border-color: #f59e0b;
            background: #374151;
        }
        body.dark input::placeholder,
        body.dark textarea::placeholder {
            color: #9ca3af;
        }
        body.dark .border-gray-300 {
            border-color: #4b5563;
        }
        
        /* Chart containers - responsive height */
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }
        
        @media (max-width: 640px) {
            .chart-container {
                height: 220px;
            }
        }
        
        @media (min-width: 768px) {
            .chart-container {
                height: 280px;
            }
        }
        
        @media (min-width: 1024px) {
            .chart-container {
                height: 300px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen pb-20">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative m-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        
        @yield('content')
    </div>
    
    @include('mobile.components.bottom-nav')
    
    <script>
    // Initialize dark mode on all pages
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme');
        const body = document.body;
        
        if (savedTheme === 'dark') {
            body.classList.add('dark');
        } else {
            body.classList.remove('dark');
        }
    });
    </script>
</body>
</html>

