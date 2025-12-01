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
        
        $categories = [
            // Housing
            ['name' => 'rent', 'super' => 'housing'],
            ['name' => 'utilities_common', 'super' => 'housing'],
            ['name' => 'electricity_deh', 'super' => 'housing'],
            ['name' => 'water', 'super' => 'housing'],
            ['name' => 'home_insurance', 'super' => 'housing'],
            ['name' => 'maintenance', 'super' => 'housing'],
            ['name' => 'home_office', 'super' => 'housing'],
            
            // Transportation
            ['name' => 'fuel', 'super' => 'transportation'],
            ['name' => 'car_maintenance', 'super' => 'transportation'],
            ['name' => 'car_insurance', 'super' => 'transportation'],
            ['name' => 'tolls', 'super' => 'transportation'],
            ['name' => 'public_transport', 'super' => 'transportation'],
            ['name' => 'parking', 'super' => 'transportation'],
            ['name' => 'car', 'super' => 'transportation'],
            ['name' => 'motorcycle', 'super' => 'transportation'],
            ['name' => 'mobile_transport', 'super' => 'transportation'],
            
            // Food
            ['name' => 'supermarket', 'super' => 'food'],
            ['name' => 'restaurants', 'super' => 'food'],
            ['name' => 'coffee', 'super' => 'food'],
            ['name' => 'potatoes', 'super' => 'food'],
            ['name' => 'groceries', 'super' => 'food'],
            
            // Utilities
            ['name' => 'landline', 'super' => 'utilities'],
            ['name' => 'mobile_phone', 'super' => 'utilities'],
            ['name' => 'internet', 'super' => 'utilities'],
            ['name' => 'spotify', 'super' => 'utilities'],
            ['name' => 'netflix', 'super' => 'utilities'],
            ['name' => 'disney_plus', 'super' => 'utilities'],
            ['name' => 'log', 'super' => 'utilities'],
            ['name' => 'stathero', 'super' => 'utilities'],
            
            // Health
            ['name' => 'medical_visits', 'super' => 'health'],
            ['name' => 'medications', 'super' => 'health'],
            ['name' => 'dental_care', 'super' => 'health'],
            ['name' => 'health_insurance', 'super' => 'health'],
            
            // Insurance
            ['name' => 'efka', 'super' => 'insurance'],
            ['name' => 'life_insurance', 'super' => 'insurance'],
            ['name' => 'home_insurance_insurance', 'super' => 'insurance'],
            ['name' => 'car_insurance_insurance', 'super' => 'insurance'],
            ['name' => 'insurance', 'super' => 'insurance'],
            ['name' => 'tax', 'super' => 'insurance'],
            
            // Education
            ['name' => 'seminars', 'super' => 'education'],
            ['name' => 'tuition', 'super' => 'education'],
            ['name' => 'educational_materials', 'super' => 'education'],
            
            // Entertainment
            ['name' => 'gym', 'super' => 'entertainment'],
            ['name' => 'travel', 'super' => 'entertainment'],
            ['name' => 'events', 'super' => 'entertainment'],
            ['name' => 'gymnastirio', 'super' => 'entertainment'],
            ['name' => 'taksidia', 'super' => 'entertainment'],
            
            // Personal
            ['name' => 'clothing', 'super' => 'personal'],
            ['name' => 'cosmetics', 'super' => 'personal'],
            ['name' => 'hair_salon', 'super' => 'personal'],
            ['name' => 'gifts', 'super' => 'personal'],
            ['name' => 'dwra', 'super' => 'personal'],
            ['name' => 'personal_life', 'super' => 'personal'],
            
            // Work/Business
            ['name' => 'work_materials', 'super' => 'work_business'],
            ['name' => 'server', 'super' => 'work_business'],
            ['name' => 'domains', 'super' => 'work_business'],
            ['name' => 'ai_services', 'super' => 'work_business'],
            ['name' => 'ylika_douleias', 'super' => 'work_business'],
            ['name' => 'ai', 'super' => 'work_business'],
            ['name' => 'aproblepta', 'super' => 'work_business'],
            
            // Other
            ['name' => 'unexpected', 'super' => 'other'],
            ['name' => 'donations', 'super' => 'other'],
            ['name' => 'other_expenses', 'super' => 'other'],
            ['name' => 'eksodoi', 'super' => 'other'],
            ['name' => 'erini', 'super' => 'other'],
            ['name' => 'spanos', 'super' => 'other'],
            ['name' => 'revma', 'super' => 'other'],
            ['name' => 'super', 'super' => 'other'],
            ['name' => 'loipa', 'super' => 'other'],
        ];

        foreach ($categories as $category) {
            $superCategory = $superCategories->get($category['super']);
            if ($superCategory) {
                ExpenseCategory::create([
                    'name' => $category['name'],
                    'expense_super_category_id' => $superCategory->id,
                    'is_system' => true,
                    'user_id' => null,
                ]);
            }
        }
    }
}
