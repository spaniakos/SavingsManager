<!-- a075b550-433b-45a5-8b57-29025f912842 334ad1f3-5099-4621-8c6e-bdf53dd34cbd -->
# Fix MySQL Migration Errors

## Issues Identified

1. **Table Already Exists Error**: Migration `2025_12_01_101621_create_expense_categories_table.php` fails because table already exists
2. **Migration Order Conflict**: `2025_12_01_102128_update_categories_to_use_translations.php` drops and recreates tables that earlier migrations try to create
3. **Restructure Migration**: `2025_12_01_132817_restructure_expense_super_categories_to_three_tier.php` may fail if old super categories don't exist
4. **Column Name Consistency**: Verify `invited_by` vs `invited_by_user_id` consistency

## Solution

### 1. Fix Migration Order - Update Categories Migration

**File**: `database/migrations/2025_12_01_102128_update_categories_to_use_translations.php`

- Change `Schema::dropIfExists()` to check if tables exist before dropping
- Add conditional logic to only drop if tables exist
- Use `Schema::hasTable()` checks

### 2. Fix Restructure Migration

**File**: `database/migrations/2025_12_01_132817_restructure_expense_super_categories_to_three_tier.php`

- Add checks for existing old super categories before trying to map
- Handle case where old categories don't exist (fresh install)
- Only delete old categories if they exist and are empty
- Add safety checks for foreign key constraints

### 3. Fix Invitation Fields Migration

**File**: `database/migrations/2025_12_01_141848_add_invitation_fields_to_savings_goal_members_table.php`

- Verify column name is `invited_by` (not `invited_by_user_id`)
- Ensure foreign key constraint name is unique
- Add check to prevent adding columns if they already exist

### 4. Add Migration Safety Checks

For all migrations that modify existing tables:

- Check if columns exist before adding
- Check if foreign keys exist before dropping
- Use `Schema::hasColumn()` and `Schema::hasTable()` checks

### 5. Update Migration to Handle Existing Database

**File**: `database/migrations/2025_12_01_101621_create_expense_categories_table.php` and related create migrations

- Change `Schema::create()` to `Schema::createIfNotExists()` or add existence checks
- Or mark these migrations as conditional based on whether update migration has run

## Implementation Steps

1. Update `2025_12_01_102128_update_categories_to_use_translations.php` to safely handle existing tables
2. Update `2025_12_01_132817_restructure_expense_super_categories_to_three_tier.php` to handle empty database
3. Add existence checks to `2025_12_01_141848_add_invitation_fields_to_savings_goal_members_table.php`
4. Test migrations on fresh MySQL database
5. Test migrations on existing database with data

## Testing

After fixes:

- Run `php artisan migrate:fresh` to test on clean database
- Run `php artisan migrate` to test on existing database
- Verify all tables are created correctly
- Verify foreign key constraints work
- Verify enum columns work with MySQL

### To-dos

- [ ] Add total saved money calculation to reports (seed_capital + savings goals + save_for_later)
- [ ] Create migration to add emoji column to expense_super_categories, expense_categories, income_categories
- [ ] Create seeder to set default emojis based on category names
- [ ] Update ExpenseSuperCategory, ExpenseCategory, IncomeCategory models to include emoji field
- [ ] Add emoji input fields to category forms in admin panel
- [ ] Create DetectMobile middleware for auto-redirecting mobile users
- [ ] Add mobile routes (/admin/mobile, /admin/mobile/expense, etc.)
- [ ] Create mobile controllers for dashboard, expense, income, settings
- [ ] Create mobile Blade views with bottom navigation and category buttons
- [ ] Create mobile-optimized layout with bottom navigation component
- [ ] Add missing translations for mobile UI and emoji-related labels