# Development Rules & Guidelines

## Architecture Principles

1. **Service Layer**: Business logic in service classes, not controllers
2. **User Isolation**: All data queries filtered by `user_id`
3. **Translation First**: All strings use `__('common.key')` or `__('categories.key')`
4. **Test-Driven**: Write tests before implementation
5. **Thin Controllers**: Controllers delegate to services

## Coding Standards

- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Type hints for all method parameters and return types
- Use Eloquent relationships instead of manual joins
- Always use `Auth::id()` for user-scoped queries

## Database Guidelines

- Use migrations for all schema changes
- System categories have `is_system=true` and `user_id=null`
- User-created categories have `is_system=false` and `user_id` set
- Translation keys stored in database, translations in `lang/` files

## Testing Requirements

- Unit tests for all service classes
- Feature tests for all CRUD operations
- Smoke tests for critical user paths
- E2E tests for major workflows
- All tests must pass before merging

## Translation Guidelines

- Never hardcode strings in views or code
- Always use translation keys: `__('common.key')`
- Add translations to both `lang/en/` and `lang/el/`
- Category names use `getTranslatedName()` method

## Git Workflow

- Create feature branches from `main`
- Write descriptive commit messages
- Ensure all tests pass before pushing
- Update documentation for significant changes

## Security

- Always validate user input
- Use Laravel's built-in authentication
- Never expose user data without proper scoping
- Use parameterized queries (Eloquent handles this)
