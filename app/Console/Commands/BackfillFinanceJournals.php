<?php

namespace App\Console\Commands;

use App\Models\Finance\KasBank;
use App\Models\LaundryOrder;
use App\Models\Pembayaran;
use App\Models\TopUpRequest;
use App\Models\TransaksiKasir;
use App\Services\Finance\AutoJournalService;
use Illuminate\Console\Command;

class BackfillFinanceJournals extends Command
{
    protected $signature = 'finance:backfill {--module=all : kantin|laundry|topup|pembayaran|all}';
    protected $description = 'Generate auto-journal entries untuk transaksi modul existing yang belum punya jurnal';

    public function handle(AutoJournalService $service): int
    {
        $module = $this->option('module');
        $this->info("Backfill module: {$module}");

        if (in_array($module, ['kantin', 'all'])) $this->backfillKantin($service);
        if (in_array($module, ['laundry', 'all'])) $this->backfillLaundry($service);
        if (in_array($module, ['topup', 'all'])) $this->backfillTopup($service);
        if (in_array($module, ['pembayaran', 'all'])) $this->backfillPembayaran($service);

        $this->info('Done.');
        return self::SUCCESS;
    }

    protected function backfillKantin(AutoJournalService $service): void
    {
        $items = TransaksiKasir::all();
        $this->withProgressBar($items, function ($trx) use ($service) {
            try { $service->transaksiKantin($trx); } catch (\Throwable $e) { $this->error(' '.$e->getMessage()); }
        });
        $this->newLine(); $this->info("Kantin: {$items->count()} transaksi diproses.");
    }

    protected function backfillLaundry(AutoJournalService $service): void
    {
        $items = LaundryOrder::all();
        $this->withProgressBar($items, function ($order) use ($service) {
            try { $service->transaksiLaundry($order); } catch (\Throwable $e) {}
        });
        $this->newLine(); $this->info("Laundry: {$items->count()} order diproses.");
    }

    protected function backfillTopup(AutoJournalService $service): void
    {
        $items = TopUpRequest::whereIn('status', ['paid', 'success'])->get();
        $kasBank = KasBank::where('kode', 'BNK001')->first() ?? KasBank::first();
        if (!$kasBank) { $this->error('Belum ada Kas/Bank.'); return; }

        $this->withProgressBar($items, function ($t) use ($service, $kasBank) {
            try { $service->topUp($t, $kasBank->id); } catch (\Throwable $e) {}
        });
        $this->newLine(); $this->info("TopUp: {$items->count()} diproses.");
    }

    protected function backfillPembayaran(AutoJournalService $service): void
    {
        $items = Pembayaran::where('status', 'paid')->with('tagihan.jenisTagihan')->get();
        $kasBank = KasBank::where('kode', 'BNK001')->first() ?? KasBank::first();
        if (!$kasBank) { $this->error('Belum ada Kas/Bank.'); return; }

        $this->withProgressBar($items, function ($p) use ($service, $kasBank) {
            try { $service->pembayaranTagihan($p, $kasBank->id); } catch (\Throwable $e) {}
        });
        $this->newLine(); $this->info("Pembayaran: {$items->count()} diproses.");
    }
}
