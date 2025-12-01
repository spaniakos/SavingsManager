@extends('mobile.layout')

@section('content')
@php
    use App\Services\SavingsCalculatorService;
    $calculator = app(SavingsCalculatorService::class);
@endphp

<div class="p-4 space-y-4">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ __('common.savings_goals') }}</h1>
        <a href="{{ route('mobile.savings-goals-admin.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">
            {{ __('common.create') }}
        </a>
    </div>
    
    <div class="space-y-3">
        @forelse($goals as $goal)
            @php
                $progress = $calculator->getProgressData($goal);
            @endphp
            <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <div class="font-semibold text-lg">{{ $goal->name }}</div>
                        <div class="text-sm text-gray-600">
                            €{{ number_format($goal->current_amount, 2) }} / €{{ number_format($goal->target_amount, 2) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $goal->start_date->format('d/m/Y') }} - {{ $goal->target_date->format('d/m/Y') }}
                        </div>
                        @if($goal->is_joint)
                            <span class="inline-block mt-1 px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded">{{ __('common.joint_goal') }}</span>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('mobile.savings-goals-admin.edit', $goal->id) }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm">
                            {{ __('common.edit') }}
                        </a>
                        <form method="POST" action="{{ route('mobile.savings-goals-admin.destroy', $goal->id) }}" onsubmit="return confirm('{{ __('common.confirm_delete') }}')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm">
                                {{ __('common.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                    <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-3 rounded-full" style="width: {{ min(100, $progress['overall_progress']) }}%"></div>
                </div>
                <div class="text-xs text-gray-600">
                    {{ number_format($progress['overall_progress'], 0) }}% {{ __('common.complete') }}
                    @if($progress['months_remaining'] > 0)
                        | {{ __('common.monthly_needed') }}: €{{ number_format($progress['monthly_saving_needed'], 2) }}
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-xl border-2 border-gray-200 text-center">
                <p class="text-gray-600">{{ __('common.no_savings_goals') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

