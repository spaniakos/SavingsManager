<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_joint',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'initial_checkpoint' => 'decimal:2',
            'start_date' => 'date',
            'target_date' => 'date',
            'is_joint' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'savings_goal_members')
                    ->withPivot('invited_by', 'status', 'invited_at', 'accepted_at', 'role')
                    ->withTimestamps();
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(SavingsContribution::class);
    }
}
