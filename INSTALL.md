# INSTALL.md — Panduan Instalasi Master Template

Panduan setup awal aplikasi Master Pesantren App di lingkungan **development** (laptop / mesin lokal).

## Prasyarat

| Komponen | Versi minimum | Catatan |
|---|---|---|
| PHP | 8.4+ | dengan ekstensi: `pdo_mysql`, `mbstring`, `xml`, `gd`, `zip`, `bcmath`, `fileinfo` |
| Composer | 2.x | manajer dependency PHP |
| Node.js | 20+ | untuk Vite asset build |
| MySQL / MariaDB | 8.0+ / 10.5+ | database |
| Git | terbaru | version control |

Cek versi yang terinstall:

```bash
php -v
composer -V
node -v
mysql --version
```

## Langkah Instalasi

### 1. Clone repository

```bash
git clone https://github.com/dwyn-ux/master-pesantren-app.git
cd master-pesantren-app
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`, sesuaikan:

```env
APP_NAME="Master Pesantren App"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=master_pesantren_dev
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat database

Login ke MySQL lalu:

```sql
CREATE DATABASE master_pesantren_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Migrate & seed

```bash
php artisan migrate --seed
```

Output yang diharapkan:
- Semua migration sukses
- 16 santri, 15 wali, 8+ ustadz, paket fitur, akademik di-seed

Kredensial seeder otomatis tersimpan di:
- `storage/app/private/credentials-superadmin.txt` — kredensial superadmin
- `storage/app/private/credentials-staff.txt` — admin, bendahara, kepala pondok
- `storage/app/private/credentials-outlet.txt` — kantin, laundry
- `storage/app/private/credentials-akademik.txt` — ustadz dummy

### 6. Storage symlink

```bash
php artisan storage:link
```

### 7. Build assets

```bash
npm run build
```

Atau untuk development dengan hot-reload:

```bash
npm run dev
```

### 8. Jalankan server

**Opsi A — PHP built-in server (paling reliable di Windows):**

```bash
php -S 127.0.0.1:8000 server.php
```

**Opsi B — Laravel artisan serve:**

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Default Akun (Development)

| Role | Username | Password | Wajib Ganti? |
|---|---|---|---|
| Superadmin | `superadmin` | `Super@1234` | Ya |
| Admin | `admin` | `Admin@1234` | Ya |
| Bendahara | `bendahara` | (random, lihat credentials-staff.txt) | Ya |
| Kepala Pondok | `kepalapondok` | (random) | Ya |
| Outlet Kantin | `kantin` | (random, lihat credentials-outlet.txt) | Ya |
| Outlet Laundry | `laundry` | (random) | Ya |
| Ustadz dummy | `ustadz.ahmad` | `ustadz123` | Tidak (dev only) |
| Wali dummy | `wali.bapak.hasan` | `wali123` | Tidak (dev only) |

## Verifikasi Instalasi

```bash
# Cek route ter-load
php artisan route:list | head -20

# Cek koneksi DB & jumlah data
php artisan tinker --execute="echo App\Models\Santri::count() . ' santri';"

# Test akses
curl http://127.0.0.1:8000/login
```

Bila login page muncul (status 200), instalasi sukses.

## Troubleshooting

### Error: Class not found
```bash
composer dump-autoload
```

### Error: APP_KEY missing
```bash
php artisan key:generate
```

### Error: Vite asset 404
```bash
npm run build
php artisan view:clear
```

### Error: Database connection refused
- Pastikan MySQL service jalan
- Cek `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD` di `.env`
- Test manual: `mysql -u root -p`

### Error: artisan serve - Failed to listen
Pakai built-in PHP server saja (`php -S 127.0.0.1:8000 server.php`). Issue ini common di Windows.

### Lupa password superadmin
```bash
php artisan tinker --execute="App\Models\User::where('username','superadmin')->update(['password' => bcrypt('Super@1234'), 'must_change_pw' => true]);"
```

## Reset Database (kalau perlu fresh start)

⚠️ **Akan menghapus semua data**

```bash
php artisan migrate:fresh --seed
```

## Update dari upstream

```bash
git pull
composer install
npm install
php artisan migrate
npm run build
php artisan view:clear
php artisan route:clear
php artisan config:clear
```
