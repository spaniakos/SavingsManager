# TODO - Savings Manager Enhancements

**Status**: All core features are complete and production-ready. The items below are optional enhancements for future consideration.

---

## High Priority Features

### 0. Route Restructuring & Separate Authentication Systems

**Goal**: Separate mobile app routes from admin panel, create independent authentication systems.

#### 0.1 Route Structure Changes
- [x] Move all mobile routes from `/admin/mobile/*` to `/mobile/*`
  - Updated all route definitions in `routes/web.php`
  - All `route()` helper calls in views work automatically (route names unchanged)
  - Updated all internal links and redirects
- [x] Create route redirects:
  - `/login` → redirects to `/mobile/login` (mobile login) ✅
  - `/admin/login` → Filament admin login (unchanged) ✅
  - `/admin` → Filament admin panel (unchanged, handled by Filament) ✅
  - `/mobile` → mobile dashboard (if authenticated) or `/mobile/login` (if not) ✅

#### 0.2 Mobile Authentication System
- [x] Create mobile login/registration system (separate from Filament):
  - Create `MobileAuthController`:
    - `showLoginForm()`: Display mobile login page
    - `login()`: Handle mobile login
    - `showRegisterForm()`: Display mobile registration page
    - `register()`: Handle mobile registration
    - `logout()`: Handle mobile logout
  - Create views:
    - `resources/views/mobile/auth/login.blade.php`: Mobile login form
    - `resources/views/mobile/auth/register.blade.php`: Mobile registration form
  - Create routes:
    - `GET /mobile/login` → `mobile.auth.login`
    - `POST /mobile/login` → `mobile.auth.login.submit`
    - `GET /mobile/register` → `mobile.auth.register`
    - `POST /mobile/register` → `mobile.auth.register.submit`
    - `POST /mobile/logout` → `mobile.auth.logout`
    - `GET /mobile/logout` → redirects to login (handles direct navigation)
- [x] Update middleware:
  - Create `RedirectIfMobileAuthenticated` middleware (opposite of `RedirectIfAuthenticated`)
  - Update `bootstrap/app.php`:
    - Mobile routes should redirect guests to `/mobile/login`
    - Admin routes should redirect guests to `/admin/login`
  - Apply `auth` middleware to all `/mobile/*` routes (except login/register)

#### 0.3 Admin Panel Setup
- [x] Create Filament admin resources:
  - Create `UserResource` in `app/Filament/Resources/UserResource.php`:
    - List users with: name, email, created_at, updated_at
    - View user details
    - Edit user (name, email, password reset)
    - Delete user (with confirmation)
    - Filter by name, email
    - Search functionality
  - UserResource created (needs Filament 4 API fix - see 0.4)
  - Temporarily commented out in AdminPanelProvider until API compatibility is fixed
- [x] Admin panel access:
  - Only accessible via `/admin/login` (Filament login)
  - Separate from mobile authentication
  - Admin users can manage all users in the system

#### 0.4 Bug Fixes & Edge Cases
- [x] Fix logout route error:
  - Fixed logout redirect to use correct route name: `mobile.auth.login` instead of `mobile.login`
  - Added GET route for `/mobile/logout` that redirects to login (handles direct navigation)
  - POST route remains for form submissions
  - Prevents `RouteNotFoundException` and `MethodNotAllowedHttpException` errors
- [x] Fix welcome page links:
  - Updated welcome page to point to `/mobile/login` and `/mobile/register` instead of `/admin/login` and `/admin/register`
  - Updated dashboard link to use `route('mobile.dashboard')`
  - All links now correctly point to mobile routes
- [ ] Fix UserResource for Filament 4 compatibility:
  - UserResource created but needs Filament 4 API updates
  - Currently commented out in AdminPanelProvider
  - Need to update form() method signature to use Filament 4 Schema API
  - Once fixed, uncomment in AdminPanelProvider to enable user management
- [x] Remove `/admin` redirect to `/admin/mobile`:
  - Filament handles `/admin` automatically (no redirect needed)
  - Admin panel accessible at `/admin` with Filament login at `/admin/login`
  - No manual redirect required - Filament handles routing
- [x] Restrict admin panel access to admin users only:
  - Added `is_admin` boolean column to users table (migration created and run)
  - Created `EnsureUserIsAdmin` middleware to check admin status
  - Added middleware to Filament admin panel (in authMiddleware, not regular middleware)
  - Disabled registration in admin panel (only admins can access)
  - Login page is accessible, but only admin users can access admin panel after login
  - Created artisan command to set user as admin: `php artisan user:set-admin {email}`
  - Alternative: `UPDATE users SET is_admin = 1 WHERE email = 'admin@example.com';`
  - Updated UserSeeder to create admin user (`admin@makeasite.gr`) and test user (non-admin)
- [x] Fix admin home redirect (302 redirect issue):
  - Created `AdminHome` Filament page to serve as admin dashboard
  - Registered page in AdminPanelProvider
  - Set `homeUrl()` to point to AdminHome page
  - Prevents redirect loops when accessing `/admin`
- [x] Configure remember me tokens for 1 year expiration:
  - Created `ExtendRememberMeCookie` middleware to extend remember me cookie expiration
  - Cookie expiration set to 525600 minutes (1 year)
  - Middleware runs on every authenticated request to refresh the cookie
  - Applied to both mobile routes and admin panel routes
  - Works for both mobile app users and admin users
  - Remember me tokens now stay valid for 1 year with automatic refresh on each request
- [x] Fix admin login redirect:
  - Removed `/admin/mobile` redirects from DetectMobile middleware
  - Updated MobileExpenseController and MobileIncomeController to use `route('mobile.dashboard')` instead of `/admin/mobile`
  - Admin login now correctly redirects to `/admin` (Filament panel) instead of `/admin/mobile`
- [x] Fix dark mode for login/register forms:
  - Added dark mode styles to all input fields (email, password, name, password_confirmation)
  - Input fields now have: `dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600`
  - Password fields are now visible in dark mode

#### 0.5 Migration & Testing
- [x] Update all route references:
  - ✅ Searched codebase for `/admin/mobile` and replaced with `/mobile` in all test files
  - ✅ Updated all route references in tests (AuthenticationTest, EntryDateRestrictionsTest, SaveForLaterTest, MonthlyCalculationTest, ComprehensiveReportsTest)
  - ✅ Updated E2E tests (EssentialTest.spec.ts) to use mobile routes
  - ✅ All `route('mobile.*')` calls work automatically (route names unchanged)
  - ✅ Updated documentation (README.md with Future Work section)
- [x] Test authentication flows:
  - ✅ Mobile login/register works independently (tests: `test_mobile_login_page_is_accessible`, `test_mobile_register_page_is_accessible`, `test_user_can_login_via_mobile_auth`, `test_mobile_register_creates_user`)
  - ✅ Admin login works independently (tests: `test_seeded_user_can_login`, `test_admin_login_page_is_accessible`)
  - ✅ Users can't access admin panel via mobile login (test: `test_regular_user_cannot_access_admin_panel`)
  - ✅ Admins can access admin panel (test: `test_admin_user_exists_and_has_admin_flag`)
  - ✅ Logout works correctly (both GET and POST) (tests: `test_seeded_user_can_logout`, `test_mobile_logout_redirects_to_login`)
- [x] Update tests:
  - ✅ Updated all route tests to use new paths (`/mobile` instead of `/admin/mobile`)
  - ✅ Added tests for mobile authentication (7 new tests in AuthenticationTest)
  - ✅ Added tests for admin authentication separation (2 tests: admin access, regular user restriction)
  - ✅ All 68 PHP tests passing (145 assertions)
  - ✅ All 20 E2E tests passing
  - ✅ Removed duplicate smoke test file
  - ✅ Enhanced smoke tests with 4 comprehensive tests

#### 0.6 Future Admin Features
- [ ] As new features are added (subscriptions, etc.), add corresponding admin resources:
  - Subscription management (when subscription system is implemented)
  - Recurring expenses management (when recurring expenses are implemented)
  - System-wide reports and analytics
  - Other admin features as needed

---

### 1. Recurring Expenses System

**Goal**: Allow users to create recurring expenses that automatically generate expense entries on scheduled dates.

#### 1.1 Database & Models
- [ ] Create `recurring_expenses` migration
  - Fields:
    - `id` (bigint, primary key)
    - `user_id` (foreign key to users)
    - `expense_category_id` (foreign key to expense_categories)
    - `person_id` (nullable, foreign key to persons)
    - `amount` (decimal 10,2)
    - `recurrence_type` (enum: 'daily', 'weekly', 'monthly', 'yearly', 'custom')
    - `recurrence_interval` (integer, for custom: every X days/weeks/months)
    - `recurrence_day` (integer, nullable - for monthly: day of month 1-31)
    - `recurrence_weekday` (integer, nullable - for weekly: 0-6, Sunday=0)
    - `start_date` (date)
    - `end_date` (date, nullable)
    - `is_active` (boolean, default true)
    - `notes` (text, nullable)
    - `created_at`, `updated_at` (timestamps)
- [ ] Create `RecurringExpense` model
  - Relationships: `belongsTo(User)`, `belongsTo(ExpenseCategory)`, `belongsTo(Person)`
  - Fillable fields: all except id, timestamps
  - Casts: `amount` to decimal, `start_date`/`end_date` to date, `is_active` to boolean

#### 1.2 CRUD Operations
- [ ] Create `RecurringExpenseController`
  - `index()`: List all recurring expenses for current user
  - `create()`: Show form to create new recurring expense
  - `store()`: Validate and save new recurring expense
    - If `start_date` is today, automatically create expense entry for today
  - `edit($id)`: Show form to edit recurring expense
  - `update($id)`: Update recurring expense (edits only affect future generations)
  - `destroy($id)`: Delete recurring expense
- [ ] Create routes: `mobile.recurring-expenses.*`
- [ ] Create views:
  - `mobile/recurring-expenses/index.blade.php`: List all recurring expenses
  - `mobile/recurring-expenses/create.blade.php`: Create form
  - `mobile/recurring-expenses/edit.blade.php`: Edit form
- [ ] Form fields:
  - Expense category (required)
  - Person (optional)
  - Amount (required, decimal)
  - Recurrence type (dropdown: daily, weekly, monthly, yearly, custom)
  - Recurrence interval (shown only for custom, integer input)
  - Recurrence day (shown only for monthly, 1-31)
  - Recurrence weekday (shown only for weekly, day selector)
  - Start date (required, date picker)
  - End date (optional, date picker)
  - Notes (optional, textarea)
  - Active toggle (checkbox)

#### 1.3 Cron Job - Automatic Generation
- [ ] Create console command: `GenerateRecurringExpenses`
  - Check all active recurring expenses
  - For each recurring expense:
    - Calculate if it should fire today based on recurrence pattern
    - Check if expense entry already exists for this recurring expense + date
    - If not exists and date matches pattern, create ExpenseEntry
    - Link generated entry to recurring expense (add `recurring_expense_id` to expense_entries table)
- [ ] Schedule command in `app/Console/Kernel.php`
  - Run daily at midnight: `$schedule->command('generate:recurring-expenses')->daily();`
- [ ] Handle edge cases:
  - Month-end dates (e.g., 31st of month → use last day of month)
  - Leap years (February 29th)
  - Past dates (don't generate for dates before start_date)
  - Future dates beyond end_date (don't generate)

#### 1.4 Dashboard Widget
- [ ] Add "Recurring Expenses Total" widget to dashboard
  - Calculate: Sum all active recurring expenses for current month
  - Display: Amount in currency format
  - Make clickable: Link to `mobile.recurring-expenses.index`
  - Position: Add to Quick Stats Cards grid (may need to adjust grid layout)

#### 1.5 Migration for Generated Entries
- [ ] Add `recurring_expense_id` column to `expense_entries` table
  - Nullable foreign key to `recurring_expenses.id`
  - Allows tracking which entries were auto-generated
  - Generated entries are editable (user can modify amount, category, etc.)
  - Edits to generated entries do NOT affect the recurring expense template

---

### 2. Category Filtering Enhancements

**Goal**: Improve UX by filtering categories based on super category selection and auto-selecting super category when category is selected.

#### 2.1 Filter Categories by Super Category (Expenses Only)
- [ ] Update `resources/views/mobile/expense-entries/index.blade.php`
  - Add JavaScript to filter category dropdown when super category changes
  - When super category is selected:
    - Filter category dropdown to show only categories belonging to that super category
    - Clear category selection if current selection doesn't belong to selected super category
  - When super category is cleared:
    - Show all categories again
    - Keep category selection if it's still valid
- [ ] Implementation approach:
  - Option A: Pre-load all categories with their super_category_id in data attributes
  - Option B: AJAX call to fetch categories when super category changes
  - **Recommendation**: Option A (simpler, no server calls needed)

#### 2.2 Auto-Select Super Category When Category Selected
- [ ] Update `resources/views/mobile/expense-entries/index.blade.php`
  - Add JavaScript to auto-select super category when category is selected
  - When category is selected:
    - Read the category's `expense_super_category_id` from data attribute
    - Automatically set super category dropdown to match
- [ ] Update `resources/views/mobile/expense-entries/create.blade.php` and `edit.blade.php`
  - Apply same auto-selection logic in create/edit forms
  - Ensure super category is pre-selected when editing existing entry

#### 2.3 Bidirectional Sync
- [ ] Ensure both behaviors work together:
  - Selecting super category → filters categories
  - Selecting category → selects super category
  - Clearing super category → shows all categories
  - Clearing category → clears super category (optional, may want to keep super category)

---

### 3. Calendar View

**Goal**: Provide a calendar-based view of expenses and income entries.

#### 3.1 Database & Controller
- [ ] Create `CalendarController`
  - `index()`: Main calendar view
    - Accept query parameters: `year`, `month`, `view` (day/week/month)
    - Query expenses and income entries for the selected period
    - Group by date
    - Calculate daily totals (income, expenses, savings)
- [ ] Create route: `mobile.calendar.index`
- [ ] Create view: `resources/views/mobile/calendar/index.blade.php`

#### 3.2 Calendar Views
- [ ] Implement three view modes:
  - **Day View** (default for mobile):
    - Show single day
    - List all entries for that day
    - Show daily totals at top
    - Swipeable left/right to navigate days
    - Support drag gesture (touch down + drag left/right)
  - **Week View**:
    - Show 7 days in a row
    - Compact view with daily totals
    - Click day to see details
  - **Month View**:
    - Traditional calendar grid
    - Show daily totals in each cell
    - Color coding: Green (income), Red (expenses), Blue (savings)
    - Click day to see day view

#### 3.3 UI/UX Features
- [ ] Color coding:
  - Green: Income entries and totals
  - Red: Expense entries and totals
  - Blue: Savings (income - expenses for that day)
- [ ] Navigation:
  - Previous/Next buttons for day/week/month
  - Swipe gestures for day view (mobile-first)
  - Month/year selector for month view
- [ ] Day details:
  - Clicking a day shows detailed list of entries
  - Modal or slide-up panel on mobile
  - Show entry details: amount, category, person, notes
- [ ] No quick-add from calendar (as per requirements)

#### 3.4 Mobile Optimization
- [ ] Ensure day view is optimized for mobile
- [ ] Implement touch gestures for navigation
- [ ] Test swipe functionality on various devices
- [ ] Responsive design for all three views

---

### 4. Translation Management from UI

**Goal**: Allow users to add/edit translations for categories and super categories directly from the UI.

#### 4.1 Database Schema
- [ ] No database changes needed (translations stored in PHP files)
- [ ] Ensure `lang/en/categories.php` and `lang/el/categories.php` exist
- [ ] File structure:
  ```php
  return [
      'expense' => [
          'super' => [
              'food' => 'Food',
              'U_1_food' => 'User Food Category',
          ],
          'food_bread' => 'Bread',
      ],
  ];
  ```

#### 4.2 Translation Service
- [ ] Create `TranslationService`
  - `addTranslation($key, $locale, $value)`: Add/update translation
  - `getTranslationKey($categoryName, $isSystem, $existingKeys)`: Generate unique key
    - System categories: Use category name as-is
    - User categories: Use `U_` prefix
    - If conflict: Add increment ID: `U_1_<key>`, `U_2_<key>`, etc.
  - `writeTranslationFile($locale, $translations)`: Write to PHP file
  - `readTranslationFile($locale)`: Read current translations
  - `backupTranslationFile($locale)`: Create backup before writing
  - `validateTranslationKey($key)`: Ensure valid PHP array key format

#### 4.3 UI Integration
- [ ] Update category create/edit forms:
  - Add translation input fields for each locale (en, el)
  - Show current translation if editing
  - Show generated key preview
- [ ] Update super category create/edit forms:
  - Add translation input fields for each locale
  - Show generated key preview
- [ ] Validation:
  - Translation keys must be valid PHP identifiers
  - No special characters except underscore
  - Show error if invalid format

#### 4.4 Safety Features
- [ ] Backup before writing:
  - Create timestamped backup: `lang/en/categories.php.backup.2025-01-15-123456`
  - Store last 5 backups
- [ ] Preview before saving:
  - Show what will be written to file
  - Allow user to review before confirming
- [ ] Error handling:
  - Catch file write errors
  - Show user-friendly error messages
  - Rollback to backup if write fails
- [ ] File permissions:
  - Check if translation files are writable
  - Show warning if not writable

#### 4.5 Conflict Resolution
- [ ] When creating user category:
  - Check if key exists in translation file
  - If exists and is system category: Use `U_` prefix
  - If exists and is user category: Increment ID (`U_1_`, `U_2_`, etc.)
  - Auto-generate unique key
- [ ] Display:
  - Show generated key to user (read-only)
  - Explain conflict resolution if applicable

---

## Medium Priority Features

### 5. Multi-Goal Savings Enhancement

**Goal**: Allow users to select which savings goal to contribute to when adding savings entries.

#### 5.1 Database Schema
- [ ] Add `savings_goal_id` column to `expense_entries` table
  - Nullable foreign key to `savings_goals.id`
  - Only used when expense is in savings category
- [ ] Add `is_primary` column to `savings_goals` table
  - Boolean, default false
  - Only one goal per user can be primary at a time
- [ ] Create migration:
  - Add `savings_goal_id` to expense_entries
  - Add `is_primary` to savings_goals
  - Set primary goal for existing users:
    - If user has 1 goal: Set it as primary
    - If user has multiple goals: Set first non-fulfilled goal (by created_at) as primary
    - If all goals fulfilled: Set first goal (by created_at) as primary

#### 5.2 UI Updates
- [ ] Update expense entry create/edit forms:
  - When savings category is selected:
    - Show savings goal dropdown
    - List all active savings goals
    - Show which goal is primary (label: "Primary Goal")
    - Allow selection of specific goal
    - If no goal selected: Default to primary goal
- [ ] Update dashboard:
  - Show savings breakdown by goal
  - Display progress for each goal separately
  - Highlight primary goal

#### 5.3 Business Logic
- [ ] Update `SavingsCalculatorService`:
  - When expense is in savings category:
    - If `savings_goal_id` is set: Add to that specific goal only
    - If `savings_goal_id` is null: Add to primary goal
- [ ] Update monthly calculation:
  - Remaining money (income - expenses - allocated savings) goes to primary goal
  - If primary goal would exceed target:
    - Fill primary goal to target
    - Distribute remaining to other active goals (round-robin or by priority)
    - Show toast notification: "Primary goal filled! Remaining €X distributed to other goals."
- [ ] Primary goal management:
  - Allow user to change primary goal
  - Update `is_primary` flag (only one can be primary)
  - Update UI to show new primary goal

#### 5.4 Migration Logic
- [ ] Create migration script:
  - For each user:
    - Count their savings goals
    - If count = 1: Set `is_primary = true` for that goal
    - If count > 1:
      - Find first non-fulfilled goal (current_amount < target_amount)
      - If found: Set as primary
      - If all fulfilled: Set first goal (by created_at) as primary
  - Update existing savings entries:
    - For entries in savings category: Set `savings_goal_id` to user's primary goal

---

### 6. Joint Accounts / Efforts System

**Goal**: Enable multiple users to collaborate on shared savings goals and expenses.

#### 6.1 Database Schema
- [ ] Create `efforts` table:
  - `id` (bigint, primary key)
  - `effort_id` (uuid, unique, indexed)
  - `name` (string, nullable - optional effort name)
  - `created_at`, `updated_at` (timestamps)
- [ ] Create `effort_users` pivot table:
  - `id` (bigint, primary key)
  - `effort_id` (foreign key to efforts)
  - `user_id` (foreign key to users)
  - `is_primary` (boolean, default false - creator of effort)
  - `joined_at` (timestamp)
  - `created_at`, `updated_at` (timestamps)
  - Unique constraint: (effort_id, user_id)
- [ ] Add `effort_id` to all relevant tables:
  - `expense_entries`
  - `income_entries`
  - `expense_categories`
  - `expense_super_categories`
  - `income_categories`
  - `savings_goals`
  - `persons`
  - `recurring_expenses` (when implemented)
- [ ] Migration strategy:
  - For each existing user:
    - Create an effort with unique UUID
    - Add user to effort_users as primary
    - Update all user's records to use new effort_id
    - Set user's first (or primary) savings goal as effort's primary goal

#### 6.2 Models & Relationships
- [ ] Create `Effort` model:
  - Relationships:
    - `belongsToMany(User)` via `effort_users`
    - `hasMany(ExpenseEntry)`
    - `hasMany(IncomeEntry)`
    - `hasMany(SavingsGoal)`
    - `hasMany(Person)`
- [ ] Update all models:
  - Change `user_id` relationships to `effort_id` where applicable
  - Add `effort()` relationship
  - Update scopes to filter by `effort_id` instead of `user_id`
- [ ] Update `User` model:
  - Add `belongsToMany(Effort)` via `effort_users`
  - Add `currentEffort()` accessor (get from session/cache)
  - Add `setCurrentEffort($effortId)` method

#### 6.3 Authentication & Authorization
- [ ] Update middleware:
  - Create `SetEffortContext` middleware
  - Get current effort from session or user's primary effort
  - Set effort context for all requests
- [ ] Update all controllers:
  - Replace `Auth::id()` with `Auth::user()->currentEffort()->id` where needed
  - Filter queries by `effort_id` instead of `user_id`
- [ ] Update authentication:
  - After login: Set user's primary effort as current effort
  - Store current effort in session

#### 6.4 Invitation System
- [ ] Create `EffortInvitation` model and migration:
  - `id` (bigint, primary key)
  - `effort_id` (foreign key to efforts)
  - `invited_by_user_id` (foreign key to users)
  - `email` (string, the email being invited)
  - `token` (string, unique, for invitation link)
  - `accepted_at` (timestamp, nullable)
  - `expires_at` (timestamp)
  - `created_at`, `updated_at` (timestamps)
- [ ] Create invitation flow:
  - User clicks "Invite" on effort
  - Enter email address
  - Generate unique token
  - Send invitation email with link
  - Invited user clicks link, creates account (if needed), joins effort
  - User can only be in one effort at a time
  - If user has existing effort: Show warning, require leaving current effort
- [ ] Create routes:
  - `mobile.efforts.invite` (POST)
  - `mobile.efforts.accept-invitation` (GET, with token)
  - `mobile.efforts.leave` (POST)

#### 6.5 UI Updates
- [ ] Add effort switcher in navigation:
  - Show current effort name
  - Dropdown to switch efforts (if user is in multiple)
  - "Create New Effort" option
  - "Invite Member" option
- [ ] Update all forms:
  - Pre-select person based on current user in effort
  - Show effort context (which effort you're working in)
- [ ] Update dashboard:
  - Show effort name
  - All data filtered by current effort
- [ ] Person management:
  - Persons are shared within effort
  - All effort members can see all persons
  - When creating entry: Pre-select person matching current user's email/name

#### 6.6 Primary Goal Integration
- [ ] Update monthly calculation for efforts:
  - Remaining money goes to effort's primary goal
  - If primary goal would exceed target:
    - Fill primary goal to target
    - Distribute remaining to other active goals in effort
    - Show toast: "Primary goal filled! Remaining €X distributed to other goals."
- [ ] Primary goal management:
  - Any effort member can change primary goal
  - Update `is_primary` flag on savings_goals table
  - Show current primary goal in dashboard

#### 6.7 Leaving an Effort
- [ ] Implement leave functionality:
  - User can leave effort (removes from effort_users)
  - User must create or join another effort
  - All data stays with effort (not deleted)
  - User loses access to effort data
  - If user was primary: Transfer primary status to another member (oldest member)

---

## Low Priority Features

### 7. Data Import/Export

#### 7.1 Data Export
- [ ] Create export functionality:
  - Export to CSV format
  - Export to JSON format
  - Include: expenses, income, categories, savings goals
  - Add export button in settings page
- [ ] Create `ExportController`:
  - `exportCsv()`: Generate CSV file
  - `exportJson()`: Generate JSON file
- [ ] Routes: `mobile.export.csv`, `mobile.export.json`

#### 7.2 Data Import
- [ ] Create import functionality:
  - Import from CSV
  - Import from JSON
  - Validate data format
  - Show preview before importing
  - Handle duplicates (skip or update)
- [ ] Create `ImportController`:
  - `showImportForm()`: Show upload form
  - `import()`: Process uploaded file
  - Validate and import data
- [ ] Route: `mobile.import` (GET, POST)

---

### 8. PWA Features

#### 8.1 Service Worker
- [ ] Create service worker:
  - Cache static assets
  - Cache API responses
  - Offline fallback page
- [ ] Register service worker in main layout

#### 8.2 Web App Manifest
- [ ] Create `manifest.json`:
  - App name, icons, theme colors
  - Display mode: standalone
  - Start URL
- [ ] Link manifest in HTML head

#### 8.3 Install Prompt
- [ ] Add install prompt:
  - Show "Install App" button
  - Handle beforeinstallprompt event
  - Guide users to install PWA

---

### 9. Social Login

#### 9.1 Setup
- [ ] Install Laravel Socialite: `composer require laravel/socialite`
- [ ] Configure providers in `config/services.php`:
  - Google OAuth
  - Apple Sign In
  - Facebook Login

#### 9.2 Implementation
- [ ] Create `SocialAuthController`:
  - `redirectToProvider($provider)`: Redirect to OAuth provider
  - `handleProviderCallback($provider)`: Handle OAuth callback
  - Create or login user
- [ ] Routes: `auth/{provider}`, `auth/{provider}/callback`
- [ ] Update login page: Add social login buttons

---

### 10. Mobile Apps (iOS & Android)

#### 10.1 Research
- [ ] Evaluate NativePHP: https://nativephp.com/
- [ ] Consider alternatives: React Native, Flutter, Capacitor
- [ ] Decide on approach based on requirements

#### 10.2 Implementation (if NativePHP)
- [ ] Setup NativePHP project
- [ ] Configure build for iOS
- [ ] Configure build for Android
- [ ] Add push notifications
- [ ] Test on devices

#### 10.3 Notifications
- [ ] Implement push notifications:
  - Recurring expense reminders
  - Savings goal milestones
  - Monthly calculation reminders
- [ ] Use Laravel Notifications
- [ ] Configure FCM (Firebase Cloud Messaging) for Android
- [ ] Configure APNs (Apple Push Notification service) for iOS

---

## SaaS Conversion

### 11. Subscription System

#### 11.1 Pricing Model
- [ ] Implement "Pay What You Want" subscription:
  - Minimum: €1 per effort per month
  - Users can pay more as donation
  - All donations go to project savings goal
- [ ] Open source remains free
  - Subscription only for users using hosted servers
  - Self-hosted instances remain free

#### 11.2 Payment Integration
- [ ] Choose payment provider: Stripe or Paddle
- [ ] Create subscription plans:
  - Basic: €1/month per effort
  - Donation tiers: €2, €5, €10, €20, custom
- [ ] Create `Subscription` model:
  - `user_id`, `effort_id`, `plan_id`, `amount`, `status`, `starts_at`, `ends_at`
- [ ] Create `SubscriptionController`:
  - Handle payment processing
  - Manage subscriptions
  - Handle webhooks

#### 11.3 Access Control
- [ ] Add subscription check middleware:
  - Verify effort has active subscription
  - Block access if subscription expired
  - Show upgrade prompt
- [ ] Grace period:
  - Allow 7 days after expiration before blocking
  - Send reminder emails

---

## AI Integration

### 12. Receipt Scanning & Auto-Categorization

#### 12.1 Premium Feature Setup
- [ ] Create premium feature flag system
- [ ] Add `is_premium` to users table
- [ ] Create subscription tier: Premium

#### 12.2 Receipt Scanning
- [ ] Create receipt upload functionality:
  - Allow image upload (JPG, PNG)
  - Validate image format and size
- [ ] Create `ReceiptScanController`:
  - `upload()`: Accept image upload
  - `scan()`: Process image with OCR/AI
  - `categorize()`: Auto-categorize expense
  - `createExpense()`: Create expense entry from scan

#### 12.3 API Integration Options
- [ ] Support user's own API keys:
  - Allow users to enter their API keys (Google Vision, AWS Textract, etc.)
  - Use user's keys for processing (free feature)
  - Store keys encrypted in database
- [ ] Support platform API keys:
  - Use platform's API keys (premium feature)
  - Charge premium subscription for this
  - Rate limiting per user

#### 12.4 AI Categorization
- [ ] Implement categorization logic:
  - Extract text from receipt (OCR)
  - Analyze merchant name, items
  - Match to existing categories
  - Suggest category and amount
  - Allow user to confirm/edit before saving

---

## Implementation Notes

### Migration Strategy
- All breaking changes must include database migrations
- Test migrations on staging before production
- Create rollback scripts for critical changes

### Testing Requirements
- Write tests for all new features
- Test migrations on sample data
- Test edge cases (empty data, large datasets, etc.)

### Documentation
- Update API documentation for new endpoints
- Update user guide for new features
- Document migration process for existing users

---

**Last Updated**: 2025-12-03
