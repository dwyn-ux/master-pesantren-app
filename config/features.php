<?php

/*
|--------------------------------------------------------------------------
| Feature Registry
|--------------------------------------------------------------------------
|
| Daftar semua fitur yang bisa diaktifkan/dinonaktifkan oleh Superadmin.
| Format:
|   'key' => [
|       'label'       => Nama tampilan,
|       'description' => Penjelasan singkat fitur,
|       'group'       => Grouping di halaman pengaturan,
|       'core'        => true jika fitur tidak boleh dimatikan (selalu aktif),
|       'default'     => Status awal saat fresh install,
|   ]
|
*/

return [

    'features' => [

        // ── Spiritual ───────────────────────────────────────────
        'quran' => [
            'label'       => 'Al-Quran',
            'description' => 'Baca Al-Quran lengkap dengan terjemah & tafsir.',
            'group'       => 'Spiritual',
            'core'        => false,
            'default'     => true,
        ],
        'prayer' => [
            'label'       => 'Jadwal Sholat',
            'description' => 'Jadwal sholat 5 waktu, kalender, dan arah kiblat.',
            'group'       => 'Spiritual',
            'core'        => false,
            'default'     => true,
        ],

        // ── Akademik Tahfidz ────────────────────────────────────
        'halaqah' => [
            'label'       => 'Manajemen Halaqah & Hafalan',
            'description' => 'Halaqah tahfidz, setoran hafalan, jurnal ustadz.',
            'group'       => 'Akademik Tahfidz',
            'core'        => false,
            'default'     => true,
        ],

        // ── Akademik Diniyah ────────────────────────────────────
        'akademik_master' => [
            'label'       => 'Master Akademik (Kelas, Mapel, Jadwal)',
            'description' => 'Kelola tahun ajaran, tingkat, kelas, mata pelajaran diniyah, kitab referensi, jadwal pelajaran.',
            'group'       => 'Akademik Diniyah',
            'core'        => false,
            'default'     => false,
        ],
        'akademik_absensi' => [
            'label'       => 'Absensi & Jurnal Mengajar',
            'description' => 'Ustadz absen santri per jam pelajaran & catat jurnal mengajar harian.',
            'group'       => 'Akademik Diniyah',
            'core'        => false,
            'default'     => false,
        ],
        'akademik_penilaian' => [
            'label'       => 'Penilaian & Nilai',
            'description' => 'Input nilai UH, Tugas, UTS, UAS. Hitung otomatis dengan bobot, KKM, predikat.',
            'group'       => 'Akademik Diniyah',
            'core'        => false,
            'default'     => false,
        ],
        'akademik_raport' => [
            'label'       => 'Raport PDF',
            'description' => 'Cetak raport semester per santri (nilai mapel, sikap, kehadiran, catatan wali kelas).',
            'group'       => 'Akademik Diniyah',
            'core'        => false,
            'default'     => false,
        ],
        'akademik_kenaikan' => [
            'label'       => 'Kenaikan Kelas',
            'description' => 'Workflow kenaikan tahun ajaran, keputusan naik/tidak naik, mutasi santri.',
            'group'       => 'Akademik Diniyah',
            'core'        => false,
            'default'     => false,
        ],
        'halaqah_diniyah' => [
            'label'       => 'Halaqah Diniyah (Kajian Kitab)',
            'description' => 'Tracking bandongan/sorogan kitab kuning, posisi halaman, khataman kitab, sertifikat.',
            'group'       => 'Akademik Diniyah',
            'core'        => false,
            'default'     => false,
        ],

        // ── Keuangan ────────────────────────────────────────────
        'tagihan' => [
            'label'       => 'Tagihan & Pembayaran',
            'description' => 'SPP, jenis tagihan, payment gateway (Tripay/Midtrans).',
            'group'       => 'Keuangan',
            'core'        => false,
            'default'     => true,
        ],
        'finance' => [
            'label'       => 'Modul Keuangan (Akuntansi)',
            'description' => 'Jurnal, buku besar, neraca, laba-rugi, arus kas.',
            'group'       => 'Keuangan',
            'core'        => false,
            'default'     => false,
        ],
        'wallet' => [
            'label'       => 'Wallet & Top Up',
            'description' => 'Saldo santri, top up, limit uang saku.',
            'group'       => 'Keuangan',
            'core'        => false,
            'default'     => false,
        ],

        // ── Outlet ──────────────────────────────────────────────
        'kantin' => [
            'label'       => 'Kasir Kantin',
            'description' => 'Produk kantin, transaksi kasir, potong saldo wallet.',
            'group'       => 'Outlet',
            'core'        => false,
            'default'     => false,
        ],
        'laundry' => [
            'label'       => 'Laundry',
            'description' => 'Pencatatan order laundry & tarif.',
            'group'       => 'Outlet',
            'core'        => false,
            'default'     => false,
        ],
        'marketplace' => [
            'label'       => 'Marketplace Wali',
            'description' => 'Wali pesan barang ke outlet untuk anaknya.',
            'group'       => 'Outlet',
            'core'        => false,
            'default'     => false,
        ],

        // ── Kesantrian ──────────────────────────────────────────
        'klinik' => [
            'label'       => 'Klinik / Rekam Medis',
            'description' => 'Catatan kunjungan, rekam medis santri.',
            'group'       => 'Kesantrian',
            'core'        => false,
            'default'     => false,
        ],
        'voice_note' => [
            'label'       => 'Voice Note',
            'description' => 'Pesan suara wali ↔ ustadz.',
            'group'       => 'Kesantrian',
            'core'        => false,
            'default'     => false,
        ],
        'perizinan' => [
            'label'       => 'Perizinan & Kepulangan',
            'description' => 'Surat izin, sesi kepulangan, ACC bendahara.',
            'group'       => 'Kesantrian',
            'core'        => false,
            'default'     => false,
        ],

        // ── Utilitas ────────────────────────────────────────────
        'fingerprint' => [
            'label'       => 'Fingerprint Device',
            'description' => 'Integrasi mesin sidik jari (presensi).',
            'group'       => 'Utilitas',
            'core'        => false,
            'default'     => false,
        ],
        'rfid' => [
            'label'       => 'Kartu RFID',
            'description' => 'Identifikasi santri via RFID.',
            'group'       => 'Utilitas',
            'core'        => false,
            'default'     => false,
        ],
        'laporan_pdf' => [
            'label'       => 'Laporan PDF',
            'description' => 'Cetak laporan keuangan, tahfidz, kantin, laundry ke PDF.',
            'group'       => 'Utilitas',
            'core'        => false,
            'default'     => true,
        ],
        'laporan_otomatis' => [
            'label'       => 'Laporan Otomatis',
            'description' => 'Notifikasi laporan mingguan & bulanan.',
            'group'       => 'Utilitas',
            'core'        => false,
            'default'     => false,
        ],
        'import_excel' => [
            'label'       => 'Import Excel',
            'description' => 'Import data santri/wali via Excel.',
            'group'       => 'Utilitas',
            'core'        => false,
            'default'     => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Packages (Preset)
    |--------------------------------------------------------------------------
    |
    | Paket bundling yang bisa di-apply sekali klik oleh superadmin.
    | Harga setup = one-time fee instalasi, training, migrasi data.
    | Harga bulanan = subscription untuk hosting, maintenance, support, update.
    |
    */
    'packages' => [

        'starter' => [
            'name'           => 'Paket Starter',
            'setup_price'    => 3_000_000,
            'monthly_price'  => 850_000,
            'description'    => 'Untuk pesantren tahfidz kecil (<150 santri). Fokus hafalan & tagihan SPP.',
            'target_size'    => 'Kecil (<150 santri)',
            'features'       => [
                'quran', 'prayer', 'halaqah', 'tagihan',
                'laporan_pdf', 'import_excel', 'voice_note',
            ],
        ],

        'growth' => [
            'name'           => 'Paket Growth',
            'setup_price'    => 6_500_000,
            'monthly_price'  => 1_800_000,
            'description'    => 'Pesantren menengah (150-400 santri) dengan outlet & akademik diniyah dasar.',
            'target_size'    => 'Menengah (150-400 santri)',
            'features'       => [
                'quran', 'prayer', 'halaqah', 'tagihan',
                'wallet', 'kantin', 'laundry', 'marketplace',
                'voice_note', 'perizinan',
                'akademik_master', 'akademik_absensi',
                'laporan_pdf', 'import_excel',
            ],
        ],

        'pro' => [
            'name'           => 'Paket Pro',
            'setup_price'    => 10_000_000,
            'monthly_price'  => 3_000_000,
            'description'    => 'Pesantren besar (400+ santri) dengan raport diniyah, akuntansi, laporan otomatis.',
            'target_size'    => 'Besar (400+ santri)',
            'features'       => [
                'quran', 'prayer', 'halaqah', 'tagihan',
                'wallet', 'kantin', 'laundry', 'marketplace',
                'voice_note', 'perizinan', 'klinik',
                'akademik_master', 'akademik_absensi', 'akademik_penilaian', 'akademik_raport', 'akademik_kenaikan',
                'finance',
                'laporan_pdf', 'laporan_otomatis', 'import_excel',
            ],
        ],

        'pro_plus' => [
            'name'           => 'Paket Pro+',
            'setup_price'    => 12_500_000,
            'monthly_price'  => 3_500_000,
            'description'    => 'Pro + Halaqah Diniyah (kajian kitab kuning, bandongan/sorogan, khataman kitab).',
            'target_size'    => 'Besar (400+ santri) dengan kajian kitab',
            'features'       => [
                'quran', 'prayer', 'halaqah', 'tagihan',
                'wallet', 'kantin', 'laundry', 'marketplace',
                'voice_note', 'perizinan', 'klinik',
                'akademik_master', 'akademik_absensi', 'akademik_penilaian', 'akademik_raport', 'akademik_kenaikan',
                'halaqah_diniyah',
                'finance',
                'laporan_pdf', 'laporan_otomatis', 'import_excel',
            ],
        ],

        'enterprise' => [
            'name'           => 'Paket Enterprise',
            'setup_price'    => 18_000_000,
            'monthly_price'  => 5_500_000,
            'description'    => 'Semua fitur + hardware (fingerprint, RFID), custom branding, training onsite.',
            'target_size'    => 'Sangat Besar (700+ santri) / Multi-cabang',
            'features'       => [
                'quran', 'prayer', 'halaqah', 'tagihan',
                'wallet', 'kantin', 'laundry', 'marketplace',
                'voice_note', 'perizinan', 'klinik',
                'akademik_master', 'akademik_absensi', 'akademik_penilaian', 'akademik_raport', 'akademik_kenaikan',
                'halaqah_diniyah',
                'finance',
                'fingerprint', 'rfid',
                'laporan_pdf', 'laporan_otomatis', 'import_excel',
            ],
        ],

    ],

];
