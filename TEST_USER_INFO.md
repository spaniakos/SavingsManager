# Test User Information

## Seeded Test Users

The application automatically seeds two test users during database seeding for testing purposes.

### Regular Test User

**Email:** `test@makeasite.gr`  
**Password:** `12341234`  
**Role:** Regular user (mobile app access only)

### Admin Test User

**Email:** `admin@makeasite.gr`  
**Password:** `12341234`  
**Role:** Admin user (admin panel access + mobile app access)

### Usage in Tests

#### E2E Tests (Playwright)
All E2E tests in `tests/Browser/` use this seeded user for authentication:
- `DashboardTest.spec.ts`
- `ExpenseEntryTest.spec.ts`
- `FullRouteTest.spec.ts`
- `IncomeEntryTest.spec.ts`
- `ReportingTest.spec.ts`
- `SavingsGoalTest.spec.ts`

#### Feature Tests
The `AuthenticationTest` suite specifically tests login and route access with both seeded users:
- `test_seeded_user_can_login()` - Tests regular user login
- `test_seeded_user_can_access_protected_routes()` - Tests mobile route access
- `test_seeded_user_can_access_mobile_routes()` - Tests mobile routes
- `test_seeded_user_credentials_are_correct()` - Verifies credentials
- `test_admin_user_exists_and_has_admin_flag()` - Verifies admin user exists
- `test_regular_user_cannot_access_admin_panel()` - Tests admin access restriction
- `test_mobile_login_page_is_accessible()` - Tests mobile login UI
- `test_mobile_register_page_is_accessible()` - Tests mobile register UI
- `test_user_can_login_via_mobile_auth()` - Tests mobile authentication
- `test_mobile_logout_redirects_to_login()` - Tests logout flow

Other feature tests create users using factories (`User::factory()->create()`) to test user isolation.

### Database Seeding

The test user is created by `Database\Seeders\UserSeeder` which is automatically called when running:
```bash
php artisan db:seed
```

Or during migrations:
```bash
php artisan migrate:fresh --seed
```

### GitHub Actions Workflows

All CI/CD workflows automatically:
1. Run migrations
2. Seed the database (which includes creating the test user)
3. Run tests that can use this seeded user

### Manual Testing

To use these users for manual testing:
1. Run migrations and seeders: `php artisan migrate:fresh --seed`

**Mobile App Access:**
- Login at `/mobile/login` with:
  - Email: `test@makeasite.gr` or `admin@makeasite.gr`
  - Password: `12341234`

**Admin Panel Access:**
- Login at `/admin/login` with:
  - Email: `admin@makeasite.gr` (admin users only)
  - Password: `12341234`
- Regular users (`test@makeasite.gr`) cannot access the admin panel

### Notes

- Both users are created with default values (seed_capital: 0, no median income set)
- The password is hashed using Laravel's Hash facade
- Users are created using `updateOrCreate`, so running seeders multiple times is safe
- The admin user has `is_admin` flag set to `true`
- The regular user has `is_admin` flag set to `false` (default)

