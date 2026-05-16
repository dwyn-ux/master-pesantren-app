# DEPLOY.md — Panduan Deploy ke VPS untuk Pesantren Baru

Panduan deploy aplikasi **Master Pesantren App** ke VPS production untuk client pesantren. Setiap pesantren = 1 instalasi terpisah (single-tenant).

---

## Workflow Onboarding Pesantren Baru

```
1. Pesantren beli paket (Starter / Growth / Pro / Pro+ / Enterprise)
2. Provisioning VPS sesuai tier
3. Deploy aplikasi
4. Konfigurasi (.env, payment gateway, WA, dll)
5. Aktifkan paket fitur via Superadmin
6. Migrasi data awal (santri, wali, ustadz)
7. Training admin & bendahara
8. Go-live
```

Estimasi total waktu: **1-2 hari kerja** per pesantren.

---

## 1. Provisioning VPS

### Spec rekomendasi per tier

| Tier | RAM | CPU | Storage | Provider rekomendasi |
|---|---|---|---|---|
| Starter (<150 santri) | 2 GB | 2 vCPU | 40 GB | Niagahoster KVM 2, IDCloudHost VPS-2 |
| Growth (150-400) | 4 GB | 2 vCPU | 60 GB | Niagahoster KVM 3, Biznet Gio Linux Cloud |
| Pro (400+) | 8 GB | 4 vCPU | 100 GB | Biznet Gio NEO Lite |
| Enterprise | 16 GB | 4-8 vCPU | 200 GB | Custom + dedicated IP |

### OS rekomendasi
**Ubuntu 22.04 LTS** atau **Ubuntu 24.04 LTS** (long-term support).

### Akses awal
SSH ke VPS dengan user root atau sudo:

```bash
ssh root@<IP_VPS>
```

---

## 2. Setup Server

Setelah login pertama, jalankan setup awal:

### 2.1 Update system & install dependency

```bash
apt update && apt upgrade -y

# PHP 8.4 + ekstensi yang diperlukan
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.4 php8.4-cli php8.4-fpm php8.4-mysql php8.4-xml \
  php8.4-mbstring php8.4-gd php8.4-curl php8.4-zip php8.4-bcmath \
  php8.4-intl php8.4-fileinfo php8.4-tokenizer

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Nginx
apt install -y nginx

# MariaDB
apt install -y mariadb-server
mysql_secure_installation

# Git, unzip, curl
apt install -y git unzip curl supervisor
```

### 2.2 Buat user non-root

```bash
adduser deploy
usermod -aG sudo,www-data deploy
su - deploy
```

### 2.3 Setup database

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE pesantren_<nama_pesantren> CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pesantren_user'@'localhost' IDENTIFIED BY '<PASSWORD_KUAT>';
GRANT ALL PRIVILEGES ON pesantren_<nama_pesantren>.* TO 'pesantren_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 3. Deploy Aplikasi

### 3.1 Clone repo (sebagai user deploy)

```bash
cd /var/www
sudo mkdir pesantren-<nama>
sudo chown deploy:www-data pesantren-<nama>
cd pesantren-<nama>

git clone https://github.com/dwyn-ux/master-pesantren-app.git .
```

### 3.2 Install dependency

```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

### 3.3 Konfigurasi `.env`

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

Sesuaikan untuk production:

```env
APP_NAME="<Nama Pesantren>"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<domain.pesantren>.id

LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pesantren_<nama>
DB_USERNAME=pesantren_user
DB_PASSWORD=<PASSWORD_KUAT>

SESSION_DRIVER=database
SESSION_LIFETIME=43200
SESSION_SECURE_COOKIE=true

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=<sesuaikan>
MAIL_PASSWORD=<sesuaikan>
MAIL_FROM_ADDRESS=noreply@<domain>
MAIL_FROM_NAME="${APP_NAME}"

# Payment gateway (kalau paket include tagihan)
TRIPAY_API_KEY=
TRIPAY_PRIVATE_KEY=
TRIPAY_MERCHANT_CODE=
TRIPAY_MODE=production

# Optional: WA Gateway
FONNTE_TOKEN=
```

### 3.4 Migrate & seed

```bash
php artisan migrate --force
php artisan db:seed --force
```

⚠️ **Penting:** Untuk pesantren baru, biasanya hanya seed yang diperlukan: `RoleSeeder`, `SuperadminSeeder`, `AdminSeeder`, `SurahSeeder`, `FinanceSettingSeeder`, `FeatureSeeder`. Skip `AkademikSeeder` (data dummy) dan `OutletSeeder` (kecuali pesantren punya outlet).

```bash
# Seed minimal untuk production
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=SuperadminSeeder --force
php artisan db:seed --class=AdminSeeder --force
php artisan db:seed --class=SurahSeeder --force
php artisan db:seed --class=ChartOfAccountSeeder --force
php artisan db:seed --class=KasBankSeeder --force
php artisan db:seed --class=KategoriKeuanganSeeder --force
php artisan db:seed --class=FinanceSettingSeeder --force
php artisan db:seed --class=FeatureSeeder --force
php artisan db:seed --class=JenisTagihanSeeder --force
```

### 3.5 Storage symlink & permissions

```bash
php artisan storage:link

sudo chown -R deploy:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 3.6 Optimize untuk production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 4. Konfigurasi Nginx + SSL

### 4.1 Buat virtual host

```bash
sudo nano /etc/nginx/sites-available/pesantren-<nama>
```

Isi:

```nginx
server {
    listen 80;
    server_name <domain.pesantren>.id;
    root /var/www/pesantren-<nama>/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    client_max_body_size 50M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|gif|png|svg|ico|css|js|woff|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

Aktifkan & restart:

```bash
sudo ln -s /etc/nginx/sites-available/pesantren-<nama> /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 4.2 SSL gratis dengan Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d <domain.pesantren>.id
```

Auto-renewal sudah otomatis via cronjob certbot.

---

## 5. Queue & Scheduler

### 5.1 Cronjob Laravel scheduler

```bash
sudo crontab -e -u deploy
```

Tambah:

```
* * * * * cd /var/www/pesantren-<nama> && php artisan schedule:run >> /dev/null 2>&1
```

### 5.2 Queue worker via Supervisor

```bash
sudo nano /etc/supervisor/conf.d/pesantren-<nama>-worker.conf
```

Isi:

```ini
[program:pesantren-<nama>-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pesantren-<nama>/artisan queue:work --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pesantren-<nama>/storage/logs/worker.log
stopwaitsecs=3600
```

Reload:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pesantren-<nama>-worker:*
```

---

## 6. Aktivasi Paket Fitur

Login ke aplikasi via browser:

```
https://<domain.pesantren>.id/login
```

Pakai akun `superadmin` (kredensial di `storage/app/private/credentials-superadmin.txt`).

1. Wajib ganti password superadmin saat login pertama
2. Buka **Manajemen Fitur**
3. Klik tombol **Terapkan** sesuai paket yang dibeli pesantren:
   - Starter: Quran, Sholat, Halaqah, Tagihan, Voice Note
   - Growth: Starter + Wallet, Kantin, Laundry, Marketplace, Akademik master + absensi
   - Pro: Growth + Penilaian + Raport + Akuntansi
   - Pro+: Pro + Halaqah Diniyah
   - Enterprise: Pro+ + Fingerprint, RFID
4. Logout, login ulang sebagai admin

---

## 7. Migrasi Data Awal

### 7.1 Import dari Excel

Pesantren biasanya punya data lama di Excel. Pakai fitur **Import Excel**:

1. Login admin → menu **Import Excel**
2. Download template Excel
3. Pesantren isi template, kirim balik
4. Upload ke aplikasi

### 7.2 Setup akademik (untuk paket Growth+)

1. Buat **Tahun Ajaran** aktif
2. Setup **Tingkat / Marhalah**
3. Buat **Kelas** + assign wali kelas
4. Tambah **Mata Pelajaran** + Kitab referensi
5. Atur **Komponen Nilai** per mapel (UH, Tugas, UTS, UAS)
6. Set **KKM** per tingkat per mapel
7. Buat **Jadwal Pelajaran**
8. Assign santri ke kelas

---

## 8. Backup Otomatis

### 8.1 Backup database harian

```bash
sudo nano /etc/cron.daily/backup-pesantren-<nama>
```

Isi:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/pesantren-<nama>"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

mysqldump -u pesantren_user -p'<PASSWORD>' pesantren_<nama> | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Hapus backup > 30 hari
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete
```

```bash
sudo chmod +x /etc/cron.daily/backup-pesantren-<nama>
```

### 8.2 Backup storage (foto santri, voice note, dll)

```bash
sudo nano /etc/cron.weekly/backup-storage-pesantren-<nama>
```

Isi:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/pesantren-<nama>"
DATE=$(date +%Y%m%d)
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz -C /var/www/pesantren-<nama>/storage/app public
find $BACKUP_DIR -name "storage_*.tar.gz" -mtime +90 -delete
```

```bash
sudo chmod +x /etc/cron.weekly/backup-storage-pesantren-<nama>
```

### 8.3 Off-site backup (rekomen)

Sync ke S3-compatible (Wasabi, IDCloudHost Object Storage, atau Wasabi):

```bash
# Install rclone
curl https://rclone.org/install.sh | sudo bash
rclone config  # setup remote bernama 'backup'

# Cron harian:
0 3 * * * rclone sync /var/backups/pesantren-<nama> backup:pesantren-backups/<nama>/
```

---

## 9. Monitoring

### 9.1 UptimeRobot
- Tambah monitor HTTPS untuk domain
- Alert via email/WhatsApp kalau down > 5 menit

### 9.2 Log monitoring

```bash
# Real-time tail
tail -f storage/logs/laravel.log

# Filter error
grep "ERROR\|Exception" storage/logs/laravel.log | tail -50
```

### 9.3 Disk & RAM
```bash
df -h
free -h
htop
```

---

## 10. Update / Patch

### Workflow update aplikasi

```bash
cd /var/www/pesantren-<nama>

# Maintenance mode
php artisan down --message="Sedang update sistem, mohon tunggu beberapa menit."

# Pull update
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Migration & cache
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart worker
sudo supervisorctl restart pesantren-<nama>-worker:*

# Bring back online
php artisan up
```

---

## 11. Checklist Go-Live

- [ ] VPS provisioned & accessible
- [ ] Domain pointing ke IP VPS
- [ ] SSL aktif (`https://`)
- [ ] Database & user MySQL sudah dibuat
- [ ] `.env` production sudah dikonfigurasi
- [ ] `APP_DEBUG=false`
- [ ] Migrate sukses
- [ ] Seeder minimal sukses
- [ ] Storage symlink sudah dibuat
- [ ] Permissions storage/cache sudah benar (775, www-data)
- [ ] Nginx vhost aktif & nginx restart sukses
- [ ] Queue worker (supervisor) jalan
- [ ] Cronjob schedule terpasang
- [ ] Backup harian jalan (cek hari berikutnya)
- [ ] Superadmin password sudah diganti
- [ ] Paket fitur sudah diaktifkan via Manajemen Fitur
- [ ] Admin pesantren sudah login & ganti password
- [ ] Data master awal sudah di-import (santri, wali, ustadz)
- [ ] Tahun ajaran aktif sudah dibuat
- [ ] Test login dari device pesantren
- [ ] Training admin selesai
- [ ] Pesantren punya akses ke kontak support

---

## Troubleshooting Production

### 502 Bad Gateway
Cek PHP-FPM jalan: `sudo systemctl status php8.4-fpm`. Restart kalau perlu: `sudo systemctl restart php8.4-fpm`

### 500 Server Error setelah deploy
Cek log: `tail -100 storage/logs/laravel.log`. Biasanya:
- Permission storage/cache → fix dengan `chmod -R 775 storage bootstrap/cache && chown -R deploy:www-data storage`
- Cache lama → `php artisan optimize:clear`

### CSS/JS 404
- Pastikan `npm run build` sudah dijalankan
- Cek `public/build/manifest.json` ada
- Cek permission folder `public/build/`

### Slow performance
- Aktifkan OpCache: edit `/etc/php/8.4/fpm/php.ini` → set `opcache.enable=1`, `opcache.memory_consumption=256`
- Pakai Redis untuk cache & session (bukan database)

### Email tidak terkirim
- Cek `MAIL_*` di .env
- Test: `php artisan tinker --execute="Mail::raw('test', fn(\$m) => \$m->to('email@anda.com')->subject('test'));"`
- Cek log Mailgun/SendGrid

### Log file besar (>100MB)
Atur log rotation:

```bash
sudo nano /etc/logrotate.d/laravel-pesantren-<nama>
```

```
/var/www/pesantren-<nama>/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 deploy www-data
}
```
