@extends('mobile.layout')

@section('content')
<div class="p-4">
    <a href="{{ route('mobile.persons.index') }}" class="text-blue-600 mb-4 inline-block">← {{ __('common.back') }}</a>
    <h1 class="text-2xl font-bold mb-6">{{ __('common.edit_person') }}</h1>
    
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ route('mobile.persons.update', $person->id) }}" class="space-y-4">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.fullname') }}</label>
            <input type="text" name="fullname" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                   placeholder="{{ __('common.fullname_placeholder') }}"
                   value="{{ old('fullname', $person->fullname) }}">
        </div>
        
        <button type="submit" class="w-full bg-amber-500 text-white py-3 rounded-lg font-semibold text-lg">
            {{ __('common.save') }}
        </button>
    </form>
</div>
@endsection

