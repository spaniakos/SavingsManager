<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'seed_capital',
        'median_monthly_income',
        'income_last_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'seed_capital' => 'decimal:2',
            'median_monthly_income' => 'decimal:2',
            'income_last_verified_at' => 'date',
        ];
    }

    public function incomeCategories(): HasMany
    {
        return $this->hasMany(IncomeCategory::class);
    }

    public function incomeEntries(): HasMany
    {
        return $this->hasMany(IncomeEntry::class);
    }

    public function expenseSuperCategories(): HasMany
    {
        return $this->hasMany(ExpenseSuperCategory::class);
    }

    public function expenseCategories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function expenseEntries(): HasMany
    {
        return $this->hasMany(ExpenseEntry::class);
    }

    public function savingsGoals(): HasMany
    {
        return $this->hasMany(SavingsGoal::class);
    }

    /**
     * Get net worth (seed capital + current savings from all goals)
     */
    public function getNetWorthAttribute(): float
    {
        $seedCapital = $this->seed_capital ?? 0;
        $currentSavings = $this->savingsGoals()->sum('current_amount');

        return (float) ($seedCapital + $currentSavings);
    }

    /**
     * Get monthly income allocation for a super category
     */
    public function getMonthlyIncomeAllocation($superCategory): float
    {
        $monthlyIncome = $this->median_monthly_income ?? 0;
        $percentage = $superCategory->allocation_percentage ?? 0;

        return (float) ($monthlyIncome * ($percentage / 100));
    }

    /**
     * Get remaining allowance for a super category in a period
     */
    public function getRemainingAllowance($superCategory, $startDate, $endDate): float
    {
        $allocation = $this->getMonthlyIncomeAllocation($superCategory);
        $spent = $this->expenseEntries()
            ->whereHas('expenseCategory', function ($query) use ($superCategory) {
                $query->where('expense_super_category_id', $superCategory->id);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        return (float) max(0, $allocation - $spent);
    }
}
