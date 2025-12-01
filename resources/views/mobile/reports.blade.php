@extends('mobile.layout')

@section('content')
<div class="p-4">
    <h1 class="text-2xl font-bold text-center mb-6">{{ __('common.reports') }}</h1>
    
    <div class="bg-white p-8 rounded-xl border-2 border-gray-200 text-center">
        <p class="text-gray-600">{{ __('common.coming_soon') }}</p>
        <a href="/admin/reports" class="mt-4 inline-block px-4 py-2 bg-amber-600 text-white rounded-lg">
            {{ __('common.view_reports') }}
        </a>
    </div>
</div>
@endsection

