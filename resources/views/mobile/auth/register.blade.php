@extends('mobile.layout')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-amber-50 to-orange-50">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-amber-200">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('common.app_name') }}</h1>
                <p class="text-gray-600">{{ __('common.register') }}</p>
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

            <form method="POST" action="{{ route('mobile.auth.register.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('common.name') }}
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        required 
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
                        placeholder="{{ __('common.name') }}"
                    >
                </div>

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

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('common.confirm_password') }}
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
                        placeholder="{{ __('common.confirm_password') }}"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full py-3 bg-amber-600 text-white rounded-lg font-semibold hover:bg-amber-700 transition-colors"
                >
                    {{ __('common.register') }}
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    {{ __('common.already_have_account') }} 
                    <a href="{{ route('mobile.auth.login') }}" class="text-amber-600 font-semibold hover:text-amber-700">
                        {{ __('common.login') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

