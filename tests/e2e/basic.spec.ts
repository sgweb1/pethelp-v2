import { test, expect } from '@playwright/test';

test.describe('Basic functionality', () => {
  test('homepage loads successfully', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle(/PetHelp/);
  });

  test('registration page is accessible', async ({ page }) => {
    await page.goto('/register');
    await expect(page.locator('h1')).toContainText('Rejestracja');
    await expect(page.locator('form')).toBeVisible();
  });

  test('can switch between owner and sitter roles', async ({ page }) => {
    await page.goto('/register');

    // Check if owner role is selected by default
    await expect(page.locator('[data-role="owner"]')).toHaveClass(/bg-indigo-50/);

    // Click sitter role
    await page.click('[data-role="sitter"]');
    await expect(page.locator('[data-role="sitter"]')).toHaveClass(/bg-indigo-50/);
  });

  test('registration form validation works', async ({ page }) => {
    await page.goto('/register');

    // Try to submit empty form
    await page.click('button[type="submit"]');

    // Should see validation errors
    await expect(page.locator('.text-red-600')).toBeVisible();
  });

  test('user can fill registration form', async ({ page }) => {
    await page.goto('/register');

    // Fill form fields
    await page.fill('[name="name"]', 'testuser123');
    await page.fill('[name="first_name"]', 'Test');
    await page.fill('[name="last_name"]', 'User');
    await page.fill('[name="email"]', 'test@example.com');
    await page.fill('[name="password"]', 'password123');
    await page.fill('[name="password_confirmation"]', 'password123');

    // Check if form is filled
    await expect(page.locator('[name="name"]')).toHaveValue('testuser123');
    await expect(page.locator('[name="email"]')).toHaveValue('test@example.com');
  });
});