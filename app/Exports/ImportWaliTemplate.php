<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ImportWaliTemplate implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                'Bapak Ahmad Wijaya',
                '085712345678',
                'ayah',
                'Ahmad Wulansari',
                'PST.2026.001',
            ],
            [
                'Ibu Siti Nurhaliza',
                '081987654321',
                'ibu',
                'Ahmad Wulansari',
                'PST.2026.001',
            ],
            [
                'Paman Bambang Sutrisno',
                '082345678901',
                'wali',
                'Rina Wulansari',
                'PST.2026.002',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Orang Tua',
            'No HP',
            'Hubungan',
            'Nama Santri',
            'NIS',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        $sheet->setAutoFilter('A1:E1');

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
