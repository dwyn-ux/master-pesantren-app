<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TopUpRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class PaymentSettlementService
{
    public function applyPaymentStatus(Pembayaran $payment, string $gatewayStatus): Pembayaran
    {
        $newStatus = $this->paymentStatus($gatewayStatus);

        return DB::transaction(function () use ($payment, $newStatus) {
            $locked = Pembayaran::query()->lockForUpdate()->findOrFail($payment->id);

            if ($locked->status === 'paid') {
                return $locked;
            }

            $locked->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === 'paid' ? now() : $locked->paid_at,
            ]);

            if ($newStatus === 'paid') {
                $this->settlePaymentItems($locked);
            }

            return $locked->refresh();
        }, 3);
    }

    public function confirmManual(Pembayaran $payment, User $admin, ?string $note = null): Pembayaran
    {
        return DB::transaction(function () use ($payment, $admin, $note) {
            $locked = Pembayaran::query()->lockForUpdate()->findOrFail($payment->id);

            if (! $locked->isManualTransfer() || $locked->status !== 'pending') {
                throw new \RuntimeException('Pembayaran manual ini sudah diproses atau bukan transfer manual.');
            }

            $locked->update([
                'confirmed_by' => $admin->id,
                'confirmed_at' => now(),
                'manual_note' => $note,
                'rejection_note' => null,
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $this->settlePaymentItems($locked);

            return $locked->refresh();
        }, 3);
    }

    public function rejectManual(Pembayaran $payment, User $admin, string $reason): Pembayaran
    {
        return DB::transaction(function () use ($payment, $admin, $reason) {
            $locked = Pembayaran::query()->lockForUpdate()->findOrFail($payment->id);

            if (! $locked->isManualTransfer() || $locked->status !== 'pending') {
                throw new \RuntimeException('Pembayaran manual ini sudah diproses atau bukan transfer manual.');
            }

            $locked->update([
                'confirmed_by' => $admin->id,
                'confirmed_at' => now(),
                'rejection_note' => $reason,
                'status' => 'failed',
            ]);

            return $locked->refresh();
        }, 3);
    }

    public function applyTopUpStatus(TopUpRequest $topUp, string $gatewayStatus): TopUpRequest
    {
        $newStatus = $this->topUpStatus($gatewayStatus);

        return DB::transaction(function () use ($topUp, $newStatus) {
            $locked = TopUpRequest::query()->lockForUpdate()->findOrFail($topUp->id);

            if ($locked->status === 'paid') {
                return $locked;
            }

            $locked->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === 'paid' ? now() : $locked->paid_at,
            ]);

            if ($newStatus === 'paid') {
                $this->creditWallet(
                    $locked->santri_id,
                    (int) $locked->nominal,
                    $locked->id,
                    TopUpRequest::class,
                    'Top up saldo via payment gateway',
                );
            }

            return $locked->refresh();
        }, 3);
    }

    private function settlePaymentItems(Pembayaran $payment): void
    {
        $tagihanIds = collect($payment->tagihan_ids ?: [])
            ->when($payment->tagihan_id, fn ($ids) => $ids->push($payment->tagihan_id))
            ->unique()
            ->values();

        if ($tagihanIds->isNotEmpty()) {
            Tagihan::whereIn('id', $tagihanIds)->update(['status' => 'lunas']);
        }

        foreach (collect($payment->topup_items ?: [])->groupBy('santri_id') as $santriId => $items) {
            $this->creditWallet(
                (int) $santriId,
                (int) $items->sum('nominal'),
                $payment->id,
                Pembayaran::class,
                'Top up via checkout pembayaran',
            );
        }
    }

    private function creditWallet(
        int $santriId,
        int $nominal,
        int $referenceId,
        string $referenceType,
        string $description,
    ): void {
        $alreadyCredited = WalletTransaction::query()
            ->where('santri_id', $santriId)
            ->where('tipe', 'topup')
            ->where('referensi_id', $referenceId)
            ->where('referensi_tipe', $referenceType)
            ->exists();

        if ($alreadyCredited) {
            return;
        }

        $santri = Santri::query()->lockForUpdate()->findOrFail($santriId);
        $before = (int) $santri->saldo;
        $after = $before + $nominal;

        $santri->update(['saldo' => $after]);

        WalletTransaction::create([
            'santri_id' => $santri->id,
            'tipe' => 'topup',
            'referensi_id' => $referenceId,
            'referensi_tipe' => $referenceType,
            'nominal' => $nominal,
            'jenis' => 'kredit',
            'saldo_sebelum' => $before,
            'saldo_sesudah' => $after,
            'keterangan' => $description,
        ]);
    }

    private function paymentStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'PAID', 'SETTLED', 'SETTLEMENT', 'CAPTURE' => 'paid',
            'EXPIRED', 'EXPIRE' => 'expired',
            'FAILED', 'CANCEL', 'DENY', 'REFUND' => 'failed',
            default => 'pending',
        };
    }

    private function topUpStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'PAID', 'SETTLED', 'SETTLEMENT', 'CAPTURE' => 'paid',
            'EXPIRED', 'EXPIRE' => 'expired',
            'FAILED', 'CANCEL', 'DENY', 'REFUND' => 'failed',
            default => 'unpaid',
        };
    }
}
