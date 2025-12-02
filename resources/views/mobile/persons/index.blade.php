@extends('mobile.layout')

@section('content')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">{{ __('common.persons') }}</h1>
        <a href="{{ route('mobile.persons.create') }}" class="px-4 py-2 bg-amber-500 text-white rounded-lg font-semibold">
            {{ __('common.create_person') }}
        </a>
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if($persons->count() > 0)
        <div class="space-y-3">
            @foreach($persons as $person)
                <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-semibold text-lg text-gray-800">{{ $person->fullname }}</div>
                        </div>
                        <a href="{{ route('mobile.persons.edit', $person->id) }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm">
                            {{ __('common.edit') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white p-8 rounded-xl border-2 border-gray-200 text-center">
            <p class="text-gray-600 mb-4">{{ __('common.no_persons_found') }}</p>
            <a href="{{ route('mobile.persons.create') }}" class="inline-block px-4 py-2 bg-amber-500 text-white rounded-lg font-semibold">
                {{ __('common.create_person') }}
            </a>
        </div>
    @endif
</div>
@endsection

