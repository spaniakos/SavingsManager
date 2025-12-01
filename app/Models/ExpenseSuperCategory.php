<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseSuperCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', // Translation key
        'is_system',
        'user_id',
        'allocation_percentage',
    ];
    
    public function getTranslatedName(): string
    {
        return __("categories.expense_super.{$this->name}", [], app()->getLocale());
    }

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'allocation_percentage' => 'decimal:2',
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

    /**
     * Check if this is a fixed super category (all are fixed now)
     */
    public function isFixed(): bool
    {
        return $this->is_system && in_array($this->name, ['essentials', 'lifestyle', 'savings']);
    }
}
