<?php

namespace App\Exports;

use App\Models\Setoran;
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

class LaporanHalaqahExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithMapping
{
    public function __construct(
        protected Carbon $dari,
        protected Carbon $sampai,
        protected ?int   $halaqahId = null,
    ) {}

    public function collection()
    {
        $query = Setoran::with(['santri', 'penerima'])
            ->whereBetween('tanggal', [$this->dari->toDateString(), $this->sampai->toDateString()])
            ->latest('tanggal');

        if ($this->halaqahId) {
            $santriIds = \App\Models\Halaqah::find($this->halaqahId)
                ?->santri()->pluck('santri.id') ?? collect();
            $query->whereIn('santri_id', $santriIds);
        }

        return $query->get();
    }

    public function map($row): array
    {
        return [
            $row->tanggal instanceof \Carbon\Carbon
                ? $row->tanggal->format('d/m/Y')
                : Carbon::parse($row->tanggal)->format('d/m/Y'),
            $row->santri?->nama ?? '-',
            $row->santri?->nis  ?? '-',
            $row->penerima?->nama ?? '-',
            ucfirst($row->jenis),
            $row->jumlah_halaman,
            ucfirst($row->status),
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Santri',
            'NIS',
            'Penerima / Ustadz',
            'Jenis',
            'Jumlah Halaman',
            'Status',
        ];
    }

    public function title(): string
    {
        return 'Laporan Halaqah';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 28,
            'C' => 16,
            'D' => 28,
            'E' => 12,
            'F' => 16,
            'G' => 12,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'G';

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->setAutoFilter("A1:{$lastCol}1");

        return [];
    }
}
