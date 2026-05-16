<?php

namespace App\Console\Commands;

use App\Models\JenisTagihan;
use App\Models\Santri;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BuatTagihanBulanan extends Command
{
    protected $signature = 'tagihan:buat-bulanan
                            {--periode= : Periode dalam format YYYY-MM (default: bulan ini)}
                            {--dry-run  : Simulasikan tanpa menyimpan ke database}';

    protected $description = 'Buat tagihan bulanan otomatis untuk semua santri aktif';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $periodeStr = $this->option('periode');

        $periode  = $periodeStr
            ? Carbon::createFromFormat('Y-m', $periodeStr)->startOfMonth()
            : now()->startOfMonth();

        $periodeKey = $periode->format('Y-m');
        $dueDate    = $periode->copy()->endOfMonth()->format('Y-m-d');

        $this->info("Periode  : {$periodeKey}");
        $this->info("Due date : {$dueDate}");
        if ($isDryRun) {
            $this->warn('DRY RUN — tidak ada data yang disimpan.');
        }

        $jenisList = JenisTagihan::where('kelompok', 'bulanan')
            ->where('is_aktif', true)
            ->get();

        if ($jenisList->isEmpty()) {
            $this->warn('Tidak ada jenis tagihan bulanan aktif.');
            return self::SUCCESS;
        }

        $santriList = Santri::where('is_aktif', true)->get();

        if ($santriList->isEmpty()) {
            $this->warn('Tidak ada santri aktif.');
            return self::SUCCESS;
        }

        $this->info("Jenis tagihan bulanan : {$jenisList->count()}");
        $this->info("Santri aktif          : {$santriList->count()}");
        $this->line('');

        $created = 0;
        $skipped = 0;

        foreach ($jenisList as $jenis) {
            foreach ($santriList as $santri) {
                $exists = Tagihan::where('santri_id', $santri->id)
                    ->where('jenis_tagihan_id', $jenis->id)
                    ->where('periode', $periodeKey)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                if (!$isDryRun) {
                    Tagihan::create([
                        'santri_id'        => $santri->id,
                        'jenis_tagihan_id' => $jenis->id,
                        'nominal'          => $jenis->nominal,
                        'periode'          => $periodeKey,
                        'status'           => 'belum_bayar',
                        'due_date'         => $dueDate,
                    ]);
                }

                $created++;
                $this->line("  + {$santri->nama} — {$jenis->nama}");
            }
        }

        $this->line('');
        $this->info("Selesai: {$created} tagihan " . ($isDryRun ? 'akan dibuat' : 'dibuat') . ", {$skipped} dilewati (sudah ada).");

        return self::SUCCESS;
    }
}
