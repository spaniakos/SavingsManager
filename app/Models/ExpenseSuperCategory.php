<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseSuperCategory extends Model
{
    protected $fillable = [
        'name', // Translation key
        'is_system',
        'user_id',
    ];
    
    public function getTranslatedName(): string
    {
        return __("categories.expense_super.{$this->name}", [], app()->getLocale());
    }

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenseCategories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('is_system', true)
              ->orWhere('user_id', $userId);
        });
    }
}
