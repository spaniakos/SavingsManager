import { test, expect } from '@playwright/test';

const TEST_EMAIL = 'test@makeasite.gr';
const TEST_PASSWORD = '12341234';

test.describe('Savings Goal', () => {
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

  test('should create savings goal', async ({ page }) => {
    await page.goto('/admin/savings-goals/create');
    
    // Fill form
    await page.fill('input[name="name"]', 'Test Goal');
    await page.fill('input[name="target_amount"]', '10000');
    await page.fill('input[name="current_amount"]', '2000');
    await page.fill('input[name="start_date"]', new Date().toISOString().split('T')[0]);
    
    const futureDate = new Date();
    futureDate.setMonth(futureDate.getMonth() + 12);
    await page.fill('input[name="target_date"]', futureDate.toISOString().split('T')[0]);
    
    // Submit
    await page.click('button[type="submit"]');
    
    // Verify success
    await expect(page.locator('text=created successfully')).toBeVisible();
  });

  test('should display progress bars', async ({ page }) => {
    await page.goto('/admin/savings-goals');
    
    // Check for progress display
    await expect(page.locator('text=Progress')).toBeVisible();
  });
});

