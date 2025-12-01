<?php

namespace Database\Seeders;

use App\Models\IncomeCategory;
use Illuminate\Database\Seeder;

class IncomeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'salary',
            'bonus',
            'raises',
            'business_income',
            'freelancer',
            'property_rent',
            'vehicle_rent',
            'dividends',
            'interest',
            'capital_gains',
            'donations_received',
            'inheritance',
            'other_income',
        ];

        foreach ($categories as $category) {
            IncomeCategory::create([
                'name' => $category,
                'is_system' => true,
                'user_id' => null,
            ]);
        }
    }
}
