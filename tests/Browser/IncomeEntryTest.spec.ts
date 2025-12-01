import { test, expect } from '@playwright/test';

test.describe('Income Entry', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin');
  });

  test('should create income entry', async ({ page }) => {
    await page.goto('/admin/income-entries/create');
    
    // Fill form
    await page.selectOption('select[name="income_category_id"]', { index: 1 });
    await page.fill('input[name="amount"]', '2000');
    await page.fill('input[name="date"]', new Date().toISOString().split('T')[0]);
    
    // Submit
    await page.click('button[type="submit"]');
    
    // Verify success
    await expect(page.locator('text=created successfully')).toBeVisible();
  });

  test('should validate required fields', async ({ page }) => {
    await page.goto('/admin/income-entries/create');
    
    // Try to submit without filling
    await page.click('button[type="submit"]');
    
    // Should show validation errors
    await expect(page.locator('text=required')).toBeVisible();
  });
});

