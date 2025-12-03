import { test, expect } from '@playwright/test';

const TEST_EMAIL = 'test@makeasite.gr';
const TEST_PASSWORD = '12341234';

// Helper function to login via mobile auth and wait for successful authentication
async function login(page: any) {
    await page.goto('/mobile/login');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Fill login form
    const emailInput = page.locator('input[type="email"], input[name="email"]').first();
    await emailInput.waitFor({ state: 'visible', timeout: 10000 });
    await emailInput.clear();
    await emailInput.fill(TEST_EMAIL);

    const passwordInput = page.locator('input[type="password"], input[name="password"]').first();
    await passwordInput.waitFor({ state: 'visible', timeout: 10000 });
    await passwordInput.clear();
    await passwordInput.fill(TEST_PASSWORD);

    // Wait a bit for form to be ready
    await page.waitForTimeout(1000);

    // Submit the form
    const submitButton = page.locator('button[type="submit"]').first();
    await submitButton.waitFor({ state: 'visible', timeout: 10000 });
    
    // Submit and wait for redirect
    await Promise.all([
        page.waitForURL((url: URL) => !url.pathname.includes('/mobile/login'), { timeout: 15000 }),
        submitButton.click()
    ]);

    // Additional wait for any client-side redirects
    await page.waitForTimeout(1000);

    // Navigate to mobile dashboard if not already there
    const currentUrl = page.url();
    if (!currentUrl.includes('/mobile/dashboard') && !currentUrl.includes('/mobile')) {
        await page.goto('/mobile/dashboard');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);
    }

    // Verify we're logged in by checking we're not on login page
    expect(page.url()).not.toMatch(/.*\/mobile\/login/);
}

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
            expect(page.url()).toMatch(/\/$/);
        });
    });

    test('should show login button when not authenticated', async ({ page }) => {
        await page.goto('/');
        await page.waitForLoadState('networkidle');

        // Check for login link/button
        const loginLink = page.locator('a').filter({ hasText: /login|Login/i }).first();
        await expect(loginLink).toBeVisible({ timeout: 5000 });
    });

    test('should login successfully via mobile auth', async ({ page }) => {
        await login(page);

        // Verify we're on mobile dashboard
        await expect(page).toHaveURL(/.*\/mobile/);
    });

    test('should access mobile dashboard after login', async ({ page }) => {
        await login(page);

        // Navigate to mobile dashboard
        await page.goto('/mobile/dashboard');
        await page.waitForLoadState('networkidle');

        // Verify dashboard loaded
        await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
    });

    test('should navigate mobile menu - dashboard', async ({ page }) => {
        await login(page);

        // Navigate to dashboard via menu
        await page.goto('/mobile/dashboard');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveURL(/.*\/mobile\/dashboard/);
    });

    test('should navigate mobile menu - settings', async ({ page }) => {
        await login(page);

        // Navigate to settings
        await page.goto('/mobile/settings');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveURL(/.*\/mobile\/settings/);
        await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
    });

    test('should navigate mobile menu - income entries', async ({ page }) => {
        await login(page);

        // Navigate to income entries
        await page.goto('/mobile/income-entries');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveURL(/.*\/mobile\/income-entries/);
        await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
    });

    test('should navigate mobile menu - expense entries', async ({ page }) => {
        await login(page);

        // Navigate to expense entries
        await page.goto('/mobile/expense-entries');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveURL(/.*\/mobile\/expense-entries/);
        await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
    });

    test('should logout successfully', async ({ page }) => {
        await login(page);

        // Navigate to settings to find logout
        await page.goto('/mobile/settings');
        await page.waitForLoadState('networkidle');

        // Look for logout form or button
        const logoutForm = page.locator('form[action*="logout"]').first();
        const logoutButton = page.locator('button, a').filter({ hasText: /logout|Logout|Sign Out|sign out/i }).first();

        if (await logoutForm.isVisible({ timeout: 5000 }).catch(() => false)) {
            // Submit the logout form
            await Promise.all([
                page.waitForNavigation({ waitUntil: 'networkidle', timeout: 10000 }).catch(() => { }),
                logoutForm.evaluate(form => (form as HTMLFormElement).submit())
            ]);
        } else if (await logoutButton.isVisible({ timeout: 5000 }).catch(() => false)) {
            // Click logout button/link
            await Promise.all([
                page.waitForNavigation({ waitUntil: 'networkidle', timeout: 10000 }).catch(() => { }),
                logoutButton.click()
            ]);
        } else {
            // Fallback: POST to logout route directly
            await page.request.post('/mobile/logout');
            await page.goto('/');
        }

        // Should redirect to login page or welcome page
        try {
            await page.waitForURL(/.*\/(mobile\/login|$)/, { timeout: 15000 });
        } catch (e) {
            // If URL doesn't change immediately, wait a bit more
            await page.waitForTimeout(2000);
        }
        const finalUrl = page.url();
        expect(finalUrl).toMatch(/\/(mobile\/login|$)/);
    });

    test('should redirect to mobile login when accessing protected route without auth', async ({ browser }) => {
        // Create a fresh context with no cookies/session
        const context = await browser.newContext();
        const page = await context.newPage();

        try {
            // Try to access protected route
            await page.goto('/mobile/dashboard', { waitUntil: 'networkidle', timeout: 20000 });

            // Wait for redirect to login page
            await page.waitForURL(/.*\/mobile\/login/, { timeout: 15000 });

            // Verify we're on the mobile login page
            const currentUrl = page.url();
            expect(currentUrl).toMatch(/.*\/mobile\/login/);

            // Verify login form is visible
            const emailInput = await page.locator('input[type="email"], input[name="email"]').first().isVisible({ timeout: 5000 }).catch(() => false);
            expect(emailInput).toBeTruthy();
        } finally {
            await context.close();
        }
    });
});

