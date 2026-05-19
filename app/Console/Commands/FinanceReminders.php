<?php

namespace App\Console\Commands;

use App\Models\Finance\ExpenseRequest;
use App\Models\Finance\Payable;
use App\Models\Finance\Setting;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FinanceReminders extends Command
{
    protected $signature = 'finance:reminders';
    protected $description = 'Kirim notifikasi reminder untuk hutang jatuh tempo & approval pending';

    public function __construct(protected NotificationService $notifier)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $hMinus = (int) Setting::get('reminder.hutang_h_minus', 7);
        $sentPayable  = $this->remindPayables($hMinus);
        $sentApproval = $this->remindApprovals();
        $sentBudget   = $this->checkBudgetAlerts();

        $this->info("Reminder hutang     : {$sentPayable} notifikasi");
        $this->info("Reminder approval   : {$sentApproval} notifikasi");
        $this->info("Alert budget overrun: {$sentBudget} notifikasi");

        return self::SUCCESS;
    }

    protected function remindPayables(int $hMinus): int
    {
        $bendaharaUsers = User::role('bendahara')->get();
        if ($bendaharaUsers->isEmpty()) return 0;

        $deadline = today()->addDays($hMinus);
        $payables = Payable::with('vendor')
            ->whereIn('status', ['belum_bayar', 'sebagian'])
            ->whereDate('jatuh_tempo', '<=', $deadline)
            ->get();

        $sent = 0;
        foreach ($payables as $p) {
            $hari  = today()->diffInDays($p->jatuh_tempo, false);
            $label = $hari < 0
                ? 'TERLAMBAT ' . abs($hari) . ' hari'
                : ($hari == 0 ? 'JATUH TEMPO HARI INI' : "H-{$hari}");

            foreach ($bendaharaUsers as $user) {
                $this->notifier->send(
                    user: $user,
                    type: 'finance_reminder',
                    title: "Hutang {$label}: {$p->vendor->nama}",
                    body: "{$p->nomor} - Sisa Rp " . number_format($p->sisa) . " jatuh tempo " . $p->jatuh_tempo->format('d/m/Y'),
                    data: ['type' => 'payable_reminder', 'payable_id' => (string) $p->id],
                );
                $sent++;
            }
        }
        return $sent;
    }

    protected function remindApprovals(): int
    {
        if (!Setting::isEnabled('approval')) return 0;

        $kepalaUsers  = User::role('kepala_pondok')->get();
        $yayasanUsers = User::role('admin')->get();
        $sent = 0;

        $pendingKepala = ExpenseRequest::where('status', 'pending_kepala')->count();
        if ($pendingKepala > 0) {
            foreach ($kepalaUsers as $user) {
                $this->notifier->send(
                    user: $user,
                    type: 'finance_approval',
                    title: "Ada {$pendingKepala} permintaan menunggu approval",
                    body: "Buka menu Permintaan Pengeluaran untuk meninjau.",
                    data: ['type' => 'expense_pending', 'level' => 'kepala'],
                );
                $sent++;
            }
        }

        $pendingYayasan = ExpenseRequest::where('status', 'pending_yayasan')->count();
        if ($pendingYayasan > 0) {
            foreach ($yayasanUsers as $user) {
                $this->notifier->send(
                    user: $user,
                    type: 'finance_approval',
                    title: "Ada {$pendingYayasan} permintaan menunggu yayasan",
                    body: "Buka menu Permintaan Pengeluaran untuk meninjau.",
                    data: ['type' => 'expense_pending', 'level' => 'yayasan'],
                );
                $sent++;
            }
        }

        return $sent;
    }

    protected function checkBudgetAlerts(): int
    {
        if (!Setting::isEnabled('budgeting')) return 0;

        $threshold      = (int) Setting::get('reminder.budget_warning', 80);
        $bendaharaUsers = User::role('bendahara')->get();
        if ($bendaharaUsers->isEmpty()) return 0;

        $budgets = \App\Models\Finance\Budget::with('kategori')
            ->where('tahun', now()->year)
            ->where(function ($q) {
                $q->whereNull('bulan')->orWhere('bulan', now()->month);
            })
            ->get();

        $sent = 0;
        foreach ($budgets as $b) {
            if ((float) $b->nominal_anggaran <= 0) continue;
            $persen = ((float) $b->nominal_realisasi / (float) $b->nominal_anggaran) * 100;
            if ($persen < $threshold) continue;

            foreach ($bendaharaUsers as $user) {
                $this->notifier->send(
                    user: $user,
                    type: 'budget_alert',
                    title: "Budget Alert: {$b->kategori->nama}",
                    body: "Realisasi sudah " . number_format($persen, 1) . "% dari anggaran "
                        . ($b->bulan ? Carbon::create()->month($b->bulan)->translatedFormat('F') : 'tahun')
                        . " {$b->tahun}",
                    data: ['type' => 'budget_alert', 'budget_id' => (string) $b->id],
                );
                $sent++;
            }
        }
        return $sent;
    }
}
