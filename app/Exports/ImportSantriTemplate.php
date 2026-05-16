<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ImportSantriTemplate implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            ['PST.2026.001', '3201010101010001', 'Ahmad Wulansari',  'L', '2010-05-15', '7A', 'Jl. Merdeka No. 1, Jakarta', '081234567890'],
            ['PST.2026.002', '3201010101010002', 'Rina Wulansari',   'P', '2011-03-22', '8B', 'Jl. Sudirman No. 5, Bandung', '082345678901'],
            ['PST.2026.003', '3201010101010003', 'Muhammad Rizki',   'L', '2009-12-10', '9C', 'Jl. Pahlawan No. 3, Surabaya', '083456789012'],
        ];
    }

    public function headings(): array
    {
        return [
            'NIS',
            'NIK',
            'Nama',
            'Jenis Kelamin (L/P)',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Kelas',
            'Alamat',
            'No HP Orang Tua',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'H';

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        $sheet->setAutoFilter("A1:{$lastCol}1");

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
