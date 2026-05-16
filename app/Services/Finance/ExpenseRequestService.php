<?php

namespace App\Services\Finance;

use App\Models\Finance\ExpenseRequest;
use App\Models\Finance\Setting;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ExpenseRequestService
{
    public function __construct(protected TransaksiService $trxService) {}

    /**
     * Submit expense request, set status sesuai threshold approval.
     */
    public function submit(ExpenseRequest $req): ExpenseRequest
    {
        $thresholdKepala  = (float) Setting::get('approval.threshold_kepala_pondok', 5000000);
        $thresholdYayasan = (float) Setting::get('approval.threshold_yayasan', 50000000);

        if ((float) $req->nominal >= $thresholdYayasan) {
            $req->update(['status' => 'pending_kepala', 'current_level' => 1]);
        } elseif ((float) $req->nominal >= $thresholdKepala) {
            $req->update(['status' => 'pending_kepala', 'current_level' => 1]);
        } else {
            $req->update(['status' => 'approved', 'current_level' => 0]);
        }
        return $req->fresh();
    }

    public function approve(ExpenseRequest $req, string $level): ExpenseRequest
    {
        $thresholdYayasan = (float) Setting::get('approval.threshold_yayasan', 50000000);

        if ($level === 'kepala' && $req->status === 'pending_kepala') {
            $req->update([
                'approved_by_kepala'  => auth()->id(),
                'approved_kepala_at'  => now(),
            ]);

            if ((float) $req->nominal >= $thresholdYayasan) {
                $req->update(['status' => 'pending_yayasan', 'current_level' => 2]);
            } else {
                $req->update(['status' => 'approved', 'current_level' => 0]);
            }
            return $req->fresh();
        }

        if ($level === 'yayasan' && $req->status === 'pending_yayasan') {
            $req->update([
                'approved_by_yayasan'  => auth()->id(),
                'approved_yayasan_at'  => now(),
                'status'               => 'approved',
                'current_level'        => 0,
            ]);
            return $req->fresh();
        }

        throw new InvalidArgumentException('Status approval tidak sesuai.');
    }

    public function reject(ExpenseRequest $req, string $reason): ExpenseRequest
    {
        $req->update([
            'status'        => 'rejected',
            'reject_reason' => $reason,
        ]);
        return $req;
    }

    /**
     * Setelah approved, eksekusi pengeluaran (buat transaksi keluar + jurnal).
     */
    public function execute(ExpenseRequest $req): ExpenseRequest
    {
        if ($req->status !== 'approved') {
            throw new InvalidArgumentException('Hanya request status approved yang bisa dieksekusi.');
        }
        if (!$req->kategori_id || !$req->kas_bank_id) {
            throw new InvalidArgumentException('Kategori & Kas/Bank wajib dipilih sebelum eksekusi.');
        }

        return DB::transaction(function () use ($req) {
            $trx = $this->trxService->keluar([
                'tanggal'     => $req->tanggal->toDateString(),
                'kategori_id' => $req->kategori_id,
                'kas_bank_id' => $req->kas_bank_id,
                'nominal'     => $req->nominal,
                'pihak'       => $req->vendor?->nama ?? null,
                'keterangan'  => "[{$req->nomor}] {$req->judul} | {$req->deskripsi}",
                'bukti'       => $req->bukti,
            ]);

            $req->update([
                'status'        => 'paid',
                'transaksi_id'  => $trx->id,
            ]);
            return $req->fresh();
        });
    }
}
