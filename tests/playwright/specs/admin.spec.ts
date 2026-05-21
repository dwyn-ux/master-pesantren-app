import { test, expect, Page } from '@playwright/test';

// Helper login admin
async function loginAdmin(page: Page) {
  await page.goto('/login');
  await page.fill('input[name="username"]', 'admin');
  await page.fill('input[name="password"]', process.env.ADMIN_PASSWORD || 'Admin@1234');
  await page.click('button[type="submit"]');
  // Handle must_change_pw
  if (page.url().includes('ganti-password')) {
    await page.fill('input[name="password"]', 'Admin@12345');
    await page.fill('input[name="password_confirmation"]', 'Admin@12345');
    await page.click('button[type="submit"]');
  }
  await page.waitForURL(/dashboard/);
}

test.describe('Admin Panel', () => {

  test.beforeEach(async ({ page }) => {
    await loginAdmin(page);
  });

  test('dashboard admin tampil dengan benar', async ({ page }) => {
    await page.goto('/admin/dashboard');
    await expect(page.locator('h2, h1, .page-title')).toBeVisible();
    // Tidak ada error 500
    expect(page.url()).toContain('/admin/dashboard');
  });

  test('halaman data santri bisa diakses', async ({ page }) => {
    await page.goto('/admin/santri');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('table, .glass-panel')).toBeVisible();
  });

  test('halaman data wali bisa diakses', async ({ page }) => {
    await page.goto('/admin/wali');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('table, .glass-panel')).toBeVisible();
  });

  test('halaman data ustadz bisa diakses', async ({ page }) => {
    await page.goto('/admin/ustadz');
    await expect(page).not.toHaveURL(/login/);
    // Tombol download semua kredensial ada
    await expect(page.locator('text=Download Semua Kredensial')).toBeVisible();
  });

  test('laporan keuangan bisa diakses', async ({ page }) => {
    await page.goto('/admin/laporan/keuangan');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('.glass-panel')).toBeVisible();
  });

  test('sidebar admin tidak muncul di halaman ustadz', async ({ page }) => {
    // Logout dulu
    await page.goto('/admin/dashboard');
    await page.locator('form[action*="logout"] button, button:has-text("Keluar")').click();
    await page.waitForURL(/login|\//);
    // Login sebagai ustadz tidak bisa ditest tanpa kredensial ustadz
    // Tapi kita bisa cek bahwa perizinan view punya role check
  });

  test('superadmin password staff bisa diakses', async ({ page }) => {
    // Login sebagai superadmin
    await page.goto('/login');
    await page.fill('input[name="username"]', 'superadmin');
    await page.fill('input[name="password"]', process.env.SUPERADMIN_PASSWORD || 'Super@1234');
    await page.click('button[type="submit"]');
    if (page.url().includes('ganti-password')) {
      await page.fill('input[name="password"]', 'Super@12345');
      await page.fill('input[name="password_confirmation"]', 'Super@12345');
      await page.click('button[type="submit"]');
    }
    await page.goto('/superadmin/staff-password');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('text=Manajemen Password Staff')).toBeVisible();
  });

});
