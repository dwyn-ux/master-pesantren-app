<?php

namespace App\Observers;

use App\Models\Finance\KasBank;
use App\Models\Pembayaran;
use App\Services\Finance\AutoJournalService;

class PembayaranObserver
{
    public function __construct(protected AutoJournalService $service) {}

    public function updated(Pembayaran $pembayaran): void
    {
        if ($pembayaran->wasChanged('status') && $pembayaran->status === 'paid') {
            $this->createJournal($pembayaran);
        }
    }

    public function created(Pembayaran $pembayaran): void
    {
        if ($pembayaran->status === 'paid') {
            $this->createJournal($pembayaran);
        }
    }

    protected function createJournal(Pembayaran $pembayaran): void
    {
        try {
            $kasBank = $this->resolveKasBank($pembayaran->metode);
            if (!$kasBank) return;
            $this->service->pembayaranTagihan($pembayaran, $kasBank->id);
        } catch (\Throwable $e) {
            \Log::error("AutoJournal pembayaran failed for #{$pembayaran->id}: {$e->getMessage()}");
        }
    }

    protected function resolveKasBank(?string $metode): ?KasBank
    {
        $kode = match (strtolower((string) $metode)) {
            'cash', 'tunai'                 => 'KAS001',
            'transfer', 'va', 'bank'        => 'BNK001',
            'syariah', 'bsi', 'bsm'         => 'BNK002',
            default                         => 'BNK001',
        };
        return KasBank::where('kode', $kode)->first()
            ?? KasBank::where('tipe', 'bank')->first()
            ?? KasBank::first();
    }
}
