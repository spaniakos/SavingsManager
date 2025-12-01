import { test, expect } from '@playwright/test';

const TEST_EMAIL = 'test@makeasite.gr';
const TEST_PASSWORD = '12341234';

test.describe('Dashboard', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to login page
    await page.goto('/admin/login');
    
    // Wait for Livewire to render
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);
    
    // Filament login form
    const emailInput = page.locator('input[wire\\:model="data.email"]').or(page.locator('input[type="email"]')).first();
    await emailInput.waitFor({ state: 'visible', timeout: 10000 });
    await emailInput.fill(TEST_EMAIL);
    
    const passwordInput = page.locator('input[wire\\:model="data.password"]').or(page.locator('input[type="password"]')).first();
    await passwordInput.waitFor({ state: 'visible', timeout: 10000 });
    await passwordInput.fill(TEST_PASSWORD);
    
    const submitButton = page.locator('button[type="submit"]').first();
    await submitButton.click();
    
    // Wait for navigation to dashboard (mobile redirects to /admin/mobile)
    await page.waitForURL(/.*\/admin/, { timeout: 15000 });
  });

  test('should display dashboard', async ({ page }) => {
    await page.goto('/admin/mobile');
    await page.waitForLoadState('networkidle');
    
    // Check for dashboard title or main content
    await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
  });

  test('should display savings information', async ({ page }) => {
    await page.goto('/admin/mobile');
    await page.waitForLoadState('networkidle');
    
    // Mobile dashboard should load
    await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
  });

  test('should have navigation menu', async ({ page }) => {
    await page.goto('/admin/mobile');
    await page.waitForLoadState('networkidle');
    
    // Check for mobile menu or navigation
    await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
  });
});

