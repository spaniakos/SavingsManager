import { test, expect } from '@playwright/test';

test.describe('Reporting', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin');
  });

  test('should access reports page', async ({ page }) => {
    await page.goto('/admin/reports');
    
    // Check for report type selector
    await expect(page.locator('text=Report Type')).toBeVisible();
  });

  test('should generate monthly report', async ({ page }) => {
    await page.goto('/admin/reports');
    
    // Select monthly report
    await page.selectOption('select[name="report_type"]', 'monthly');
    
    // Generate report
    await page.click('button:has-text("Generate Report")');
    
    // Verify report generated
    await expect(page.locator('text=Report Results')).toBeVisible();
  });

  test('should export CSV', async ({ page }) => {
    await page.goto('/admin/reports');
    
    // Generate a report first
    await page.selectOption('select[name="report_type"]', 'monthly');
    await page.click('button:has-text("Generate Report")');
    await page.waitForSelector('text=Report Results');
    
    // Click export CSV
    const downloadPromise = page.waitForEvent('download');
    await page.click('button:has-text("Export CSV")');
    const download = await downloadPromise;
    
    expect(download.suggestedFilename()).toContain('.csv');
  });
});

