import { test, expect, Page } from '@playwright/test';

// Helper login wali — pakai NIS santri sebagai username & password
async function loginWali(page: Page, username: string, password: string) {
  await page.goto('/login');
  await page.fill('input[name="username"]', username);
  await page.fill('input[name="password"]', password);
  await page.click('button[type="submit"]');
  if (page.url().includes('ganti-password')) {
    const newPass = password + '!';
    await page.fill('input[name="password"]', newPass);
    await page.fill('input[name="password_confirmation"]', newPass);
    await page.click('button[type="submit"]');
  }
}

test.describe('Wali Panel', () => {

  // Skip kalau tidak ada kredensial wali di env
  test.skip(!process.env.WALI_USERNAME, 'WALI_USERNAME tidak diset di env');

  test.beforeEach(async ({ page }) => {
    await loginWali(
      page,
      process.env.WALI_USERNAME || '',
      process.env.WALI_PASSWORD || '',
    );
  });

  test('dashboard wali tampil', async ({ page }) => {
    await page.goto('/wali/dashboard');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('text=Daftar Santri Anda')).toBeVisible();
  });

  test('halaman tagihan bisa diakses', async ({ page }) => {
    await page.goto('/wali/tagihan');
    await expect(page).not.toHaveURL(/login/);
  });

  test('halaman voice note bisa diakses', async ({ page }) => {
    await page.goto('/wali/voice-note');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('text=Pesan Suara')).toBeVisible();
  });

  test('halaman kesehatan bisa diakses', async ({ page }) => {
    await page.goto('/wali/kesehatan');
    await expect(page).not.toHaveURL(/login/);
  });

  test('notifikasi bisa diakses', async ({ page }) => {
    await page.goto('/notifications');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('text=Inbox Notifikasi')).toBeVisible();
  });

  test('wali tidak bisa akses halaman admin', async ({ page }) => {
    await page.goto('/admin/santri');
    // Harus redirect atau 403, bukan tampil halaman admin
    await expect(page).not.toHaveURL('/admin/santri');
  });

  test('wali tidak bisa akses data wali lain', async ({ page }) => {
    // Coba akses laporan santri yang bukan miliknya (ID 9999)
    await page.goto('/wali/laporan/9999');
    // Harus 403 atau redirect
    const status = await page.evaluate(() => document.title);
    expect(status).not.toMatch(/laporan/i);
  });

});
