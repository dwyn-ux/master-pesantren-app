# Quick Start - Setup Aplikasi Pesantren

## Prerequisites

- PHP 8.3+
- Composer
- MySQL / MariaDB
- Node.js (untuk asset building)

---

## Step 1: Clone & Install

```bash
# Clone repository
git clone https://github.com/yourusername/pesantren-app.git
cd pesantren-app

# Install dependencies
composer install
npm install

# Build assets
npm run build
```

---

## Step 2: Setup Environment

```bash
# Copy .env
cp .env.example .env

# Generate app key
php artisan key:generate
```

---

## Step 3: Configure Database

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pesantren_app
DB_USERNAME=root
DB_PASSWORD=
```

Buat database:

```bash
mysql -u root -p
CREATE DATABASE pesantren_app;
EXIT;
```

---

## Step 4: Setup Tripay (Optional untuk testing)

Buka [Tripay.co.id](https://tripay.co.id):

1. Daftar & login
2. Buka menu **API Keys** → **Developer**
3. Copy: API Key, Private Key, Merchant Code
4. Tambahkan ke `.env`:

```env
TRIPAY_API_KEY=your_api_key
TRIPAY_PRIVATE_KEY=your_private_key
TRIPAY_MERCHANT_CODE=your_merchant_code
TRIPAY_MODE=sandbox
```

---

## Step 5: Migrasi & Seed Database

```bash
# Run migrations (create tables)
php artisan migrate

# Seed initial data (role, admin, surah, jenis tagihan, outlet, tarif laundry)
php artisan db:seed
```

Kredensial default akan disimpan di `storage/app/private/credentials-staff.txt`

---

## Step 6: Jalankan Development Server

```bash
# Method 1: PHP built-in server (recommended untuk Windows)
php -S 127.0.0.1:8080 -t public

# Method 2: Artisan serve (jika tidak ada port conflict)
php artisan serve

# Method 3: Dari HP/PC lain di jaringan lokal
php -S 0.0.0.0:8080 -t public
# Akses dari device lain: http://<IP_komputer>:8080
```

---

## Step 7: Akses Aplikasi

Buka browser:

```
http://127.0.0.1:8080
```

### Default Credentials

Login dengan salah satu:

```
Admin:
- Username: admin
- Password: Admin@1234

Bendahara:
- Username: bendahara
- Password: (cek di credentials-staff.txt)

Kepala Pondok:
- Username: kepalapondok
- Password: (cek di credentials-staff.txt)
```

> ⚠️ Setelah login pertama, **WAJIB ganti password**

---

## Step 8: Setup First Data

### 1. Tambah Santri

**Admin > Santri > Tambah Santri**

Isi:
- NIS
- Nama
- Tanggal Masuk
- Kamar (optional)

### 2. Tambah Wali (Bisa bulk import)

**Option A: Manual**
- Admin > Wali > Tambah Wali

**Option B: Import Excel**
- Admin > Import Excel
- Download template untuk Wali
- Isi data
- Upload

### 3. Buat Tagihan

**Admin > Tagihan > Tambah Tagihan**

Isi:
- Santri (pilih)
- Jenis Tagihan (SPP, Infak, dll)
- Nominal (jika jenis nominal bebas isi)
- Periode (2026-04)
- Jatuh Tempo

### 4. Proses Pembayaran

**Admin > Pembayaran > Tambah Pembayaran**

- Pilih tagihan
- Pilih metode (VA BCA, QRIS, Manual, dll)
- Klik Buat Pembayaran
- Jika Tripay, akan redirect ke payment page

---

## Fitur yang Sudah Ada

- ✅ Master data: Santri, Wali, Ustadz, Halaqah
- ✅ Keuangan: Jenis Tagihan, Tagihan, Pembayaran (Tripay)
- ✅ Fingerprint management
- ✅ Import Excel XLSX template
- ✅ Laporan keuangan
- ✅ Dashboard per role (Admin, Bendahara, Ustadz, Wali, Outlet)

---

## Fitur yang Belum Ada (Sprint Berikutnya)

- 🔄 Marketplace
- 🔄 Voice Note
- 🔄 Al-Quran reader (equran.id API)
- 🔄 Jadwal sholat & arah kiblat (aladhan.com API)
- 🔄 Mobile app (PWA)
- 🔄 Kasir kantin & laundry (POS)

---

## Helpful Commands

```bash
# Check routes
php artisan route:list

# Check database
php artisan tinker
>>> \App\Models\Santri::count()
>>> exit

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Reset database
php artisan migrate:fresh --seed

# Generate sample data
php artisan tinker
>>> \App\Models\Santri::factory(10)->create()
>>> exit
```

---

## Troubleshooting

| Error | Solusi |
|-------|--------|
| "No application key has been generated" | Jalankan: `php artisan key:generate` |
| "SQLSTATE[HY000]: General error" | DB belum connect, cek `.env` DB config |
| "Class not found" | Jalankan: `composer dump-autoload` |
| "Port 8000 already in use" | Gunakan port lain: `php -S 127.0.0.1:8080` |
| "Cannot login" | Cek credentials di `credentials-staff.txt` |
| "Assets not loaded" | Jalankan: `npm run build` |

---

## Performance Tips

- Cache config: `php artisan config:cache`
- Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- Preload routes: `php artisan route:cache`

---

## Support

Untuk detail lebih lanjut:
- Documentation: lihat folder docs/
- Setup Tripay: `SETUP_TRIPAY.md`
- Setup Fingerprint: `SETUP_FINGERPRINT.md`
- Template Import: `TEMPLATE_IMPORT.md`
