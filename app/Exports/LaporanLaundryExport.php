<?php

namespace App\Exports;

use App\Models\LaundryOrder;
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

class LaporanLaundryExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithMapping
{
    public function __construct(
        protected Carbon  $dari,
        protected Carbon  $sampai,
        protected ?string $status = null,
    ) {}

    public function collection()
    {
        $query = LaundryOrder::with(['santri', 'outlet'])
            ->whereBetween('created_at', [$this->dari, $this->sampai])
            ->latest();

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function map($row): array
    {
        $statusLabel = match ($row->status) {
            'diterima' => 'Diterima',
            'dicuci'   => 'Dicuci',
            'selesai'  => 'Selesai',
            'diambil'  => 'Diambil',
            default    => ucfirst($row->status),
        };

        return [
            $row->created_at->format('d/m/Y H:i'),
            $row->nomor_tiket,
            $row->santri?->nama ?? '-',
            $row->santri?->nis  ?? '-',
            $row->outlet?->nama ?? '-',
            $row->berat_kg,
            $row->harga_per_kg,
            $row->total,
            $statusLabel,
            $row->tanggal_antar?->format('d/m/Y') ?? '-',
            $row->tanggal_selesai?->format('d/m/Y') ?? '-',
            $row->tanggal_diambil?->format('d/m/Y') ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal Masuk',
            'No. Tiket',
            'Nama Santri',
            'NIS',
            'Outlet',
            'Berat (kg)',
            'Harga/kg (Rp)',
            'Total (Rp)',
            'Status',
            'Tgl Antar',
            'Tgl Selesai',
            'Tgl Diambil',
        ];
    }

    public function title(): string
    {
        return 'Laporan Laundry';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 16,
            'C' => 28,
            'D' => 16,
            'E' => 20,
            'F' => 12,
            'G' => 14,
            'H' => 14,
            'I' => 12,
            'J' => 14,
            'K' => 14,
            'L' => 14,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'L';

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->setAutoFilter("A1:{$lastCol}1");

        return [];
    }
}
