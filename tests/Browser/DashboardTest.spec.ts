import { test, expect } from '@playwright/test';

test.describe('Dashboard', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to login page
    await page.goto('/admin/login');
    
    // Login (assuming test user exists)
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Wait for navigation to dashboard
    await page.waitForURL('**/admin');
  });

  test('should display dashboard with widgets', async ({ page }) => {
    await page.goto('/admin');
    
    // Check for savings goal progress widget
    await expect(page.locator('text=Savings Goals')).toBeVisible();
    
    // Check for budget allocation widget
    await expect(page.locator('text=Budget Allocation')).toBeVisible();
    
    // Check for save-for-later widget
    await expect(page.locator('text=Save for Later Progress')).toBeVisible();
  });

  test('should display net worth', async ({ page }) => {
    await page.goto('/admin');
    
    // Check for net worth display
    await expect(page.locator('text=Net Worth')).toBeVisible();
  });

  test('should display charts', async ({ page }) => {
    await page.goto('/admin');
    
    // Check for expense chart
    await expect(page.locator('text=Expenses by Category')).toBeVisible();
    
    // Check for income trends chart
    await expect(page.locator('text=Income Trends')).toBeVisible();
  });
});

