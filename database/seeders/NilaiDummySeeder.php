<?php

namespace Database\Seeders;

use App\Models\Akademik\AbsensiPelajaran;
use App\Models\Akademik\CatatanWaliKelas;
use App\Models\Akademik\JadwalPelajaran;
use App\Models\Akademik\KelasSantri;
use App\Models\Akademik\KomponenNilai;
use App\Models\Akademik\Nilai;
use App\Models\Akademik\NilaiAkhir;
use App\Models\Akademik\NilaiSikap;
use App\Models\Akademik\TahunAjaran;
use App\Models\Santri;
use Illuminate\Database\Seeder;

class NilaiDummySeeder extends Seeder
{
    public function run(): void
    {
        $taActive = TahunAjaran::active();
        if (!$taActive) {
            $this->command->error('Tidak ada tahun ajaran aktif. Run AkademikSeeder dulu.');
            return;
        }

        $santriList = Santri::whereHas('kelasSantri', fn ($q) => $q->where('tahun_ajaran_id', $taActive->id))->get();
        if ($santriList->isEmpty()) {
            $this->command->error('Belum ada santri di tahun ajaran aktif. Run AkademikSeeder dulu.');
            return;
        }

        $komponenAll = KomponenNilai::where('tahun_ajaran_id', $taActive->id)->get();
        if ($komponenAll->isEmpty()) {
            $this->command->error('Belum ada komponen nilai. Run AkademikSeeder dulu.');
            return;
        }

        $aspekSikap = ['Kejujuran', 'Kedisiplinan', 'Ibadah', 'Kebersihan', 'Sopan Santun'];
        $deskripsiSikap = [
            'A' => 'Sangat baik dan menjadi teladan.',
            'B' => 'Baik dan konsisten.',
            'C' => 'Cukup, perlu pembiasaan lebih.',
            'D' => 'Perlu bimbingan lebih lanjut.',
        ];

        $count = ['nilai' => 0, 'akhir' => 0, 'sikap' => 0, 'catatan' => 0, 'absensi' => 0];

        foreach ($santriList as $santri) {
            // ── Nilai per komponen ──
            foreach ($komponenAll as $k) {
                $nilai = rand(70, 95) + (rand(0, 99) / 100);
                Nilai::updateOrCreate(
                    [
                        'santri_id'         => $santri->id,
                        'mata_pelajaran_id' => $k->mata_pelajaran_id,
                        'komponen_nilai_id' => $k->id,
                        'tahun_ajaran_id'   => $taActive->id,
                    ],
                    ['nilai' => round($nilai, 2)]
                );
                $count['nilai']++;
            }

            // ── Hitung nilai akhir per mapel ──
            $mapelIds = $komponenAll->pluck('mata_pelajaran_id')->unique();
            foreach ($mapelIds as $mapelId) {
                $komponenMapel = $komponenAll->where('mata_pelajaran_id', $mapelId);
                $totalBobot = $komponenMapel->sum('bobot');
                if ($totalBobot == 0) continue;

                $totalNilai = 0;
                foreach ($komponenMapel as $k) {
                    $n = Nilai::where('santri_id', $santri->id)
                        ->where('komponen_nilai_id', $k->id)
                        ->where('tahun_ajaran_id', $taActive->id)
                        ->first();
                    if ($n) {
                        $totalNilai += ($n->nilai * $k->bobot) / 100;
                    }
                }

                $predikat = NilaiAkhir::predikatFor($totalNilai);

                NilaiAkhir::updateOrCreate(
                    [
                        'santri_id'         => $santri->id,
                        'mata_pelajaran_id' => $mapelId,
                        'tahun_ajaran_id'   => $taActive->id,
                    ],
                    [
                        'nilai_akhir' => round($totalNilai, 2),
                        'predikat'    => $predikat,
                    ]
                );
                $count['akhir']++;
            }

            // ── Nilai sikap (5 aspek default) ──
            foreach ($aspekSikap as $aspek) {
                $predikat = ['A', 'A', 'B', 'B', 'C'][rand(0, 4)];
                NilaiSikap::updateOrCreate(
                    [
                        'santri_id'       => $santri->id,
                        'tahun_ajaran_id' => $taActive->id,
                        'aspek'           => $aspek,
                    ],
                    [
                        'predikat'  => $predikat,
                        'deskripsi' => $deskripsiSikap[$predikat],
                    ]
                );
                $count['sikap']++;
            }

            // ── Catatan wali kelas (random per santri) ──
            $catatan = [
                'Pertahankan prestasi yang sudah baik. Tingkatkan terus semangat belajarnya.',
                'Tunjukkan peningkatan yang konsisten. Lebih aktif dalam diskusi kelas.',
                'Akhlak dan kedisiplinan sangat baik, perlu lebih giat dalam menghafal.',
                'Sudah cukup baik, namun perlu fokus lebih dalam pelajaran nahwu dan sharaf.',
                'Anak yang santun dan rajin. Tingkatkan kemampuan baca kitab.',
            ];
            $kelas = $santri->kelasSantri->firstWhere('tahun_ajaran_id', $taActive->id)?->kelas;
            $waliKelasId = $kelas?->wali_kelas_id;

            if ($waliKelasId) {
                CatatanWaliKelas::updateOrCreate(
                    [
                        'santri_id'       => $santri->id,
                        'tahun_ajaran_id' => $taActive->id,
                    ],
                    [
                        'ustadz_id' => $waliKelasId,
                        'catatan'   => $catatan[array_rand($catatan)],
                    ]
                );
                $count['catatan']++;
            }

            // ── Absensi pelajaran (sample 30 hari, mostly hadir) ──
            $jadwalIds = JadwalPelajaran::where('tahun_ajaran_id', $taActive->id)
                ->whereHas('kelas.santri', fn ($q) => $q->where('santri_id', $santri->id))
                ->pluck('id');

            foreach ($jadwalIds as $jadwalId) {
                for ($i = 0; $i < 20; $i++) {
                    $tanggal = now()->subDays(rand(1, 90))->format('Y-m-d');
                    $rand = rand(1, 100);
                    $status = match (true) {
                        $rand <= 85 => 'hadir',
                        $rand <= 92 => 'sakit',
                        $rand <= 97 => 'izin',
                        default     => 'alpa',
                    };

                    AbsensiPelajaran::updateOrCreate(
                        [
                            'jadwal_pelajaran_id' => $jadwalId,
                            'santri_id'           => $santri->id,
                            'tanggal'             => $tanggal,
                        ],
                        ['status' => $status]
                    );
                    $count['absensi']++;
                }
            }
        }

        $this->command->info('Nilai dummy berhasil di-seed:');
        $this->command->info("  - {$count['nilai']} nilai komponen");
        $this->command->info("  - {$count['akhir']} nilai akhir mapel");
        $this->command->info("  - {$count['sikap']} penilaian sikap");
        $this->command->info("  - {$count['catatan']} catatan wali kelas");
        $this->command->info("  - {$count['absensi']} record absensi");
    }
}
