# Security Checklist DomaiNesia

Checklist ini wajib dijalankan setelah deploy atau update production di shared hosting.

## 1. Environment Production

Pastikan `.env` production berisi:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://apps.ponpesashiddiq.or.id
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
VERIFY_SSL=true
QUEUE_CONNECTION=sync
```

Jalankan ulang cache setelah mengubah `.env`:

```bash
cd /home/ponpesa7/master-pesantren-app
/opt/alt/php84/usr/bin/php artisan config:clear
/opt/alt/php84/usr/bin/php artisan cache:clear
/opt/alt/php84/usr/bin/php artisan config:cache
/opt/alt/php84/usr/bin/php artisan route:cache
/opt/alt/php84/usr/bin/php artisan view:cache
```

## 2. Password dan Akun

- Ganti password `superadmin`, `admin`, `bendahara`, `kepalapondok`, outlet, ustadz, dan wali setelah install.
- Jangan pakai akun demo untuk production.
- Nonaktifkan user yang tidak dipakai.
- Gunakan tombol reset password hanya saat diperlukan; password reset sekarang dibuat acak.

## 3. Document Root

Document root domain harus berisi file public saja:

```text
/home/ponpesa7/apps.ponpesashiddiq.or.id
```

Core Laravel harus tetap di luar document root:

```text
/home/ponpesa7/master-pesantren-app
```

Tes URL ini harus tidak bisa diakses:

```text
https://apps.ponpesashiddiq.or.id/.env
https://apps.ponpesashiddiq.or.id/composer.json
https://apps.ponpesashiddiq.or.id/package.json
https://apps.ponpesashiddiq.or.id/storage/test.php
```

Jika salah satu terbuka, jangan lanjut production.

## 4. Permission

Rekomendasi permission:

```bash
find /home/ponpesa7/master-pesantren-app -type d -exec chmod 755 {} \;
find /home/ponpesa7/master-pesantren-app -type f -exec chmod 644 {} \;
chmod -R 775 /home/ponpesa7/master-pesantren-app/storage
chmod -R 775 /home/ponpesa7/master-pesantren-app/bootstrap/cache
chmod 600 /home/ponpesa7/master-pesantren-app/.env
```

Jangan pakai `777` kecuali sementara untuk debugging, lalu kembalikan.

## 5. Upload dan Storage

- Upload SVG publik dimatikan.
- File upload aktif dibatasi image/audio/pdf/excel sesuai fitur.
- `.htaccess` memblokir file executable di `/storage`.
- Jika symlink storage ditolak hosting dan memakai copy manual, pastikan tidak pernah meng-copy file `.php`, `.phtml`, `.phar`, `.sh`, `.cgi`, `.exe` ke folder public storage.

## 6. Payment Callback

Payment callback sekarang wajib signature.

Untuk Tripay, `.env` harus punya:

```env
TRIPAY_API_KEY=
TRIPAY_PRIVATE_KEY=
TRIPAY_MERCHANT_CODE=
TRIPAY_MODE=production
```

Untuk Midtrans, server key harus tersimpan di pengaturan payment app.

Callback tanpa signature akan ditolak.

## 7. Hardware API

Endpoint ini sekarang wajib token:

```text
/api/fingerprint/sync
/api/rfid/sync
```

Jika belum pakai hardware, biarkan:

```env
HARDWARE_API_TOKEN=
```

Dengan token kosong, endpoint hardware otomatis mati.

Jika hardware dipakai, buat token kuat:

```bash
openssl rand -hex 32
```

Lalu isi:

```env
HARDWARE_API_TOKEN=hasil_token
```

Request hardware wajib mengirim salah satu header:

```text
Authorization: Bearer hasil_token
X-Hardware-Token: hasil_token
```

Body juga wajib punya `timestamp` Unix time dan hanya diterima dalam rentang 5 menit.

## 8. Cron

Cron pakai PHP 8.4:

```bash
/opt/alt/php84/usr/bin/php /home/ponpesa7/master-pesantren-app/artisan schedule:run >> /home/ponpesa7/master-pesantren-app/storage/logs/cron.log 2>&1
```

Pastikan `cron.log` tidak berisi error.

## 9. Update dari GitHub

Setiap update production:

```bash
cd /home/ponpesa7/master-pesantren-app
/opt/alt/php84/usr/bin/php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-sodium
/opt/alt/php84/usr/bin/php artisan migrate --force
cp -a public/. /home/ponpesa7/apps.ponpesashiddiq.or.id/
cp deploy/domainesia/public_html_index.php /home/ponpesa7/apps.ponpesashiddiq.or.id/index.php
cp deploy/domainesia/public_html.htaccess /home/ponpesa7/apps.ponpesashiddiq.or.id/.htaccess
/opt/alt/php84/usr/bin/php artisan config:cache
/opt/alt/php84/usr/bin/php artisan route:cache
/opt/alt/php84/usr/bin/php artisan view:cache
/opt/alt/php84/usr/bin/php artisan up
```

Catatan: `--ignore-platform-req=ext-sodium` hanya sementara. Minta DomaiNesia mengaktifkan `sodium` untuk PHP CLI agar Composer bisa berjalan normal.

## 10. Indikasi Kemasukan Script

Cek file mencurigakan:

```bash
find /home/ponpesa7/apps.ponpesashiddiq.or.id -type f \( -name "*.php" -o -name "*.phtml" -o -name "*.phar" -o -name "*.cgi" -o -name "*.sh" \) -print
find /home/ponpesa7/master-pesantren-app/storage/app/public -type f \( -name "*.php" -o -name "*.phtml" -o -name "*.phar" -o -name "*.cgi" -o -name "*.sh" \) -print
```

Di document root normalnya hanya `index.php` yang boleh ada sebagai PHP file.

Jika ada file aneh seperti `wp-*.php`, `autoload_classmap.php` di public, `shell.php`, `mailer.php`, atau file PHP di `storage`, hapus setelah backup bukti dan segera rotasi semua password.
