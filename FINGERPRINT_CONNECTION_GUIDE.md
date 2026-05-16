# Panduan Koneksi Fingerprint ke Web

## Overview

Sistem fingerprint terintegrasi antara hardware reader dan aplikasi web melalui API endpoint. Hardware reader akan mengirim data fingerprint ke web untuk verifikasi dan transaksi.

---

## 1. Hardware Setup

### Perangkat yang Dibutuhkan

- **Fingerprint Scanner USB** (contoh: ZKTeco, Suprema, Morpho)
- **PC/Komputer** dengan Windows/Linux
- **Software SDK** dari vendor fingerprint reader

### Software Requirements

- **Python 3.8+** untuk script konektor
- **Requests library**: `pip install requests`
- **SDK Fingerprint** (sesuai vendor)

---

## 2. Script Konektor Python

Buat file `fingerprint_connector.py` di PC kasir:

```python
import requests
import json
import time
from datetime import datetime
import fingerprint_sdk  # Ganti dengan SDK yang sesuai

# Konfigurasi
WEB_API_URL = "http://localhost:8080/api/fingerprint/sync"  # Ganti dengan IP server
DEVICE_ID = "KASIR-KANTIN-001"  # Unique per device
DEVICE_TOKEN = "kasir_kantin_token_123"  # Optional untuk security

class FingerprintConnector:
    def __init__(self):
        self.fp_reader = fingerprint_sdk.FingerprintReader()
        self.session = requests.Session()

    def send_to_web(self, fingerprint_id, action, nis=None):
        """Kirim data ke web API"""

        payload = {
            "device_id": DEVICE_ID,
            "fingerprint_id": fingerprint_id,
            "action": action,
            "timestamp": int(time.time())
        }

        if nis:
            payload["nis"] = nis

        headers = {
            "Content-Type": "application/json"
        }

        if DEVICE_TOKEN:
            headers["Authorization"] = f"Bearer {DEVICE_TOKEN}"

        try:
            response = self.session.post(WEB_API_URL, json=payload, headers=headers)
            result = response.json()

            if result.get("success"):
                print(f"✅ {action.upper()}: {result['message']}")
                if "data" in result:
                    santri = result["data"]
                    print(f"   Santri: {santri['nama']} ({santri['nis']})")
                    print(f"   Saldo: Rp {santri['saldo']:,}")
                return result
            else:
                print(f"❌ Error: {result.get('message', 'Unknown error')}")
                return None

        except Exception as e:
            print(f"❌ Connection Error: {e}")
            return None

    def register_fingerprint(self, nis):
        """Registrasi fingerprint baru"""
        print(f"📝 Registrasi fingerprint untuk NIS: {nis}")

        # Scan fingerprint
        fingerprint_id = self.fp_reader.scan_and_get_id()

        if fingerprint_id:
            return self.send_to_web(fingerprint_id, "register", nis)
        else:
            print("❌ Gagal scan fingerprint")
            return None

    def scan_fingerprint(self):
        """Scan untuk verifikasi transaksi"""
        print("🔍 Scan fingerprint...")

        fingerprint_id = self.fp_reader.scan_and_get_id()

        if fingerprint_id:
            return self.send_to_web(fingerprint_id, "scan")
        else:
            print("❌ Gagal scan fingerprint")
            return None

    def run_transaction_flow(self, nominal):
        """Flow lengkap untuk transaksi"""
        print(f"💰 Transaksi sebesar Rp {nominal:,}")

        # 1. Scan fingerprint
        result = self.scan_fingerprint()

        if not result or not result.get("success"):
            return False

        santri = result["data"]

        # 2. Cek saldo
        if santri["saldo"] < nominal:
            print(f"❌ Saldo tidak cukup. Saldo: Rp {santri['saldo']:,}")
            return False

        # 3. Konfirmasi transaksi
        confirm = input(f"Konfirmasi transaksi Rp {nominal:,} untuk {santri['nama']}? (y/n): ")
        if confirm.lower() != 'y':
            return False

        # 4. Kirim transaksi ke web
        return self.send_transaction(santri["santri_id"], nominal)

    def send_transaction(self, santri_id, nominal):
        """Kirim data transaksi ke web"""
        payload = {
            "device_id": DEVICE_ID,
            "santri_id": santri_id,
            "nominal": nominal,
            "timestamp": int(time.time())
        }

        try:
            response = self.session.post(
                "http://localhost:8080/api/transaction/process",
                json=payload,
                headers={"Content-Type": "application/json"}
            )

            result = response.json()
            if result.get("success"):
                print(f"✅ Transaksi berhasil: {result['message']}")
                return True
            else:
                print(f"❌ Transaksi gagal: {result.get('message')}")
                return False

        except Exception as e:
            print(f"❌ Error: {e}")
            return False

# Main program
if __name__ == "__main__":
    connector = FingerprintConnector()

    while True:
        print("\n" + "="*50)
        print("FINGERPRINT KASIR SYSTEM")
        print("="*50)
        print("1. Register Fingerprint")
        print("2. Scan untuk Transaksi")
        print("3. Exit")

        choice = input("Pilih menu (1-3): ")

        if choice == "1":
            nis = input("Masukkan NIS santri: ")
            connector.register_fingerprint(nis)

        elif choice == "2":
            try:
                nominal = int(input("Masukkan nominal transaksi: "))
                connector.run_transaction_flow(nominal)
            except ValueError:
                print("❌ Nominal harus angka")

        elif choice == "3":
            break

        else:
            print("❌ Pilihan tidak valid")

        input("\nTekan Enter untuk lanjut...")
```

---

## 3. Setup di PC Kasir

### Step 1: Install Python & Dependencies

```bash
# Install Python 3.8+
# Download dari https://python.org

# Install requests
pip install requests

# Install SDK fingerprint (contoh untuk ZKTeco)
pip install pyzk
# Atau SDK lain sesuai vendor
```

### Step 2: Configure Script

Edit bagian konfigurasi di `fingerprint_connector.py`:

```python
WEB_API_URL = "http://192.168.1.100:8080/api/fingerprint/sync"  # IP server
DEVICE_ID = "KASIR-KANTIN-001"
DEVICE_TOKEN = "kasir_kantin_token_123"
```

### Step 3: Test Koneksi

Jalankan script:

```bash
python fingerprint_connector.py
```

---

## 4. API Endpoint Web (Sudah Ada)

**Endpoint:** `POST /api/fingerprint/sync`

**Request Body:**
```json
{
  "device_id": "KASIR-KANTIN-001",
  "fingerprint_id": "FP00001A2B3C",
  "action": "register",  // atau "scan"
  "nis": "PST.2026.001", // hanya untuk register
  "timestamp": 1704067200
}
```

**Response Success:**
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

---

## 5. Setup Multiple Devices

Untuk setup di beberapa lokasi:

### Device IDs

| Lokasi | Device ID |
|--------|-----------|
| Kasir Kantin Utama | `KASIR-KANTIN-001` |
| Kasir Kantin Cabang | `KASIR-KANTIN-002` |
| Kasir Laundry | `KASIR-LAUNDRY-001` |

### Konfigurasi Per Device

Setiap PC kasir punya script sendiri dengan `DEVICE_ID` berbeda.

---

## 6. Security & Best Practices

### Authentication

Tambahkan token per device:

```python
DEVICE_TOKEN = "unique_token_per_device"
headers["Authorization"] = f"Bearer {DEVICE_TOKEN}"
```

### Validation

- Validasi `timestamp` tidak terlalu lama (max 5 menit)
- Rate limiting per device
- Log semua aktivitas

### Error Handling

- Retry otomatis jika koneksi gagal
- Fallback ke manual input jika fingerprint gagal
- Alert admin jika device offline

---

## 7. Testing Flow

### Test Register

1. Admin buat santri baru di web
2. Di PC kasir: pilih menu 1 → input NIS → scan jari
3. Cek di web: Admin > Fingerprint → cari santri → lihat FP ID terisi

### Test Scan

1. Pastikan santri sudah ada FP ID
2. Di PC kasir: pilih menu 2 → input nominal → scan jari
3. Sistem akan identifikasi santri & proses transaksi

---

## 8. Troubleshooting

| Problem | Solution |
|---------|----------|
| SDK tidak terinstall | Install ulang SDK vendor |
| Tidak bisa connect ke web | Cek IP server & firewall |
| Fingerprint tidak terdeteksi | Cek USB connection & driver |
| Response timeout | Tambah timeout di requests |
| Invalid fingerprint ID | Cek format ID dari hardware |

---

## 9. Production Deployment

### Auto Start

Buat batch file untuk auto start:

`start_fingerprint.bat`:
```batch
@echo off
cd /d "C:\fingerprint-system"
python fingerprint_connector.py
pause
```

### Windows Service

Register sebagai Windows service untuk auto start saat boot.

---

## 10. Monitoring & Logs

### Web Logs

Cek logs di `storage/logs/laravel.log` untuk aktivitas API.

### Device Logs

Script akan print status ke console. Simpan ke file untuk monitoring.

---

## Files yang Dibutuhkan

- `fingerprint_connector.py` - Script konektor utama
- `start_fingerprint.bat` - Auto start script
- Dokumentasi SDK fingerprint vendor

---

## Next Steps

1. Setup hardware & SDK
2. Test koneksi ke web API
3. Configure multiple devices
4. Test full transaction flow
5. Deploy ke production