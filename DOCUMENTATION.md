# Savings Manager - Documentation

## Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Translation System](#translation-system)
4. [Database Schema](#database-schema)
5. [API Documentation](#api-documentation)
6. [Testing](#testing)
7. [Deployment](#deployment)

## Overview

Savings Manager is a comprehensive financial tracking application that helps individuals and couples manage their income, expenses, and savings goals. The application is built with Laravel 12 and Filament 4, providing a modern, responsive interface that works on both desktop and mobile devices.

### Key Features

- **Income Tracking**: Record income entries with categories
- **Expense Tracking**: Record expenses with categories and super categories
- **Savings Goals**: Create individual or joint savings goals with progress tracking
- **Bilingual Support**: Full English/Greek translation
- **Custom Categories**: Users can create their own categories
- **Analytics**: View expenses per category, income trends, and month-over-month comparisons
- **Reports**: Generate monthly and category-wise reports

## Architecture

### Technology Stack

- **Backend**: Laravel 12.x
- **Admin Panel**: Filament 4.x
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Frontend**: Filament (Livewire 3)
- **Testing**: PHPUnit, Playwright
- **Language**: PHP 8.2+

### Project Structure

```
app/
├── Filament/
│   ├── Resources/          # CRUD resources
│   │   ├── IncomeEntries/
│   │   ├── ExpenseEntries/
│   │   ├── SavingsGoals/
│   │   ├── IncomeCategories/
│   │   ├── ExpenseCategories/
│   │   └── ExpenseSuperCategories/
│   ├── Pages/              # Custom pages
│   │   └── Dashboard.php
│   └── Widgets/            # Dashboard widgets
│       ├── SavingsGoalProgressWidget.php
│       ├── ExpensesByCategoryChart.php
│       ├── IncomeTrendsChart.php
│       └── MoMSavingsChart.php
├── Models/                 # Eloquent models
└── Services/               # Business logic
    ├── SavingsCalculatorService.php
    └── ChartDataService.php

database/
├── migrations/            # Database migrations
└── seeders/               # Data seeders

lang/
├── en/                    # English translations
│   ├── common.php
│   └── categories.php
└── el/                    # Greek translations
    ├── common.php
    └── categories.php

tests/
├── Unit/                  # Unit tests
├── Feature/               # Feature tests
├── Browser/               # Playwright E2E tests
└── Smoke/                 # Smoke tests
```

## Translation System

The application uses Laravel's built-in translation system. All user-facing strings are translatable.

### Translation Files

- **Common strings**: `lang/{locale}/common.php`
- **Category names**: `lang/{locale}/categories.php`

### Using Translations

In PHP code:

```php
__('common.amount')
__('categories.income.salary')
```

In Blade templates:

```blade
{{ __('common.app_name') }}
```

### Adding New Translations

1. Add the key to `lang/en/common.php` or `lang/en/categories.php`
2. Add the Greek translation to `lang/el/common.php` or `lang/el/categories.php`
3. Use `__('file.key')` in your code

### Changing Language

Set `APP_LOCALE` in `.env`:

```env
APP_LOCALE=en  # English
APP_LOCALE=el  # Greek
```

## Database Schema

### Tables

#### `users`
- Standard Laravel users table
- Extended with relationships to all financial data

#### `income_categories`
- `id`, `name` (translation key), `is_system`, `user_id`, `timestamps`

#### `income_entries`
- `id`, `user_id`, `income_category_id`, `amount`, `date`, `notes`, `timestamps`

#### `expense_super_categories`
- `id`, `name` (translation key), `is_system`, `user_id`, `timestamps`

#### `expense_categories`
- `id`, `name` (translation key), `expense_super_category_id`, `is_system`, `user_id`, `timestamps`

#### `expense_entries`
- `id`, `user_id`, `expense_category_id`, `amount`, `date`, `notes`, `timestamps`

#### `savings_goals`
- `id`, `user_id`, `name`, `target_amount`, `current_amount`, `start_date`, `target_date`, `is_joint`, `timestamps`

#### `savings_goal_members`
- `id`, `savings_goal_id`, `user_id`, `timestamps`
- Pivot table for joint goals

#### `savings_contributions`
- `id`, `savings_goal_id`, `user_id`, `amount`, `date`, `notes`, `timestamps`

### Relationships

- User hasMany IncomeEntry, ExpenseEntry, SavingsGoal
- IncomeCategory hasMany IncomeEntry
- ExpenseSuperCategory hasMany ExpenseCategory
- ExpenseCategory belongsTo ExpenseSuperCategory, hasMany ExpenseEntry
- SavingsGoal belongsTo User, belongsToMany User (members), hasMany SavingsContribution

## Services

The application uses service classes to encapsulate business logic and calculations.

### SavingsCalculatorService

Located at `app/Services/SavingsCalculatorService.php`, this service handles all savings goal calculations.

#### Methods

- `calculateMonthlySavingNeeded(SavingsGoal $goal): float`
  - Calculates the monthly saving amount needed to reach the goal
  - Formula: `(target_amount - current_amount) / months_remaining`
  - Returns 0 if goal is reached or past due

- `calculateMonthsRemaining(SavingsGoal $goal): int`
  - Calculates months remaining until target date
  - Returns 0 if target date has passed

- `calculateOverallProgress(SavingsGoal $goal): float`
  - Calculates overall progress percentage (0-100)
  - Formula: `(current_amount / target_amount) * 100`

- `calculateMonthlyProgress(SavingsGoal $goal): float`
  - Calculates current month progress percentage (0-100)
  - Compares current month savings vs monthly target needed

- `calculateCurrentMonthSavings(?int $userId = null): float`
  - Calculates total savings for the current month
  - Formula: `total_income - total_expenses` for current month

- `calculateProjectedSavings(SavingsGoal $goal): float`
  - Projects savings if no additional spending occurs
  - Uses current month savings as baseline

### ChartDataService

Located at `app/Services/ChartDataService.php`, this service aggregates data for charts and visualizations.

#### Methods

- `getExpensesByCategory(?Carbon $startDate, ?Carbon $endDate, ?int $userId): array`
  - Aggregates expenses by category for a given period
  - Returns array with category names and amounts

- `getExpensesByItem(?Carbon $startDate, ?Carbon $endDate, ?int $userId): array`
  - Aggregates expenses by individual items within categories
  - Returns detailed breakdown

- `getIncomeTrends(?Carbon $startDate, ?Carbon $endDate, ?int $userId): array`
  - Calculates income trends over time
  - Returns monthly income totals

- `getMoMSavingsComparison(int $months, ?int $userId): array`
  - Compares savings month-over-month
  - Returns array with monthly savings data

- `formatForPieChart(array $data): array`
  - Formats data for pie chart widgets
  - Returns Chart.js compatible format

- `formatForBarChart(array $data): array`
  - Formats data for bar chart widgets
  - Returns Chart.js compatible format

- `formatForMoMChart(array $data): array`
  - Formats month-over-month data for charts
  - Returns Chart.js compatible format

## Widgets

The application includes several Filament widgets for dashboard visualization.

### SavingsGoalProgressWidget

Located at `app/Filament/Widgets/SavingsGoalProgressWidget.php`, displays dual progress bars for savings goals.

**Features:**
- Overall goal progress bar (total saved vs target)
- Monthly progress bar (current month savings vs monthly target)
- Shows monthly saving needed, months remaining
- Displays projected savings message
- Game-like XP bar styling

**View:** `resources/views/filament/widgets/savings-goal-progress-widget.blade.php`

### ExpensesByCategoryChart

Located at `app/Filament/Widgets/ExpensesByCategoryChart.php`, displays expenses by category as a pie chart.

**Chart Type:** Pie
**Data Source:** ChartDataService::getExpensesByCategory()

### IncomeTrendsChart

Located at `app/Filament/Widgets/IncomeTrendsChart.php`, displays income trends over time as a line chart.

**Chart Type:** Line
**Data Source:** ChartDataService::getIncomeTrends()

### MoMSavingsChart

Located at `app/Filament/Widgets/MoMSavingsChart.php`, displays month-over-month savings comparison as a bar chart.

**Chart Type:** Bar
**Data Source:** ChartDataService::getMoMSavingsComparison()

**Note:** All widgets are auto-discovered by Filament and appear on the dashboard automatically.

## API Documentation

### Filament Resources

All resources are accessible through the Filament admin panel at `/admin`.

#### Income Entries
- **List**: `/admin/income-entries`
- **Create**: `/admin/income-entries/create`
- **Edit**: `/admin/income-entries/{id}/edit`

#### Expense Entries
- **List**: `/admin/expense-entries`
- **Create**: `/admin/expense-entries/create`
- **Edit**: `/admin/expense-entries/{id}/edit`

#### Savings Goals
- **List**: `/admin/savings-goals`
- **Create**: `/admin/savings-goals/create`
- **Edit**: `/admin/savings-goals/{id}/edit`

#### Category Management
- **Income Categories**: `/admin/income-categories`
- **Expense Categories**: `/admin/expense-categories`
- **Expense Super Categories**: `/admin/expense-super-categories`

#### Dashboard
- **Dashboard**: `/admin` (default Filament dashboard with widgets)

## Testing

### Test Structure

```
tests/
├── Unit/
│   ├── Services/          # Service tests
│   ├── Models/            # Model tests
│   └── Calculations/      # Calculation tests
├── Feature/               # Feature tests
├── Browser/               # Playwright E2E tests
└── Smoke/                 # Smoke tests
```

### Running Tests

```bash
# All tests
php artisan test

# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature

# Playwright E2E tests
npm run test:e2e
```

### Test Coverage

Each feature should have:
- Unit tests for services and calculations
- Feature tests for CRUD operations
- Playwright tests for UI workflows
- Smoke tests for critical paths

## Deployment

### Production Checklist

1. **Environment Configuration**
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Configure production database
   - Set secure `APP_KEY`

2. **Optimize Application**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```

3. **Build Assets**
   ```bash
   npm run build
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate --force
   ```

5. **Set Permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

### Server Requirements

- PHP 8.2+
- MySQL 8.0+ or PostgreSQL 13+
- Web server (Apache/Nginx)
- Composer
- Node.js (for asset building)

### Recommended Server Configuration

**Nginx Example:**

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/savings-manager/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines.

## License

This project is open-sourced software licensed under the [MIT license](../LICENSE).

