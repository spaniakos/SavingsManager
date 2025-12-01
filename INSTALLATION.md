# Installation Guide

This guide provides step-by-step instructions for installing and setting up the Savings Manager application.

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.2+** with extensions:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
- **Composer** (PHP package manager)
- **MySQL 8.0+** or **PostgreSQL 13+**
- **Node.js 20.19+** or **22.12+**
- **npm** or **yarn**

## Step-by-Step Installation

### Step 1: Clone or Download the Project

```bash
git clone <repository-url>
cd SavingsManager
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

This will install all required PHP packages including Laravel 12, Filament 4, and other dependencies.

### Step 3: Install Node Dependencies

```bash
npm install
```

This installs frontend dependencies including Vite, Tailwind CSS, and Playwright for testing.

### Step 4: Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

### Step 5: Set Up MySQL Database

#### Option A: Using MySQL Command Line

1. Log into MySQL:

```bash
mysql -u root -p
```

2. Create the database:

```sql
CREATE DATABASE savings_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. Create a user (optional, recommended for production):

```sql
CREATE USER 'savings_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON savings_manager.* TO 'savings_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Option B: Using phpMyAdmin or MySQL Workbench

1. Open phpMyAdmin or MySQL Workbench
2. Create a new database named `savings_manager`
3. Set character set to `utf8mb4` and collation to `utf8mb4_unicode_ci`

### Step 6: Configure Database in .env

Open `.env` file and update the database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=savings_manager
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

**For production or custom user:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=savings_manager
DB_USERNAME=savings_user
DB_PASSWORD=your_secure_password
```

### Step 7: Generate Application Key

```bash
php artisan key:generate
```

This generates a unique encryption key for your application.

### Step 8: Run Database Migrations

Create all database tables:

```bash
php artisan migrate
```

You should see output like:

```
INFO  Running migrations.

2025_12_01_101620_create_income_categories_table ........ DONE
2025_12_01_101621_create_expense_super_categories_table ... DONE
...
```

### Step 9: Seed Initial Data

Populate the database with initial categories:

```bash
php artisan db:seed
```

This will create:
- Income categories (Salary, Business, Rental, etc.)
- Expense super categories (Housing, Transportation, Food, etc.)
- Expense categories (all categories mapped to super categories)

### Step 10: Build Frontend Assets

For production:

```bash
npm run build
```

For development (with hot reload):

```bash
npm run dev
```

### Step 11: Create Your First User

#### Option A: Using Laravel Tinker

```bash
php artisan tinker
```

Then run:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
]);
```

#### Option B: Using Filament Registration

1. Start the server: `php artisan serve`
2. Visit: `http://localhost:8000/admin/register`
3. Fill in the registration form

### Step 12: Start the Development Server

```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

Access the admin panel at: **http://localhost:8000/admin**

## Verification

### Check Database Connection

```bash
php artisan tinker
```

```php
DB::connection()->getPdo();
// Should return: PDO object without errors
```

### Check Migrations Status

```bash
php artisan migrate:status
```

All migrations should show as "Ran".

### Check Seeded Data

```bash
php artisan tinker
```

```php
App\Models\IncomeCategory::count();
// Should return: 13 (or number of seeded categories)

App\Models\ExpenseCategory::count();
// Should return: number of seeded expense categories
```

## Common Issues and Solutions

### Issue: "SQLSTATE[HY000] [2002] Connection refused"

**Solution**: 
- Check MySQL service is running: `sudo service mysql start` (Linux) or start MySQL service (Windows/Mac)
- Verify `DB_HOST` in `.env` is correct (usually `127.0.0.1` or `localhost`)

### Issue: "SQLSTATE[HY000] [1045] Access denied"

**Solution**:
- Verify database username and password in `.env`
- Ensure user has permissions: `GRANT ALL PRIVILEGES ON savings_manager.* TO 'user'@'localhost';`

### Issue: "SQLSTATE[42000] [1049] Unknown database"

**Solution**:
- Create the database: `CREATE DATABASE savings_manager;`
- Or update `DB_DATABASE` in `.env` to match existing database

### Issue: "Class 'PDO' not found"

**Solution**:
- Install PHP PDO extension: `sudo apt-get install php-pdo php-mysql` (Linux)
- Enable extension in `php.ini`: `extension=pdo_mysql`

### Issue: Migration errors after changing schema

**Solution**:
```bash
# WARNING: This will drop all tables and data
php artisan migrate:fresh --seed
```

### Issue: Translations not showing

**Solution**:
1. Clear config cache: `php artisan config:clear`
2. Check `APP_LOCALE` in `.env` (should be `en` or `el`)
3. Verify language files exist in `lang/en/` and `lang/el/`

## Production Setup

### 1. Update .env for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your_production_db_host
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_production_user
DB_PASSWORD=your_secure_production_password
```

### 2. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 3. Set Proper Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Run Migrations

```bash
php artisan migrate --force
```

### 5. Build Assets

```bash
npm run build
```

## Next Steps

After installation:

1. Log in to the admin panel
2. Create your first income entry
3. Add expense entries
4. Create a savings goal
5. Explore the dashboard and reports

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [MySQL Documentation](https://dev.mysql.com/doc/)

