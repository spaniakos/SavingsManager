<?php

namespace Database\Seeders;

use App\Models\ExpenseSuperCategory;
use Illuminate\Database\Seeder;

class ExpenseSuperCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Only create 3 fixed super categories with allocation percentages
        $superCategories = [
            ['name' => 'essentials', 'allocation_percentage' => 50.00],
            ['name' => 'lifestyle', 'allocation_percentage' => 30.00],
            ['name' => 'savings', 'allocation_percentage' => 20.00],
        ];

        foreach ($superCategories as $superCategory) {
            ExpenseSuperCategory::create([
                'name' => $superCategory['name'],
                'is_system' => true,
                'user_id' => null,
                'allocation_percentage' => $superCategory['allocation_percentage'],
            ]);
        }
    }
}
