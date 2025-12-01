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
        
        // Map categories to new 3-tier system: essentials, lifestyle, savings
        $categories = [
            // Essentials (50%)
            ['name' => 'rent', 'super' => 'essentials'],
            ['name' => 'utilities_common', 'super' => 'essentials'],
            ['name' => 'electricity_deh', 'super' => 'essentials'],
            ['name' => 'water', 'super' => 'essentials'],
            ['name' => 'home_insurance', 'super' => 'essentials'],
            ['name' => 'maintenance', 'super' => 'essentials'],
            ['name' => 'home_office', 'super' => 'essentials'],
            ['name' => 'fuel', 'super' => 'essentials'],
            ['name' => 'car_maintenance', 'super' => 'essentials'],
            ['name' => 'car_insurance', 'super' => 'essentials'],
            ['name' => 'tolls', 'super' => 'essentials'],
            ['name' => 'public_transport', 'super' => 'essentials'],
            ['name' => 'parking', 'super' => 'essentials'],
            ['name' => 'car', 'super' => 'essentials'],
            ['name' => 'motorcycle', 'super' => 'essentials'],
            ['name' => 'mobile_transport', 'super' => 'essentials'],
            ['name' => 'supermarket', 'super' => 'essentials'],
            ['name' => 'restaurants', 'super' => 'essentials'],
            ['name' => 'coffee', 'super' => 'essentials'],
            ['name' => 'potatoes', 'super' => 'essentials'],
            ['name' => 'groceries', 'super' => 'essentials'],
            ['name' => 'landline', 'super' => 'essentials'],
            ['name' => 'mobile_phone', 'super' => 'essentials'],
            ['name' => 'internet', 'super' => 'essentials'],
            ['name' => 'spotify', 'super' => 'essentials'],
            ['name' => 'netflix', 'super' => 'essentials'],
            ['name' => 'disney_plus', 'super' => 'essentials'],
            ['name' => 'log', 'super' => 'essentials'],
            ['name' => 'stathero', 'super' => 'essentials'],
            ['name' => 'medical_visits', 'super' => 'essentials'],
            ['name' => 'medications', 'super' => 'essentials'],
            ['name' => 'dental_care', 'super' => 'essentials'],
            ['name' => 'health_insurance', 'super' => 'essentials'],
            ['name' => 'self_insured', 'super' => 'essentials'], // Renamed from 'efka'
            ['name' => 'life_insurance', 'super' => 'essentials'],
            ['name' => 'home_insurance_insurance', 'super' => 'essentials'],
            ['name' => 'car_insurance_insurance', 'super' => 'essentials'],
            ['name' => 'insurance', 'super' => 'essentials'],
            ['name' => 'tax', 'super' => 'essentials'],
            
            // Lifestyle (30%)
            ['name' => 'seminars', 'super' => 'lifestyle'],
            ['name' => 'tuition', 'super' => 'lifestyle'],
            ['name' => 'educational_materials', 'super' => 'lifestyle'],
            ['name' => 'gym', 'super' => 'lifestyle'],
            ['name' => 'travel', 'super' => 'lifestyle'],
            ['name' => 'events', 'super' => 'lifestyle'],
            ['name' => 'gymnastirio', 'super' => 'lifestyle'],
            ['name' => 'taksidia', 'super' => 'lifestyle'],
            ['name' => 'clothing', 'super' => 'lifestyle'],
            ['name' => 'cosmetics', 'super' => 'lifestyle'],
            ['name' => 'hair_salon', 'super' => 'lifestyle'],
            ['name' => 'gifts', 'super' => 'lifestyle'],
            ['name' => 'dwra', 'super' => 'lifestyle'],
            ['name' => 'personal_life', 'super' => 'lifestyle'],
            ['name' => 'work_materials', 'super' => 'lifestyle'],
            ['name' => 'server', 'super' => 'lifestyle'],
            ['name' => 'domains', 'super' => 'lifestyle'],
            ['name' => 'ai_services', 'super' => 'lifestyle'],
            ['name' => 'ylika_douleias', 'super' => 'lifestyle'],
            ['name' => 'ai', 'super' => 'lifestyle'],
            ['name' => 'aproblepta', 'super' => 'lifestyle'],
            ['name' => 'unexpected', 'super' => 'lifestyle'],
            ['name' => 'donations', 'super' => 'lifestyle'],
            ['name' => 'other_expenses', 'super' => 'lifestyle'],
            ['name' => 'eksodoi', 'super' => 'lifestyle'],
            ['name' => 'erini', 'super' => 'lifestyle'],
            ['name' => 'spanos', 'super' => 'lifestyle'],
            ['name' => 'revma', 'super' => 'lifestyle'],
            ['name' => 'super', 'super' => 'lifestyle'],
            ['name' => 'loipa', 'super' => 'lifestyle'],
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
