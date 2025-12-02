<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('common.app_name') }} - Personal Savings Management</title>
    <meta name="description" content="A comprehensive savings management application built with Laravel and Filament. Track income, expenses, and savings goals with bilingual support (English/Greek).">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .page {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .menu {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .menu a {
            flex: 1;
            min-width: 120px;
            padding: 0.875rem 1.25rem;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 600;
            text-align: center;
            border: 1px solid rgba(102, 126, 234, 0.3);
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        
        .menu a:hover {
            background: rgba(102, 126, 234, 0.15);
            border-color: rgba(102, 126, 234, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
        }
        
        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .subtitle {
            font-size: 1.125rem;
            color: #6b7280;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .features {
            margin: 2rem 0;
        }
        
        .features h3 {
            color: #1f2937;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .feature-list {
            list-style: none;
        }
        
        .feature-list li {
            padding: 0.75rem 0;
            color: #4b5563;
            display: flex;
            align-items: flex-start;
            font-size: 0.95rem;
        }
        
        .feature-list li:before {
            content: "✓";
            color: #10b981;
            font-weight: bold;
            margin-right: 0.75rem;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .cta-button {
            display: block;
            width: 100%;
            margin-top: 2rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 600;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .tech-stack {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.875rem;
            text-align: center;
        }
        
        .tech-stack strong {
            color: #1f2937;
        }
        
        /* Mobile optimizations */
        @media (max-width: 480px) {
            body {
                padding: 0.5rem;
            }
            
            .page {
                padding: 1.5rem;
                border-radius: 0.75rem;
            }
            
            .menu {
                gap: 0.5rem;
                margin-bottom: 1.5rem;
            }
            
            .menu a {
                min-width: 0;
                padding: 0.75rem 1rem;
                font-size: 0.85rem;
            }
            
            .logo {
                font-size: 2rem;
            }
            
            .subtitle {
                font-size: 1rem;
            }
            
            .features h3 {
                font-size: 1.25rem;
            }
            
            .feature-list li {
                font-size: 0.9rem;
                padding: 0.625rem 0;
            }
            
            .cta-button {
                padding: 0.875rem 1.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <nav class="menu">
            @auth
                <a href="/admin/mobile">{{ __('common.dashboard') }}</a>
            @else
                <a href="{{ url('/admin/login') }}">{{ __('common.login') }}</a>
                <a href="{{ url('/admin/register') }}">{{ __('common.register') }}</a>
            @endauth
        </nav>
        
        <div class="logo">
            💰 {{ __('common.app_name') }}
        </div>
        
        <p class="subtitle">
            {{ __('common.app_description') }}
        </p>
        
        <div class="features">
            <h3>Key Features</h3>
            <ul class="feature-list">
                <li>Track income and expenses with detailed categorization</li>
                <li>Set and monitor savings goals with progress tracking</li>
                <li>Joint savings goals with member collaboration</li>
                <li>50/30/20 budget allocation system (Essentials/Lifestyle/Savings)</li>
                <li>Recurring expense management with auto-generation</li>
                <li>Save-for-later functionality for future expenses</li>
                <li>Comprehensive financial reports (Monthly, Category, Savings)</li>
                <li>Data export in CSV and JSON formats</li>
                <li>Bilingual support (English / Ελληνικά)</li>
                <li>Positive reinforcement and milestone notifications</li>
                <li>Net worth calculation and seed capital tracking</li>
            </ul>
        </div>
        
        @auth
            <a href="/admin/mobile" class="cta-button">
                {{ __('common.dashboard') }} →
            </a>
        @else
            <a href="{{ url('/admin/login') }}" class="cta-button">
                {{ __('common.get_started') }} →
            </a>
        @endauth
        
        <div class="tech-stack">
            <p>
                <strong>Built with:</strong> Laravel 12.x • Filament 4.x • MySQL/PostgreSQL
            </p>
            <p style="margin-top: 0.5rem;">
                <strong>Status:</strong> Production Ready ✅ | 
                <strong>Tests:</strong> 44 tests passing (88 assertions)
            </p>
        </div>
    </div>
</body>
</html>
