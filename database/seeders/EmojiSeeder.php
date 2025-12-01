<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\ExpenseSuperCategory;
use App\Models\IncomeCategory;
use Illuminate\Database\Seeder;

class EmojiSeeder extends Seeder
{
    /**
     * Default emojis based on category names
     */
    private array $superCategoryEmojis = [
        'essentials' => '🏠',
        'lifestyle' => '✨',
        'savings' => '💰',
    ];

    /**
     * Default emojis for expense categories
     */
    private array $expenseCategoryEmojis = [
        // Essentials
        'rent' => '🏠',
        'utilities_common' => '💡',
        'electricity_deh' => '⚡',
        'water' => '💧',
        'home_insurance' => '🛡️',
        'maintenance' => '🔧',
        'home_office' => '💻',
        'fuel' => '⛽',
        'car_maintenance' => '🔧',
        'car_insurance' => '🛡️',
        'tolls' => '🛣️',
        'public_transport' => '🚌',
        'parking' => '🅿️',
        'car' => '🚗',
        'motorcycle' => '🏍️',
        'mobile_transport' => '📱',
        'supermarket' => '🛒',
        'restaurants' => '🍽️',
        'coffee' => '☕',
        'potatoes' => '🥔',
        'groceries' => '🛒',
        'landline' => '📞',
        'mobile_phone' => '📱',
        'internet' => '🌐',
        'spotify' => '🎵',
        'netflix' => '📺',
        'disney_plus' => '🎬',
        'log' => '🪵',
        'stathero' => '📊',
        'medical_visits' => '🏥',
        'medications' => '💊',
        'dental_care' => '🦷',
        'health_insurance' => '🏥',
        'self_insured' => '🛡️',
        'life_insurance' => '🛡️',
        'home_insurance_insurance' => '🏠',
        'car_insurance_insurance' => '🚗',
        'insurance' => '🛡️',
        'tax' => '📋',
        
        // Lifestyle
        'seminars' => '🎓',
        'tuition' => '📚',
        'educational_materials' => '📖',
        'gym' => '💪',
        'travel' => '✈️',
        'events' => '🎉',
        'gymnastirio' => '🏋️',
        'taksidia' => '✈️',
        'clothing' => '👕',
        'cosmetics' => '💄',
        'hair_salon' => '💇',
        'gifts' => '🎁',
        'dwra' => '🎁',
        'personal_life' => '👤',
        'work_materials' => '💼',
        'server' => '🖥️',
        'domains' => '🌐',
        'ai_services' => '🤖',
        'ylika_douleias' => '📦',
        'ai' => '🤖',
        'aproblepta' => '❓',
        'unexpected' => '⚠️',
        'donations' => '❤️',
        'other_expenses' => '📝',
        'eksodoi' => '💸',
        'erini' => '👤',
        'spanos' => '👤',
        'revma' => '🌊',
        'super' => '🛒',
        'loipa' => '📋',
        
        // Savings
        'savings' => '💰',
    ];

    /**
     * Default emojis for income categories
     */
    private array $incomeCategoryEmojis = [
        'salary' => '💼',
        'bonus' => '🎁',
        'raises' => '📈',
        'business_income' => '🏢',
        'freelancer' => '⚔️',
        'property_rent' => '🏠',
        'vehicle_rent' => '🚗',
        'dividends' => '📊',
        'interest' => '💹',
        'capital_gains' => '📈',
        'donations_received' => '❤️',
        'inheritance' => '💎',
        'other_income' => '💰',
    ];

    public function run(): void
    {
        // Update expense super categories
        foreach ($this->superCategoryEmojis as $name => $emoji) {
            ExpenseSuperCategory::where('name', $name)
                ->where('is_system', true)
                ->update(['emoji' => $emoji]);
        }

        // Update expense categories
        foreach ($this->expenseCategoryEmojis as $name => $emoji) {
            ExpenseCategory::where('name', $name)
                ->where('is_system', true)
                ->update(['emoji' => $emoji]);
        }

        // Update income categories
        $incomeCategories = IncomeCategory::where('is_system', true)->get();
        foreach ($incomeCategories as $category) {
            $emoji = $this->incomeCategoryEmojis[$category->name] ?? '💰';
            $category->update(['emoji' => $emoji]);
        }
    }
}
