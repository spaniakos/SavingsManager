<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('common.reports') }} - {{ $startDate }} to {{ $endDate }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .emoji-img {
            width: 14px;
            height: 14px;
            vertical-align: middle;
            display: inline-block;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        h3 {
            font-size: 14px;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .summary-box {
            display: inline-block;
            width: 18%;
            margin: 5px;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }
        .text-right {
            text-align: right;
        }
        .goal-progress {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }
        .progress-fill {
            height: 100%;
            background-color: #f59e0b;
            text-align: center;
            line-height: 20px;
            color: white;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <h1>{{ __('common.reports') }}</h1>
    <p><strong>{{ __('common.period') }}:</strong> {{ $startDate }} {{ __('common.to') }} {{ $endDate }}</p>
    <p><strong>{{ __('common.user') }}:</strong> {{ $user->name }}</p>

    <!-- Summary -->
    <h2>{{ __('common.summary') }}</h2>
    <div>
        <div class="summary-box">
            <div class="summary-label">{{ __('common.total_income') }}</div>
            <div class="summary-value">€{{ number_format($reportData['summary']['total_income'] ?? 0, 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">{{ __('common.total_expenses') }}</div>
            <div class="summary-value">€{{ number_format($reportData['summary']['total_expenses'] ?? 0, 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">{{ __('common.net_savings') }}</div>
            <div class="summary-value">€{{ number_format($reportData['summary']['net_savings'] ?? 0, 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">{{ __('common.savings_rate') }}</div>
            <div class="summary-value">{{ number_format($reportData['summary']['savings_rate'] ?? 0, 1) }}%</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">{{ __('common.total_saved') }}</div>
            <div class="summary-value">€{{ number_format($reportData['summary']['total_saved'] ?? 0, 2) }}</div>
        </div>
    </div>

    <!-- Income Analysis -->
    <h2>{{ __('common.income_analysis') }}</h2>

    @if(!empty($reportData['income']['hierarchical']))
        @php
            $totalIncome = $reportData['summary']['total_income'];
        @endphp
        
        @foreach($reportData['income']['hierarchical'] as $category)
            <!-- Category -->
            <div style="margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">
                <!-- Category Header -->
                <div style="background-color: #f2f2f2; padding: 10px; border-bottom: 2px solid #333;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="border: none; font-weight: bold; font-size: 14px;">
                                @php
                                    $emojiImg = !empty($category['emoji']) ? \App\Helpers\EmojiHelper::emojiToImageTag($category['emoji'], 14) : '';
                                @endphp
                                {!! $emojiImg !!} {{ $category['name'] }}
                            </td>
                            <td style="border: none; text-align: right; font-weight: bold; font-size: 14px;">
                                €{{ number_format($category['total'], 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; font-size: 10px; color: #666;">
                                {{ $totalIncome > 0 ? number_format(($category['total'] / $totalIncome) * 100, 1) : 0 }}% | 
                                {{ $category['count'] }} {{ __('common.items') }}
                            </td>
                            <td style="border: none;"></td>
                        </tr>
                    </table>
                </div>

                <!-- Items (if breakdown type is item) -->
                @if($breakdownType === 'item' && !empty($category['items']))
                    <table style="width: 100%; border: none; margin-left: 20px; margin-bottom: 5px;">
                        <thead>
                            <tr style="background-color: #f9f9f9;">
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left; font-size: 10px;">{{ __('common.date') }}</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: right; font-size: 10px;">{{ __('common.amount') }}</th>
                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left; font-size: 10px;">{{ __('common.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category['items'] as $item)
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">{{ $item['date'] }}</td>
                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: right; font-size: 10px; font-weight: bold;">
                                        €{{ number_format($item['amount'], 2) }}
                                        @if($item['person'] ?? null)
                                            <div style="margin-top: 2px; font-size: 9px; font-weight: normal; text-align: right;">
                                                <span style="display: inline-block; padding: 1px 4px; background-color: #d1fae5; color: #065f46; border-radius: 3px;">
                                                    @php
                                                        $emojiImg = \App\Helpers\EmojiHelper::emojiToImageTag('👥', 10);
                                                    @endphp
                                                    {!! $emojiImg !!} {{ $item['person'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px; color: #666;">{{ $item['notes'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    @endif

    <!-- Expenses Analysis -->
    <h2>{{ __('common.expenses_analysis') }}</h2>

    @if(!empty($reportData['expenses']['hierarchical']))
        @php
            $totalExpenses = $reportData['summary']['total_expenses'];
        @endphp
        
        @foreach($reportData['expenses']['hierarchical'] as $superCategory)
            <!-- Super Category -->
            <div style="margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">
                <!-- Super Category Header -->
                <div style="background-color: #f2f2f2; padding: 10px; border-bottom: 2px solid #333;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="border: none; font-weight: bold; font-size: 14px;">
                                @php
                                    $emojiImg = !empty($superCategory['emoji']) ? \App\Helpers\EmojiHelper::emojiToImageTag($superCategory['emoji'], 14) : '';
                                @endphp
                                {!! $emojiImg !!} {{ $superCategory['name'] }}
                            </td>
                            <td style="border: none; text-align: right; font-weight: bold; font-size: 14px;">
                                €{{ number_format($superCategory['total'], 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; font-size: 10px; color: #666;">
                                {{ $totalExpenses > 0 ? number_format(($superCategory['total'] / $totalExpenses) * 100, 1) : 0 }}% | 
                                {{ $superCategory['count'] }} {{ __('common.items') }}
                            </td>
                            <td style="border: none;"></td>
                        </tr>
                    </table>
                </div>

                <!-- Categories (only if breakdown type is category or item) -->
                @if(($breakdownType === 'category' || $breakdownType === 'item') && !empty($superCategory['categories']))
                    <div style="background-color: #fff;">
                        @foreach($superCategory['categories'] as $category)
                            <div style="border-bottom: 1px solid #eee;">
                                <!-- Category Row -->
                                <table style="width: 100%; border: none;">
                                    <tr>
                                        <td style="border: none; padding: 8px 8px 8px 20px; font-weight: bold; font-size: 12px;">
                                            @php
                                                $emojiImg = !empty($category['emoji']) ? \App\Helpers\EmojiHelper::emojiToImageTag($category['emoji'], 12) : '';
                                            @endphp
                                            {!! $emojiImg !!} {{ $category['name'] }}
                                        </td>
                                        <td style="border: none; padding: 8px; text-align: right; font-weight: bold; font-size: 12px;">
                                            €{{ number_format($category['total'], 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding: 0 8px 8px 20px; font-size: 10px; color: #666;">
                                            {{ $totalExpenses > 0 ? number_format(($category['total'] / $totalExpenses) * 100, 1) : 0 }}% | 
                                            {{ $category['count'] }} {{ __('common.items') }}
                                        </td>
                                        <td style="border: none;"></td>
                                    </tr>
                                </table>

                                <!-- Items (if breakdown type is item) -->
                                @if($breakdownType === 'item' && !empty($category['items']))
                                    <table style="width: 100%; border: none; margin-left: 30px; margin-bottom: 5px;">
                                        <thead>
                                            <tr style="background-color: #f9f9f9;">
                                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left; font-size: 10px;">{{ __('common.date') }}</th>
                                                <th style="border: 1px solid #ddd; padding: 4px; text-align: right; font-size: 10px;">{{ __('common.amount') }}</th>
                                                <th style="border: 1px solid #ddd; padding: 4px; text-align: left; font-size: 10px;">{{ __('common.notes') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($category['items'] as $item)
                                                <tr>
                                                    <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">{{ $item['date'] }}</td>
                                                    <td style="border: 1px solid #ddd; padding: 4px; text-align: right; font-size: 10px; font-weight: bold;">
                                                        €{{ number_format($item['amount'], 2) }}
                                                        <div style="margin-top: 2px; font-size: 9px; font-weight: normal;">
                                                            @if($item['is_save_for_later'] ?? false)
                                                                <span style="display: inline-block; padding: 1px 4px; background-color: #e9d5ff; color: #6b21a8; border-radius: 3px; margin-right: 2px;">
                                                                    @php
                                                                        $emojiImg = \App\Helpers\EmojiHelper::emojiToImageTag('💰', 10);
                                                                    @endphp
                                                                    {!! $emojiImg !!} {{ __('common.save_for_later') }}
                                                                </span>
                                                            @endif
                                                            @if($item['is_personal'] ?? false)
                                                                <span style="display: inline-block; padding: 1px 4px; background-color: #dbeafe; color: #1e40af; border-radius: 3px; margin-right: 2px;">
                                                                    @php
                                                                        $emojiImg = \App\Helpers\EmojiHelper::emojiToImageTag('👤', 10);
                                                                    @endphp
                                                                    {!! $emojiImg !!} {{ __('common.personal') }}
                                                                </span>
                                                            @endif
                                                            @if($item['person'] ?? null)
                                                                <span style="display: inline-block; padding: 1px 4px; background-color: #d1fae5; color: #065f46; border-radius: 3px; margin-right: 2px;">
                                                                    @php
                                                                        $emojiImg = \App\Helpers\EmojiHelper::emojiToImageTag('👥', 10);
                                                                    @endphp
                                                                    {!! $emojiImg !!} {{ $item['person'] }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px; color: #666;">{{ $item['notes'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    <!-- Goal Progression -->
    @if(!empty($reportData['goals_progress']))
        <h2>{{ __('common.goal_progression') }}</h2>
        @foreach($reportData['goals_progress'] as $goal)
            <div class="goal-progress">
                <strong>{{ $goal['name'] }}</strong><br>
                €{{ number_format($goal['current_amount'] ?? 0, 2) }} / €{{ number_format($goal['target_amount'], 2) }} 
                ({{ number_format($goal['progress'] ?? 0, 1) }}%)
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ min(100, $goal['progress'] ?? 0) }}%">
                        {{ number_format($goal['progress'] ?? 0, 1) }}%
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Personal Expense Totals -->
    @if(!empty($reportData['personal_expense_totals']))
        <h2>{{ __('common.personal_expense_summary') }}</h2>
        
        <div style="margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; font-weight: bold; font-size: 12px;">
                        {{ __('common.total_spend') }}
                    </td>
                    <td style="border: none; text-align: right; font-weight: bold; font-size: 14px;">
                        €{{ number_format($reportData['personal_expense_totals']['total_spend'] ?? 0, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; font-size: 10px; color: #666;" colspan="2">
                        {{ __('common.total_spend_description') }}
                    </td>
                </tr>
            </table>
        </div>

        @if(!empty($reportData['personal_expense_totals']['personal_by_person']))
            @foreach($reportData['personal_expense_totals']['personal_by_person'] as $personName => $total)
                <div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background-color: #e6f3ff;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="border: none; font-weight: bold; font-size: 12px;">
                                {{ __('common.total_spend_personal', ['person' => $personName]) }}
                            </td>
                            <td style="border: none; text-align: right; font-weight: bold; font-size: 14px;">
                                €{{ number_format($total, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        @endif
    @endif

    <!-- Non-Personal Expenses Breakdown by Person (Household Contribution) -->
    @if(!empty($reportData['non_personal_expenses_by_person']) && $reportData['non_personal_expenses_by_person']['total'] > 0)
        <h2>{{ __('common.non_personal_expenses_breakdown') }}</h2>
        
        <div style="margin-bottom: 10px; padding: 8px; border: 1px solid #ddd; background-color: #fff8e1;">
            <p style="font-size: 10px; color: #666; margin-bottom: 10px;">
                {{ __('common.household_contribution_note') }}
            </p>
            
            <table style="width: 100%; border: none; margin-bottom: 15px;">
                <tr>
                    <td style="border: none; font-weight: bold; font-size: 12px;">
                        {{ __('common.total') }}:
                    </td>
                    <td style="border: none; text-align: right; font-weight: bold; font-size: 14px;">
                        €{{ number_format($reportData['non_personal_expenses_by_person']['total'], 2) }}
                    </td>
                </tr>
            </table>

            @if(!empty($reportData['non_personal_expenses_by_person']['by_person']))
                @foreach($reportData['non_personal_expenses_by_person']['by_person'] as $personName => $amount)
                    <div style="margin-bottom: 8px; padding: 8px; border: 1px solid #ddd; background-color: #fffde7;">
                        <table style="width: 100%; border: none;">
                            <tr>
                                <td style="border: none; font-weight: bold; font-size: 11px;">
                                    {{ $personName }} {{ __('common.spent') }} ({{ __('common.non_personal') }})
                                </td>
                                <td style="border: none; text-align: right; font-weight: bold; font-size: 13px;">
                                    €{{ number_format($amount, 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            @endif
        </div>
    @endif

    <p style="margin-top: 30px; font-size: 10px; color: #666;">
        {{ __('common.generated_on') }}: {{ now()->format('d/m/Y H:i') }}
    </p>
</body>
</html>

