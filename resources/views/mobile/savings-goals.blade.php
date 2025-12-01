@extends('mobile.layout')

@section('content')
@php
    use App\Services\SavingsCalculatorService;
    $calculator = app(SavingsCalculatorService::class);
@endphp

<div class="p-4 space-y-4">
    <h1 class="text-2xl font-bold text-center mb-6">{{ __('common.savings_goals') }}</h1>
    
    <div class="space-y-3">
        @forelse($goals as $goal)
            @php
                $progress = $calculator->getProgressData($goal);
            @endphp
            <div class="bg-white p-4 rounded-xl border-2 border-gray-200">
                <div class="font-semibold text-lg mb-2">{{ $goal->name }}</div>
                <div class="text-sm text-gray-600 mb-2">
                    €{{ number_format($goal->current_amount, 2) }} / €{{ number_format($goal->target_amount, 2) }}
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

