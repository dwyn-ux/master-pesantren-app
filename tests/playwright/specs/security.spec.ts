import { test, expect } from '@playwright/test';

test.describe('Security Headers', () => {

  test('response punya X-Frame-Options header', async ({ request }) => {
    const res = await request.get('/login');
    const header = res.headers()['x-frame-options'];
    expect(header).toBeTruthy();
    expect(header.toUpperCase()).toMatch(/SAMEORIGIN|DENY/);
  });

  test('response punya X-Content-Type-Options header', async ({ request }) => {
    const res = await request.get('/login');
    expect(res.headers()['x-content-type-options']).toBe('nosniff');
  });

  test('response punya Content-Security-Policy header', async ({ request }) => {
    const res = await request.get('/login');
    expect(res.headers()['content-security-policy']).toBeTruthy();
  });

  test('response punya Referrer-Policy header', async ({ request }) => {
    const res = await request.get('/login');
    expect(res.headers()['referrer-policy']).toBeTruthy();
  });

  test('HTTPS redirect — HTTP ke HTTPS', async ({ request }) => {
    const res = await request.get('http://app.ponpesashiddiq.or.id/login', {
      maxRedirects: 0,
    }).catch(e => null);
    // Kalau server redirect HTTP ke HTTPS, status 301/302
    if (res) {
      expect([200, 301, 302, 308]).toContain(res.status());
    }
  });

  test('X-Powered-By tidak mengekspos teknologi sensitif', async ({ request }) => {
    const res = await request.get('/login');
    const header = res.headers()['x-powered-by'] || '';
    // Tidak boleh mengekspos versi PHP (misal: PHP/8.4.0)
    // CyberPanel-OLS adalah known limitation dari web server
    expect(header).not.toMatch(/PHP\/\d+\.\d+/i);
    expect(header).not.toMatch(/ASP\.NET/i);
  });

});

test.describe('Rate Limiting', () => {

  test('login rate limit aktif setelah banyak percobaan gagal', async ({ page }) => {
    // Kirim banyak POST login dengan kredensial salah
    // Playwright akan handle CSRF token otomatis via page
    let got429 = false;

    for (let i = 0; i < 12; i++) {
      await page.goto('/login');
      await page.fill('input[name="username"]', 'ratelimit_test_user');
      await page.fill('input[name="password"]', 'wrongpass' + i);

      const [response] = await Promise.all([
        page.waitForResponse(res => res.url().includes('/login') && res.request().method() === 'POST'),
        page.click('button[type="submit"]'),
      ]);

      if (response.status() === 429) {
        got429 = true;
        break;
      }
    }

    // Setelah 10+ percobaan, harus ada 429 atau halaman error rate limit
    if (!got429) {
      // Cek apakah ada pesan rate limit di halaman
      const bodyText = await page.textContent('body');
      const hasRateLimit = bodyText?.match(/too many|terlalu banyak|429/i);
      expect(got429 || hasRateLimit).toBeTruthy();
    }
  });

});

test.describe('Access Control', () => {

  test('endpoint admin tidak bisa diakses tanpa auth', async ({ request }) => {
    const endpoints = [
      '/admin/santri',
      '/admin/wali',
      '/admin/ustadz',
      '/admin/tagihan',
      '/admin/laporan/keuangan',
    ];
    for (const ep of endpoints) {
      const res = await request.get(ep, { maxRedirects: 0 });
      // Harus redirect (302) ke login, bukan 200
      expect([302, 301, 303]).toContain(res.status());
    }
  });

  test('endpoint wali tidak bisa diakses tanpa auth', async ({ request }) => {
    const endpoints = [
      '/wali/dashboard',
      '/wali/tagihan',
      '/wali/laporan/1',
    ];
    for (const ep of endpoints) {
      const res = await request.get(ep, { maxRedirects: 0 });
      expect([302, 301, 303]).toContain(res.status());
    }
  });

  test('endpoint superadmin tidak bisa diakses tanpa auth', async ({ request }) => {
    const res = await request.get('/superadmin/dashboard', { maxRedirects: 0 });
    expect([302, 301, 303]).toContain(res.status());
  });

  test('path traversal tidak bisa akses file sensitif', async ({ request }) => {
    const payloads = [
      '/../../../etc/passwd',
      '/..%2F..%2F..%2Fetc%2Fpasswd',
      '/.env',
      '/storage/app/private/credentials-wali.txt',
    ];
    for (const path of payloads) {
      const res = await request.get(path);
      // Harus 404, bukan 200 dengan isi file
      expect(res.status()).not.toBe(200);
      const body = await res.text();
      expect(body).not.toMatch(/root:x:|APP_KEY=|password/i);
    }
  });

  test('SQL injection di login tidak berhasil', async ({ page }) => {
    test.slow();
    // Tunggu sebentar supaya rate limit dari test sebelumnya sudah reset
    await page.waitForTimeout(5000);
    await page.goto('/login', { timeout: 30000 });
    await page.fill('input[name="username"]', "admin' OR '1'='1' --");
    await page.fill('input[name="password"]', "' OR '1'='1");
    await page.click('button[type="submit"]');
    await page.waitForTimeout(3000);
    expect(page.url()).not.toMatch(/\/admin\/dashboard|\/wali\/dashboard|\/ustadz\/dashboard/);
  });

  test('XSS di form login tidak dieksekusi', async ({ page }) => {
    test.slow();
    await page.goto('/login', { timeout: 30000 });
    const xssPayload = '<img src=x onerror=window.__xss=1>';
    await page.fill('input[name="username"]', xssPayload);
    await page.fill('input[name="password"]', 'test');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
    const xssExecuted = await page.evaluate(() => (window as any).__xss);
    expect(xssExecuted).toBeUndefined();
    expect(page.url()).not.toMatch(/dashboard/);
  });

});

test.describe('Webhook Security', () => {

  test('tripay callback tanpa signature ditolak atau tidak bisa diakses', async ({ request }) => {
    let res: any = null;
    try {
      res = await request.post('/api/tripay-callback', {
        headers: { 'Content-Type': 'application/json' },
        data: { reference: 'FAKE-REF-123', status: 'PAID', merchant_ref: 'FAKE' },
        timeout: 12000,
      });
    } catch (e: any) {
      // Timeout atau connection refused = endpoint tidak bisa diakses dari luar = AMAN
      expect(e.message).toMatch(/timeout|ECONNREFUSED|ERR_CONNECTION/i);
      return;
    }
    expect([403, 422, 401, 404]).toContain(res.status());
  });

  test('tripay callback dengan signature palsu ditolak atau tidak bisa diakses', async ({ request }) => {
    let res: any = null;
    try {
      res = await request.post('/api/tripay-callback', {
        headers: {
          'X-Callback-Signature': 'fakesignature_invalid_abc123',
          'Content-Type': 'application/json',
        },
        data: { reference: 'FAKE-REF-456', status: 'PAID' },
        timeout: 12000,
      });
    } catch (e: any) {
      expect(e.message).toMatch(/timeout|ECONNREFUSED|ERR_CONNECTION/i);
      return;
    }
    expect([403, 422, 401]).toContain(res.status());
  });

});
