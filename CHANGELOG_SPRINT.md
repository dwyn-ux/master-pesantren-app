# Fitur Baru - Sprint Admin & Pembayaran

## Overview

Sprint ini menambahkan module pembayaran Tripay, dashboard bendahara, fingerprint management, dan template Excel import dengan format XLSX yang proper.

---

## 1. Pembayaran & Tripay Integration

### Files
- `app/Http/Controllers/Admin/PembayaranController.php` - Payment management
- `resources/views/admin/pembayaran/index.blade.php` - List & filter pembayaran

### Features
- ✅ Daftar pembayaran dengan filter status & metode
- ✅ Buat pembayaran baru (trigger invoice Tripay)
- ✅ Verifikasi pembayaran otomatis dari Tripay
- ✅ Metode pembayaran: VA BCA, VA Mandiri, QRIS, GoPay, OVO, Manual
- ✅ Check status pembayaran manual

### Routes
```
GET  /admin/pembayaran              → List pembayaran
GET  /admin/pembayaran/create       → Form buat pembayaran
POST /admin/pembayaran              → Store pembayaran (trigger Tripay)
POST /admin/pembayaran/{id}/check-status → Cek status ke Tripay
```

### Tripay Config
- `.env` variables: `TRIPAY_API_KEY`, `TRIPAY_PRIVATE_KEY`, `TRIPAY_MERCHANT_CODE`, `TRIPAY_MODE`
- Endpoint API callback: `/api/tripay-callback` (untuk notifikasi dari Tripay)
- Docs: `SETUP_TRIPAY.md`

---

## 2. Dashboard Bendahara

### Files
- `app/Http/Controllers/Bendahara/DashboardController.php` - Dashboard logic
- `resources/views/bendahara/dashboard.blade.php` - Dashboard view

### Features
- ✅ Summary: Total tagihan, belum bayar, total pembayaran, pending
- ✅ List pembayaran terbaru (real-time)
- ✅ Status tagihan (belum bayar vs lunas)
- ✅ Quick links ke daftar tagihan & pembayaran

### Routes
```
GET /bendahara/dashboard   → Dashboard bendahara
GET /bendahara/pembayaran  → Redirect ke admin pembayaran
GET /bendahara/tagihan     → Redirect ke admin tagihan
```

---

## 3. Fingerprint Management

### Files
- `app/Http/Controllers/Admin/FingerprintController.php` - Fingerprint CRUD & API
- `resources/views/admin/fingerprint/index.blade.php` - List santri & status
- `resources/views/admin/fingerprint/form.blade.php` - Edit fingerprint

### Features
- ✅ Daftar santri dengan status fingerprint
- ✅ Assign/edit fingerprint ID per santri
- ✅ Filter: belum ada fingerprint, sudah ada fingerprint
- ✅ API endpoint untuk hardware reader:
  - `POST /api/fingerprint/sync` - Scan & register fingerprint
  - Support: `action: register` (tambah FP baru)
  - Support: `action: scan` (identifikasi santri)

### Routes
```
GET    /admin/fingerprint              → List santri & status
GET    /admin/fingerprint/{id}/edit    → Form edit FP
PUT    /admin/fingerprint/{id}         → Update FP ID
DELETE /admin/fingerprint/{id}         → Delete FP

POST   /api/fingerprint/sync           → API untuk hardware
```

### API Response
**Scan/Register berhasil:**
```json
{
  "success": true,
  "message": "Santri teridentifikasi",
  "data": {
    "santri_id": 1,
    "nama": "Ahmad Wulansari",
    "nis": "PST.2026.001",
    "saldo": 150000
  }
}
```

### Docs
- `SETUP_FINGERPRINT.md` - Setup & integration guide

---

## 4. Template Excel Import (XLSX)

### Files
- `app/Exports/ImportTemplates.php` - Template generator (XLSX)
- Updated: `app/Http/Controllers/Admin/ImportController.php`
- Updated: `resources/views/admin/import/index.blade.php`

### Features
- ✅ Download template Excel format XLSX (bukan CSV)
- ✅ Template untuk import Wali:
  - Kolom: Nama Orang Tua, No HP, Hubungan, Nama Santri, NIS
  - Pre-filled dengan contoh data
- ✅ Template untuk import Santri:
  - Kolom: NIS, Nama, Tanggal Masuk, Kamar, Fingerprint ID
  - Pre-filled dengan contoh data
- ✅ Header dengan styling (bold, warna)
- ✅ Auto-fit column width
- ✅ Validasi: hanya accept `.xlsx`

### Routes
```
GET  /admin/import                  → Page import
POST /admin/import                  → Upload & process
POST /admin/import/download-template → Download template XLSX
```

### Docs
- `TEMPLATE_IMPORT.md` - Detail kolom & cara penggunaan

---

## 5. Sidebar & Navigation Updates

### Changes
- **Admin sidebar**: tambah menu Pembayaran, Fingerprint
- **Bendahara**: punya dashboard sendiri (sebelumnya generic)
- Semua route sudah registered & tested

---

## Database Config (Sudah Ada)

Migrations sudah jalan:
- ✅ `pembayaran` table
- ✅ `tagihan` table
- ✅ `santri` table (ada kolom `fingerprint_id`)
- ✅ Semua model relationships

---

## Testing Checklist

- [x] PHP syntax: semua controller valid
- [x] Routes: semua route registered
- [x] Views: templates bisa di-render
- [x] Excel templates: XLSX format valid
- [x] API endpoint: ready untuk integration hardware

---

## Next Steps

1. **Tripay Integration**: Setup API keys di `.env`
2. **Bendahara Test**: Login bendahara → akses dashboard
3. **Import Test**: Download template → isi data → upload
4. **Fingerprint Hardware**: Setup USB reader → test API endpoint
5. **Payment Flow**: Buat tagihan → trigger pembayaran Tripay → monitor

---

## Environment Variables yang Diperlukan

Tambahkan ke `.env`:
```env
# Tripay
TRIPAY_API_KEY=xxx
TRIPAY_PRIVATE_KEY=xxx
TRIPAY_MERCHANT_CODE=xxx
TRIPAY_MODE=sandbox  # atau production
```

---

## File Documentation

- `SETUP_TRIPAY.md` - Setup Tripay payment gateway
- `SETUP_FINGERPRINT.md` - Setup fingerprint hardware & API
- `TEMPLATE_IMPORT.md` - Template Excel & import guide
