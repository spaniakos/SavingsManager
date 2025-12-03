<?php

use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::post('/admin/language-switch', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->middleware(['web']);

// Redirect /login to mobile login
Route::get('/login', function () {
    return redirect()->route('mobile.auth.login');
})->name('login');

// Mobile authentication routes (public)
use App\Http\Controllers\Mobile\MobileAuthController;

Route::get('/mobile/login', [MobileAuthController::class, 'showLoginForm'])
    ->name('mobile.auth.login')
    ->middleware('guest');

Route::post('/mobile/login', [MobileAuthController::class, 'login'])
    ->name('mobile.auth.login.submit')
    ->middleware('guest');

Route::get('/mobile/register', [MobileAuthController::class, 'showRegisterForm'])
    ->name('mobile.auth.register')
    ->middleware('guest');

Route::post('/mobile/register', [MobileAuthController::class, 'register'])
    ->name('mobile.auth.register.submit')
    ->middleware('guest');

// Handle GET requests to logout (redirect to login)
Route::get('/mobile/logout', function () {
    return redirect()->route('mobile.auth.login');
})->name('mobile.auth.logout.get');

Route::post('/mobile/logout', [MobileAuthController::class, 'logout'])
    ->name('mobile.auth.logout')
    ->middleware('auth');

// Mobile routes (authenticated)
use App\Http\Controllers\Mobile\MobileDashboardController;
use App\Http\Controllers\Mobile\MobileExpenseController;
use App\Http\Controllers\Mobile\MobileIncomeController;
use App\Http\Controllers\Mobile\MobileSettingsController;

Route::middleware(['auth', \App\Http\Middleware\RedirectIfMobileGuest::class, \App\Http\Middleware\ExtendRememberMeCookie::class])->group(function () {
    // Redirect /mobile to dashboard if authenticated, or login if not
    Route::get('/mobile', function () {
        return redirect()->route('mobile.dashboard');
    });

    Route::get('/mobile/dashboard', [MobileDashboardController::class, 'index'])
        ->name('mobile.dashboard');

    // Expense routes
    Route::get('/mobile/expense', [MobileExpenseController::class, 'index'])
        ->name('mobile.expense.index');
    Route::get('/mobile/expense/super-category/{superCategoryId}', [MobileExpenseController::class, 'showCategories'])
        ->name('mobile.expense.categories');
    Route::get('/mobile/expense/category/{categoryId}/create', [MobileExpenseController::class, 'create'])
        ->name('mobile.expense.create');
    Route::post('/mobile/expense/category/{categoryId}', [MobileExpenseController::class, 'store'])
        ->name('mobile.expense.store');

    // Income routes
    Route::get('/mobile/income', [MobileIncomeController::class, 'index'])
        ->name('mobile.income.index');
    Route::get('/mobile/income/category/{categoryId}/create', [MobileIncomeController::class, 'create'])
        ->name('mobile.income.create');
    Route::post('/mobile/income/category/{categoryId}', [MobileIncomeController::class, 'store'])
        ->name('mobile.income.store');

    // Settings route
    Route::get('/mobile/settings', [MobileSettingsController::class, 'index'])
        ->name('mobile.settings');

    // Profile Settings
    Route::get('/mobile/profile-settings', [\App\Http\Controllers\Mobile\MobileProfileSettingsController::class, 'index'])
        ->name('mobile.profile-settings');
    Route::put('/mobile/profile-settings', [\App\Http\Controllers\Mobile\MobileProfileSettingsController::class, 'update'])
        ->name('mobile.profile-settings.update');

    // Expense Entries
    Route::get('/mobile/expense-entries', [\App\Http\Controllers\Mobile\MobileExpenseEntriesController::class, 'index'])
        ->name('mobile.expense-entries.index');
    Route::get('/mobile/expense-entries/{id}/edit', [\App\Http\Controllers\Mobile\MobileExpenseEntriesController::class, 'edit'])
        ->name('mobile.expense-entries.edit');
    Route::put('/mobile/expense-entries/{id}', [\App\Http\Controllers\Mobile\MobileExpenseEntriesController::class, 'update'])
        ->name('mobile.expense-entries.update');
    Route::delete('/mobile/expense-entries/{id}', [\App\Http\Controllers\Mobile\MobileExpenseEntriesController::class, 'destroy'])
        ->name('mobile.expense-entries.destroy');

    // Income Entries
    Route::get('/mobile/income-entries', [\App\Http\Controllers\Mobile\MobileIncomeEntriesController::class, 'index'])
        ->name('mobile.income-entries.index');
    Route::get('/mobile/income-entries/{id}/edit', [\App\Http\Controllers\Mobile\MobileIncomeEntriesController::class, 'edit'])
        ->name('mobile.income-entries.edit');
    Route::put('/mobile/income-entries/{id}', [\App\Http\Controllers\Mobile\MobileIncomeEntriesController::class, 'update'])
        ->name('mobile.income-entries.update');
    Route::delete('/mobile/income-entries/{id}', [\App\Http\Controllers\Mobile\MobileIncomeEntriesController::class, 'destroy'])
        ->name('mobile.income-entries.destroy');

    // Persons
    Route::get('/mobile/persons', [\App\Http\Controllers\Mobile\MobilePersonsController::class, 'index'])
        ->name('mobile.persons.index');
    Route::get('/mobile/persons/create', [\App\Http\Controllers\Mobile\MobilePersonsController::class, 'create'])
        ->name('mobile.persons.create');
    Route::post('/mobile/persons', [\App\Http\Controllers\Mobile\MobilePersonsController::class, 'store'])
        ->name('mobile.persons.store');
    Route::get('/mobile/persons/{id}/edit', [\App\Http\Controllers\Mobile\MobilePersonsController::class, 'edit'])
        ->name('mobile.persons.edit');
    Route::put('/mobile/persons/{id}', [\App\Http\Controllers\Mobile\MobilePersonsController::class, 'update'])
        ->name('mobile.persons.update');

    // Expense Super Categories
    Route::get('/mobile/expense-super-categories', [\App\Http\Controllers\Mobile\MobileExpenseSuperCategoriesController::class, 'index'])
        ->name('mobile.expense-super-categories.index');
    Route::get('/mobile/expense-super-categories/{id}/edit', [\App\Http\Controllers\Mobile\MobileExpenseSuperCategoriesController::class, 'edit'])
        ->name('mobile.expense-super-categories.edit');
    Route::put('/mobile/expense-super-categories/{id}', [\App\Http\Controllers\Mobile\MobileExpenseSuperCategoriesController::class, 'update'])
        ->name('mobile.expense-super-categories.update');

    // Expense Categories (full CRUD)
    Route::get('/mobile/expense-categories', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'index'])
        ->name('mobile.expense-categories.index');
    Route::get('/mobile/expense-categories/create', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'create'])
        ->name('mobile.expense-categories.create');
    Route::post('/mobile/expense-categories', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'store'])
        ->name('mobile.expense-categories.store');
    Route::get('/mobile/expense-categories/{id}/edit', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'edit'])
        ->name('mobile.expense-categories.edit');
    Route::put('/mobile/expense-categories/{id}', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'update'])
        ->name('mobile.expense-categories.update');
    Route::delete('/mobile/expense-categories/{id}', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'destroy'])
        ->name('mobile.expense-categories.destroy');

    // Income Categories (full CRUD)
    Route::get('/mobile/income-categories', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'index'])
        ->name('mobile.income-categories.index');
    Route::get('/mobile/income-categories/create', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'create'])
        ->name('mobile.income-categories.create');
    Route::post('/mobile/income-categories', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'store'])
        ->name('mobile.income-categories.store');
    Route::get('/mobile/income-categories/{id}/edit', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'edit'])
        ->name('mobile.income-categories.edit');
    Route::put('/mobile/income-categories/{id}', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'update'])
        ->name('mobile.income-categories.update');
    Route::delete('/mobile/income-categories/{id}', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'destroy'])
        ->name('mobile.income-categories.destroy');

    // Savings Goals (view only)
    Route::get('/mobile/savings-goals', [\App\Http\Controllers\Mobile\MobileSavingsGoalsController::class, 'index'])
        ->name('mobile.savings-goals.index');

    // Savings Goals Admin (full CRUD)
    Route::get('/mobile/savings-goals-admin', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'index'])
        ->name('mobile.savings-goals-admin.index');
    Route::get('/mobile/savings-goals-admin/create', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'create'])
        ->name('mobile.savings-goals-admin.create');
    Route::post('/mobile/savings-goals-admin', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'store'])
        ->name('mobile.savings-goals-admin.store');
    Route::get('/mobile/savings-goals-admin/{id}/edit', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'edit'])
        ->name('mobile.savings-goals-admin.edit');
    Route::put('/mobile/savings-goals-admin/{id}', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'update'])
        ->name('mobile.savings-goals-admin.update');
    Route::delete('/mobile/savings-goals-admin/{id}', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'destroy'])
        ->name('mobile.savings-goals-admin.destroy');

    // Reports
    Route::get('/mobile/reports', [\App\Http\Controllers\Mobile\MobileReportsController::class, 'index'])
        ->name('mobile.reports.index');
    Route::get('/mobile/reports/export-pdf', [\App\Http\Controllers\Mobile\MobileReportsController::class, 'exportPdf'])
        ->name('mobile.reports.export-pdf');

    // Monthly Calculation
    Route::post('/mobile/monthly-calculation', [\App\Http\Controllers\Mobile\MonthlyCalculationController::class, 'calculate'])
        ->name('mobile.monthly-calculation.calculate');
});
