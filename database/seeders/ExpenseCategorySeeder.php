<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\ExpenseSuperCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $superCategories = ExpenseSuperCategory::where('is_system', true)->get()->keyBy('name');
        
        // Clean categories - no duplicates, properly categorized
        $categories = [
            // Essentials (50%)
            ['name' => 'rent', 'super' => 'essentials'],
            ['name' => 'utilities_common', 'super' => 'essentials'],
            ['name' => 'electricity_deh', 'super' => 'essentials'],
            ['name' => 'water', 'super' => 'essentials'],
            ['name' => 'internet', 'super' => 'essentials'],
            ['name' => 'mobile_phone', 'super' => 'essentials'],
            ['name' => 'landline', 'super' => 'essentials'],
            ['name' => 'supermarket', 'super' => 'essentials'],
            ['name' => 'groceries', 'super' => 'essentials'],
            ['name' => 'fuel', 'super' => 'essentials'],
            ['name' => 'public_transport', 'super' => 'essentials'],
            ['name' => 'car_maintenance', 'super' => 'essentials'],
            ['name' => 'car_insurance', 'super' => 'essentials'],
            ['name' => 'parking', 'super' => 'essentials'],
            ['name' => 'tolls', 'super' => 'essentials'],
            ['name' => 'car', 'super' => 'essentials'],
            ['name' => 'motorcycle', 'super' => 'essentials'],
            ['name' => 'mobile_transport', 'super' => 'essentials'],
            ['name' => 'home_insurance', 'super' => 'essentials'],
            ['name' => 'maintenance', 'super' => 'essentials'],
            ['name' => 'home_office', 'super' => 'essentials'],
            ['name' => 'medical_visits', 'super' => 'essentials'],
            ['name' => 'medications', 'super' => 'essentials'],
            ['name' => 'dental_care', 'super' => 'essentials'],
            ['name' => 'health_insurance', 'super' => 'essentials'],
            ['name' => 'life_insurance', 'super' => 'essentials'],
            ['name' => 'self_insured', 'super' => 'essentials'],
            ['name' => 'tax', 'super' => 'essentials'],
            ['name' => 'seminars', 'super' => 'essentials'],
            ['name' => 'tuition', 'super' => 'essentials'],
            ['name' => 'educational_materials', 'super' => 'essentials'],
            ['name' => 'work_materials', 'super' => 'essentials'],
            ['name' => 'server', 'super' => 'essentials'],
            ['name' => 'domains', 'super' => 'essentials'],
            ['name' => 'ai_services', 'super' => 'essentials'],
            
            // Lifestyle (30%)
            ['name' => 'restaurants', 'super' => 'lifestyle'],
            ['name' => 'coffee', 'super' => 'lifestyle'],
            ['name' => 'spotify', 'super' => 'lifestyle'],
            ['name' => 'netflix', 'super' => 'lifestyle'],
            ['name' => 'disney_plus', 'super' => 'lifestyle'],
            ['name' => 'gym', 'super' => 'lifestyle'],
            ['name' => 'travel', 'super' => 'lifestyle'],
            ['name' => 'events', 'super' => 'lifestyle'],
            ['name' => 'clothing', 'super' => 'lifestyle'],
            ['name' => 'cosmetics', 'super' => 'lifestyle'],
            ['name' => 'hair_salon', 'super' => 'lifestyle'],
            ['name' => 'gifts', 'super' => 'lifestyle'],
            ['name' => 'unexpected', 'super' => 'lifestyle'],
            ['name' => 'donations', 'super' => 'lifestyle'],
            ['name' => 'other_expenses', 'super' => 'lifestyle'],
            
            // Savings (20%)
            ['name' => 'savings', 'super' => 'savings'],
        ];

        foreach ($categories as $category) {
            $superCategory = $superCategories->get($category['super']);
            if ($superCategory) {
                // Use updateOrCreate to avoid duplicates
                ExpenseCategory::updateOrCreate(
                    [
                        'name' => $category['name'],
                        'is_system' => true,
                        'user_id' => null,
                    ],
                    [
                        'expense_super_category_id' => $superCategory->id,
                    ]
                );
            }
        }
    }
}
