import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/Browser',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  use: {
    baseURL: process.env.APP_URL || 'http://localhost:8000',
    trace: 'on-first-retry',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'Mobile Chrome',
      use: { ...devices['Pixel 5'] },
    },
  ],
  webServer: process.env.CI ? {
    command: 'php artisan serve --host=0.0.0.0',
    url: 'http://localhost:8000',
    reuseExistingServer: false,
    timeout: 120 * 1000,
  } : {
    command: 'php artisan serve',
    url: 'http://localhost:8000',
    reuseExistingServer: true,
  },
});


