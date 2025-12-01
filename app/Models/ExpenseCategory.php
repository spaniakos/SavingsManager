<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', // Translation key
        'emoji',
        'expense_super_category_id',
        'is_system',
        'user_id',
        'save_for_later_target',
        'save_for_later_frequency',
        'save_for_later_amount',
    ];
    
    public function getTranslatedName(): string
    {
        return __("categories.expense.{$this->name}", [], app()->getLocale());
    }

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'save_for_later_target' => 'decimal:2',
            'save_for_later_amount' => 'decimal:2',
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

    public function expenseEntries(): HasMany
    {
        return $this->hasMany(ExpenseEntry::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('is_system', true)
              ->orWhere('user_id', $userId);
        });
    }

    /**
     * Get save-for-later progress (total saved vs target)
     */
    public function getSaveForLaterProgress(): float
    {
        if (!$this->save_for_later_target || $this->save_for_later_target <= 0) {
            return 0;
        }
        
        // Calculate total saved from expense entries (negative amounts = savings)
        $saved = abs($this->expenseEntries()
            ->where('amount', '<', 0)
            ->sum('amount'));
        
        return min(100, ($saved / $this->save_for_later_target) * 100);
    }

    /**
     * Get remaining amount needed for save-for-later target
     */
    public function getRemainingSaveForLaterAmount(): float
    {
        if (!$this->save_for_later_target) {
            return 0;
        }
        
        $saved = abs($this->expenseEntries()
            ->where('amount', '<', 0)
            ->sum('amount'));
        
        return max(0, $this->save_for_later_target - $saved);
    }
}
