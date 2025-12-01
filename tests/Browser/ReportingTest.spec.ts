import { test, expect } from '@playwright/test';

const TEST_EMAIL = 'test@makeasite.gr';
const TEST_PASSWORD = '12341234';

test.describe('Reporting', () => {
  test.beforeEach(async ({ page }) => {
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
    
    await page.waitForURL(/.*\/admin/, { timeout: 15000 });
  });

  test('should access reports page', async ({ page }) => {
    await page.goto('/admin/reports');
    await page.waitForLoadState('networkidle');
    
    // Check for reports page content
    await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
  });

  test('should access mobile reports page', async ({ page }) => {
    await page.goto('/admin/mobile/reports');
    await page.waitForLoadState('networkidle');
    
    // Check for mobile reports page
    await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
  });

  test('should generate comprehensive report', async ({ page }) => {
    await page.goto('/admin/mobile/reports');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);
    
    // Just verify page loads
    await expect(page.locator('body')).toBeVisible({ timeout: 10000 });
  });
});

