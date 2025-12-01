# Development Rules & Guidelines

This document outlines the rules, architecture decisions, and best practices for the Savings Manager project.

## Architecture Rules

### Technology Stack
- **Framework**: Laravel 12.x (PHP 8.2+)
- **Admin Panel**: Filament 4.x
- **Database**: MySQL 8.0+ (primary) or PostgreSQL 13+
- **Testing**: PHPUnit 11.x, Playwright (latest)
- **All packages**: Must use latest stable versions

### Code Organization
- Follow Laravel conventions
- Use service classes for business logic
- Keep controllers thin (Filament resources handle this)
- Models should only contain relationships and accessors/mutators
- Complex calculations go in Service classes

### Database Rules
- All tables must have `timestamps`
- Use foreign keys with proper constraints
- Index frequently queried columns
- Use `utf8mb4` character set for Greek support
- Translation keys stored as strings, not separate columns

## Translation Rules

### Translation System
- **MUST** use Laravel's `__()` helper for all user-facing strings
- **MUST** store translation keys in database, not translated text
- **MUST** have translations in both `lang/en/` and `lang/el/`
- Translation files:
  - `common.php` - All UI strings, labels, messages
  - `categories.php` - Category names

### Translation Key Format
- Use dot notation: `common.amount`, `categories.income.salary`
- Keep keys descriptive and organized
- Never hardcode strings in views or components

### Adding Translations
1. Add key to `lang/en/common.php` or `lang/en/categories.php`
2. Add Greek translation to `lang/el/common.php` or `lang/el/categories.php`
3. Use `__('file.key')` in code

## Testing Rules

### Test Coverage Requirements
Each feature **MUST** include:

1. **Unit Tests** (tests/Unit/)
   - Test individual methods and calculations
   - Mock dependencies
   - Test edge cases
   - Example: `SavingsCalculatorServiceTest`

2. **Feature Tests** (tests/Feature/)
   - Test complete workflows
   - Test CRUD operations
   - Test business logic
   - Example: `IncomeManagementTest`

3. **Smoke Tests** (tests/Smoke/)
   - Quick validation of critical paths
   - Should run fast (< 30 seconds total)
   - Test: login, create entry, view dashboard

4. **Playwright E2E Tests** (tests/Browser/)
   - Test user interactions
   - Test UI workflows
   - Test on multiple viewports (desktop, mobile)
   - Example: `DashboardTest`

### Test Naming
- Unit tests: `{Class}Test.php`
- Feature tests: `{Feature}Test.php`
- Browser tests: `{Feature}Test.php` (in Browser directory)

### Test Execution
- Run all tests before committing: `php artisan test && npm run test:e2e`
- Tests must pass in CI/CD
- Aim for >80% code coverage

## Database Rules

### Migrations
- One migration per table
- Use descriptive names: `create_income_entries_table`
- Always include `down()` method
- Use foreign keys with `onDelete()` and `onDelete('restrict')` for categories

### Models
- Use Eloquent relationships
- Add `$fillable` for mass assignment
- Use `$casts` for type casting
- Add scopes for common queries (e.g., `scopeForUser`)

### Seeders
- System data in seeders (categories)
- User data via factories or manual creation
- Seeders must be idempotent (can run multiple times)

## Security Rules

### Authentication
- All routes require authentication
- Users can only see/modify their own data
- Use `Auth::id()` for user filtering
- Never trust user input

### Data Validation
- Validate all form inputs
- Use Laravel validation rules
- Sanitize user input
- Use parameterized queries (Eloquent handles this)

### Authorization
- Check ownership before allowing edits/deletes
- Use policies if needed for complex permissions
- Joint goals: verify user is owner or member

## Code Quality Rules

### PHP Standards
- Follow PSR-12 coding standards
- Use Laravel Pint for formatting: `./vendor/bin/pint`
- Maximum line length: 120 characters
- Use type hints where possible

### Naming Conventions
- Classes: PascalCase (`SavingsCalculatorService`)
- Methods: camelCase (`calculateMonthlySaving`)
- Variables: camelCase (`$monthlySaving`)
- Constants: UPPER_SNAKE_CASE (`MAX_AMOUNT`)
- Database tables: snake_case (`income_entries`)
- Database columns: snake_case (`user_id`)

### Comments
- Document complex logic
- Use PHPDoc for classes and methods
- Explain "why" not "what"
- Keep comments up to date

## Git Rules

### Commit Messages
- Use clear, descriptive messages
- Format: `type: description`
- Types: `feat`, `fix`, `docs`, `test`, `refactor`
- Example: `feat: add savings calculator service`

### Branching
- `main` - production-ready code
- `develop` - development branch
- Feature branches: `feature/description`
- Bug fixes: `fix/description`

## Performance Rules

### Database Queries
- Use eager loading to prevent N+1 queries
- Add indexes for frequently queried columns
- Use `select()` to limit columns when possible
- Cache expensive queries when appropriate

### Frontend
- Minimize JavaScript bundle size
- Use lazy loading for images
- Optimize assets with `npm run build`

## Documentation Rules

### Code Documentation
- Document all public methods
- Explain complex algorithms
- Include examples for service methods

### Project Documentation
- Keep README.md up to date
- Document breaking changes
- Update INSTALLATION.md for new requirements
- Keep TODO.md current

## Deployment Rules

### Pre-Deployment Checklist
- [ ] All tests passing
- [ ] No debug code (`APP_DEBUG=false`)
- [ ] Environment variables configured
- [ ] Database migrations run
- [ ] Assets built (`npm run build`)
- [ ] Caches cleared and rebuilt
- [ ] Permissions set correctly

### Production Environment
- `APP_ENV=production`
- `APP_DEBUG=false`
- Use production database
- Enable query logging (optional)
- Set up error tracking

## Category Management Rules

### System Categories
- Cannot be deleted by users
- Cannot be modified by users
- Identified by `is_system=true` and `user_id=null`

### User Categories
- Can be created by users
- Can be modified by creator
- Can be deleted by creator
- Identified by `is_system=false` and `user_id=creator_id`
- Must have translation keys (user provides both languages or uses English key)

## Savings Goal Rules

### Progress Calculation
- Monthly saving needed = (target_amount - current_amount) / months_remaining
- Months remaining = (target_date - current_date) in months
- Handle edge cases: past dates, zero amounts, division by zero

### Joint Goals
- Owner can add/remove members
- Members can contribute
- All members can view progress
- Only owner can modify goal details

## Chart & Reporting Rules

### Data Aggregation
- Group by category for expense charts
- Group by month for trends
- Handle empty data gracefully
- Format currency consistently (€)

### Performance
- Cache expensive aggregations
- Limit data range for large datasets
- Use database aggregation when possible

## Mobile Support Rules

### Responsive Design
- All Filament components are responsive by default
- Test on mobile viewports (320px, 375px, 414px)
- Ensure touch targets are adequate (min 44x44px)
- Test form inputs on mobile

### Playwright Mobile Tests
- Test critical paths on mobile viewport
- Verify navigation works
- Test form submissions
- Check chart rendering

## Error Handling Rules

### User-Friendly Messages
- Never expose technical errors to users
- Use translated error messages
- Log errors for debugging
- Provide helpful guidance

### Validation Errors
- Show field-specific errors
- Use translated validation messages
- Highlight invalid fields

## Future Considerations

### Scalability
- Consider caching for large datasets
- Optimize queries for performance
- Consider queue jobs for heavy operations

### Features to Consider
- Recurring transactions
- Budget planning
- Goal templates
- Data import/export
- API endpoints for mobile app

