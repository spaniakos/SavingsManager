# Savings Manager

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
- **Savings Goals**: Create and track savings goals with progress visualization
- **Budget System**: 3-tier allocation (Essentials, Lifestyle, Savings) with customizable percentages
- **Save-for-Later**: Mark expenses as savings that directly add to savings goals
- **Budget Tracking**: Real-time monitoring of spent vs allowance per super category
- **Monthly Calculation**: One-click calculation to adjust savings goals based on previous month's net savings
- **Comprehensive Reporting**: Generate detailed reports with hierarchical breakdowns (by item, category, super category) and PDF export
- **Mobile-First UI**: Responsive mobile interface with emoji-enabled category buttons
- **Analytics Dashboard**: Interactive charts showing income trends, expense trends, and savings progress
- **Bilingual Support**: Full English/Greek translation
- **Date Restrictions**: Entries locked after monthly calculation to maintain data integrity

## 🚀 Quick Start

### Requirements

- PHP 8.2+
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

Visit `http://localhost:8000` and navigate to `/admin/login` to access the application.

**Default Test User**: `test@makeasite.gr` / `12341234` (created by seeders)

For detailed installation instructions, see [INSTALLATION.md](INSTALLATION.md).

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

## 📝 License

MIT License - See [LICENSE](LICENSE) file

## 🤝 Contributing

This is a personal project, but contributions are welcome. Please read [RULES.md](RULES.md) for development guidelines.
