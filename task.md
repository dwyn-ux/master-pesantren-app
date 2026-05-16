# Tasks: Modul Klinik, Perizinan, & Laporan

- [x] **1. Update Biodata Santri & Ustadz**
  - [x] Buat file migration untuk menambahkan `nik`, `alamat`, `tanggal_lahir` di tabel `santri`.
  - [x] Buat file migration untuk menambahkan `nik`, `alamat`, `tanggal_lahir` di tabel `ustadz`.
  - [x] Update Model `Santri` dan `Ustadz` (tambahkan kolom di `$fillable` dan *casts*).
  - [x] Update View dan Controller Form Santri.
  - [x] Update View dan Controller Form Ustadz.

- [x] **2. Modul Klinik (Rekam Medis)**
  - [x] Buat file migration & Model `KunjunganKlinik` (Polymorphic untuk Santri & Ustadz).
  - [x] Buat Controller `KlinikController` untuk hak akses Ustadz.
  - [x] Buat API Endpoint untuk *Live Search* Santri/Ustadz (termasuk riwayat medisnya).
  - [x] Buat View Dashboard Klinik & Form Input Pemeriksaan.
  - [x] Buat notifikasi internal ke Dashboard Wali Santri saat data klinik ditambahkan.

- [x] **3. Modul Perizinan (Izin Biasa & Kepulangan Serentak)**
  - [x] Tambahkan role `kesantrian` di Seeder.
  - [x] Buat migration & Model `Perizinan` (untuk izin insidental).
  - [x] Buat migration & Model `SesiKepulangan` dan `KepulanganSantri`.
    - [x] Tambahkan kolom `tanggal_janji_bayar` untuk surat kesanggupan bendahara.
  - [x] Buat menu pengajuan Izin di Dashboard Wali Santri.
  - [x] Buat menu *Approval* Izin di Dashboard Kesantrian & Ustadz Halaqah.
  - [x] Buat menu Sesi Kepulangan di Dashboard Admin/Kepala Pondok.
  - [x] Buat menu pengecekan dan *ACC Surat Kesanggupan* di Dashboard Bendahara.

- [x] **3b. Limit Uang Saku**
  - [x] Migration: tambah `tipe_limit` & `nominal_limit` ke tabel `santri`.
  - [x] Admin dapat set limit harian/mingguan per santri di form edit.
  - [x] Wali dapat set limit untuk anak sendiri di halaman Limit Uang Saku.
  - [x] Enforcement otomatis di transaksi Kantin & Laundry.

- [x] **4. Laporan Terpadu (Halaqah, Outlet, Laundry)**
  - [x] Buat Controller Khusus Laporan untuk Admin/Kepala Pondok.
  - [x] Buat View Dashboard Laporan (filter bulan/tahun, 4 stat cards, bar chart revenue, donut setoran, 3 tabel).
  - [x] Integrasikan Chart.js via CDN.
