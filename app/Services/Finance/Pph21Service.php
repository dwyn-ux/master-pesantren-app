<?php

namespace App\Services\Finance;

use App\Models\Finance\Setting;

/**
 * Kalkulator PPh 21 sederhana berdasarkan UU HPP / lapis tarif progresif.
 * Disederhanakan dengan asumsi pegawai tetap, status TK/0, tanpa biaya jabatan
 * yang spesifik. Untuk akurasi penuh, perlu disesuaikan dengan PTKP per status
 * dan komponen biaya jabatan/iuran pensiun.
 */
class Pph21Service
{
    /**
     * Hitung PPh 21 untuk satu slip gaji bulanan.
     *
     * @param  float  $gajiBruto  Gaji bruto sebulan (gaji pokok + tunjangan, sebelum potongan)
     * @return array{pkp_setahun: float, pph_setahun: float, pph_sebulan: float}
     */
    public function hitungBulanan(float $gajiBruto): array
    {
        if (!Setting::isEnabled('pajak')) {
            return ['pkp_setahun' => 0, 'pph_setahun' => 0, 'pph_sebulan' => 0];
        }

        $brutoSetahun = $gajiBruto * 12;

        // Biaya jabatan: 5% dari bruto, maks Rp 6.000.000/tahun
        $biayaJabatan = min($brutoSetahun * 0.05, 6_000_000);
        $netoSetahun  = $brutoSetahun - $biayaJabatan;

        $ptkp = (float) Setting::get('pajak.ptkp_setahun', 54_000_000);
        $pkp  = max(0, $netoSetahun - $ptkp);
        // Pembulatan ke ribuan ke bawah
        $pkp  = floor($pkp / 1000) * 1000;

        $pph = $this->hitungPphProgresif($pkp);

        return [
            'pkp_setahun' => $pkp,
            'pph_setahun' => $pph,
            'pph_sebulan' => $pph / 12,
        ];
    }

    protected function hitungPphProgresif(float $pkp): float
    {
        if ($pkp <= 0) return 0;

        $lapis1Max     = (float) Setting::get('pajak.lapis1_max', 60_000_000);
        $lapis1Persen  = (float) Setting::get('pajak.lapis1_persen', 5) / 100;
        $lapis2Max     = (float) Setting::get('pajak.lapis2_max', 250_000_000);
        $lapis2Persen  = (float) Setting::get('pajak.lapis2_persen', 15) / 100;
        // Lapis 3 default 25%, lapis 4 default 30%, lapis 5 default 35%
        $lapis3Persen  = 0.25;
        $lapis3Max     = 500_000_000;
        $lapis4Persen  = 0.30;
        $lapis4Max     = 5_000_000_000;
        $lapis5Persen  = 0.35;

        $pph = 0;

        if ($pkp <= $lapis1Max) {
            $pph = $pkp * $lapis1Persen;
        } elseif ($pkp <= $lapis2Max) {
            $pph = $lapis1Max * $lapis1Persen
                 + ($pkp - $lapis1Max) * $lapis2Persen;
        } elseif ($pkp <= $lapis3Max) {
            $pph = $lapis1Max * $lapis1Persen
                 + ($lapis2Max - $lapis1Max) * $lapis2Persen
                 + ($pkp - $lapis2Max) * $lapis3Persen;
        } elseif ($pkp <= $lapis4Max) {
            $pph = $lapis1Max * $lapis1Persen
                 + ($lapis2Max - $lapis1Max) * $lapis2Persen
                 + ($lapis3Max - $lapis2Max) * $lapis3Persen
                 + ($pkp - $lapis3Max) * $lapis4Persen;
        } else {
            $pph = $lapis1Max * $lapis1Persen
                 + ($lapis2Max - $lapis1Max) * $lapis2Persen
                 + ($lapis3Max - $lapis2Max) * $lapis3Persen
                 + ($lapis4Max - $lapis3Max) * $lapis4Persen
                 + ($pkp - $lapis4Max) * $lapis5Persen;
        }

        return round($pph);
    }
}
