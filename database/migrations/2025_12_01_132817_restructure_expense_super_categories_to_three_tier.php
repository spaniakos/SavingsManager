<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mapping from old super categories to new 3-tier system
        $categoryMapping = [
            'essentials' => ['housing', 'transportation', 'food', 'utilities', 'health', 'insurance'],
            'lifestyle' => ['education', 'entertainment', 'personal', 'work_business'],
            'savings' => [], // New category, empty initially
        ];
        
        // Store old super category IDs for mapping
        $oldSuperCategoryIds = [];
        foreach (['housing', 'transportation', 'food', 'utilities', 'health', 'insurance', 'education', 'entertainment', 'personal', 'work_business', 'other'] as $oldName) {
            $old = DB::table('expense_super_categories')->where('name', $oldName)->where('is_system', true)->first();
            if ($old) {
                $oldSuperCategoryIds[$oldName] = $old->id;
            }
        }
        
        // Create new 3-tier super categories with allocation percentages
        $newSuperCategories = [
            'essentials' => ['id' => null, 'percentage' => 50.00],
            'lifestyle' => ['id' => null, 'percentage' => 30.00],
            'savings' => ['id' => null, 'percentage' => 20.00],
        ];
        
        foreach ($newSuperCategories as $name => $data) {
            $id = DB::table('expense_super_categories')->insertGetId([
                'name' => $name,
                'is_system' => true,
                'user_id' => null,
                'allocation_percentage' => $data['percentage'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $newSuperCategories[$name]['id'] = $id;
        }
        
        // Map expense categories to new super categories
        foreach ($categoryMapping as $newSuperName => $oldSuperNames) {
            $newSuperId = $newSuperCategories[$newSuperName]['id'];
            
            foreach ($oldSuperNames as $oldSuperName) {
                if (isset($oldSuperCategoryIds[$oldSuperName])) {
                    $oldSuperId = $oldSuperCategoryIds[$oldSuperName];
                    // Update all expense categories pointing to old super category
                    DB::table('expense_categories')
                        ->where('expense_super_category_id', $oldSuperId)
                        ->update(['expense_super_category_id' => $newSuperId]);
                }
            }
        }
        
        // Map 'other' category to 'lifestyle' as default
        if (isset($oldSuperCategoryIds['other'])) {
            $otherSuperId = $oldSuperCategoryIds['other'];
            DB::table('expense_categories')
                ->where('expense_super_category_id', $otherSuperId)
                ->update(['expense_super_category_id' => $newSuperCategories['lifestyle']['id']]);
        }
        
        // Delete old super categories (they should be empty now)
        foreach ($oldSuperCategoryIds as $oldSuperId) {
            DB::table('expense_super_categories')->where('id', $oldSuperId)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This is a destructive migration. Reversing would require
        // restoring the original 11 super categories and remapping categories.
        // For safety, we'll just remove the new super categories.
        DB::table('expense_super_categories')
            ->whereIn('name', ['essentials', 'lifestyle', 'savings'])
            ->where('is_system', true)
            ->delete();
    }
};
