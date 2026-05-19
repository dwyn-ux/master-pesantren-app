<?php

namespace App\Exports;

use App\Models\Pembayaran;
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

class LaporanKeuanganExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithMapping
{
    public function __construct(
        protected Carbon $dari,
        protected Carbon $sampai,
    ) {}

    public function collection()
    {
        return Pembayaran::with(['wali', 'tagihan.santri', 'tagihan.jenisTagihan'])
            ->whereBetween('created_at', [$this->dari, $this->sampai])
            ->latest('created_at')
            ->get();
    }

    public function map($row): array
    {
        $tglBayar = $row->paid_at ?? $row->created_at;

        return [
            $tglBayar->format('d/m/Y H:i'),
            $row->wali?->nama ?? '-',
            $row->wali?->no_hp ?? '-',
            $row->tagihan?->santri?->nama ?? '-',
            $row->tagihan?->santri?->nis  ?? '-',
            $row->tagihan?->jenisTagihan?->nama ?? '-',
            $row->tagihan?->periode ?? '-',
            $row->nominal,
            $row->metode ?? '-',
            strtoupper($row->status),
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal Bayar',
            'Nama Wali',
            'No HP Wali',
            'Nama Santri',
            'NIS',
            'Jenis Tagihan',
            'Periode',
            'Nominal (Rp)',
            'Metode',
            'Status',
        ];
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 28,
            'C' => 16,
            'D' => 28,
            'E' => 16,
            'F' => 24,
            'G' => 14,
            'H' => 16,
            'I' => 14,
            'J' => 12,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'J';

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->setAutoFilter("A1:{$lastCol}1");

        return [];
    }
}
