# Deploy Laravel ke Shared Hosting DomaiNesia

Panduan ini untuk memindahkan **Master Pesantren App** dari VPS ke shared hosting DomaiNesia. Targetnya production aman tanpa root access, Supervisor, atau service worker yang harus hidup 24 jam.

## 1. Syarat Hosting

Pastikan di cPanel/DomaiNesia:

- PHP 8.4 aktif untuk domain.
- Extension PHP aktif: `pdo_mysql`, `curl`, `gd`, `zip`, `sodium`, `fileinfo`, `openssl`, `mbstring`, `dom`, `xml`, `tokenizer`.
- MySQL/MariaDB tersedia.
- Cron Job tersedia.
- SSL aktif.
- Outbound HTTPS tidak diblokir, karena app memanggil Tripay/Midtrans, API jadwal sholat/Quran, Firebase, dan SMTP.

Kalau SSH/Terminal tersedia, deploy bisa lebih enak. Kalau tidak ada SSH, build dependency di lokal/VPS lalu upload hasilnya.

## 2. Build Paket Deploy

Ada dua cara deploy ke DomaiNesia: pull langsung dari GitHub, atau upload ZIP. Jika hosting punya SSH/Terminal, Git, dan Composer, pakai cara Git pull karena update berikutnya lebih rapi.

### Opsi A: Pull dari GitHub

Di Terminal DomaiNesia:

```bash
cd /home/cpaneluser
git clone https://github.com/dwyn-ux/master-pesantren-app.git master-pesantren-app
cd master-pesantren-app
composer install --no-dev --optimize-autoloader
```

Untuk update berikutnya:

```bash
cd /home/cpaneluser/master-pesantren-app
php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan up
```

Jika hosting tidak punya Node/npm, pastikan asset `public/build` sudah tersedia dari proses release/upload terpisah. Jika Node/npm tersedia, jalankan `npm ci && npm run build` setelah `git pull`.

### Opsi B: Upload ZIP

Jalankan di lokal atau VPS lama dari root project:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
```

Upload project lengkap ke hosting, termasuk folder `vendor` dan `public/build`. Jangan upload `.env` development.

Di Windows, paket ZIP bisa dibuat otomatis dengan script:

```powershell
powershell -ExecutionPolicy Bypass -File deploy/domainesia/build-package.ps1
```

Hasil default:

```text
deploy/domainesia/master-pesantren-app-domainesia.zip
```

ZIP ini mengecualikan `.env`, `node_modules`, `.git`, log/cache runtime, dan file kredensial lokal yang tidak boleh ikut terupload.

Secara default ZIP juga mengecualikan `vendor`, jadi setelah upload jalankan `composer install` di hosting. Kalau hosting tidak punya Terminal/Composer dan perlu paket yang menyertakan `vendor`, jalankan:

```powershell
powershell -ExecutionPolicy Bypass -File deploy/domainesia/build-package.ps1 -IncludeVendor
```

## 3. Struktur Folder Aman

Rekomendasi:

```text
/home/cpaneluser/master-pesantren-app
/home/cpaneluser/public_html
```

Isi aplikasi Laravel taruh di `master-pesantren-app`. Yang boleh terekspos ke web hanya isi folder `public`.

Kalau panel bisa mengubah document root, arahkan domain langsung ke:

```text
/home/cpaneluser/master-pesantren-app/public
```

Kalau document root tidak bisa diubah dan harus memakai `public_html`, salin isi folder `public` ke `public_html`, lalu sesuaikan `public_html/index.php` menjadi:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$appPath = __DIR__.'/../master-pesantren-app';

if (file_exists($maintenance = $appPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appPath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $appPath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

Copy juga `.htaccess` dari folder `public` ke `public_html`.

Template siap pakai juga sudah disediakan di:

```text
deploy/domainesia/public_html_index.php
deploy/domainesia/public_html.htaccess
```

## 4. File `.env` Production

Di hosting, buat `.env` dari template:

```bash
cp .env.domainesia.example .env
```

Isi nilai asli dari hosting dan VPS lama. Yang wajib diperhatikan:

- `APP_KEY` harus sama dengan VPS lama. Jangan jalankan `php artisan key:generate` saat migrasi data lama.
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://domain-anda.com`
- `DB_*` pakai database dari cPanel.
- `QUEUE_CONNECTION=sync`, karena shared hosting tidak punya Supervisor.
- `VERIFY_SSL=true` untuk production.
- `TRIPAY_MODE=production` jika payment gateway sudah live.
- `FIREBASE_CREDENTIALS=storage/app/firebase_credentials.json` jika Firebase dipakai.

## 5. Migrasi Database dari VPS

Di VPS lama:

```bash
php artisan down
mysqldump -u USER -p NAMA_DB > backup-production.sql
```

Import `backup-production.sql` ke database DomaiNesia via phpMyAdmin atau command line hosting.

Setelah import, jangan menjalankan seeder production kecuali memang membuat instalasi pesantren baru dari nol.

## 5A. Fresh Install Jika VPS Tidak Bisa Diakses

Kalau database production dari VPS tidak bisa diambil, app tetap bisa dibuat jalan sebagai instalasi baru. Data lama tidak ikut pindah.

Setelah `.env` production benar, jalankan:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Seeder akan membuat role, akun awal, master surah, jenis tagihan, outlet/tarif awal, master finance, dan fitur default.

Kredensial awal akan tertulis di:

```text
storage/app/private/credentials-superadmin.txt
storage/app/private/credentials-staff.txt
storage/app/private/credentials-outlet.txt
```

Setelah login pertama, langsung ganti semua password default.

Sebelum dibuka ke user, jalankan checklist keamanan di `SECURITY-DOMAINESIA.md`.

## 6. Copy Storage Upload

Wajib copy folder ini dari VPS lama:

```text
storage/app/public
```

Folder itu berisi file upload seperti logo, gambar produk, bukti transaksi, voice note, dan lampiran lain.

Kalau Firebase dipakai, simpan credential di:

```text
storage/app/firebase_credentials.json
```

Jangan taruh credential Firebase di `public_html`.

## 7. Storage Link

Kalau SSH tersedia:

```bash
php artisan storage:link
```

Kalau symlink tidak didukung shared hosting, buat folder `public_html/storage` lalu isi/copy dari:

```text
master-pesantren-app/storage/app/public
```

Untuk mode tanpa symlink, setiap ada upload lama yang dipindah atau backup restore, pastikan isi `public_html/storage` tetap sinkron.

## 8. Permission

Pastikan folder ini writable oleh user hosting:

```text
storage
bootstrap/cache
```

Di cPanel File Manager biasanya permission aman:

- Folder: `755`
- File: `644`

Kalau masih error cache/log, ubah folder `storage` dan `bootstrap/cache` menjadi `775`.

## 9. Cache Production

Setelah `.env` benar:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Untuk migrasi dari VPS yang database-nya sudah lengkap, `migrate --force` hanya untuk memastikan tabel baru ikut naik. Jangan jalankan `db:seed`.

## 10. Cron Job

Tambahkan Cron Job di cPanel DomaiNesia. Jika tersedia tiap menit:

```bash
* * * * * /usr/local/bin/php /home/cpaneluser/master-pesantren-app/artisan schedule:run >> /home/cpaneluser/master-pesantren-app/storage/logs/cron.log 2>&1
```

Jika DomaiNesia membatasi minimal 5-6 menit, pakai interval paling kecil yang tersedia:

```bash
*/6 * * * * /usr/local/bin/php /home/cpaneluser/master-pesantren-app/artisan schedule:run >> /home/cpaneluser/master-pesantren-app/storage/logs/cron.log 2>&1
```

Jika path PHP berbeda, cek dari Terminal:

```bash
which php
php -v
```

Scheduler app ini dipakai untuk tagihan bulanan, laporan otomatis, reminder finance, dan reminder Quran. Kalau cron hanya tiap 6 menit, tugas harian tetap jalan, tetapi bisa mundur beberapa menit.

## 11. Testing Setelah Go Live

Cek minimal:

- Login admin.
- Dashboard dan data santri terbuka.
- Upload file/logo/bukti transaksi.
- File upload bisa diakses dari URL `/storage/...`.
- Export PDF/Excel.
- Email SMTP terkirim.
- Firebase notification, kalau dipakai.
- Tripay/Midtrans callback memakai URL domain baru.
- Cron menulis log dan tidak error.

## 12. Rollback

Jangan matikan VPS lama langsung. Simpan minimal 48-72 jam untuk rollback.

Saat cutover:

1. Aktifkan maintenance di VPS lama.
2. Dump database final.
3. Import ke DomaiNesia.
4. Copy storage final.
5. Arahkan DNS/domain.
6. Test production.

Kalau ada masalah besar, arahkan DNS balik ke VPS lama dan matikan maintenance.
