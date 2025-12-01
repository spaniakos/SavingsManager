<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // Translation key
        'emoji',
        'is_system',
        'user_id',
    ];

    public function getTranslatedName(): string
    {
        return __("categories.income.{$this->name}", [], app()->getLocale());
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

    public function incomeEntries(): HasMany
    {
        return $this->hasMany(IncomeEntry::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('is_system', true)
                ->orWhere('user_id', $userId);
        });
    }
}
