# GitHub Actions Workflows

This directory contains GitHub Actions workflows for automated testing and code quality checks.

## Workflows

### 1. `tests.yml` - Comprehensive Test Suite
**Triggers:** Push/PR to `main` or `develop` branches

**Jobs:**
- **php-tests**: Runs PHPUnit tests on PHP 8.4
  - Sets up MySQL service
  - Runs migrations and seeders
  - Executes all unit and feature tests
  - Uploads test logs on failure

- **code-quality**: Runs Laravel Pint for code style checks
  - Validates code formatting
  - Non-blocking (continue-on-error)

- **e2e-tests**: Runs Playwright E2E tests
  - Sets up Node.js and PHP
  - Installs Playwright browsers
  - Starts Laravel server
  - Runs browser tests
  - Uploads Playwright reports

- **test-summary**: Aggregates test results

### 2. `lint.yml` - Code Quality Checks
**Triggers:** Push/PR to `main` or `develop` branches

**Jobs:**
- **laravel-pint**: Code style validation
- **phpstan**: Static analysis (if configured)

### 3. `ci.yml` - Simplified CI Pipeline
**Triggers:** Push/PR to `main` or `develop`, manual dispatch

**Jobs:**
- **test**: Single job that runs the full test suite
  - Sets up MySQL
  - Runs migrations and seeders
  - Executes all tests
  - Uploads logs on failure

## Usage

### Running Tests Locally

```bash
# Run all PHP tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run with testdox output
php artisan test --testdox

# Run E2E tests
npm run test:e2e
```

### Workflow Status

Check workflow status in the GitHub Actions tab:
- ✅ Green: All tests passing
- ❌ Red: Tests failing
- 🟡 Yellow: Tests running or warnings

## Configuration

### Environment Variables

The workflows automatically configure:
- `APP_ENV=testing`
- `DB_CONNECTION=mysql` (MySQL service for all jobs)
- Database credentials for MySQL service

### Required Extensions

PHP extensions required:
- `mbstring`, `dom`, `curl`, `libxml`
- `mysql`, `pdo`, `pdo_mysql`
- `sqlite`, `pdo_sqlite`
- `zip`, `gd`

## Test Coverage

Current test coverage:
- **56 PHP tests** (Unit + Feature + Authentication)
- **124 assertions**
- **6 E2E test files** (Playwright)

See `TEST_RESULTS.md` for detailed test results.]
