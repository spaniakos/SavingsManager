# Quick Setup Guide

## Prerequisites Check

Before starting, verify you have:

```bash
php -v          # Should be 8.2+
composer -v     # Should be installed
mysql --version # Should be 8.0+ (or psql --version for PostgreSQL)
node -v         # Should be 20.19+ or 22.12+
npm -v          # Should be installed
```

## Quick Start (5 minutes)

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Set Up Database

**For MySQL:**

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE savings_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=savings_manager
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

### 5. Build Assets

```bash
npm run build
```

### 6. Create User and Start Server

```bash
# Create user via tinker
php artisan tinker
# Then: User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')]);

# Start server
php artisan serve
```

Visit: **http://localhost:8000/admin**

## Common Setup Issues

### MySQL Connection Refused

```bash
# Check MySQL is running
sudo service mysql status  # Linux
brew services list         # Mac (check mysql)

# Start MySQL
sudo service mysql start   # Linux
brew services start mysql  # Mac
```

### Permission Denied

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

### Translation Not Working

```bash
# Clear caches
php artisan config:clear
php artisan cache:clear
```

## Next Steps

1. Log in to `/admin`
2. Add your first income entry
3. Add expense entries
4. Create a savings goal
5. Explore the dashboard

For detailed instructions, see [INSTALLATION.md](INSTALLATION.md)

