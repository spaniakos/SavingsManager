# Installation Guide

## Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Node.js 20.19+ or 22.12+
- npm or yarn

## Step-by-Step Installation

### 1. Clone Repository

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

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Database Setup

Edit `.env` file and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=savings_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**For MySQL with Greek support**, ensure your database uses `utf8mb4`:

```sql
CREATE DATABASE savings_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Seed Initial Data

```bash
php artisan db:seed
```

This will create:
- System income categories (13 categories)
- System expense super categories (3: Essentials, Lifestyle, Savings)
- System expense categories (70+ categories)

### 8. Build Frontend Assets

```bash
npm run build
```

For development with hot reload:

```bash
npm run dev
```

### 9. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` and navigate to `/admin/login`.

## First User Setup

1. Visit `/admin/register` to create your first user account
2. Log in at `/admin/login`
3. Configure your financial settings:
   - Set seed capital (starting balance)
   - Set median monthly income
   - Verify income date

## Troubleshooting

### Vite Manifest Error

If you see "Vite manifest not found", run:

```bash
npm run build
```

### Database Connection Issues

- Verify database credentials in `.env`
- Ensure database exists
- Check MySQL/PostgreSQL service is running

### Permission Issues

Ensure storage and cache directories are writable:

```bash
chmod -R 775 storage bootstrap/cache
```

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Run `php artisan view:cache`
5. Ensure `APP_DEBUG=false`
6. Build production assets: `npm run build`
