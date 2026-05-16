<?php

namespace App\Console\Commands;

use App\Models\ReportSetting;
use App\Models\Santri;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\ReportService;
use Illuminate\Console\Command;

class SendWeeklyReport extends Command
{
    protected $signature = 'reports:weekly {--force : Force run meski bukan hari yang disetting}';
    protected $description = 'Kirim laporan mingguan ke wali santri sesuai setting hari';

    public function handle(ReportService $reports, NotificationService $notifier): int
    {
        $setting = ReportSetting::get('weekly_report', [
            'enabled' => true,
            'day_of_week' => 0,
            'hour' => 8,
            'minute' => 0,
        ]);

        if (!($setting['enabled'] ?? true) && !$this->option('force')) {
            $this->info('Weekly report disabled via setting.');
            return self::SUCCESS;
        }

        $targetDay = (int) ($setting['day_of_week'] ?? 0);
        if (!$this->option('force') && now()->dayOfWeek !== $targetDay) {
            $this->info("Hari ini bukan hari kirim laporan (setting: {$targetDay}, now: " . now()->dayOfWeek . ").");
            return self::SUCCESS;
        }

        $santriList = Santri::where('is_aktif', true)
            ->with('wali.user')
            ->get();

        $total = 0;
        $sent = 0;

        foreach ($santriList as $santri) {
            $total++;

            if ($santri->wali->isEmpty()) continue;

            $report = $reports->generateWeeklyReport($santri);

            $title = "Laporan Mingguan: {$santri->nama}";
            $body = sprintf(
                "Saldo: Rp %s | Hafalan: %s hal baru (total %s juz %s hal) | Belanja: Rp %s",
                number_format($report['saldo']),
                $report['tahfidz']['halaman_baru'],
                $report['tahfidz']['juz'],
                $report['tahfidz']['sisa_halaman'],
                number_format($report['belanja']),
            );

            foreach ($santri->wali as $wali) {
                if (!$wali->user) continue;
                $notifier->send(
                    user: $wali->user,
                    type: 'weekly_report',
                    title: $title,
                    body: $body,
                    data: [
                        'santri_id' => (string) $santri->id,
                        'saldo' => (string) $report['saldo'],
                        'halaman_baru' => (string) $report['tahfidz']['halaman_baru'],
                    ],
                    actionUrl: route('wali.laporan.show', $santri->id),
                );
                $sent++;
            }
        }

        $this->info("Selesai. Santri diproses: {$total}, notifikasi terkirim: {$sent}.");
        return self::SUCCESS;
    }
}
