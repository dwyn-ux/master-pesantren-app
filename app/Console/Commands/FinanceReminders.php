<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Finance\ExpenseRequest;
use App\Models\Finance\Payable;
use App\Models\Finance\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FinanceReminders extends Command
{
    protected $signature = 'finance:reminders';
    protected $description = 'Kirim notifikasi reminder untuk hutang jatuh tempo & approval pending';

    public function handle(): int
    {
        $hMinus = (int) Setting::get('reminder.hutang_h_minus', 7);
        $sentPayable = $this->remindPayables($hMinus);
        $sentApproval = $this->remindApprovals();
        $sentBudget = $this->checkBudgetAlerts();

        $this->info("Reminder hutang     : {$sentPayable} notifikasi");
        $this->info("Reminder approval   : {$sentApproval} notifikasi");
        $this->info("Alert budget overrun: {$sentBudget} notifikasi");

        return self::SUCCESS;
    }

    protected function remindPayables(int $hMinus): int
    {
        $bendaharaIds = User::role('bendahara')->pluck('id')->all();
        if (empty($bendaharaIds)) return 0;

        $deadline = today()->addDays($hMinus);
        $payables = Payable::with('vendor')
            ->whereIn('status', ['belum_bayar', 'sebagian'])
            ->whereDate('jatuh_tempo', '<=', $deadline)
            ->get();

        $sent = 0;
        foreach ($payables as $p) {
            $hari = today()->diffInDays($p->jatuh_tempo, false);
            $label = $hari < 0 ? 'TERLAMBAT ' . abs($hari) . ' hari' : ($hari == 0 ? 'JATUH TEMPO HARI INI' : "H-{$hari}");

            foreach ($bendaharaIds as $uid) {
                AppNotification::create([
                    'user_id' => $uid,
                    'title'   => "Hutang {$label}: {$p->vendor->nama}",
                    'message' => "{$p->nomor} - Sisa Rp " . number_format($p->sisa) . " jatuh tempo " . $p->jatuh_tempo->format('d/m/Y'),
                    'data'    => json_encode(['type' => 'payable_reminder', 'payable_id' => $p->id]),
                ]);
                $sent++;
            }
        }
        return $sent;
    }

    protected function remindApprovals(): int
    {
        if (!Setting::isEnabled('approval')) return 0;

        $kepalaIds  = User::role('kepala_pondok')->pluck('id')->all();
        $yayasanIds = User::role('admin')->pluck('id')->all();
        $sent = 0;

        $pendingKepala = ExpenseRequest::where('status', 'pending_kepala')->count();
        if ($pendingKepala > 0) {
            foreach ($kepalaIds as $uid) {
                AppNotification::create([
                    'user_id' => $uid,
                    'title'   => "Ada {$pendingKepala} permintaan menunggu approval",
                    'message' => "Buka menu Permintaan Pengeluaran untuk meninjau.",
                    'data'    => json_encode(['type' => 'expense_pending', 'level' => 'kepala']),
                ]);
                $sent++;
            }
        }

        $pendingYayasan = ExpenseRequest::where('status', 'pending_yayasan')->count();
        if ($pendingYayasan > 0) {
            foreach ($yayasanIds as $uid) {
                AppNotification::create([
                    'user_id' => $uid,
                    'title'   => "Ada {$pendingYayasan} permintaan menunggu yayasan",
                    'message' => "Buka menu Permintaan Pengeluaran untuk meninjau.",
                    'data'    => json_encode(['type' => 'expense_pending', 'level' => 'yayasan']),
                ]);
                $sent++;
            }
        }

        return $sent;
    }

    protected function checkBudgetAlerts(): int
    {
        if (!Setting::isEnabled('budgeting')) return 0;

        $threshold = (int) Setting::get('reminder.budget_warning', 80);
        $bendaharaIds = User::role('bendahara')->pluck('id')->all();
        if (empty($bendaharaIds)) return 0;

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

            foreach ($bendaharaIds as $uid) {
                AppNotification::create([
                    'user_id' => $uid,
                    'title'   => "Budget Alert: {$b->kategori->nama}",
                    'message' => "Realisasi sudah " . number_format($persen, 1) . "% dari anggaran " . ($b->bulan ? Carbon::create()->month($b->bulan)->translatedFormat('F') : 'tahun') . " {$b->tahun}",
                    'data'    => json_encode(['type' => 'budget_alert', 'budget_id' => $b->id]),
                ]);
                $sent++;
            }
        }
        return $sent;
    }
}
