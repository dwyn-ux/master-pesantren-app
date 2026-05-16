<?php

namespace Database\Seeders;

use App\Models\Akademik\Kelas;
use App\Models\Akademik\KelasSantri;
use App\Models\Akademik\Kitab;
use App\Models\Akademik\Kkm;
use App\Models\Akademik\KomponenNilai;
use App\Models\Akademik\MataPelajaran;
use App\Models\Akademik\TahunAjaran;
use App\Models\Akademik\Tingkat;
use App\Models\Akademik\JadwalPelajaran;
use App\Models\Santri;
use App\Models\SantriWali;
use App\Models\Ustadz;
use App\Models\User;
use App\Models\Wali;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AkademikSeeder extends Seeder
{
    public function run(): void
    {
        // ── Tahun Ajaran ────────────────────────────────────────
        $taGanjil = TahunAjaran::firstOrCreate(
            ['nama' => '2025/2026', 'semester' => 'ganjil'],
            [
                'tanggal_mulai'   => '2025-07-15',
                'tanggal_selesai' => '2025-12-20',
                'is_active'       => true,
            ]
        );

        TahunAjaran::firstOrCreate(
            ['nama' => '2025/2026', 'semester' => 'genap'],
            [
                'tanggal_mulai'   => '2026-01-05',
                'tanggal_selesai' => '2026-06-20',
                'is_active'       => false,
            ]
        );

        // ── Tingkat ─────────────────────────────────────────────
        $tingkatList = [
            ['nama' => 'Ibtidaiyah 1', 'urutan' => 1],
            ['nama' => 'Ibtidaiyah 2', 'urutan' => 2],
            ['nama' => 'Ibtidaiyah 3', 'urutan' => 3],
            ['nama' => 'Tsanawiyah 1', 'urutan' => 4],
            ['nama' => 'Tsanawiyah 2', 'urutan' => 5],
            ['nama' => 'Tsanawiyah 3', 'urutan' => 6],
            ['nama' => 'Aliyah 1',     'urutan' => 7],
            ['nama' => 'Aliyah 2',     'urutan' => 8],
            ['nama' => 'Aliyah 3',     'urutan' => 9],
        ];

        $tingkat = collect($tingkatList)->map(fn ($t) => Tingkat::firstOrCreate(['nama' => $t['nama']], $t));

        // ── Ustadz Dummy (5 ustadz) ─────────────────────────────
        $ustadzData = [
            ['username' => 'ustadz.ahmad',   'name' => 'Ust. Ahmad Hidayat',   'no_hp' => '081234567801'],
            ['username' => 'ustadz.fauzan',  'name' => 'Ust. Muhammad Fauzan', 'no_hp' => '081234567802'],
            ['username' => 'ustadz.salman',  'name' => 'Ust. Salman Al-Farisi','no_hp' => '081234567803'],
            ['username' => 'ustadz.bilal',   'name' => 'Ust. Bilal Maulana',   'no_hp' => '081234567804'],
            ['username' => 'ustadz.umar',    'name' => 'Ust. Umar Faruq',      'no_hp' => '081234567805'],
        ];

        $ustadzList = collect();
        $credLines = ["=== Kredensial Ustadz Dummy (generated: " . now() . ") ===\n"];

        foreach ($ustadzData as $u) {
            $password = 'ustadz123';

            $user = User::firstOrCreate(
                ['username' => $u['username']],
                [
                    'name'           => $u['name'],
                    'password'       => Hash::make($password),
                    'must_change_pw' => false,
                    'is_active'      => true,
                ]
            );
            $user->syncRoles(['ustadz']);

            $ustadz = Ustadz::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama'  => $u['name'],
                    'no_hp' => $u['no_hp'],
                ]
            );

            $ustadzList->push($ustadz);
            $credLines[] = "[ustadz] {$u['username']} | password: {$password}";
        }

        \Illuminate\Support\Facades\Storage::disk('local')->put(
            'credentials-akademik.txt',
            implode("\n", $credLines) . "\n"
        );

        // ── Kelas (1 kelas per tingkat dengan wali kelas rotasi) ─
        foreach ($tingkat as $idx => $t) {
            $waliKelasId = $ustadzList->get($idx % $ustadzList->count())->id;
            Kelas::firstOrCreate(
                ['tingkat_id' => $t->id, 'nama' => $t->nama . 'A'],
                [
                    'wali_kelas_id' => $waliKelasId,
                    'kapasitas'     => 30,
                ]
            );
        }

        // ── Mata Pelajaran Diniyah + Kitab ──────────────────────
        $mapelData = [
            ['kode' => 'FIQ', 'nama' => 'Fiqih',         'urutan' => 1, 'kitab' => [['Safinatun Najah', 'Salim bin Sumair', 64], ['Fathul Qarib', 'Ibnu Qasim al-Ghazi', 200]]],
            ['kode' => 'AQI', 'nama' => 'Aqidah',        'urutan' => 2, 'kitab' => [['Aqidatul Awam', 'Ahmad Marzuqi', 30], ['Tijan Darari', 'Nawawi al-Bantani', 80]]],
            ['kode' => 'NAH', 'nama' => 'Nahwu',         'urutan' => 3, 'kitab' => [['Jurumiyah', 'Ash-Shanhaji', 40], ['Imrithi', 'Syarafuddin Yahya', 100], ['Alfiyah', 'Ibnu Malik', 200]]],
            ['kode' => 'SHF', 'nama' => 'Sharaf',        'urutan' => 4, 'kitab' => [['Amtsilatu Tashrifiyyah', 'Maksum bin Ali', 60]]],
            ['kode' => 'TAF', 'nama' => 'Tafsir',        'urutan' => 5, 'kitab' => [['Tafsir Jalalain', 'Jalaluddin', 600]]],
            ['kode' => 'HAD', 'nama' => 'Hadits',        'urutan' => 6, 'kitab' => [['Arbain Nawawi', 'Imam Nawawi', 80], ['Bulughul Maram', 'Ibnu Hajar', 400]]],
            ['kode' => 'AKH', 'nama' => 'Akhlak',        'urutan' => 7, 'kitab' => [['Taisirul Khallaq', 'Hafidz Hasan', 50], ['Akhlaqul Banin', 'Umar bin Ahmad Baradja', 80]]],
            ['kode' => 'BAR', 'nama' => 'Bahasa Arab',   'urutan' => 8, 'kitab' => [['Madarijud Durus', 'KH. Basori', 100]]],
            ['kode' => 'TJW', 'nama' => 'Tajwid',        'urutan' => 9, 'kitab' => [['Hidayatul Mustafid', 'Muhammad Mahmud', 40]]],
            ['kode' => 'SIR', 'nama' => 'Sirah Nabawiyah', 'urutan' => 10, 'kitab' => [['Khulasah Nurul Yaqin', 'Umar Abdul Jabbar', 120]]],
        ];

        foreach ($mapelData as $data) {
            $mapel = MataPelajaran::firstOrCreate(
                ['kode' => $data['kode']],
                ['nama' => $data['nama'], 'urutan' => $data['urutan']]
            );

            foreach ($data['kitab'] as [$nama, $pengarang, $halaman]) {
                Kitab::firstOrCreate(
                    ['mata_pelajaran_id' => $mapel->id, 'nama' => $nama],
                    ['pengarang' => $pengarang, 'total_halaman' => $halaman]
                );
            }
        }

        // ── Komponen Nilai default per mapel (UH 25%, Tugas 25%, UTS 20%, UAS 30%) ──
        $komponenDefault = [
            ['nama' => 'Ulangan Harian', 'bobot' => 25, 'urutan' => 1],
            ['nama' => 'Tugas',          'bobot' => 25, 'urutan' => 2],
            ['nama' => 'UTS',            'bobot' => 20, 'urutan' => 3],
            ['nama' => 'UAS',            'bobot' => 30, 'urutan' => 4],
        ];

        $allMapel = MataPelajaran::all();
        foreach ($allMapel as $mp) {
            foreach ($komponenDefault as $k) {
                KomponenNilai::firstOrCreate(
                    [
                        'mata_pelajaran_id' => $mp->id,
                        'tahun_ajaran_id'   => $taGanjil->id,
                        'nama'              => $k['nama'],
                    ],
                    [
                        'bobot'  => $k['bobot'],
                        'urutan' => $k['urutan'],
                    ]
                );
            }
        }

        // ── KKM default 70 untuk semua kombinasi ────────────────
        foreach ($tingkat as $tk) {
            foreach ($allMapel as $mp) {
                Kkm::firstOrCreate(
                    [
                        'tingkat_id'        => $tk->id,
                        'mata_pelajaran_id' => $mp->id,
                        'tahun_ajaran_id'   => $taGanjil->id,
                    ],
                    ['nilai_kkm' => 70]
                );
            }
        }

        // ── Santri & Wali Dummy (15 santri di tingkat berbeda) ──
        $santriData = [
            ['nis' => 'S2025001', 'nama' => 'Ahmad Zaki',         'kelas_legacy' => 'T1A', 'tingkat' => 'Tsanawiyah 1', 'wali' => 'Bapak Hasan'],
            ['nis' => 'S2025002', 'nama' => 'Muhammad Fadhil',    'kelas_legacy' => 'T1A', 'tingkat' => 'Tsanawiyah 1', 'wali' => 'Bapak Mahmud'],
            ['nis' => 'S2025003', 'nama' => 'Abdullah Karim',     'kelas_legacy' => 'T1A', 'tingkat' => 'Tsanawiyah 1', 'wali' => 'Bapak Yusuf'],
            ['nis' => 'S2025004', 'nama' => 'Yusuf Habibie',      'kelas_legacy' => 'T2A', 'tingkat' => 'Tsanawiyah 2', 'wali' => 'Bapak Anwar'],
            ['nis' => 'S2025005', 'nama' => 'Ibrahim Khalid',     'kelas_legacy' => 'T2A', 'tingkat' => 'Tsanawiyah 2', 'wali' => 'Bapak Karim'],
            ['nis' => 'S2025006', 'nama' => 'Ismail Rafiq',       'kelas_legacy' => 'T3A', 'tingkat' => 'Tsanawiyah 3', 'wali' => 'Bapak Hidayat'],
            ['nis' => 'S2025007', 'nama' => 'Hamzah Anshari',     'kelas_legacy' => 'T3A', 'tingkat' => 'Tsanawiyah 3', 'wali' => 'Bapak Saiful'],
            ['nis' => 'S2025008', 'nama' => 'Ali Akram',          'kelas_legacy' => 'A1A', 'tingkat' => 'Aliyah 1',     'wali' => 'Bapak Burhan'],
            ['nis' => 'S2025009', 'nama' => 'Umar Faiq',          'kelas_legacy' => 'A1A', 'tingkat' => 'Aliyah 1',     'wali' => 'Bapak Rahman'],
            ['nis' => 'S2025010', 'nama' => 'Bilal Mutawakkil',   'kelas_legacy' => 'A2A', 'tingkat' => 'Aliyah 2',     'wali' => 'Bapak Saleh'],
            ['nis' => 'S2025011', 'nama' => 'Salman Hafiz',       'kelas_legacy' => 'A2A', 'tingkat' => 'Aliyah 2',     'wali' => 'Bapak Mubarok'],
            ['nis' => 'S2025012', 'nama' => 'Khalid Syakir',      'kelas_legacy' => 'A3A', 'tingkat' => 'Aliyah 3',     'wali' => 'Bapak Adnan'],
            ['nis' => 'S2025013', 'nama' => 'Zaid Munir',         'kelas_legacy' => 'I3A', 'tingkat' => 'Ibtidaiyah 3', 'wali' => 'Bapak Idris'],
            ['nis' => 'S2025014', 'nama' => 'Adam Tariq',         'kelas_legacy' => 'I3A', 'tingkat' => 'Ibtidaiyah 3', 'wali' => 'Bapak Junaid'],
            ['nis' => 'S2025015', 'nama' => 'Hasan Ridho',        'kelas_legacy' => 'I3A', 'tingkat' => 'Ibtidaiyah 3', 'wali' => 'Bapak Faruq'],
        ];

        foreach ($santriData as $sd) {
            // Bikin user wali
            $waliUsername = 'wali.' . Str::slug($sd['wali'], '.');
            $waliUser = User::firstOrCreate(
                ['username' => $waliUsername],
                [
                    'name'           => $sd['wali'],
                    'password'       => Hash::make('wali123'),
                    'must_change_pw' => false,
                    'is_active'      => true,
                ]
            );
            $waliUser->syncRoles(['wali']);

            $wali = Wali::firstOrCreate(
                ['user_id' => $waliUser->id],
                ['nama' => $sd['wali'], 'no_hp' => '0812' . rand(10000000, 99999999)]
            );

            // Bikin santri
            $santri = Santri::firstOrCreate(
                ['nis' => $sd['nis']],
                [
                    'nama'          => $sd['nama'],
                    'jenis_kelamin' => 'L',
                    'kelas'         => $sd['kelas_legacy'],
                    'is_aktif'      => true,
                    'saldo'         => 0,
                ]
            );

            // Pasang relasi santri-wali
            SantriWali::firstOrCreate(
                ['santri_id' => $santri->id, 'wali_id' => $wali->id],
                ['hubungan' => 'ayah']
            );

            // Assign ke kelas akademik
            $tingkatRow = $tingkat->firstWhere('nama', $sd['tingkat']);
            if ($tingkatRow) {
                $kelas = Kelas::where('tingkat_id', $tingkatRow->id)->first();
                if ($kelas) {
                    KelasSantri::firstOrCreate(
                        [
                            'santri_id'       => $santri->id,
                            'kelas_id'        => $kelas->id,
                            'tahun_ajaran_id' => $taGanjil->id,
                        ]
                    );
                }
            }
        }

        // ── Jadwal Pelajaran sample (Tsanawiyah 1A, Senin & Selasa) ──
        $kelasSample = Kelas::whereHas('tingkat', fn ($q) => $q->where('nama', 'Tsanawiyah 1'))->first();
        if ($kelasSample) {
            $jadwalSample = [
                ['hari' => 'senin',  'jam_mulai' => '07:00', 'jam_selesai' => '08:00', 'kode' => 'FIQ'],
                ['hari' => 'senin',  'jam_mulai' => '08:00', 'jam_selesai' => '09:00', 'kode' => 'NAH'],
                ['hari' => 'senin',  'jam_mulai' => '09:30', 'jam_selesai' => '10:30', 'kode' => 'AQI'],
                ['hari' => 'selasa', 'jam_mulai' => '07:00', 'jam_selesai' => '08:00', 'kode' => 'HAD'],
                ['hari' => 'selasa', 'jam_mulai' => '08:00', 'jam_selesai' => '09:00', 'kode' => 'TJW'],
                ['hari' => 'rabu',   'jam_mulai' => '07:00', 'jam_selesai' => '08:00', 'kode' => 'TAF'],
                ['hari' => 'kamis',  'jam_mulai' => '07:00', 'jam_selesai' => '08:00', 'kode' => 'AKH'],
            ];

            foreach ($jadwalSample as $idx => $j) {
                $mapel = MataPelajaran::where('kode', $j['kode'])->first();
                if (!$mapel) continue;

                JadwalPelajaran::firstOrCreate(
                    [
                        'kelas_id'          => $kelasSample->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'tahun_ajaran_id'   => $taGanjil->id,
                        'hari'              => $j['hari'],
                        'jam_mulai'         => $j['jam_mulai'],
                    ],
                    [
                        'ustadz_id'   => $ustadzList->get($idx % $ustadzList->count())->id,
                        'jam_selesai' => $j['jam_selesai'],
                        'ruangan'     => 'Ruang ' . ($idx + 1),
                    ]
                );
            }
        }

        $this->command->info('Akademik diniyah berhasil di-seed:');
        $this->command->info('  - 2 tahun ajaran, 9 tingkat, 9 kelas');
        $this->command->info('  - 5 ustadz dummy (password: ustadz123)');
        $this->command->info('  - 15 santri + 15 wali dummy (password wali: wali123)');
        $this->command->info('  - 10 mapel + 16 kitab referensi');
        $this->command->info('  - 40 komponen nilai (4 per mapel) + 90 KKM');
        $this->command->info('  - 7 jadwal pelajaran sample (Tsanawiyah 1A)');
        $this->command->warn('Kredensial ustadz disimpan di: storage/app/private/credentials-akademik.txt');
    }
}

