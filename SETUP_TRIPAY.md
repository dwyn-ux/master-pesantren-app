# Setup Pembayaran Tripay

## Langkah 1: Dapatkan Kredensial Tripay

1. Daftar di [Tripay.co.id](https://tripay.co.id)
2. Masuk ke dashboard
3. Buka menu **API Keys** → **Developer**
4. Copy:
   - `API Key`
   - `Private Key`
   - `Merchant Code`

## Langkah 2: Setup Environment Variables

Tambahkan ke file `.env`:

```env
# Tripay Payment Gateway
TRIPAY_API_KEY=xxxxxx_your_api_key_here
TRIPAY_PRIVATE_KEY=xxxxxx_your_private_key_here
TRIPAY_MERCHANT_CODE=xxxxx
TRIPAY_MODE=sandbox  # sandbox untuk testing, production untuk live
```

## Langkah 3: Konfigurasi Metode Pembayaran

Metode pembayaran yang tersedia:
- `va_bca` - Virtual Account BCA
- `va_mandiri` - Virtual Account Mandiri
- `qris` - QRIS Code
- `gopay` - GoPay
- `ovo` - OVO
- `manual` - Pembayaran Manual (langsung lunas)

## Langkah 4: Testing

### Sandbox Testing
1. Gunakan `TRIPAY_MODE=sandbox` di `.env`
2. Akses admin → **Pembayaran** → **Tambah Pembayaran**
3. Pilih tagihan santri
4. Pilih metode pembayaran
5. Klik **Buat Pembayaran** - akan redirect ke Tripay sandbox

### Callback dari Tripay
Pastikan webhook sudah dikonfigurasi di Tripay dashboard:

```
POST /api/tripay-callback
```

Endpoint ini akan digunakan Tripay untuk notifikasi pembayaran masuk.

## Catatan

- Untuk production, ganti `TRIPAY_MODE=production`
- Setiap transaksi diberi reference unik: `PESANTREN-{tagihan_id}-{timestamp}`
- Invoice berlaku 1 hari (24 jam)
- Cek status pembayaran bisa dilakukan manual dari menu Pembayaran dengan tombol **Cek Status**
