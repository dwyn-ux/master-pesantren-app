# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: security.spec.ts >> Webhook Security >> tripay callback tanpa signature ditolak atau diproses aman
- Location: specs\security.spec.ts:138:7

# Error details

```
TimeoutError: apiRequestContext.post: Timeout 15000ms exceeded.
Call log:
  - → POST https://app.ponpesashiddiq.or.id/api/tripay-callback
    - user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.7778.96 Safari/537.36
    - accept: */*
    - accept-encoding: gzip,deflate,br
    - content-type: application/json
    - content-length: 66

```

# Test source

```ts
  39  |     expect(res.headers()['x-powered-by']).toBeUndefined();
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
> 139 |     const res = await request.post('/api/tripay-callback', {
      |                               ^ TimeoutError: apiRequestContext.post: Timeout 15000ms exceeded.
  140 |       data: {
  141 |         reference: 'FAKE-REF-123',
  142 |         status: 'PAID',
  143 |         merchant_ref: 'FAKE',
  144 |       },
  145 |     });
  146 |     // Tanpa signature valid, harus 403 atau 422, bukan 200 sukses
  147 |     expect([403, 422, 401, 500]).toContain(res.status());
  148 |   });
  149 | 
  150 |   test('tripay callback dengan signature palsu ditolak', async ({ request }) => {
  151 |     const res = await request.post('/api/tripay-callback', {
  152 |       headers: {
  153 |         'X-Callback-Signature': 'fakesignature123',
  154 |         'Content-Type': 'application/json',
  155 |       },
  156 |       data: {
  157 |         reference: 'FAKE-REF-456',
  158 |         status: 'PAID',
  159 |       },
  160 |     });
  161 |     expect([403, 422, 401]).toContain(res.status());
  162 |   });
  163 | 
  164 | });
  165 | 
```