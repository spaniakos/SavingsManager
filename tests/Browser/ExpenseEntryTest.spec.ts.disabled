import { test, expect } from '@playwright/test';

const TEST_EMAIL = 'test@makeasite.gr';
const TEST_PASSWORD = '12341234';

test.describe('Expense Entry', () => {
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

  test('should create expense entry', async ({ page }) => {
    await page.goto('/admin/expense-entries/create');
    
    // Fill form
    await page.selectOption('select[name="expense_category_id"]', { index: 1 });
    await page.fill('input[name="amount"]', '500');
    await page.fill('input[name="date"]', new Date().toISOString().split('T')[0]);
    
    // Submit
    await page.click('button[type="submit"]');
    
    // Verify success
    await expect(page.locator('text=created successfully')).toBeVisible();
  });

  test('should validate required fields', async ({ page }) => {
    await page.goto('/admin/expense-entries/create');
    
    // Try to submit without filling
    await page.click('button[type="submit"]');
    
    // Should show validation errors
    await expect(page.locator('text=required')).toBeVisible();
  });
});

