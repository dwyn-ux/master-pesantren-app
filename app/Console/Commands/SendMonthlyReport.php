<?php

namespace App\Console\Commands;

use App\Models\ReportSetting;
use App\Models\Santri;
use App\Services\NotificationService;
use App\Services\ReportService;
use Illuminate\Console\Command;

class SendMonthlyReport extends Command
{
    protected $signature = 'reports:monthly {--force} {--month=} {--year=}';
    protected $description = 'Kirim laporan bulanan ke wali santri';

    public function handle(ReportService $reports, NotificationService $notifier): int
    {
        $setting = ReportSetting::get('monthly_report', [
            'enabled' => true,
            'day_of_month' => 1,
            'hour' => 8,
            'minute' => 0,
        ]);

        if (!($setting['enabled'] ?? true) && !$this->option('force')) {
            $this->info('Monthly report disabled.');
            return self::SUCCESS;
        }

        $targetDay = (int) ($setting['day_of_month'] ?? 1);
        if (!$this->option('force') && now()->day !== $targetDay) {
            $this->info("Hari ini bukan tanggal kirim laporan bulanan (setting: {$targetDay}).");
            return self::SUCCESS;
        }

        $month = (int) ($this->option('month') ?: now()->subMonth()->month);
        $year  = (int) ($this->option('year')  ?: now()->subMonth()->year);

        $santriList = Santri::where('is_aktif', true)
            ->with('wali.user')
            ->get();

        $sent = 0;

        foreach ($santriList as $santri) {
            if ($santri->wali->isEmpty()) continue;

            $report = $reports->generateMonthlyReport($santri, $month, $year);

            $title = "Laporan Bulanan {$report['periode']['bulan']}: {$santri->nama}";

            $posisi = $report['tahfidz']['posisi_terakhir'] ?? null;
            $posisiStr = $posisi
                ? "Posisi: {$posisi['surah']} ayat {$posisi['ayat']}"
                : '';

            $body = sprintf(
                "Hafalan: %s hal (%s juz %s hal) | %sSaldo: Rp %s | Top-up: Rp %s | Belanja: Rp %s",
                $report['tahfidz']['halaman_baru'],
                $report['tahfidz']['juz'],
                $report['tahfidz']['sisa_halaman'],
                $posisiStr ? "{$posisiStr} | " : '',
                number_format($report['saldo']),
                number_format($report['topup']),
                number_format($report['belanja']),
            );

            foreach ($santri->wali as $wali) {
                if (!$wali->user) continue;
                $notifier->send(
                    user: $wali->user,
                    type: 'monthly_report',
                    title: $title,
                    body: $body,
                    data: [
                        'santri_id' => (string) $santri->id,
                        'month' => (string) $month,
                        'year' => (string) $year,
                    ],
                    actionUrl: route('wali.laporan.bulanan', [
                        'santri' => $santri->id,
                        'month' => $month,
                        'year' => $year,
                    ]),
                );
                $sent++;
            }
        }

        $this->info("Laporan bulanan terkirim ke {$sent} wali untuk periode {$month}/{$year}.");
        return self::SUCCESS;
    }
}
