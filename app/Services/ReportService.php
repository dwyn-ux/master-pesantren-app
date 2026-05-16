<?php

namespace App\Services;

use App\Models\Santri;
use App\Models\Setoran;
use App\Models\TransaksiKasir;
use App\Models\WalletTransaction;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate data laporan mingguan untuk 1 santri.
     */
    public function generateWeeklyReport(Santri $santri, ?Carbon $endDate = null): array
    {
        $end = $endDate ?? now();
        $start = $end->copy()->subDays(6)->startOfDay();
        $end = $end->copy()->endOfDay();

        $saldo = $santri->saldo;

        $topUp = WalletTransaction::where('santri_id', $santri->id)
            ->where('jenis', 'kredit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('nominal');

        $belanja = WalletTransaction::where('santri_id', $santri->id)
            ->where('jenis', 'debit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('nominal');

        $belanjaDetail = TransaksiKasir::with('outlet')
            ->where('santri_id', $santri->id)
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $setoranZiyadah = Setoran::where('santri_id', $santri->id)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->get();

        $halamanBaru = $setoranZiyadah->sum('jumlah_halaman');

        $totalHalaman = Setoran::where('santri_id', $santri->id)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->sum('jumlah_halaman');

        $juz = floor($totalHalaman / 20);
        $sisaHalaman = $totalHalaman - ($juz * 20);

        $posisiTerakhir = Setoran::where('santri_id', $santri->id)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->latest('tanggal')
            ->with(['surahAkhir'])
            ->first();

        return [
            'periode' => [
                'start' => $start->translatedFormat('d M Y'),
                'end'   => $end->translatedFormat('d M Y'),
            ],
            'saldo' => $saldo,
            'topup' => $topUp,
            'belanja' => $belanja,
            'belanja_detail' => $belanjaDetail,
            'tahfidz' => [
                'halaman_baru' => $halamanBaru,
                'total_halaman' => $totalHalaman,
                'juz' => $juz,
                'sisa_halaman' => $sisaHalaman,
                'posisi_terakhir' => $posisiTerakhir ? [
                    'surah' => $posisiTerakhir->surahAkhir?->nama_latin ?? '-',
                    'ayat' => $posisiTerakhir->ayat_akhir,
                ] : null,
            ],
        ];
    }

    /**
     * Generate laporan bulanan (4-5 minggu).
     */
    public function generateMonthlyReport(Santri $santri, int $month, int $year): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $saldo = $santri->saldo;

        $topUp = WalletTransaction::where('santri_id', $santri->id)
            ->where('jenis', 'kredit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('nominal');

        $belanja = WalletTransaction::where('santri_id', $santri->id)
            ->where('jenis', 'debit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('nominal');

        $belanjaDetail = TransaksiKasir::with('outlet')
            ->where('santri_id', $santri->id)
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $setoranZiyadah = Setoran::where('santri_id', $santri->id)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->get();

        $halamanBaru = $setoranZiyadah->sum('jumlah_halaman');

        $totalHalaman = Setoran::where('santri_id', $santri->id)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->sum('jumlah_halaman');

        $juz = floor($totalHalaman / 20);
        $sisaHalaman = $totalHalaman - ($juz * 20);

        $posisiTerakhir = Setoran::where('santri_id', $santri->id)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->latest('tanggal')
            ->with(['surahAkhir'])
            ->first();

        return [
            'periode' => [
                'bulan' => $start->translatedFormat('F Y'),
                'start' => $start->translatedFormat('d M Y'),
                'end'   => $end->translatedFormat('d M Y'),
            ],
            'saldo' => $saldo,
            'topup' => $topUp,
            'belanja' => $belanja,
            'belanja_detail' => $belanjaDetail,
            'tahfidz' => [
                'halaman_baru' => $halamanBaru,
                'total_halaman' => $totalHalaman,
                'juz' => $juz,
                'sisa_halaman' => $sisaHalaman,
                'posisi_terakhir' => $posisiTerakhir ? [
                    'surah' => $posisiTerakhir->surahAkhir?->nama_latin ?? '-',
                    'ayat' => $posisiTerakhir->ayat_akhir,
                ] : null,
            ],
        ];
    }

    /**
     * Generate laporan semester (6 bulan).
     */
    public function generateSemesterReport(Santri $santri, int $semester, int $year): array
    {
        $startMonth = $semester === 1 ? 1 : 7;
        $start = Carbon::create($year, $startMonth, 1)->startOfMonth();
        $end = $start->copy()->addMonths(5)->endOfMonth();

        $saldo = $santri->saldo;

        $topUp = WalletTransaction::where('santri_id', $santri->id)
            ->where('jenis', 'kredit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('nominal');

        $belanja = WalletTransaction::where('santri_id', $santri->id)
            ->where('jenis', 'debit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('nominal');

        $halamanBaru = Setoran::where('santri_id', $santri->id)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('jumlah_halaman');

        $totalHalaman = Setoran::where('santri_id', $santri->id)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->sum('jumlah_halaman');

        $juz = floor($totalHalaman / 20);
        $sisaHalaman = $totalHalaman - ($juz * 20);

        return [
            'periode' => [
                'semester' => $semester,
                'tahun' => $year,
                'start' => $start->translatedFormat('d M Y'),
                'end'   => $end->translatedFormat('d M Y'),
            ],
            'saldo' => $saldo,
            'topup' => $topUp,
            'belanja' => $belanja,
            'tahfidz' => [
                'halaman_baru' => $halamanBaru,
                'total_halaman' => $totalHalaman,
                'juz' => $juz,
                'sisa_halaman' => $sisaHalaman,
            ],
        ];
    }

    /**
     * Generate laporan tahunan.
     */
    public function generateYearlyReport(Santri $santri, int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end = $start->copy()->endOfYear();

        $saldo = $santri->saldo;

        $topUp = WalletTransaction::where('santri_id', $santri->id)
            ->where('jenis', 'kredit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('nominal');

        $belanja = WalletTransaction::where('santri_id', $santri->id)
            ->where('jenis', 'debit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('nominal');

        $halamanBaru = Setoran::where('santri_id', $santri->id)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('jumlah_halaman');

        $totalHalaman = Setoran::where('santri_id', $santri->id)
            ->where('jenis', 'ziyadah')
            ->where('status', 'maqbul')
            ->sum('jumlah_halaman');

        $juz = floor($totalHalaman / 20);
        $sisaHalaman = $totalHalaman - ($juz * 20);

        return [
            'periode' => [
                'tahun' => $year,
                'start' => $start->translatedFormat('d M Y'),
                'end'   => $end->translatedFormat('d M Y'),
            ],
            'saldo' => $saldo,
            'topup' => $topUp,
            'belanja' => $belanja,
            'tahfidz' => [
                'halaman_baru' => $halamanBaru,
                'total_halaman' => $totalHalaman,
                'juz' => $juz,
                'sisa_halaman' => $sisaHalaman,
            ],
        ];
    }
}
