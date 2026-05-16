<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\KasBank;
use App\Models\Finance\Kategori;
use App\Models\Finance\Transaksi;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransaksiService
{
    public function __construct(protected JournalService $journalService) {}

    /**
     * Catat pemasukan (cash in).
     *
     * @param  array{tanggal: string, kategori_id: int, kas_bank_id: int, nominal: float, pihak?: string|null, keterangan?: string|null, bukti?: string|null}  $data
     */
    public function masuk(array $data): Transaksi
    {
        $kategori = Kategori::with('account')->findOrFail($data['kategori_id']);
        $kasBank  = KasBank::with('account')->findOrFail($data['kas_bank_id']);

        if ($kategori->tipe !== 'pemasukan') {
            throw new InvalidArgumentException('Kategori harus tipe pemasukan.');
        }

        return DB::transaction(function () use ($data, $kategori, $kasBank) {
            $trx = Transaksi::create([
                'nomor'        => Transaksi::generateNomor('masuk'),
                'tanggal'      => $data['tanggal'],
                'tipe'         => 'masuk',
                'kategori_id'  => $kategori->id,
                'kas_bank_id'  => $kasBank->id,
                'nominal'      => $data['nominal'],
                'pihak'        => $data['pihak'] ?? null,
                'keterangan'   => $data['keterangan'] ?? null,
                'bukti'        => $data['bukti'] ?? null,
                'status'       => 'posted',
                'created_by'   => auth()->id(),
            ]);

            $journal = $this->journalService->post([
                'tanggal'     => $data['tanggal'],
                'tipe'        => 'kas_masuk',
                'referensi'   => $trx->nomor,
                'source_type' => Transaksi::class,
                'source_id'   => $trx->id,
                'keterangan'  => "Pemasukan: {$kategori->nama}" . ($data['keterangan'] ? " - {$data['keterangan']}" : ''),
            ], [
                ['account_id' => $kasBank->account_id, 'kas_bank_id' => $kasBank->id, 'debit' => $data['nominal'], 'kredit' => 0, 'keterangan' => $kasBank->nama],
                ['account_id' => $kategori->account_id, 'debit' => 0, 'kredit' => $data['nominal'], 'keterangan' => $kategori->nama],
            ]);

            $trx->update(['journal_id' => $journal->id]);
            $kasBank->recalcSaldo();

            return $trx->fresh(['kategori', 'kasBank', 'journal']);
        });
    }

    /**
     * Catat pengeluaran (cash out).
     *
     * @param  array{tanggal: string, kategori_id: int, kas_bank_id: int, nominal: float, pihak?: string|null, keterangan?: string|null, bukti?: string|null}  $data
     */
    public function keluar(array $data): Transaksi
    {
        $kategori = Kategori::with('account')->findOrFail($data['kategori_id']);
        $kasBank  = KasBank::with('account')->findOrFail($data['kas_bank_id']);

        if ($kategori->tipe !== 'pengeluaran') {
            throw new InvalidArgumentException('Kategori harus tipe pengeluaran.');
        }

        return DB::transaction(function () use ($data, $kategori, $kasBank) {
            $trx = Transaksi::create([
                'nomor'        => Transaksi::generateNomor('keluar'),
                'tanggal'      => $data['tanggal'],
                'tipe'         => 'keluar',
                'kategori_id'  => $kategori->id,
                'kas_bank_id'  => $kasBank->id,
                'nominal'      => $data['nominal'],
                'pihak'        => $data['pihak'] ?? null,
                'keterangan'   => $data['keterangan'] ?? null,
                'bukti'        => $data['bukti'] ?? null,
                'status'       => 'posted',
                'created_by'   => auth()->id(),
            ]);

            $journal = $this->journalService->post([
                'tanggal'     => $data['tanggal'],
                'tipe'        => 'kas_keluar',
                'referensi'   => $trx->nomor,
                'source_type' => Transaksi::class,
                'source_id'   => $trx->id,
                'keterangan'  => "Pengeluaran: {$kategori->nama}" . ($data['keterangan'] ? " - {$data['keterangan']}" : ''),
            ], [
                ['account_id' => $kategori->account_id, 'debit' => $data['nominal'], 'kredit' => 0, 'keterangan' => $kategori->nama],
                ['account_id' => $kasBank->account_id, 'kas_bank_id' => $kasBank->id, 'debit' => 0, 'kredit' => $data['nominal'], 'keterangan' => $kasBank->nama],
            ]);

            $trx->update(['journal_id' => $journal->id]);
            $kasBank->recalcSaldo();

            return $trx->fresh(['kategori', 'kasBank', 'journal']);
        });
    }

    /**
     * Transfer antar kas/bank.
     *
     * @param  array{tanggal: string, kas_bank_id: int, kas_bank_tujuan_id: int, nominal: float, keterangan?: string|null, bukti?: string|null}  $data
     */
    public function transfer(array $data): Transaksi
    {
        if ($data['kas_bank_id'] == $data['kas_bank_tujuan_id']) {
            throw new InvalidArgumentException('Kas asal dan tujuan tidak boleh sama.');
        }

        $asal   = KasBank::with('account')->findOrFail($data['kas_bank_id']);
        $tujuan = KasBank::with('account')->findOrFail($data['kas_bank_tujuan_id']);

        return DB::transaction(function () use ($data, $asal, $tujuan) {
            $trx = Transaksi::create([
                'nomor'              => Transaksi::generateNomor('transfer'),
                'tanggal'            => $data['tanggal'],
                'tipe'               => 'transfer',
                'kas_bank_id'        => $asal->id,
                'kas_bank_tujuan_id' => $tujuan->id,
                'nominal'            => $data['nominal'],
                'keterangan'         => $data['keterangan'] ?? null,
                'bukti'              => $data['bukti'] ?? null,
                'status'             => 'posted',
                'created_by'         => auth()->id(),
            ]);

            $journal = $this->journalService->post([
                'tanggal'     => $data['tanggal'],
                'tipe'        => 'transfer',
                'referensi'   => $trx->nomor,
                'source_type' => Transaksi::class,
                'source_id'   => $trx->id,
                'keterangan'  => "Transfer dari {$asal->nama} ke {$tujuan->nama}",
            ], [
                ['account_id' => $tujuan->account_id, 'kas_bank_id' => $tujuan->id, 'debit' => $data['nominal'], 'kredit' => 0, 'keterangan' => 'Transfer masuk'],
                ['account_id' => $asal->account_id, 'kas_bank_id' => $asal->id, 'debit' => 0, 'kredit' => $data['nominal'], 'keterangan' => 'Transfer keluar'],
            ]);

            $trx->update(['journal_id' => $journal->id]);
            $asal->recalcSaldo();
            $tujuan->recalcSaldo();

            return $trx->fresh(['kasBank', 'kasBankTujuan', 'journal']);
        });
    }

    public function void(Transaksi $trx, ?string $alasan = null): Transaksi
    {
        return DB::transaction(function () use ($trx, $alasan) {
            if ($trx->journal) {
                $this->journalService->reverse($trx->journal);
            }
            $trx->update([
                'status'     => 'void',
                'keterangan' => trim(($trx->keterangan ?? '') . " | VOID: " . ($alasan ?? '-')),
            ]);
            $trx->kasBank?->recalcSaldo();
            $trx->kasBankTujuan?->recalcSaldo();
            return $trx;
        });
    }
}
