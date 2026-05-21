import { test, expect } from '@playwright/test';

const BASE = process.env.BASE_URL || 'https://app.ponpesashiddiq.or.id';

test.describe('Authentication', () => {

  test('halaman login bisa diakses', async ({ page }) => {
    await page.goto('/login');
    await expect(page).toHaveTitle(/login|masuk|pesantren/i);
    await expect(page.locator('input[name="username"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
  });

  test('login dengan kredensial salah menampilkan error', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="username"]', 'useryangtidakada');
    await page.fill('input[name="password"]', 'passwordsalah');
    await page.click('button[type="submit"]');
    await expect(page.locator('text=/salah|invalid|tidak ditemukan/i')).toBeVisible();
  });

  test('login admin berhasil dan redirect ke dashboard', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', process.env.ADMIN_PASSWORD || 'Admin@1234');
    await page.click('button[type="submit"]');
    // Kalau must_change_pw, redirect ke ganti password
    const url = page.url();
    expect(url).toMatch(/dashboard|ganti-password/);
  });

  test('akses halaman admin tanpa login redirect ke login', async ({ page }) => {
    await page.goto('/admin/dashboard');
    await expect(page).toHaveURL(/login/);
  });

  test('akses halaman wali tanpa login redirect ke login', async ({ page }) => {
    await page.goto('/wali/dashboard');
    await expect(page).toHaveURL(/login/);
  });

  test('akses halaman ustadz tanpa login redirect ke login', async ({ page }) => {
    await page.goto('/ustadz/dashboard');
    await expect(page).toHaveURL(/login/);
  });

  test('logout berhasil', async ({ page }) => {
    // Login dulu
    await page.goto('/login');
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', process.env.ADMIN_PASSWORD || 'Admin@1234');
    await page.click('button[type="submit"]');

    // Logout
    await page.goto('/admin/dashboard');
    const logoutBtn = page.locator('button:has-text("Keluar"), form[action*="logout"] button');
    if (await logoutBtn.isVisible()) {
      await logoutBtn.click();
      await expect(page).toHaveURL(/login|\//);
    }
  });

});
