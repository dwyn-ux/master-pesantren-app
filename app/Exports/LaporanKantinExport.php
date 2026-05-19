<?php

namespace App\Exports;

use App\Models\TransaksiKasir;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanKantinExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithMapping
{
    public function __construct(
        protected Carbon $dari,
        protected Carbon $sampai,
        protected ?int   $outletId = null,
    ) {}

    public function collection()
    {
        $query = TransaksiKasir::with(['santri', 'outlet'])
            ->whereBetween('created_at', [$this->dari, $this->sampai])
            ->latest();

        if ($this->outletId) {
            $query->where('outlet_id', $this->outletId);
        }

        return $query->get();
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d/m/Y H:i'),
            $row->santri?->nama ?? '-',
            $row->santri?->nis  ?? '-',
            $row->santri?->kelas ?? '-',
            $row->outlet?->nama ?? '-',
            $row->total,
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal & Waktu',
            'Nama Santri',
            'NIS',
            'Kelas',
            'Outlet / Kantin',
            'Total (Rp)',
        ];
    }

    public function title(): string
    {
        return 'Laporan Kantin';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 28,
            'C' => 16,
            'D' => 10,
            'E' => 22,
            'F' => 14,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'F';

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F59E0B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->setAutoFilter("A1:{$lastCol}1");

        return [];
    }
}
