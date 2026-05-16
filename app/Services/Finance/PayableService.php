<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\KasBank;
use App\Models\Finance\Payable;
use App\Models\Finance\PayablePayment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PayableService
{
    public function __construct(protected JournalService $journalService) {}

    /**
     * Catat hutang baru. Jurnal: Beban/Aset (D) | Hutang Vendor (K).
     *
     * @param  array{tanggal: string, jatuh_tempo: string, vendor_id: int, nominal: float, account_id: int, keterangan?: string}  $data
     */
    public function create(array $data): Payable
    {
        return DB::transaction(function () use ($data) {
            $payable = Payable::create([
                'nomor'       => Payable::generateNomor(),
                'tanggal'     => $data['tanggal'],
                'jatuh_tempo' => $data['jatuh_tempo'],
                'vendor_id'   => $data['vendor_id'],
                'nominal'     => $data['nominal'],
                'sisa'        => $data['nominal'],
                'status'      => 'belum_bayar',
                'keterangan'  => $data['keterangan'] ?? null,
            ]);

            $hutangAcc = Account::where('kode', '211')->first();
            if (!$hutangAcc) throw new InvalidArgumentException('Akun Hutang Vendor (211) tidak ada di COA.');

            $journal = $this->journalService->post([
                'tanggal'     => $data['tanggal'],
                'tipe'        => 'umum',
                'referensi'   => $payable->nomor,
                'source_type' => Payable::class,
                'source_id'   => $payable->id,
                'keterangan'  => "Pencatatan hutang vendor: {$payable->nomor}",
            ], [
                ['account_id' => $data['account_id'], 'debit' => $data['nominal'], 'kredit' => 0, 'keterangan' => $data['keterangan'] ?? 'Hutang vendor'],
                ['account_id' => $hutangAcc->id, 'debit' => 0, 'kredit' => $data['nominal'], 'keterangan' => 'Hutang ke vendor'],
            ]);

            $payable->update(['journal_id' => $journal->id]);
            return $payable->fresh(['vendor', 'journal']);
        });
    }

    /**
     * Bayar hutang. Jurnal: Hutang Vendor (D) | Kas/Bank (K).
     */
    public function pay(Payable $payable, array $data): PayablePayment
    {
        if ($payable->status === 'lunas' || $payable->status === 'void') {
            throw new InvalidArgumentException('Hutang sudah lunas atau void.');
        }
        if ($data['nominal'] > $payable->sisa + 0.01) {
            throw new InvalidArgumentException('Nominal pembayaran melebihi sisa hutang.');
        }

        $kasBank = KasBank::with('account')->findOrFail($data['kas_bank_id']);

        return DB::transaction(function () use ($payable, $data, $kasBank) {
            $payment = PayablePayment::create([
                'payable_id'  => $payable->id,
                'tanggal'     => $data['tanggal'],
                'nominal'     => $data['nominal'],
                'kas_bank_id' => $kasBank->id,
                'keterangan'  => $data['keterangan'] ?? null,
                'created_by'  => auth()->id(),
            ]);

            $hutangAcc = Account::where('kode', '211')->first();

            $journal = $this->journalService->post([
                'tanggal'     => $data['tanggal'],
                'tipe'        => 'kas_keluar',
                'referensi'   => $payable->nomor . '-PAY' . $payment->id,
                'source_type' => PayablePayment::class,
                'source_id'   => $payment->id,
                'keterangan'  => "Pembayaran hutang {$payable->nomor}",
            ], [
                ['account_id' => $hutangAcc->id, 'debit' => $data['nominal'], 'kredit' => 0, 'keterangan' => 'Pelunasan hutang'],
                ['account_id' => $kasBank->account_id, 'kas_bank_id' => $kasBank->id, 'debit' => 0, 'kredit' => $data['nominal'], 'keterangan' => 'Bayar via ' . $kasBank->nama],
            ]);

            $payment->update(['journal_id' => $journal->id]);
            $payable->recalcStatus();
            $kasBank->recalcSaldo();

            return $payment;
        });
    }
}
