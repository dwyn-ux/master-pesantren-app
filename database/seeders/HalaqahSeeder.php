<?php

namespace Database\Seeders;

use App\Models\Halaqah;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Ustadz;
use Illuminate\Database\Seeder;

class HalaqahSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SurahSeeder::class,
            AkademikSeeder::class,
        ]);

        $halaqahData = [
            [
                'nama' => 'Halaqah Al-Fatih',
                'ustadz_username' => 'ustadz.ahmad',
                'santri_nis' => ['S2025001', 'S2025002', 'S2025003'],
            ],
            [
                'nama' => 'Halaqah Al-Hikmah',
                'ustadz_username' => 'ustadz.fauzan',
                'santri_nis' => ['S2025004', 'S2025005', 'S2025006'],
            ],
            [
                'nama' => 'Halaqah An-Nur',
                'ustadz_username' => 'ustadz.salman',
                'santri_nis' => ['S2025007', 'S2025008', 'S2025009'],
            ],
            [
                'nama' => 'Halaqah Ar-Rahmah',
                'ustadz_username' => 'ustadz.bilal',
                'santri_nis' => ['S2025010', 'S2025011', 'S2025012'],
            ],
            [
                'nama' => 'Halaqah Al-Furqan',
                'ustadz_username' => 'ustadz.umar',
                'santri_nis' => ['S2025013', 'S2025014', 'S2025015'],
            ],
        ];

        foreach ($halaqahData as $index => $data) {
            $ustadz = Ustadz::whereHas('user', fn($query) => $query->where('username', $data['ustadz_username']))->first();

            if (! $ustadz) {
                continue;
            }

            $halaqah = Halaqah::updateOrCreate(
                ['nama' => $data['nama']],
                ['ustadz_id' => $ustadz->id]
            );

            $santri = Santri::whereIn('nis', $data['santri_nis'])->orderBy('nis')->get();
            $syncData = [];

            foreach ($santri as $santriItem) {
                $syncData[$santriItem->id] = [
                    'tanggal_bergabung' => now()->subMonths(2)->addDays($index)->toDateString(),
                ];
            }

            $halaqah->santri()->syncWithoutDetaching($syncData);

            foreach ($santri as $position => $santriItem) {
                $this->seedSetoran($santriItem->id, $ustadz->id, $position);
            }
        }

        $this->command->info('Halaqah dummy berhasil di-seed: 5 halaqah, 15 anggota, dan sample setoran tahfidz.');
    }

    private function seedSetoran(int $santriId, int $ustadzId, int $position): void
    {
        $samples = [
            [
                'jenis' => 'ziyadah',
                'surah_awal' => 78,
                'ayat_awal' => 1,
                'surah_akhir' => 78,
                'ayat_akhir' => 20,
                'jumlah_halaman' => 1.0,
                'status' => 'maqbul',
                'catatan' => 'Bacaan lancar, lanjutkan hafalan berikutnya.',
                'tanggal' => now()->subDays(14 - $position)->toDateString(),
            ],
            [
                'jenis' => 'ziyadah',
                'surah_awal' => 78,
                'ayat_awal' => 21,
                'surah_akhir' => 78,
                'ayat_akhir' => 40,
                'jumlah_halaman' => 1.0,
                'status' => $position % 2 === 0 ? 'maqbul' : 'perbaikan',
                'catatan' => $position % 2 === 0 ? 'Makhraj sudah baik.' : 'Perlu ulang bagian tengah ayat.',
                'tanggal' => now()->subDays(7 - $position)->toDateString(),
            ],
            [
                'jenis' => 'muraja_ah',
                'surah_awal' => 112,
                'ayat_awal' => 1,
                'surah_akhir' => 114,
                'ayat_akhir' => 6,
                'jumlah_halaman' => 0.5,
                'status' => 'maqbul',
                'catatan' => 'Murojaah juz amma stabil.',
                'tanggal' => now()->subDays(2 + $position)->toDateString(),
            ],
        ];

        foreach ($samples as $sample) {
            Setoran::updateOrCreate(
                [
                    'santri_id' => $santriId,
                    'penerima_id' => $ustadzId,
                    'jenis' => $sample['jenis'],
                    'tanggal' => $sample['tanggal'],
                    'surah_awal' => $sample['surah_awal'],
                    'ayat_awal' => $sample['ayat_awal'],
                ],
                [
                    'tipe_halaqah' => 'utama',
                    'surah_akhir' => $sample['surah_akhir'],
                    'ayat_akhir' => $sample['ayat_akhir'],
                    'jumlah_halaman' => $sample['jumlah_halaman'],
                    'status' => $sample['status'],
                    'catatan' => $sample['catatan'],
                ]
            );
        }
    }
}
