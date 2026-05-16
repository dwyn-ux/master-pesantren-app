<?php

namespace Database\Seeders;

use App\Models\Akademik\Kelas;
use App\Models\Akademik\Kitab;
use App\Models\Akademik\MataPelajaran;
use App\Models\Akademik\TahunAjaran;
use App\Models\Akademik\Tingkat;
use Illuminate\Database\Seeder;

class AkademikSeeder extends Seeder
{
    public function run(): void
    {
        // ── Tahun Ajaran ────────────────────────────────────────
        $ta = TahunAjaran::firstOrCreate(
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
        $tingkat = collect([
            ['nama' => 'Ibtidaiyah 1', 'urutan' => 1],
            ['nama' => 'Ibtidaiyah 2', 'urutan' => 2],
            ['nama' => 'Ibtidaiyah 3', 'urutan' => 3],
            ['nama' => 'Tsanawiyah 1', 'urutan' => 4],
            ['nama' => 'Tsanawiyah 2', 'urutan' => 5],
            ['nama' => 'Tsanawiyah 3', 'urutan' => 6],
            ['nama' => 'Aliyah 1',     'urutan' => 7],
            ['nama' => 'Aliyah 2',     'urutan' => 8],
            ['nama' => 'Aliyah 3',     'urutan' => 9],
        ])->map(fn ($t) => Tingkat::firstOrCreate(['nama' => $t['nama']], $t));

        // ── Kelas (1 kelas per tingkat) ─────────────────────────
        $tingkat->each(function ($t) {
            Kelas::firstOrCreate(
                ['tingkat_id' => $t->id, 'nama' => $t->nama . 'A'],
                ['kapasitas' => 30]
            );
        });

        // ── Mata Pelajaran Diniyah ──────────────────────────────
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

        $this->command->info('Akademik diniyah berhasil di-seed: 2 tahun ajaran, 9 tingkat, 9 kelas, 10 mapel, dengan kitab referensi.');
    }
}
