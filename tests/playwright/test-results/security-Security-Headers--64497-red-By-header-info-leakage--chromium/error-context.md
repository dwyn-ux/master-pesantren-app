# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: security.spec.ts >> Security Headers >> tidak ada X-Powered-By header (info leakage)
- Location: specs\security.spec.ts:37:7

# Error details

```
Error: expect(received).toBeUndefined()

Received: "CyberPanel-OLS/2.4.4"
```

# Test source

```ts
  1   | import { test, expect } from '@playwright/test';
  2   | 
  3   | test.describe('Security Headers', () => {
  4   | 
  5   |   test('response punya X-Frame-Options header', async ({ request }) => {
  6   |     const res = await request.get('/login');
  7   |     const header = res.headers()['x-frame-options'];
  8   |     expect(header).toBeTruthy();
  9   |     expect(header.toUpperCase()).toMatch(/SAMEORIGIN|DENY/);
  10  |   });
  11  | 
  12  |   test('response punya X-Content-Type-Options header', async ({ request }) => {
  13  |     const res = await request.get('/login');
  14  |     expect(res.headers()['x-content-type-options']).toBe('nosniff');
  15  |   });
  16  | 
  17  |   test('response punya Content-Security-Policy header', async ({ request }) => {
  18  |     const res = await request.get('/login');
  19  |     expect(res.headers()['content-security-policy']).toBeTruthy();
  20  |   });
  21  | 
  22  |   test('response punya Referrer-Policy header', async ({ request }) => {
  23  |     const res = await request.get('/login');
  24  |     expect(res.headers()['referrer-policy']).toBeTruthy();
  25  |   });
  26  | 
  27  |   test('HTTPS redirect — HTTP ke HTTPS', async ({ request }) => {
  28  |     const res = await request.get('http://app.ponpesashiddiq.or.id/login', {
  29  |       maxRedirects: 0,
  30  |     }).catch(e => null);
  31  |     // Kalau server redirect HTTP ke HTTPS, status 301/302
  32  |     if (res) {
  33  |       expect([200, 301, 302, 308]).toContain(res.status());
  34  |     }
  35  |   });
  36  | 
  37  |   test('tidak ada X-Powered-By header (info leakage)', async ({ request }) => {
  38  |     const res = await request.get('/login');
> 39  |     expect(res.headers()['x-powered-by']).toBeUndefined();
      |                                           ^ Error: expect(received).toBeUndefined()
  40  |   });
  41  | 
  42  | });
  43  | 
  44  | test.describe('Rate Limiting', () => {
  45  | 
  46  |   test('login rate limit aktif setelah banyak percobaan gagal', async ({ request }) => {
  47  |     const attempts = [];
  48  |     for (let i = 0; i < 12; i++) {
  49  |       const res = await request.post('/login', {
  50  |         form: {
  51  |           username: 'testuser_ratelimit',
  52  |           password: 'wrongpassword' + i,
  53  |           _token: 'dummy', // akan gagal CSRF tapi kita cek rate limit
  54  |         },
  55  |       });
  56  |       attempts.push(res.status());
  57  |     }
  58  |     // Setelah 10+ percobaan, harus ada 429 Too Many Requests
  59  |     expect(attempts).toContain(429);
  60  |   });
  61  | 
  62  | });
  63  | 
  64  | test.describe('Access Control', () => {
  65  | 
  66  |   test('endpoint admin tidak bisa diakses tanpa auth', async ({ request }) => {
  67  |     const endpoints = [
  68  |       '/admin/santri',
  69  |       '/admin/wali',
  70  |       '/admin/ustadz',
  71  |       '/admin/tagihan',
  72  |       '/admin/laporan/keuangan',
  73  |     ];
  74  |     for (const ep of endpoints) {
  75  |       const res = await request.get(ep, { maxRedirects: 0 });
  76  |       // Harus redirect (302) ke login, bukan 200
  77  |       expect([302, 301, 303]).toContain(res.status());
  78  |     }
  79  |   });
  80  | 
  81  |   test('endpoint wali tidak bisa diakses tanpa auth', async ({ request }) => {
  82  |     const endpoints = [
  83  |       '/wali/dashboard',
  84  |       '/wali/tagihan',
  85  |       '/wali/laporan/1',
  86  |     ];
  87  |     for (const ep of endpoints) {
  88  |       const res = await request.get(ep, { maxRedirects: 0 });
  89  |       expect([302, 301, 303]).toContain(res.status());
  90  |     }
  91  |   });
  92  | 
  93  |   test('endpoint superadmin tidak bisa diakses tanpa auth', async ({ request }) => {
  94  |     const res = await request.get('/superadmin/dashboard', { maxRedirects: 0 });
  95  |     expect([302, 301, 303]).toContain(res.status());
  96  |   });
  97  | 
  98  |   test('path traversal tidak bisa akses file sensitif', async ({ request }) => {
  99  |     const payloads = [
  100 |       '/../../../etc/passwd',
  101 |       '/..%2F..%2F..%2Fetc%2Fpasswd',
  102 |       '/.env',
  103 |       '/storage/app/private/credentials-wali.txt',
  104 |     ];
  105 |     for (const path of payloads) {
  106 |       const res = await request.get(path);
  107 |       // Harus 404, bukan 200 dengan isi file
  108 |       expect(res.status()).not.toBe(200);
  109 |       const body = await res.text();
  110 |       expect(body).not.toMatch(/root:x:|APP_KEY=|password/i);
  111 |     }
  112 |   });
  113 | 
  114 |   test('SQL injection di login tidak berhasil', async ({ page }) => {
  115 |     await page.goto('/login');
  116 |     await page.fill('input[name="username"]', "admin' OR '1'='1");
  117 |     await page.fill('input[name="password"]', "' OR '1'='1");
  118 |     await page.click('button[type="submit"]');
  119 |     // Harus tetap di login atau error, tidak masuk dashboard
  120 |     await expect(page).not.toHaveURL(/dashboard/);
  121 |   });
  122 | 
  123 |   test('XSS di form login tidak dieksekusi', async ({ page }) => {
  124 |     await page.goto('/login');
  125 |     const xssPayload = '<script>window.__xss=1</script>';
  126 |     await page.fill('input[name="username"]', xssPayload);
  127 |     await page.fill('input[name="password"]', 'test');
  128 |     await page.click('button[type="submit"]');
  129 |     // Script tidak boleh dieksekusi
  130 |     const xssExecuted = await page.evaluate(() => (window as any).__xss);
  131 |     expect(xssExecuted).toBeUndefined();
  132 |   });
  133 | 
  134 | });
  135 | 
  136 | test.describe('Webhook Security', () => {
  137 | 
  138 |   test('tripay callback tanpa signature ditolak atau diproses aman', async ({ request }) => {
  139 |     const res = await request.post('/api/tripay-callback', {
```