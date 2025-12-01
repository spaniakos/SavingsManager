<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('common.savings_report') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .summary { margin: 20px 0; padding: 15px; background-color: #f9f9f9; }
        .summary-item { margin: 5px 0; }
    </style>
</head>
<body>
    <h1>{{ __('common.savings_report') }}</h1>
    
    <div class="summary">
        <div class="summary-item">
            <strong>{{ __('common.total_target') }}:</strong>
            {{ number_format($reportData['total_target'] ?? 0, 2) }} €
        </div>
        <div class="summary-item">
            <strong>{{ __('common.total_current') }}:</strong>
            {{ number_format($reportData['total_current'] ?? 0, 2) }} €
        </div>
        <div class="summary-item">
            <strong>{{ __('common.total_progress') }}:</strong>
            {{ number_format($reportData['total_progress'] ?? 0, 1) }}%
        </div>
    </div>

    @if(isset($reportData['goals']) && count($reportData['goals']) > 0)
        <h2>{{ __('common.savings_goals') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ __('common.name') }}</th>
                    <th style="text-align: right;">{{ __('common.current_amount') }}</th>
                    <th style="text-align: right;">{{ __('common.target_amount') }}</th>
                    <th style="text-align: right;">{{ __('common.progress') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['goals'] as $goal)
                    <tr>
                        <td>{{ $goal['name'] }}</td>
                        <td style="text-align: right;">{{ number_format($goal['current_amount'], 2) }} €</td>
                        <td style="text-align: right;">{{ number_format($goal['target_amount'], 2) }} €</td>
                        <td style="text-align: right;">{{ number_format($goal['progress_percentage'], 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p style="margin-top: 30px; font-size: 10px; color: #666;">
        {{ __('common.generated_on') }}: {{ now()->format('d/m/Y H:i') }}
    </p>
</body>
</html>

