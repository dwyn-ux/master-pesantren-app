<?php

namespace App\Observers;

use App\Models\Finance\KasBank;
use App\Models\TopUpRequest;
use App\Services\Finance\AutoJournalService;

class TopUpRequestObserver
{
    public function __construct(protected AutoJournalService $service) {}

    public function updated(TopUpRequest $topup): void
    {
        if ($topup->wasChanged('status') && in_array($topup->status, ['paid', 'success'])) {
            $this->createJournal($topup);
        }
    }

    protected function createJournal(TopUpRequest $topup): void
    {
        try {
            $kasBank = KasBank::where('kode', 'BNK001')->first()
                ?? KasBank::where('tipe', 'bank')->first()
                ?? KasBank::first();
            if (!$kasBank) return;
            $this->service->topUp($topup, $kasBank->id);
        } catch (\Throwable $e) {
            \Log::error("AutoJournal topup failed for #{$topup->id}: {$e->getMessage()}");
        }
    }
}
