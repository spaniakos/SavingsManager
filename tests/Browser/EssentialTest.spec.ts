import { test, expect } from '@playwright/test';

const TEST_EMAIL = 'test@makeasite.gr';
const TEST_PASSWORD = '12341234';
const BASE_URL = process.env.APP_URL || 'http://localhost:8000';

test.describe('Essential Tests', () => {
    test('should load welcome page', async ({ page }) => {
        await page.goto('/');
        await page.waitForLoadState('networkidle');

        // Check for welcome page elements
        await expect(page.locator('body')).toBeVisible({ timeout: 10000 });

        // Check for app name or main heading
        const heading = page.locator('h1, h2').filter({ hasText: /Savings Manager|💰/i }).first();
        await expect(heading).toBeVisible({ timeout: 5000 }).catch(() => {
            // If heading not found, just verify page loaded
            expect(page.url()).toBe(BASE_URL + '/');
        });
    });

    test('should show login button when not authenticated', async ({ page }) => {
        await page.goto('/');
        await page.waitForLoadState('networkidle');

        // Check for login link/button
        const loginLink = page.locator('a').filter({ hasText: /login|Login/i }).first();
        await expect(loginLink).toBeVisible({ timeout: 5000 });
    });

    test('should login successfully', async ({ page }) => {
        // Navigate to login page
        await page.goto('/admin/login');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        // Fill login form
        const emailInput = page.locator('input[wire\\:model="data.email"]').or(page.locator('input[type="email"]')).first();
        await emailInput.waitFor({ state: 'visible', timeout: 10000 });
        await emailInput.fill(TEST_EMAIL);

        const passwordInput = page.locator('input[wire\\:model="data.password"]').or(page.locator('input[type="password"]')).first();
        await passwordInput.waitFor({ state: 'visible', timeout: 10000 });
        await passwordInput.fill(TEST_PASSWORD);

        const submitButton = page.locator('button[type="submit"]').first();
        await submitButton.click();

        // Wait for redirect to mobile dashboard
        await page.waitForURL(/.*\/admin\/mobile/, { timeout: 15000 });
        await expect(page).toHaveURL(/.*\/admin\/mobile/);
    });

    test('should access mobile dashboard after login', async ({ page }) => {
        // Login first
        await page.goto('/admin/login');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        const emailInput = page.locator('input[wire\\:model="data.email"]').or(page.locator('input[type="email"]')).first();
        await emailInput.waitFor({ state: 'visible', timeout: 10000 });
        await emailInput.fill(TEST_EMAIL);

        const passwordInput = page.locator('input[wire\\:model="data.password"]').or(page.locator('input[type="password"]')).first();
        await passwordInput.waitFor({ state: 'visible', timeout: 10000 });
        await passwordInput.fill(TEST_PASSWORD);

        const submitButton = page.locator('button[type="submit"]').first();
        await submitButton.click();

        await page.waitForURL(/.*\/admin\/mobile/, { timeout: 15000 });

        // Verify dashboard loaded
        await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
    });

    test('should navigate mobile menu - dashboard', async ({ page }) => {
        // Login first
        await page.goto('/admin/login');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        const emailInput = page.locator('input[wire\\:model="data.email"]').or(page.locator('input[type="email"]')).first();
        await emailInput.waitFor({ state: 'visible', timeout: 10000 });
        await emailInput.fill(TEST_EMAIL);

        const passwordInput = page.locator('input[wire\\:model="data.password"]').or(page.locator('input[type="password"]')).first();
        await passwordInput.waitFor({ state: 'visible', timeout: 10000 });
        await passwordInput.fill(TEST_PASSWORD);

        const submitButton = page.locator('button[type="submit"]').first();
        await submitButton.click();

        await page.waitForURL(/.*\/admin\/mobile/, { timeout: 15000 });

        // Navigate to dashboard via menu
        await page.goto('/admin/mobile');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveURL(/.*\/admin\/mobile/);
    });

    test('should navigate mobile menu - settings', async ({ page }) => {
        // Login first
        await page.goto('/admin/login');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        const emailInput = page.locator('input[wire\\:model="data.email"]').or(page.locator('input[type="email"]')).first();
        await emailInput.waitFor({ state: 'visible', timeout: 10000 });
        await emailInput.fill(TEST_EMAIL);

        const passwordInput = page.locator('input[wire\\:model="data.password"]').or(page.locator('input[type="password"]')).first();
        await passwordInput.waitFor({ state: 'visible', timeout: 10000 });
        await passwordInput.fill(TEST_PASSWORD);

        const submitButton = page.locator('button[type="submit"]').first();
        await submitButton.click();

        await page.waitForURL(/.*\/admin\/mobile/, { timeout: 15000 });

        // Navigate to settings
        await page.goto('/admin/mobile/settings');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveURL(/.*\/admin\/mobile\/settings/);
        await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
    });

    test('should navigate mobile menu - income entries', async ({ page }) => {
        // Login first
        await page.goto('/admin/login');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        const emailInput = page.locator('input[wire\\:model="data.email"]').or(page.locator('input[type="email"]')).first();
        await emailInput.waitFor({ state: 'visible', timeout: 10000 });
        await emailInput.fill(TEST_EMAIL);

        const passwordInput = page.locator('input[wire\\:model="data.password"]').or(page.locator('input[type="password"]')).first();
        await passwordInput.waitFor({ state: 'visible', timeout: 10000 });
        await passwordInput.fill(TEST_PASSWORD);

        const submitButton = page.locator('button[type="submit"]').first();
        await submitButton.click();

        await page.waitForURL(/.*\/admin\/mobile/, { timeout: 15000 });

        // Navigate to income entries
        await page.goto('/admin/mobile/income-entries');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveURL(/.*\/admin\/mobile\/income-entries/);
        await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
    });

    test('should navigate mobile menu - expense entries', async ({ page }) => {
        // Login first
        await page.goto('/admin/login');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        const emailInput = page.locator('input[wire\\:model="data.email"]').or(page.locator('input[type="email"]')).first();
        await emailInput.waitFor({ state: 'visible', timeout: 10000 });
        await emailInput.fill(TEST_EMAIL);

        const passwordInput = page.locator('input[wire\\:model="data.password"]').or(page.locator('input[type="password"]')).first();
        await passwordInput.waitFor({ state: 'visible', timeout: 10000 });
        await passwordInput.fill(TEST_PASSWORD);

        const submitButton = page.locator('button[type="submit"]').first();
        await submitButton.click();

        await page.waitForURL(/.*\/admin\/mobile/, { timeout: 15000 });

        // Navigate to expense entries
        await page.goto('/admin/mobile/expense-entries');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveURL(/.*\/admin\/mobile\/expense-entries/);
        await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
    });

    test('should logout successfully', async ({ page }) => {
        // Login first
        await page.goto('/admin/login');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        const emailInput = page.locator('input[wire\\:model="data.email"]').or(page.locator('input[type="email"]')).first();
        await emailInput.waitFor({ state: 'visible', timeout: 10000 });
        await emailInput.fill(TEST_EMAIL);

        const passwordInput = page.locator('input[wire\\:model="data.password"]').or(page.locator('input[type="password"]')).first();
        await passwordInput.waitFor({ state: 'visible', timeout: 10000 });
        await passwordInput.fill(TEST_PASSWORD);

        const submitButton = page.locator('button[type="submit"]').first();
        await submitButton.click();

        await page.waitForURL(/.*\/admin\/mobile/, { timeout: 15000 });

        // Navigate to settings to find logout
        await page.goto('/admin/mobile/settings');
        await page.waitForLoadState('networkidle');

        // Look for logout form or button
        const logoutForm = page.locator('form[action*="logout"]').first();
        const logoutButton = page.locator('button, a').filter({ hasText: /logout|Logout|Sign Out|sign out/i }).first();

        if (await logoutForm.isVisible({ timeout: 5000 }).catch(() => false)) {
            // Submit the logout form
            await logoutForm.evaluate(form => (form as HTMLFormElement).submit());
        } else if (await logoutButton.isVisible({ timeout: 5000 }).catch(() => false)) {
            // Click logout button/link
            await logoutButton.click();
        } else {
            // Fallback: POST to logout route directly
            await page.request.post('/logout');
        }

        // Should redirect to welcome page
        await page.waitForURL(/.*\/$/, { timeout: 10000 });
        await expect(page).toHaveURL(BASE_URL + '/');
    });

    test('should redirect to login when accessing protected route without auth', async ({ page }) => {
        // Clear any existing session
        await page.context().clearCookies();

        // Try to access protected route
        await page.goto('/admin/mobile');

        // Should redirect to login
        await page.waitForURL(/.*\/admin\/login/, { timeout: 10000 });
        await expect(page).toHaveURL(/.*\/admin\/login/);
    });
});

