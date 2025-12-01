import { test, expect } from '@playwright/test';

test.describe('Savings Goal', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin');
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

