<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('common.monthly_report') }} - {{ $reportData['month'] ?? now()->format('F Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .summary { margin: 20px 0; padding: 15px; background-color: #f9f9f9; }
        .summary-item { margin: 5px 0; }
        .amount { font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ __('common.monthly_report') }} - {{ $reportData['month'] ?? now()->format('F Y') }}</h1>
    
    <div class="summary">
        <div class="summary-item">
            <strong>{{ __('common.total_income') }}:</strong>
            <span class="amount">{{ number_format($reportData['total_income'] ?? 0, 2) }} €</span>
        </div>
        <div class="summary-item">
            <strong>{{ __('common.total_expenses') }}:</strong>
            <span class="amount">{{ number_format($reportData['total_expenses'] ?? 0, 2) }} €</span>
        </div>
        <div class="summary-item">
            <strong>{{ __('common.net_savings') }}:</strong>
            <span class="amount">{{ number_format($reportData['net_savings'] ?? 0, 2) }} €</span>
        </div>
    </div>

    @if(isset($reportData['income_by_category']) && count($reportData['income_by_category']) > 0)
        <h2>{{ __('common.income_by_category') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ __('common.category') }}</th>
                    <th style="text-align: right;">{{ __('common.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['income_by_category'] as $category => $amount)
                    <tr>
                        <td>{{ $category }}</td>
                        <td style="text-align: right;">{{ number_format($amount, 2) }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(isset($reportData['expenses_by_category']) && count($reportData['expenses_by_category']) > 0)
        <h2>{{ __('common.expenses_by_category') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ __('common.category') }}</th>
                    <th style="text-align: right;">{{ __('common.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['expenses_by_category'] as $category => $amount)
                    <tr>
                        <td>{{ $category }}</td>
                        <td style="text-align: right;">{{ number_format($amount, 2) }} €</td>
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

