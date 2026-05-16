<?php

namespace App\Observers;

use App\Models\TransaksiKasir;
use App\Services\Finance\AutoJournalService;

class TransaksiKasirObserver
{
    public function __construct(protected AutoJournalService $service) {}

    public function created(TransaksiKasir $trx): void
    {
        try {
            $this->service->transaksiKantin($trx);
        } catch (\Throwable $e) {
            \Log::error("AutoJournal kantin failed for trx #{$trx->id}: {$e->getMessage()}");
        }
    }
}
