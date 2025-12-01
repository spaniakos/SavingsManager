<?php

namespace Database\Seeders;

use App\Models\ExpenseSuperCategory;
use Illuminate\Database\Seeder;

class ExpenseSuperCategorySeeder extends Seeder
{
    public function run(): void
    {
        $superCategories = [
            'housing',
            'transportation',
            'food',
            'utilities',
            'health',
            'insurance',
            'education',
            'entertainment',
            'personal',
            'work_business',
            'other',
        ];

        foreach ($superCategories as $superCategory) {
            ExpenseSuperCategory::create([
                'name' => $superCategory,
                'is_system' => true,
                'user_id' => null,
            ]);
        }
    }
}
