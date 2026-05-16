<?php

namespace App\Services;

class LaporanAnalysisService
{
    /**
     * Analisis tren dari array harian (angka).
     */
    public function analyzeTrend(array $values): array
    {
        $n = count($values);
        if ($n === 0) {
            return ['trend' => 'kosong', 'slope' => 0, 'avg' => 0, 'peak' => 0, 'peak_idx' => null];
        }

        $avg = array_sum($values) / $n;
        $peak = max($values);
        $peakIdx = array_search($peak, $values);

        // Linear regression slope (tren naik/turun)
        $x = range(0, $n - 1);
        $xMean = array_sum($x) / $n;
        $yMean = $avg;

        $num = 0;
        $den = 0;
        foreach ($x as $i => $xi) {
            $num += ($xi - $xMean) * ($values[$i] - $yMean);
            $den += ($xi - $xMean) ** 2;
        }
        $slope = $den == 0 ? 0 : $num / $den;

        $trend = match (true) {
            $slope > ($avg * 0.05) => 'naik',
            $slope < -($avg * 0.05) => 'turun',
            default => 'stabil',
        };

        return [
            'trend' => $trend,
            'slope' => $slope,
            'avg'   => $avg,
            'peak'  => $peak,
            'peak_idx' => $peakIdx,
        ];
    }

    /**
     * Analisis keuangan: rasio tagihan lunas vs belum bayar.
     */
    public function analyzeKeuangan(int $totalTagihan, int $belumBayar, int $totalBayar): array
    {
        $terbayar = $totalTagihan - $belumBayar;
        $persenTerbayar = $totalTagihan > 0 ? round(($terbayar / $totalTagihan) * 100, 1) : 0;
        $persenBelum = $totalTagihan > 0 ? round(($belumBayar / $totalTagihan) * 100, 1) : 0;

        $insights = [];

        if ($persenTerbayar >= 80) {
            $insights[] = "Tingkat pelunasan sangat baik ({$persenTerbayar}%). Pola penagihan berjalan efektif.";
        } elseif ($persenTerbayar >= 50) {
            $insights[] = "Tingkat pelunasan cukup ({$persenTerbayar}%). Perlu reminder lanjutan untuk wali yang menunggak.";
        } else {
            $insights[] = "Tingkat pelunasan rendah ({$persenTerbayar}%). Disarankan percepat follow-up tagihan yang belum dibayar (Rp " . number_format($belumBayar) . ").";
        }

        if ($totalBayar > $terbayar) {
            $insights[] = "Terdapat pembayaran di luar tagihan tercatat (top-up/lainnya).";
        }

        return [
            'persen_terbayar' => $persenTerbayar,
            'persen_belum' => $persenBelum,
            'nominal_terbayar' => $terbayar,
            'insights' => $insights,
        ];
    }

    /**
     * Analisis tahfidz: total halaman, konversi juz, rata-rata per santri.
     */
    public function analyzeTahfidz(int $totalSetoran, float $totalHalaman, int $santriUnik): array
    {
        $rataPerSantri = $santriUnik > 0 ? round($totalHalaman / $santriUnik, 1) : 0;
        $juz = floor($totalHalaman / 20);

        $insights = [];

        if ($rataPerSantri >= 10) {
            $insights[] = "Rata-rata capaian santri sangat baik ({$rataPerSantri} halaman/santri).";
        } elseif ($rataPerSantri >= 5) {
            $insights[] = "Rata-rata capaian santri cukup ({$rataPerSantri} halaman/santri).";
        } else {
            $insights[] = "Rata-rata capaian santri masih rendah ({$rataPerSantri} halaman/santri). Pertimbangkan review metode atau intensitas halaqah.";
        }

        if ($juz >= 1) {
            $insights[] = "Total halaman yang disetor setara ±{$juz} juz (20 halaman/juz).";
        }

        if ($totalSetoran > 0 && $totalHalaman / $totalSetoran < 0.5) {
            $insights[] = "Rata-rata halaman per setoran kecil. Banyak setoran kemungkinan murojaah atau potongan pendek.";
        }

        return [
            'rata_per_santri' => $rataPerSantri,
            'juz' => $juz,
            'insights' => $insights,
        ];
    }

    /**
     * Analisis kantin/laundry: tren, avg transaksi, rekomendasi.
     */
    public function analyzeRevenue(array $revenueChart, int $totalTransaksi, int $totalRevenue, int $santriUnik, string $kategori = 'kantin'): array
    {
        $trend = $this->analyzeTrend($revenueChart);
        $avgPerTransaksi = $totalTransaksi > 0 ? (int) ($totalRevenue / $totalTransaksi) : 0;
        $frekuensiPerSantri = $santriUnik > 0 ? round($totalTransaksi / $santriUnik, 1) : 0;

        $insights = [];

        $insights[] = match ($trend['trend']) {
            'naik' => "Tren pendapatan {$kategori} NAIK selama periode ini. Pertimbangkan tambah stok/kapasitas.",
            'turun' => "Tren pendapatan {$kategori} TURUN. Cek ketersediaan produk atau promosi.",
            'stabil' => "Pendapatan {$kategori} stabil.",
            default => "Belum ada data {$kategori}.",
        };

        if ($avgPerTransaksi > 0) {
            $insights[] = "Rata-rata transaksi: Rp " . number_format($avgPerTransaksi) . ".";
        }

        if ($frekuensiPerSantri > 0) {
            $insights[] = "Rata-rata {$frekuensiPerSantri} transaksi/santri pada periode ini.";
        }

        if ($trend['peak'] > 0 && $trend['peak_idx'] !== null) {
            $insights[] = "Puncak pendapatan harian Rp " . number_format($trend['peak']) . ".";
        }

        return [
            'trend' => $trend['trend'],
            'avg_per_transaksi' => $avgPerTransaksi,
            'frekuensi_per_santri' => $frekuensiPerSantri,
            'insights' => $insights,
        ];
    }
}
