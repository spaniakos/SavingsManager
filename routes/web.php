<?php

use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::post('/admin/language-switch', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->middleware(['web']);

// Mobile routes
use App\Http\Controllers\Mobile\MobileDashboardController;
use App\Http\Controllers\Mobile\MobileExpenseController;
use App\Http\Controllers\Mobile\MobileIncomeController;
use App\Http\Controllers\Mobile\MobileSettingsController;

Route::middleware(['auth'])->group(function () {
    // Logout route
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

    // Main mobile route - redirect /admin to /admin/mobile
    Route::get('/admin', function () {
        return redirect('/admin/mobile');
    });

    Route::get('/admin/mobile', [MobileDashboardController::class, 'index'])
        ->name('mobile.dashboard');

    // Expense routes
    Route::get('/admin/mobile/expense', [MobileExpenseController::class, 'index'])
        ->name('mobile.expense.index');
    Route::get('/admin/mobile/expense/super-category/{superCategoryId}', [MobileExpenseController::class, 'showCategories'])
        ->name('mobile.expense.categories');
    Route::get('/admin/mobile/expense/category/{categoryId}/create', [MobileExpenseController::class, 'create'])
        ->name('mobile.expense.create');
    Route::post('/admin/mobile/expense/category/{categoryId}', [MobileExpenseController::class, 'store'])
        ->name('mobile.expense.store');

    // Income routes
    Route::get('/admin/mobile/income', [MobileIncomeController::class, 'index'])
        ->name('mobile.income.index');
    Route::get('/admin/mobile/income/category/{categoryId}/create', [MobileIncomeController::class, 'create'])
        ->name('mobile.income.create');
    Route::post('/admin/mobile/income/category/{categoryId}', [MobileIncomeController::class, 'store'])
        ->name('mobile.income.store');

    // Settings route
    Route::get('/admin/mobile/settings', [MobileSettingsController::class, 'index'])
        ->name('mobile.settings');

    // Profile Settings
    Route::get('/admin/mobile/profile-settings', [\App\Http\Controllers\Mobile\MobileProfileSettingsController::class, 'index'])
        ->name('mobile.profile-settings');
    Route::put('/admin/mobile/profile-settings', [\App\Http\Controllers\Mobile\MobileProfileSettingsController::class, 'update'])
        ->name('mobile.profile-settings.update');

    // Expense Entries
    Route::get('/admin/mobile/expense-entries', [\App\Http\Controllers\Mobile\MobileExpenseEntriesController::class, 'index'])
        ->name('mobile.expense-entries.index');
    Route::get('/admin/mobile/expense-entries/{id}/edit', [\App\Http\Controllers\Mobile\MobileExpenseEntriesController::class, 'edit'])
        ->name('mobile.expense-entries.edit');
    Route::put('/admin/mobile/expense-entries/{id}', [\App\Http\Controllers\Mobile\MobileExpenseEntriesController::class, 'update'])
        ->name('mobile.expense-entries.update');
    Route::delete('/admin/mobile/expense-entries/{id}', [\App\Http\Controllers\Mobile\MobileExpenseEntriesController::class, 'destroy'])
        ->name('mobile.expense-entries.destroy');

    // Income Entries
    Route::get('/admin/mobile/income-entries', [\App\Http\Controllers\Mobile\MobileIncomeEntriesController::class, 'index'])
        ->name('mobile.income-entries.index');
    Route::get('/admin/mobile/income-entries/{id}/edit', [\App\Http\Controllers\Mobile\MobileIncomeEntriesController::class, 'edit'])
        ->name('mobile.income-entries.edit');
    Route::put('/admin/mobile/income-entries/{id}', [\App\Http\Controllers\Mobile\MobileIncomeEntriesController::class, 'update'])
        ->name('mobile.income-entries.update');
    Route::delete('/admin/mobile/income-entries/{id}', [\App\Http\Controllers\Mobile\MobileIncomeEntriesController::class, 'destroy'])
        ->name('mobile.income-entries.destroy');

    // Expense Super Categories
    Route::get('/admin/mobile/expense-super-categories', [\App\Http\Controllers\Mobile\MobileExpenseSuperCategoriesController::class, 'index'])
        ->name('mobile.expense-super-categories.index');
    Route::get('/admin/mobile/expense-super-categories/{id}/edit', [\App\Http\Controllers\Mobile\MobileExpenseSuperCategoriesController::class, 'edit'])
        ->name('mobile.expense-super-categories.edit');
    Route::put('/admin/mobile/expense-super-categories/{id}', [\App\Http\Controllers\Mobile\MobileExpenseSuperCategoriesController::class, 'update'])
        ->name('mobile.expense-super-categories.update');

    // Expense Categories (full CRUD)
    Route::get('/admin/mobile/expense-categories', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'index'])
        ->name('mobile.expense-categories.index');
    Route::get('/admin/mobile/expense-categories/create', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'create'])
        ->name('mobile.expense-categories.create');
    Route::post('/admin/mobile/expense-categories', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'store'])
        ->name('mobile.expense-categories.store');
    Route::get('/admin/mobile/expense-categories/{id}/edit', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'edit'])
        ->name('mobile.expense-categories.edit');
    Route::put('/admin/mobile/expense-categories/{id}', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'update'])
        ->name('mobile.expense-categories.update');
    Route::delete('/admin/mobile/expense-categories/{id}', [\App\Http\Controllers\Mobile\MobileExpenseCategoriesController::class, 'destroy'])
        ->name('mobile.expense-categories.destroy');

    // Income Categories (full CRUD)
    Route::get('/admin/mobile/income-categories', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'index'])
        ->name('mobile.income-categories.index');
    Route::get('/admin/mobile/income-categories/create', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'create'])
        ->name('mobile.income-categories.create');
    Route::post('/admin/mobile/income-categories', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'store'])
        ->name('mobile.income-categories.store');
    Route::get('/admin/mobile/income-categories/{id}/edit', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'edit'])
        ->name('mobile.income-categories.edit');
    Route::put('/admin/mobile/income-categories/{id}', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'update'])
        ->name('mobile.income-categories.update');
    Route::delete('/admin/mobile/income-categories/{id}', [\App\Http\Controllers\Mobile\MobileIncomeCategoriesController::class, 'destroy'])
        ->name('mobile.income-categories.destroy');

    // Savings Goals (view only)
    Route::get('/admin/mobile/savings-goals', [\App\Http\Controllers\Mobile\MobileSavingsGoalsController::class, 'index'])
        ->name('mobile.savings-goals.index');

    // Savings Goals Admin (full CRUD)
    Route::get('/admin/mobile/savings-goals-admin', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'index'])
        ->name('mobile.savings-goals-admin.index');
    Route::get('/admin/mobile/savings-goals-admin/create', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'create'])
        ->name('mobile.savings-goals-admin.create');
    Route::post('/admin/mobile/savings-goals-admin', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'store'])
        ->name('mobile.savings-goals-admin.store');
    Route::get('/admin/mobile/savings-goals-admin/{id}/edit', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'edit'])
        ->name('mobile.savings-goals-admin.edit');
    Route::put('/admin/mobile/savings-goals-admin/{id}', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'update'])
        ->name('mobile.savings-goals-admin.update');
    Route::delete('/admin/mobile/savings-goals-admin/{id}', [\App\Http\Controllers\Mobile\MobileSavingsGoalsAdminController::class, 'destroy'])
        ->name('mobile.savings-goals-admin.destroy');

    // Reports
    Route::get('/admin/mobile/reports', [\App\Http\Controllers\Mobile\MobileReportsController::class, 'index'])
        ->name('mobile.reports.index');
    Route::get('/admin/mobile/reports/export-pdf', [\App\Http\Controllers\Mobile\MobileReportsController::class, 'exportPdf'])
        ->name('mobile.reports.export-pdf');

    // Monthly Calculation
    Route::post('/admin/mobile/monthly-calculation', [\App\Http\Controllers\Mobile\MonthlyCalculationController::class, 'calculate'])
        ->name('mobile.monthly-calculation.calculate');
});
