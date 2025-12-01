# Test User Information

## Seeded Test User

The application automatically seeds a test user during database seeding for testing purposes.

**Email:** `test@makeasite.gr`  
**Password:** `12341234`

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
The `AuthenticationTest` suite specifically tests login and route access with this seeded user:
- `test_seeded_user_can_login()`
- `test_seeded_user_can_access_protected_routes()`
- `test_seeded_user_can_access_mobile_routes()`
- `test_seeded_user_credentials_are_correct()`

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

To use this user for manual testing:
1. Run migrations and seeders: `php artisan migrate:fresh --seed`
2. Login at `/admin/login` with:
   - Email: `test@makeasite.gr`
   - Password: `12341234`

### Notes

- This user is created with default values (seed_capital: 0, no median income set)
- The password is hashed using Laravel's Hash facade
- The user is created using `updateOrCreate`, so running seeders multiple times is safe

