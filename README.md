# Savings Manager

A comprehensive savings management application built with Laravel 12 and Filament 4. Track income, expenses, and savings goals with full bilingual support (English/Greek).

## Features

- **Income Management**: Track income entries with categories
- **Expense Management**: Track expenses with categories and super categories
- **Savings Goals**: Create individual or joint savings goals with progress tracking
- **Bilingual Support**: Full English/Greek translation support
- **Custom Categories**: Users can create their own income and expense categories
- **Analytics & Reports**: View expenses per category, income trends, and month-over-month comparisons
- **Mobile Responsive**: Works seamlessly on mobile and desktop browsers

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Node.js 20.19+ or 22.12+ (for frontend assets)
- npm or yarn

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd SavingsManager
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

### 5. Configure Database (MySQL)

Edit the `.env` file and update the database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=savings_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**Create the MySQL Database:**

```sql
CREATE DATABASE savings_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or using MySQL command line:

```bash
mysql -u root -p -e "CREATE DATABASE savings_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 6. Generate Application Key

```bash
php artisan key:generate
```

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Seed Database

This will populate the database with initial income categories, expense categories, and super categories:

```bash
php artisan db:seed
```

### 9. Build Frontend Assets

```bash
npm run build
```

Or for development:

```bash
npm run dev
```

### 10. Create a User

You can create a user using Laravel Tinker:

```bash
php artisan tinker
```

Then run:

```php
User::create([
    'name' => 'Your Name',
    'email' => 'your@email.com',
    'password' => Hash::make('your-password'),
]);
```

Or use the Filament admin panel at `/admin` to register a new user.

### 11. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

Access the admin panel at: `http://localhost:8000/admin`

## Database Setup Details

### MySQL Configuration

The application uses MySQL with the following requirements:

- **Character Set**: `utf8mb4`
- **Collation**: `utf8mb4_unicode_ci` (supports Greek characters)

**Recommended MySQL Configuration** (in `my.cnf` or `my.ini`):

```ini
[mysqld]
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
default-authentication-plugin=mysql_native_password
```

### PostgreSQL Configuration

If using PostgreSQL, update `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=savings_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Translation System

The application uses Laravel's built-in translation system. All UI strings are translatable.

### Language Files

- English: `lang/en/`
- Greek: `lang/el/`

### Translation Keys

- Common strings: `lang/{locale}/common.php`
- Category names: `lang/{locale}/categories.php`

### Changing Language

The application language is controlled by the `APP_LOCALE` setting in `.env`:

```env
APP_LOCALE=en  # or 'el' for Greek
```

## Project Structure

```
app/
├── Filament/
│   ├── Resources/          # Filament resources (CRUD)
│   └── Pages/              # Custom pages
├── Models/                 # Eloquent models
└── Services/               # Business logic services

database/
├── migrations/             # Database migrations
└── seeders/                # Database seeders

lang/
├── en/                     # English translations
└── el/                     # Greek translations

tests/
├── Unit/                   # Unit tests
├── Feature/                # Feature tests
├── Browser/                # Playwright E2E tests
└── Smoke/                  # Smoke tests
```

## Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suites

```bash
# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature

# Browser tests (Playwright)
npm run test:e2e
```

### Playwright Setup

Playwright is configured for end-to-end testing. Install browsers:

```bash
npx playwright install --with-deps chromium
```

## Development

### Code Style

The project uses Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

### Database Migrations

Create a new migration:

```bash
php artisan make:migration create_table_name
```

Run migrations:

```bash
php artisan migrate
```

Rollback last migration:

```bash
php artisan migrate:rollback
```

### Database Seeders

Run seeders:

```bash
php artisan db:seed
```

Run specific seeder:

```bash
php artisan db:seed --class=IncomeCategorySeeder
```

## Production Deployment

### 1. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 2. Build Assets

```bash
npm run build
```

### 3. Set Environment

Ensure `.env` has:

```env
APP_ENV=production
APP_DEBUG=false
```

### 4. Run Migrations

```bash
php artisan migrate --force
```

## Troubleshooting

### Database Connection Issues

1. Verify database credentials in `.env`
2. Ensure database exists
3. Check MySQL/PostgreSQL service is running
4. Verify user has proper permissions

### Migration Errors

If you encounter migration errors:

```bash
# Fresh migration (WARNING: This will drop all tables)
php artisan migrate:fresh --seed
```

### Translation Not Working

1. Clear config cache: `php artisan config:clear`
2. Verify language files exist in `lang/{locale}/`
3. Check `APP_LOCALE` in `.env`

### Permission Issues

Ensure storage and cache directories are writable:

```bash
chmod -R 775 storage bootstrap/cache
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write tests
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For issues and questions, please open an issue on the repository.
