# Savings Manager

[![Tests](https://github.com/spaniakos/SavingsManager/actions/workflows/tests.yml/badge.svg)](https://github.com/spaniakos/SavingsManager/actions)
[![Tests](https://github.com/spaniakos/SavingsManager/actions/workflows/ci.yml/badge.svg)](https://github.com/spaniakos/SavingsManager/actions)
[![Tests](https://github.com/spaniakos/SavingsManager/actions/workflows/lint.yml/badge.svg)](https://github.com/spaniakos/SavingsManager/actions)
![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel&logoColor=white)
![PHP Version](https://img.shields.io/badge/PHP-8.4-blue)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Code Style: Laravel Pint](https://img.shields.io/badge/Code_Style-Laravel_Pint-orange)

A comprehensive personal savings management application built with Laravel 12 and Filament 4. Track income, expenses, and savings goals with full bilingual support (English/Greek) and a mobile-first responsive interface.

## 🚀 Why This Program Was Created

This project wasn't born from a technical challenge alone — it comes from a real-life goal and a shared dream. We are building this software to help us save money and stay organized as we work toward three major milestones in our life:

- **Our marriage** — creating the financial stability and peace of mind to start our life together.
- **A master's degree in Swansea** — supporting my spouse in completing a postgraduate program abroad, which requires careful planning, budgeting, and long-term focus.
- **A future home and speech therapy center** — our vision is to move out of the city, build a house of our own, create a dedicated speech therapy practice, and grow my IT business in parallel.

This program exists to bring clarity, structure, and momentum to those goals. It's more than a tool — it's a roadmap for the life we're building.

## 🌟 Purpose

This application helps individuals manage their finances, track savings goals, and maintain financial discipline through:
- Detailed income and expense tracking
- Savings goal management with progress visualization
- Budget allocation system with customizable percentages
- Monthly calculation to adjust savings goals based on net savings
- Positive reinforcement and milestone notifications

## ✨ Key Features

- **Income & Expense Management**: Track all financial transactions with detailed categorization
- **Person Tracking**: Assign income and expense entries to specific persons (household members) for better tracking
- **Personal Expense Flag**: Mark expenses as personal to separate personal spending from shared household expenses
- **Savings Goals**: Create and track savings goals with progress visualization
- **Budget System**: 3-tier allocation (Essentials, Lifestyle, Savings) with customizable percentages
- **Save-for-Later**: Mark expenses as savings that directly add to savings goals
- **Budget Tracking**: Real-time monitoring of spent vs allowance per super category
- **Monthly Calculation**: One-click calculation to adjust savings goals based on previous month's net savings
- **Comprehensive Reporting**: 
  - Generate detailed reports with hierarchical breakdowns (by item, category, super category) and PDF export
  - Person-based filtering and breakdowns
  - Personal vs non-personal expense summaries
  - Household contribution breakdown showing non-personal expenses by person
- **Mobile-First UI**: Responsive mobile interface with emoji-enabled category buttons
- **Analytics Dashboard**: Interactive charts showing income trends, expense trends, and savings progress
- **Bilingual Support**: Full English/Greek translation
- **Date Restrictions**: Entries locked after monthly calculation to maintain data integrity

## 🚀 Quick Start

### Requirements

- PHP 8.4+
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Node.js 20.19+ or 22.12+
- npm or yarn

### Installation

```bash
# Clone repository
git clone <repository-url>
cd SavingsManager

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=savings_manager
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations and seeders
php artisan migrate:fresh --seed

# Build assets
npm run build

# Start development server
php artisan serve
```

Visit `http://localhost:8000`:
- Mobile app: `/mobile/login` (for regular users)
- Admin panel: `/admin/login` (for admin users only)

**Default Test Users** (created by seeders):
- Regular User: `test@makeasite.gr` / `12341234` (mobile app access)
- Admin User: `admin@makeasite.gr` / `12341234` (admin panel + mobile app access)

For detailed installation instructions, see [INSTALLATION.md](INSTALLATION.md).

### Updating

To update an existing installation:

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install
npm install

# Run new migrations
php artisan migrate

# Rebuild assets
npm run build

# Clear caches (optional but recommended)
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

**Note**: Always backup your database before running migrations in production!

## 📚 Documentation

- [INSTALLATION.md](INSTALLATION.md) - Detailed setup guide
- [DOCUMENTATION.md](DOCUMENTATION.md) - Technical documentation
- [RULES.md](RULES.md) - Development guidelines
- [TEST_RESULTS.md](TEST_RESULTS.md) - Test results and coverage
- [TEST_USER_INFO.md](TEST_USER_INFO.md) - Test user information
- [AUTHENTICATION_TESTING_SUMMARY.md](AUTHENTICATION_TESTING_SUMMARY.md) - Authentication testing details

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run E2E tests (requires server running)
php artisan serve  # In one terminal
npm run test:e2e   # In another terminal
```

**Test Coverage**: 56 tests, 124 assertions - All passing ✅

See [TEST_RESULTS.md](TEST_RESULTS.md) for detailed test results and [TEST_USER_INFO.md](TEST_USER_INFO.md) for test user credentials.

## 🏗️ Architecture

- **Backend**: Laravel 12.x
- **Admin Panel**: Filament 4.x (mobile-first)
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Frontend**: Tailwind CSS 4.x, Chart.js
- **Testing**: PHPUnit, Playwright
- **CI/CD**: GitHub Actions

## 🔮 Future Work

The application is production-ready with all core features complete. Planned enhancements include:

### High Priority
- **Recurring Expenses System**: Automatically generate expense entries on scheduled dates (daily, weekly, monthly, yearly, or custom intervals)
- **Category Filtering Enhancements**: Improve UX with dynamic category filtering based on super category selection
- **Calendar View**: Comprehensive calendar interface for viewing and managing entries with day/week/month views
- **Translation Management**: Admin interface for managing translations with conflict resolution and automatic key generation
- **Multi-Goal Savings Enhancement**: Support allocating savings to multiple goals with automatic distribution logic

### Medium Priority
- **Joint Accounts/Efforts System**: Support for shared financial goals and contributions between multiple users
- **Data Import/Export**: CSV/Excel import/export functionality for bulk data management
- **PWA Support**: Progressive Web App features for mobile installation
- **Social Login**: OAuth integration for Google, Facebook, etc.

### Low Priority
- **Mobile Apps**: Native iOS and Android applications
- **SaaS & AI Integration**: Subscription system and AI-powered financial insights

For detailed task breakdowns, see [TODO.md](TODO.md).

## 📝 License

MIT License - See [LICENSE](LICENSE) file

## 🤝 Contributing

This is a personal project, but contributions are welcome. Please read [RULES.md](RULES.md) for development guidelines.
