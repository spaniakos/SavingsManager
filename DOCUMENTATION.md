# Technical Documentation

## Architecture

### Framework Stack

- **Backend**: Laravel 12.x
- **Admin Panel**: Filament 4.x
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Frontend**: Tailwind CSS 4.x, Chart.js
- **Testing**: PHPUnit, Playwright

### Project Structure

```
app/
├── Filament/
│   ├── Resources/     # CRUD resources
│   ├── Pages/         # Custom pages
│   └── Widgets/       # Dashboard widgets
├── Models/            # Eloquent models
├── Services/           # Business logic services
└── Http/
    └── Controllers/   # Web controllers

database/
├── migrations/        # Database migrations
├── seeders/          # Data seeders
└── factories/        # Model factories
```

## Database Schema

### Core Tables

- `users` - User accounts with financial settings
- `income_categories` - Income category definitions
- `income_entries` - Income transactions
- `expense_super_categories` - Fixed super categories (Essentials, Lifestyle, Savings)
- `expense_categories` - Expense category definitions
- `expense_entries` - Expense transactions
- `savings_goals` - Savings goal definitions
- `savings_goal_members` - Joint goal memberships
- `savings_contributions` - Individual contributions
- `recurring_expenses` - Recurring expense templates
- `category_allocation_goals` - Budget allocation goals

## Key Services

### SavingsCalculatorService

Calculates savings goal progress, monthly savings needed, and remaining months.

### BudgetAllocationService

Manages 50/30/20 budget allocation, calculates allowances, and tracks spending.

### RecurringExpenseService

Handles auto-generation of recurring expense entries based on frequency.

### JointGoalService

Manages joint savings goals: invitations, permissions, contributions.

### ReportService

Generates financial reports (monthly, category, savings goals) and exports.

## Translation System

All UI strings use Laravel's translation system:

- `lang/en/common.php` - English translations
- `lang/el/common.php` - Greek translations
- `lang/en/categories.php` - Category names (English)
- `lang/el/categories.php` - Category names (Greek)

Usage: `__('common.key')` or `__('categories.key')`

## User Data Isolation

All queries are automatically filtered by `user_id`:

```php
// Models automatically scope to current user
ExpenseEntry::where('user_id', Auth::id())->get();
```

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

- `tests/Unit/` - Service unit tests
- `tests/Feature/` - CRUD and integration tests
- `tests/Smoke/` - Critical path tests
- `tests/Browser/` - Playwright E2E tests

## Development Guidelines

See [RULES.md](RULES.md) for detailed development guidelines, coding standards, and architecture decisions.
