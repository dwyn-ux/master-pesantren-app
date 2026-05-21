import { test, expect, Page } from '@playwright/test';

async function loginUstadz(page: Page) {
  await page.goto('/login');
  await page.fill('input[name="username"]', process.env.USTADZ_USERNAME || 'ustadz.test');
  await page.fill('input[name="password"]', process.env.USTADZ_PASSWORD || 'ustadz.test');
  await page.click('button[type="submit"]');
  if (page.url().includes('ganti-password')) {
    const newPass = (process.env.USTADZ_PASSWORD || 'ustadz.test') + '!';
    await page.fill('input[name="password"]', newPass);
    await page.fill('input[name="password_confirmation"]', newPass);
    await page.click('button[type="submit"]');
  }
}

test.describe('Ustadz Panel', () => {

  test.skip(!process.env.USTADZ_USERNAME, 'USTADZ_USERNAME tidak diset di env');

  test.beforeEach(async ({ page }) => {
    await loginUstadz(page);
  });

  test('dashboard ustadz tampil', async ({ page }) => {
    await page.goto('/ustadz/dashboard');
    await expect(page).not.toHaveURL(/login/);
  });

  test('halaman setoran bisa diakses', async ({ page }) => {
    await page.goto('/ustadz/setoran');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('text=Setoran Hafalan')).toBeVisible();
  });

  test('form setoran punya searchable santri picker', async ({ page }) => {
    await page.goto('/ustadz/setoran');
    // Buka form
    const btnCatat = page.locator('button:has-text("Catat Setoran")');
    if (await btnCatat.isVisible()) {
      await btnCatat.click();
      // Input search santri harus ada (bukan select biasa)
      await expect(page.locator('input[placeholder*="nama"]')).toBeVisible();
    }
  });

  test('halaman klinik bisa diakses dan search santri muncul', async ({ page }) => {
    await page.goto('/ustadz/klinik');
    await expect(page).not.toHaveURL(/login/);
    // Klik input search
    const searchInput = page.locator('input[placeholder*="nama"]').first();
    await searchInput.click();
    // Dropdown santri harus muncul
    await expect(page.locator('[x-show*="showDropdown"], .absolute.z-50')).toBeVisible({ timeout: 5000 });
  });

  test('perizinan ustadz punya sidebar ustadz (bukan admin)', async ({ page }) => {
    await page.goto('/ustadz/perizinan');
    await expect(page).not.toHaveURL(/login/);
    // Sidebar harus punya menu ustadz, bukan admin
    const sidebarText = await page.locator('nav').textContent();
    expect(sidebarText).not.toMatch(/Data Santri|Data Wali|Jenis Tagihan/);
  });

  test('ustadz tidak bisa akses halaman admin', async ({ page }) => {
    await page.goto('/admin/santri');
    await expect(page).not.toHaveURL('/admin/santri');
  });

  test('voice note create punya searchable santri', async ({ page }) => {
    await page.goto('/ustadz/voice-note/create');
    await expect(page).not.toHaveURL(/login/);
    // Input search santri harus ada
    await expect(page.locator('input[placeholder*="nama"], input[placeholder*="NIS"]')).toBeVisible();
  });

});
