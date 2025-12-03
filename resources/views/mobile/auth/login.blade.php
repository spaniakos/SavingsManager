@extends('mobile.layout')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-amber-50 to-orange-50">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-amber-200">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('common.app_name') }}</h1>
                <p class="text-gray-600">{{ __('common.login') }}</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('mobile.auth.login.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('common.email') }}
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        required 
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
                        placeholder="{{ __('common.email') }}"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('common.password') }}
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
                        placeholder="{{ __('common.password') }}"
                    >
                </div>

                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        {{ __('common.remember_me') }}
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-3 bg-amber-600 text-white rounded-lg font-semibold hover:bg-amber-700 transition-colors"
                >
                    {{ __('common.login') }}
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    {{ __('common.dont_have_account') }} 
                    <a href="{{ route('mobile.auth.register') }}" class="text-amber-600 font-semibold hover:text-amber-700">
                        {{ __('common.register') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

