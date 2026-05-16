<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\KasBank;
use App\Models\Finance\PettyCash;
use App\Models\Finance\PettyCashTransaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PettyCashService
{
    public function __construct(protected JournalService $journalService) {}

    /**
     * Pengisian (top up) kas kecil dari kas/bank besar.
     *
     * Jurnal: Petty Cash (D) | Kas/Bank (K)
     */
    public function topUp(PettyCash $petty, array $data): PettyCashTransaction
    {
        $kasBank = KasBank::with('account')->findOrFail($data['kas_bank_id']);
        $pettyAcc = Account::where('kode', '150')->first();
        if (!$pettyAcc) throw new InvalidArgumentException('Akun Kas Kecil (150) tidak ada di COA.');

        return DB::transaction(function () use ($petty, $data, $kasBank, $pettyAcc) {
            $trx = PettyCashTransaction::create([
                'petty_cash_id' => $petty->id,
                'tanggal'       => $data['tanggal'],
                'tipe'          => 'pengisian',
                'nominal'       => $data['nominal'],
                'keterangan'    => $data['keterangan'] ?? "Pengisian kas kecil",
                'created_by'    => auth()->id(),
            ]);

            $journal = $this->journalService->post([
                'tanggal'     => $data['tanggal'],
                'tipe'        => 'auto',
                'referensi'   => 'PC-IN-' . $trx->id,
                'source_type' => PettyCashTransaction::class,
                'source_id'   => $trx->id,
                'keterangan'  => "Top up kas kecil ({$petty->penanggung_jawab})",
            ], [
                ['account_id' => $pettyAcc->id, 'debit' => $data['nominal'], 'kredit' => 0, 'keterangan' => 'Pengisian kas kecil'],
                ['account_id' => $kasBank->account_id, 'kas_bank_id' => $kasBank->id, 'debit' => 0, 'kredit' => $data['nominal'], 'keterangan' => 'Tarik dari ' . $kasBank->nama],
            ]);

            $trx->update(['journal_id' => $journal->id]);

            $petty->saldo_berjalan += $data['nominal'];
            $petty->save();

            $kasBank->recalcSaldo();

            return $trx->fresh();
        });
    }

    /**
     * Pengeluaran kas kecil. Jurnal: Beban (D) | Petty Cash (K).
     */
    public function spend(PettyCash $petty, array $data): PettyCashTransaction
    {
        if ($data['nominal'] > $petty->saldo_berjalan + 0.01) {
            throw new InvalidArgumentException('Nominal melebihi saldo kas kecil.');
        }

        $pettyAcc = Account::where('kode', '150')->first();
        $kategori = \App\Models\Finance\Kategori::with('account')->findOrFail($data['kategori_id']);

        if ($kategori->tipe !== 'pengeluaran') {
            throw new InvalidArgumentException('Kategori harus tipe pengeluaran.');
        }

        return DB::transaction(function () use ($petty, $data, $pettyAcc, $kategori) {
            $trx = PettyCashTransaction::create([
                'petty_cash_id' => $petty->id,
                'tanggal'       => $data['tanggal'],
                'tipe'          => 'pengeluaran',
                'kategori_id'   => $kategori->id,
                'nominal'       => $data['nominal'],
                'keterangan'    => $data['keterangan'] ?? null,
                'bukti'         => $data['bukti'] ?? null,
                'created_by'    => auth()->id(),
            ]);

            $journal = $this->journalService->post([
                'tanggal'     => $data['tanggal'],
                'tipe'        => 'auto',
                'referensi'   => 'PC-OUT-' . $trx->id,
                'source_type' => PettyCashTransaction::class,
                'source_id'   => $trx->id,
                'keterangan'  => "Pengeluaran kas kecil: {$kategori->nama}" . ($data['keterangan'] ? " - {$data['keterangan']}" : ''),
            ], [
                ['account_id' => $kategori->account_id, 'debit' => $data['nominal'], 'kredit' => 0, 'keterangan' => $kategori->nama],
                ['account_id' => $pettyAcc->id, 'debit' => 0, 'kredit' => $data['nominal'], 'keterangan' => 'Kas kecil'],
            ]);

            $trx->update(['journal_id' => $journal->id]);

            $petty->saldo_berjalan -= $data['nominal'];
            $petty->save();

            return $trx->fresh();
        });
    }
}
