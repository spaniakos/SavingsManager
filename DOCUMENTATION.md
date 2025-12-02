# Technical Documentation

## Architecture

### Framework Stack

- **Backend**: Laravel 12.x
- **Admin Panel**: Filament 4.x
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Frontend**: Tailwind CSS 4.x, Chart.js
- **Testing**: PHPUnit, Playwright
- **CI/CD**: GitHub Actions

### Project Structure

```
app/
├── Filament/
│   ├── Resources/     # CRUD resources
│   ├── Pages/         # Custom pages (Reports, Dashboard, Profile Settings)
│   └── Widgets/       # Dashboard widgets
├── Models/            # Eloquent models
├── Services/           # Business logic services
├── Helpers/            # Helper classes (EmojiHelper)
└── Http/
    ├── Controllers/    # Web controllers
    │   └── Mobile/     # Mobile-specific controllers
    └── Middleware/     # Custom middleware (SetLocale, DetectMobile)

database/
├── migrations/        # Database migrations
├── seeders/          # Data seeders
└── factories/        # Model factories

resources/
└── views/
    ├── mobile/       # Mobile-specific Blade views
    └── filament/     # Filament admin views
```

## Database Schema

### Core Tables

- `users` - User accounts with financial settings (seed_capital, median_monthly_income)
- `persons` - Person definitions for tracking household members (user_id, fullname)
- `income_categories` - Income category definitions
- `income_entries` - Income transactions with optional `person_id` for person tracking
- `expense_super_categories` - Fixed super categories (Essentials, Lifestyle, Savings) with allocation percentages
- `expense_categories` - Expense category definitions with emoji support
- `expense_entries` - Expense transactions with `is_save_for_later` flag, `is_personal` flag, and optional `person_id` for person tracking
- `savings_goals` - Savings goal definitions with `current_amount`, `initial_checkpoint`, and `last_monthly_calculation_at`

## Key Services

### SavingsCalculatorService

Calculates savings goal progress, monthly savings needed, remaining months, overall progress, and projected savings.

**Key Methods:**
- `calculateMonthlySavingNeeded()` - Calculates monthly savings required
- `calculateOverallProgress()` - Calculates progress percentage
- `calculateCurrentMonthSavings()` - Calculates net savings for current month
- `getProgressData()` - Comprehensive progress data for a goal

### BudgetAllocationService

Manages budget allocation with customizable percentages per super category, calculates allowances, and tracks spending.

**Key Methods:**
- `getAllocationStatus()` - Current allocation status per super category
- `getRemainingAllowance()` - Remaining budget allowance

### ReportService

Generates comprehensive financial reports with hierarchical breakdowns (by item, category, super category), goal progression tracking, and PDF export functionality.

**Key Methods:**
- `generateComprehensiveReport()` - Full report with all breakdowns, person filtering, and personal expense summaries
- `getExpensesHierarchical()` - Hierarchical expense structure with person and personal expense data
- `getIncomeHierarchical()` - Hierarchical income structure with person data
- `calculateTotalSaved()` - Total saved amount (seed_capital + all goals)
- `calculatePersonalExpenseTotals()` - Calculate personal expense totals by person
- `calculateNonPersonalExpensesByPerson()` - Calculate non-personal expenses by person for household contribution breakdown
- `exportToCsv()` - CSV export functionality

### ChartDataService

Provides chart data for dashboard visualizations (income trends, expense trends, category breakdowns).

### MilestoneNotificationService

Generates positive reinforcement notifications when savings goals reach milestones.

### PositiveReinforcementService

Provides encouragement messages based on budget performance and savings progress.

## Translation System

All UI strings use Laravel's translation system:

- `lang/en/common.php` - English translations
- `lang/el/common.php` - Greek translations
- `lang/en/categories.php` - Category names (English)
- `lang/el/categories.php` - Category names (Greek)

**Usage:** `__('common.key')` or `__('categories.key')`

Categories use the `getTranslatedName()` method which automatically selects the correct language.

## User Data Isolation

All queries are automatically filtered by `user_id`:

```php
// Models automatically scope to current user
ExpenseEntry::where('user_id', Auth::id())->get();
```

## Mobile-First Architecture

The application uses a mobile-first approach:

- **Main Route**: `/admin/mobile` - Mobile dashboard
- **Middleware**: `DetectMobile` - Automatically redirects to mobile interface
- **Responsive UI**: All pages designed for mobile devices first
- **Emoji Support**: Categories use emojis for visual identification
- **Bottom Navigation**: Persistent bottom menu for easy navigation

## Key Features

### Save for Later

Expenses can be marked with `is_save_for_later = true`. These expenses:
- Count towards budget allocation (reduce available budget)
- Directly add to all active savings goals' `current_amount`
- Provide a mechanism to save money from allowances

### Monthly Calculation

The monthly calculation feature:
- Calculates previous month's net savings (income - expenses)
- Directly modifies all savings goals' `current_amount` (+ or -)
- Prevents duplicate calculations via `last_monthly_calculation_at` timestamp
- Locks previous month's entries from editing/deletion

### Date Restrictions

To maintain data integrity:
- After monthly calculation, only current month entries can be created
- Before monthly calculation, current and previous month entries are allowed
- Entries from locked months cannot be edited or deleted

### Comprehensive Reports

Reports support three breakdown types:
- **Per Super Category**: Shows only super category totals
- **Per Category**: Shows super categories with nested category totals
- **Per Item**: Shows full hierarchy (super category → category → individual entries)

## Testing

### Running Tests

```bash
# All tests
php artisan test

# Specific suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# E2E tests
npm run test:e2e
```

### Test Structure

- `tests/Unit/` - Service unit tests (13 tests)
- `tests/Feature/` - CRUD and integration tests (35 tests)
- `tests/Smoke/` - Critical path tests (1 test)
- `tests/Browser/` - Playwright E2E tests (6 test files)

**Total**: 56 tests, 124 assertions - All passing ✅

See [TEST_RESULTS.md](TEST_RESULTS.md) for detailed test coverage.

### Test User

A default test user is automatically created by seeders:
- **Email**: `test@makeasite.gr`
- **Password**: `12341234`

See [TEST_USER_INFO.md](TEST_USER_INFO.md) for more details.

## Development Guidelines

See [RULES.md](RULES.md) for detailed development guidelines, coding standards, and architecture decisions.

## CI/CD

GitHub Actions workflows automatically test the application:
- PHP tests on PHP 8.4
- Code quality checks (Laravel Pint)
- E2E tests with Playwright

See [.github/workflows/README.md](.github/workflows/README.md) for workflow documentation.
