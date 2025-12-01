<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryAllocationGoal extends Model
{
    protected $fillable = [
        'user_id',
        'expense_super_category_id',
        'target_percentage',
        'period_start',
        'period_end',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'target_percentage' => 'decimal:2',
            'period_start' => 'date',
            'period_end' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenseSuperCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseSuperCategory::class);
    }
}

