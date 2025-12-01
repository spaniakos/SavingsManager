<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'initial_checkpoint',
        'start_date',
        'target_date',
        'last_monthly_calculation_at',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'initial_checkpoint' => 'decimal:2',
            'start_date' => 'date',
            'target_date' => 'date',
            'last_monthly_calculation_at' => 'datetime',
        ];
    }

    protected $attributes = [
        'current_amount' => 0,
        'initial_checkpoint' => 0,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
