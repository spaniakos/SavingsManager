# Savings Manager

A comprehensive personal savings management application built with Laravel 12 and Filament 4. Track income, expenses, and savings goals with full bilingual support (English/Greek).

## 🚀 Why This Program Was Created
This project wasn’t born from a technical challenge alone — it comes from a real-life goal and a shared dream. We are building this software to help us save money and stay organized as we work toward three major milestones in our life:

Our marriage — creating the financial stability and peace of mind to start our life together.
A master’s degree in Swansea — supporting my spouse in completing a postgraduate program abroad, which requires careful planning, budgeting, and long-term focus.
A future home and speech therapy center — our vision is to move out of the city, build a house of our own, create a dedicated speech therapy practice, and grow my IT business in parallel.
This program exists to bring clarity, structure, and momentum to those goals. It’s more than a tool — it’s a roadmap for the life we’re building.

## 🌟 Purpose

This application helps individuals and couples manage their finances, track savings goals, and maintain financial discipline through:
- Detailed income and expense tracking
- Savings goal management with progress visualization
- Budget allocation system (50/30/20 rule)
- Joint savings goals for collaborative financial planning
- Positive reinforcement and milestone notifications

## ✨ Key Features

- **Income & Expense Management**: Track all financial transactions with detailed categorization
- **Savings Goals**: Create individual or joint goals with progress tracking
- **Budget System**: 3-tier allocation (Essentials 50%, Lifestyle 30%, Savings 20%)
- **Recurring Expenses**: Automatically generate recurring expense entries
- **Save-for-Later**: Set savings targets on categories with progress tracking
- **Budget Tracking**: Real-time monitoring of spent vs allowance per category
- **Joint Goals**: Invite members, track contributions, manage permissions
- **Reporting**: Generate monthly, category, and savings goal reports (CSV/JSON export)
- **Analytics Dashboard**: Interactive charts and visualizations
- **Bilingual Support**: Full English/Greek translation

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
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

Visit `http://localhost:8000` and navigate to `/admin/login` to access the application.

For detailed installation instructions, see [INSTALLATION.md](INSTALLATION.md).

## 📚 Documentation

- [INSTALLATION.md](INSTALLATION.md) - Detailed setup guide
- [DOCUMENTATION.md](DOCUMENTATION.md) - Technical documentation
- [RULES.md](RULES.md) - Development guidelines
- [RELEASE_NOTES.md](RELEASE_NOTES.md) - Version history

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run E2E tests
npm run test:e2e
```

**Test Coverage**: 44 tests, 88 assertions - All passing ✅

## 📝 License

MIT License - See [LICENSE](LICENSE) file

## 🤝 Contributing

This is a personal project, but contributions are welcome. Please read [RULES.md](RULES.md) for development guidelines.
