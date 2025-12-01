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
     * Default emojis for expense categories - clean structure
     */
    private array $expenseCategoryEmojis = [
        // -----------------------------
        // ESSENTIALS — monthly survival
        // -----------------------------
        'rent' => '🏠',
        'utilities_common' => '💡',
        'electricity_deh' => '⚡',
        'water' => '💧',
        'internet' => '🌐',
        'mobile_phone' => '📱',
        'landline' => '📞',
        'supermarket' => '🛒',
        'groceries' => '🛒',
        'fuel' => '⛽',
        'public_transport' => '🚌',
        'car' => '🚗',
        'motorcycle' => '🏍️',
        'car_maintenance' => '🔧',
        'car_insurance' => '🛡️',
        'self_insured' => '🛡️',
        'parking' => '🅿️',
        'tolls' => '🛣️',
        'medical_visits' => '🏥',
        'medications' => '💊',
        'dental_care' => '🦷',
        'vet' => '🐾',
        'pet_food' => '🐕',
        'work_materials' => '💼',
        'server' => '🖥️',
        'domains' => '🌐',
        'ai_services' => '🤖',
        'seminars' => '🎓',
        'tuition' => '📚',
        'educational_materials' => '📖',
        'loan_payments' => '💳',
        'credit_card_payments' => '💳',

        // -----------------------------
        // LIFESTYLE
        // -----------------------------
        'restaurants' => '🍽️',
        'coffee' => '☕',
        'drinks' => '🥤',
        'beers' => '🍺',
        'subscriptions' => '📱',
        'gaming' => '🎮',
        'e_games' => '🕹️',
        'delivery' => '🚚',
        'gym' => '💪',
        'travel' => '✈️',
        'events' => '🎉',
        'clothing' => '👕',
        'cosmetics' => '💄',
        'hair_salon' => '💇',
        'gifts' => '🎁',
        'tech' => '💻',
        'life_insurance' => '🛡️',
        'home_insurance' => '🛡️',
        'private_health_insurance' => '🏥',
        'childcare' => '👶',
        'school' => '🏫',
        'donations' => '❤️',
        'unexpected' => '⚠️',
        'other_expenses' => '📝',

        // -----------------------------
        // SAVINGS
        // -----------------------------
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
