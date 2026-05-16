# Setup Fingerprint Reader & Management

## Gambaran Umum

Sistem fingerprint terintegrasi di tiga lokasi:

| Lokasi | Fungsi | Hardware |
|--------|--------|----------|
| **Kasir Kantin** | Verifikasi transaksi santri | PC + USB Fingerprint Reader |
| **Kasir Laundry** | Verifikasi transaksi santri | PC + USB Fingerprint Reader |
| **Admin Panel** | Manage fingerprint database | Browser Web |

## Hardware Requirements

- Fingerprint Scanner USB (contoh: ZKTeco, Ekey, Morpho)
- PC / Komputer dengan OS Windows/Linux
- Driver installed & tested

## Workflow

### 1. Registrasi Fingerprint (First Time)

Santri datang ke **kios fingerprint** → scan jari → ID tersimpan di hardware → dikirim ke web via API

### 2. Assign ke Database

Admin login ke web → **Admin > Fingerprint** → cari santri → **Edit** → input/paste Fingerprint ID

### 3. Transaksi di Kasir

Santri scan jari di PC kantin → sistem identifikasi → transaksi diproses

## API Endpoint untuk Hardware

**Endpoint:** `POST /api/fingerprint/sync`

**Header:**
```
Content-Type: application/json
Authorization: Bearer {device_token} [opsional untuk security]
```

**Body (Register):**
```json
{
  "device_id": "KASIR-KANTIN-001",
  "fingerprint_id": "FP00001A2B3C",
  "action": "register",
  "nis": "PST.2026.001",
  "timestamp": 1704067200
}
```

**Response:**
```json
{
  "success": true,
  "message": "Fingerprint berhasil terdaftar",
  "data": {
    "santri_id": 1,
    "fingerprint_id": "FP00001A2B3C"
  }
}
```

**Body (Scan):**
```json
{
  "device_id": "KASIR-KANTIN-001",
  "fingerprint_id": "FP00001A2B3C",
  "action": "scan",
  "timestamp": 1704067200
}
```

**Response:**
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

## Setup di Admin Panel

### Cara Input Fingerprint ID

1. **Login** dengan akun admin
2. Buka **Admin > Fingerprint**
3. Klik **Edit** pada santri
4. Masukkan **Fingerprint ID** dari hardware
5. Klik **Simpan**

### Filter Status

- **Belum Ada Fingerprint** - santri belum terdaftar
- **Sudah Ada Fingerprint** - santri sudah bisa transaksi

## Development & Testing

### Test API Endpoint

```bash
curl -X POST http://localhost:8000/api/fingerprint/sync \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": "TEST-001",
    "fingerprint_id": "FP_TEST_001",
    "action": "scan",
    "timestamp": '$(date +%s)'
  }'
```

### Mock Hardware Response

Untuk testing tanpa hardware fisik, gunakan Postman atau curl.

## Production Notes

- Device ID harus unik per location
- Implement proper authentication token untuk security
- Log semua sync request di database untuk audit
- Backup fingerprint data secara berkala
- Test failover jika hardware disconnect

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Hardware tidak connect | Cek USB driver, restart hardware |
| Fingerprint ID tidak match | Pastikan format ID sama di hardware & web |
| Santri tidak teridentifikasi | Cek apakah fingerprint sudah terdaftar di admin |
| API timeout | Cek koneksi network, restart hardware reader |
