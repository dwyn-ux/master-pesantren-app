<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\Journal;
use App\Models\Finance\KasBank;
use App\Models\LaundryOrder;
use App\Models\Pembayaran;
use App\Models\TopUpRequest;
use App\Models\TransaksiKasir;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Auto-journal untuk integrasi modul existing (Kantin, Laundry, SPP, Topup).
 *
 * Konsep:
 * - Wallet santri = Liability (Saldo Wallet Santri / 241)
 * - Topup wali        : Kas/Bank (D) | Saldo Wallet (K)
 * - Belanja kantin    : Saldo Wallet (D) | Pendapatan Kantin (K)
 * - Bayar laundry     : Saldo Wallet (D) | Pendapatan Laundry (K)
 * - Bayar SPP via wali: Kas/Bank (D) | Pendapatan SPP (K)  → kalau langsung lunas
 *                       atau: Piutang SPP (D) saat tagihan dibuat, lalu Kas (D) | Piutang (K) saat bayar.
 */
class AutoJournalService
{
    public function __construct(protected JournalService $journalService) {}

    /**
     * Journal saat top-up wali (saldo wallet bertambah).
     */
    public function topUp(TopUpRequest $topup, int $kasBankId): ?Journal
    {
        if ($topup->status !== 'paid' && $topup->status !== 'success') return null;

        $kasBank = KasBank::find($kasBankId);
        $walletAcc = Account::where('kode', '241')->first();

        if (!$kasBank || !$walletAcc) {
            Log::warning("AutoJournal topUp: missing kasBank/walletAcc for topup #{$topup->id}");
            return null;
        }

        return DB::transaction(function () use ($topup, $kasBank, $walletAcc) {
            $existing = Journal::where('source_type', TopUpRequest::class)
                ->where('source_id', $topup->id)->first();
            if ($existing) return $existing;

            $journal = $this->journalService->post([
                'tanggal'     => $topup->updated_at?->toDateString() ?? now()->toDateString(),
                'tipe'        => 'auto',
                'referensi'   => 'TOPUP-' . $topup->id,
                'source_type' => TopUpRequest::class,
                'source_id'   => $topup->id,
                'keterangan'  => "Topup saldo wali (Wali #{$topup->wali_id}) via {$topup->metode}",
            ], [
                ['account_id' => $kasBank->account_id, 'kas_bank_id' => $kasBank->id, 'debit' => $topup->nominal, 'kredit' => 0, 'keterangan' => 'Topup masuk via ' . $kasBank->nama],
                ['account_id' => $walletAcc->id, 'debit' => 0, 'kredit' => $topup->nominal, 'keterangan' => 'Saldo wallet santri'],
            ]);

            $kasBank->recalcSaldo();
            return $journal;
        });
    }

    /**
     * Journal saat transaksi kantin (santri belanja pakai saldo).
     */
    public function transaksiKantin(TransaksiKasir $trx): ?Journal
    {
        $walletAcc      = Account::where('kode', '241')->first();
        $pendapatanAcc  = Account::where('kode', '421')->first();

        if (!$walletAcc || !$pendapatanAcc) {
            Log::warning("AutoJournal kantin: missing accounts for trx #{$trx->id}");
            return null;
        }

        return DB::transaction(function () use ($trx, $walletAcc, $pendapatanAcc) {
            $existing = Journal::where('source_type', TransaksiKasir::class)
                ->where('source_id', $trx->id)->first();
            if ($existing) return $existing;

            return $this->journalService->post([
                'tanggal'     => $trx->created_at->toDateString(),
                'tipe'        => 'auto',
                'referensi'   => 'KSR-' . $trx->id,
                'source_type' => TransaksiKasir::class,
                'source_id'   => $trx->id,
                'keterangan'  => "Penjualan kantin (Santri #{$trx->santri_id}, Outlet #{$trx->outlet_id})",
            ], [
                ['account_id' => $walletAcc->id, 'debit' => $trx->total, 'kredit' => 0, 'keterangan' => 'Pengurangan saldo wallet'],
                ['account_id' => $pendapatanAcc->id, 'debit' => 0, 'kredit' => $trx->total, 'keterangan' => 'Pendapatan kantin'],
            ]);
        });
    }

    /**
     * Journal saat order laundry dibayar.
     */
    public function transaksiLaundry(LaundryOrder $order): ?Journal
    {
        if ($order->status_pembayaran !== 'lunas' && $order->status !== 'selesai') return null;

        $walletAcc     = Account::where('kode', '241')->first();
        $pendapatanAcc = Account::where('kode', '422')->first();

        if (!$walletAcc || !$pendapatanAcc) return null;

        return DB::transaction(function () use ($order, $walletAcc, $pendapatanAcc) {
            $existing = Journal::where('source_type', LaundryOrder::class)
                ->where('source_id', $order->id)->first();
            if ($existing) return $existing;

            return $this->journalService->post([
                'tanggal'     => ($order->updated_at ?? $order->created_at)->toDateString(),
                'tipe'        => 'auto',
                'referensi'   => 'LND-' . $order->id,
                'source_type' => LaundryOrder::class,
                'source_id'   => $order->id,
                'keterangan'  => "Penjualan laundry order #{$order->id}",
            ], [
                ['account_id' => $walletAcc->id, 'debit' => $order->total, 'kredit' => 0, 'keterangan' => 'Pengurangan saldo wallet'],
                ['account_id' => $pendapatanAcc->id, 'debit' => 0, 'kredit' => $order->total, 'keterangan' => 'Pendapatan laundry'],
            ]);
        });
    }

    /**
     * Journal saat pembayaran tagihan (SPP, dll) dari wali.
     */
    public function pembayaranTagihan(Pembayaran $pembayaran, int $kasBankId): ?Journal
    {
        if ($pembayaran->status !== 'paid') return null;

        $kasBank = KasBank::find($kasBankId);
        if (!$kasBank) return null;

        $jenisKode    = optional($pembayaran->tagihan?->jenisTagihan)->kode ?? '';
        $accountKode  = $this->mapJenisToAccountKode($jenisKode);
        $pendapatanAcc = Account::where('kode', $accountKode)->first()
            ?? Account::where('kode', '411')->first();

        if (!$pendapatanAcc) return null;

        return DB::transaction(function () use ($pembayaran, $kasBank, $pendapatanAcc) {
            $existing = Journal::where('source_type', Pembayaran::class)
                ->where('source_id', $pembayaran->id)->first();
            if ($existing) return $existing;

            $journal = $this->journalService->post([
                'tanggal'     => ($pembayaran->paid_at ?? $pembayaran->updated_at)->toDateString(),
                'tipe'        => 'auto',
                'referensi'   => 'BYR-' . $pembayaran->id,
                'source_type' => Pembayaran::class,
                'source_id'   => $pembayaran->id,
                'keterangan'  => "Pembayaran tagihan #{$pembayaran->tagihan_id} via {$pembayaran->metode}",
            ], [
                ['account_id' => $kasBank->account_id, 'kas_bank_id' => $kasBank->id, 'debit' => $pembayaran->nominal, 'kredit' => 0, 'keterangan' => 'Penerimaan dari wali'],
                ['account_id' => $pendapatanAcc->id, 'debit' => 0, 'kredit' => $pembayaran->nominal, 'keterangan' => $pendapatanAcc->nama],
            ]);

            $kasBank->recalcSaldo();
            return $journal;
        });
    }

    protected function mapJenisToAccountKode(string $jenisKode): string
    {
        $upper = strtoupper($jenisKode);
        return match (true) {
            str_contains($upper, 'SPP')      => '411',
            str_contains($upper, 'DAFTAR')   => '412',
            str_contains($upper, 'SERAGAM')  => '413',
            str_contains($upper, 'KEGIATAN') => '414',
            default                          => '411',
        };
    }
}
