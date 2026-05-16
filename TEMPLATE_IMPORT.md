# Template Excel Import

## Tipe Import yang Tersedia

### 1. Import Wali

**File:** `template-import-wali.xlsx`

**Kolom yang diperlukan:**

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| Nama Orang Tua | Text | Nama lengkap wali |
| No HP | Text | Nomor HP aktif (dengan kode area) |
| Hubungan | List | Pilihan: `ayah`, `ibu`, `wali` |
| Nama Santri | Text | Nama santri yang diasuh |
| NIS | Text | Nomor induk santri (harus sesuai di database) |

**Contoh Data:**

| Nama Orang Tua | No HP | Hubungan | Nama Santri | NIS |
|---|---|---|---|---|
| Bapak Ahmad Wijaya | 085712345678 | ayah | Ahmad Wulansari | PST.2026.001 |
| Ibu Siti Nurhaliza | 081987654321 | ibu | Ahmad Wulansari | PST.2026.001 |
| Paman Bambang Sutrisno | 082345678901 | wali | Rina Wulansari | PST.2026.002 |

**Output:**
- Membuat akun `wali` otomatis
- Username dan password random (disimpan di file `credentials-wali.txt`)
- Assign wali ke santri dengan hubungan yang dipilih

### 2. Import Santri

**File:** `template-import-santri.xlsx`

**Kolom yang diperlukan:**

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| NIS | Text | Nomor induk santri (unique) |
| Nama | Text | Nama lengkap santri |
| Tanggal Masuk | Date | Format: YYYY-MM-DD (contoh: 2026-01-15) |
| Kamar | Text | Nomor atau nama kamar (opsional) |
| Fingerprint ID | Text | ID fingerprint (kosongkan untuk sekarang) |

**Contoh Data:**

| NIS | Nama | Tanggal Masuk | Kamar | Fingerprint ID |
|---|---|---|---|---|
| PST.2026.001 | Ahmad Wulansari | 2026-01-15 | Kamar A1 | (kosong) |
| PST.2026.002 | Rina Wulansari | 2026-01-15 | Kamar B2 | (kosong) |
| PST.2026.003 | Muhammad Rizki | 2026-02-01 | Kamar A3 | (kosong) |

**Catatan:**
- Fingerprint ID bisa diisi nanti melalui admin panel
- Saldo awal santri = 0 (bisa di-topup kemudian)

## Cara Download Template

1. Login ke admin
2. Buka **Admin > Import Excel**
3. Pilih tipe import (Wali atau Santri)
4. Klik tombol **Template** untuk download file `.xlsx`

## Cara Import Data

### Step 1: Persiapkan File

1. Download template dari sistem
2. Buka dengan Excel / LibreOffice Calc
3. Isi data sesuai kolom yang ada
4. **Jangan hapus header (baris pertama)**
5. **Jangan ubah nama kolom**
6. Save sebagai `.xlsx` (bukan `.csv` atau `.xls`)

### Step 2: Upload

1. Login sebagai admin
2. Buka **Admin > Import Excel**
3. Pilih tipe import
4. Klik **Pilih File** → pilih file `.xlsx` Anda
5. Klik **Mulai Import**

### Step 3: Cek Hasil

- Hasil import muncul di log (tab **Riwayat Import**)
- Jumlah berhasil vs gagal ditampilkan
- Jika ada error, cek di log file `storage/logs/`

## Validasi Data

### Wali Import
- ✅ Nama wali tidak boleh kosong
- ✅ No HP harus valid
- ✅ Hubungan harus: ayah, ibu, atau wali
- ✅ NIS santri harus ada di database
- ❌ Duplikat username akan skip dan coba dengan suffix angka

### Santri Import
- ✅ NIS harus unique (tidak boleh duplikat)
- ✅ Nama tidak boleh kosong
- ✅ Tanggal masuk format YYYY-MM-DD
- ❌ Fingerprint ID boleh kosong (di-input nanti di admin)

## Troubleshooting

| Error | Solusi |
|-------|--------|
| "File tidak berisi data yang valid" | Format Excel salah atau file kosong |
| "Santri tidak ditemukan" | NIS di kolom Santri tidak sesuai dengan database |
| "Duplikat data" | Data sudah pernah di-import sebelumnya |
| "Format tanggal salah" | Gunakan format YYYY-MM-DD (contoh: 2026-04-29) |
| "Nomor HP invalid" | Pastikan nomor HP format Indonesia (08XX...) |

## Tips

- Buat backup file Excel sebelum import
- Test import dengan data sedikit dulu sebelum import bulk
- Selalu cek log hasil import untuk memastikan semuanya OK
- Jika ada error, perbaiki di file Excel, lalu import ulang
- Username wali otomatis generated (nama_depan_NIS)
- Password random dikirim via SMS atau email (untuk Sprint berikutnya)
