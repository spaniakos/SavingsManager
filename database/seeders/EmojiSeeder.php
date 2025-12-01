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
        'rent'               => '🏠',
        'utilities_common'   => '💡',
        'electricity_deh'    => '⚡',
        'water'              => '💧',
        'internet'           => '🌐',
        'mobile_phone'       => '📱',
        'landline'           => '📞',
        'supermarket'        => '🛒',
        'groceries'          => '🛒',
        'medical_visits'     => '🏥',
        'medications'        => '💊',
        'dental_care'        => '🦷',
        'health_insurance'   => '🏥',
        'life_insurance'     => '🛡️',
        'home_insurance'     => '🛡️',
        'car_insurance'      => '🛡️',
        'self_insured'       => '🛡️',
        'tax'                => '📋',
        
        // -----------------------------
        // TRANSPORTATION
        // -----------------------------
        'fuel'               => '⛽',
        'public_transport'   => '🚌',
        'car'                => '🚗',
        'motorcycle'         => '🏍️',
        'parking'            => '🅿️',
        'tolls'              => '🛣️',
        'car_maintenance'    => '🔧',
        'mobile_transport'   => '📱',
        
        // -----------------------------
        // HOME & MAINTENANCE
        // -----------------------------
        'maintenance'        => '🔧',
        'home_office'        => '💻',
        
        // -----------------------------
        // FOOD & LIFESTYLE
        // -----------------------------
        'restaurants'        => '🍽️',
        'coffee'             => '☕',
        'drinks'             => '🥤',
        'beers'              => '🍺',
        'tech'               => '💻',
        'clothing'           => '👕',
        'cosmetics'          => '💄',
        'hair_salon'         => '💇',
        'gifts'              => '🎁',
        'donations'          => '❤️',
        
        // -----------------------------
        // ENTERTAINMENT & SUBSCRIPTIONS
        // -----------------------------
        'spotify'            => '🎵',
        'netflix'            => '📺',
        'disney_plus'        => '🎬',
        'events'             => '🎉',
        'travel'             => '✈️',
        
        // -----------------------------
        // EDUCATION / SELF-IMPROVEMENT
        // -----------------------------
        'seminars'           => '🎓',
        'tuition'            => '📚',
        'educational_materials' => '📖',
        'gym'                => '💪',
        
        // -----------------------------
        // WORK / BUSINESS EXPENSES
        // -----------------------------
        'work_materials'     => '💼',
        'server'             => '🖥️',
        'domains'            => '🌐',
        'ai_services'        => '🤖',
        
        // -----------------------------
        // MISC / EDGE CASES
        // -----------------------------
        'unexpected'         => '⚠️',
        'other_expenses'     => '📝',
        
        // -----------------------------
        // SAVINGS
        // -----------------------------
        'savings'            => '💰',
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
