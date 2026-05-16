<?php

namespace App\Http\Controllers\Api\Kantin;

use App\Http\Controllers\Controller;
use App\Models\KantinOfflineTransaksi;
use App\Models\KantinSyncLog;
use App\Models\Produk;
use App\Models\Santri;
use App\Models\TransaksiKasir;
use App\Models\TransaksiKasirItem;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * PUSH: terima batch transaksi offline dari device kantin.
     *
     * Payload:
     * {
     *   "transaksi": [
     *     {
     *       "client_uuid": "uuid-v4",
     *       "santri_id": 12,
     *       "total": 15000,
     *       "items": [{"produk_id":1,"nama":"Roti","qty":2,"harga":5000}, ...],
     *       "metode_bayar": "saldo",
     *       "transaksi_at": "2026-05-16T10:30:00+07:00"
     *     }
     *   ]
     * }
     *
     * Idempotent: kalau client_uuid sudah ada, skip.
     */
    public function push(Request $request)
    {
        $device = $request->attributes->get('kantin_device');

        $validated = $request->validate([
            'transaksi'                       => 'required|array|min:1',
            'transaksi.*.client_uuid'         => 'required|uuid',
            'transaksi.*.santri_id'           => 'nullable|exists:santri,id',
            'transaksi.*.total'               => 'required|integer|min:0',
            'transaksi.*.items'               => 'required|array|min:1',
            'transaksi.*.items.*.produk_id'   => 'required|integer',
            'transaksi.*.items.*.nama'        => 'required|string',
            'transaksi.*.items.*.qty'         => 'required|integer|min:1',
            'transaksi.*.items.*.harga'       => 'required|integer|min:0',
            'transaksi.*.metode_bayar'        => 'required|in:saldo,tunai',
            'transaksi.*.transaksi_at'        => 'required|date',
        ]);

        $results = [];
        $successCount = 0;
        $failCount    = 0;

        foreach ($validated['transaksi'] as $row) {
            // Idempotency: cek kalau client_uuid sudah pernah masuk
            $existing = KantinOfflineTransaksi::where('client_uuid', $row['client_uuid'])->first();
            if ($existing) {
                $results[] = [
                    'client_uuid'        => $row['client_uuid'],
                    'status'             => 'duplicate',
                    'transaksi_kasir_id' => $existing->transaksi_kasir_id,
                    'message'            => 'Transaksi sudah pernah disinkronkan.',
                ];
                continue;
            }

            try {
                $transaksiId = DB::transaction(function () use ($row, $device) {
                    return $this->processTransaction($row, $device);
                });

                $results[] = [
                    'client_uuid'        => $row['client_uuid'],
                    'status'             => 'success',
                    'transaksi_kasir_id' => $transaksiId,
                ];
                $successCount++;
            } catch (\Throwable $e) {
                // Simpan offline transaksi sebagai failed (untuk debugging)
                KantinOfflineTransaksi::create([
                    'kantin_device_id' => $device->id,
                    'client_uuid'      => $row['client_uuid'],
                    'santri_id'        => $row['santri_id'] ?? null,
                    'total'            => $row['total'],
                    'items'            => $row['items'],
                    'metode_bayar'     => $row['metode_bayar'],
                    'transaksi_at'     => $row['transaksi_at'],
                    'sync_status'      => 'failed',
                    'sync_error'       => $e->getMessage(),
                ]);

                $results[] = [
                    'client_uuid' => $row['client_uuid'],
                    'status'      => 'failed',
                    'message'     => $e->getMessage(),
                ];
                $failCount++;
            }
        }

        KantinSyncLog::create([
            'kantin_device_id' => $device->id,
            'arah'             => 'push',
            'jenis'            => 'transaksi',
            'jumlah_record'    => count($validated['transaksi']),
            'detail'           => [
                'success' => $successCount,
                'failed'  => $failCount,
            ],
            'status' => $failCount === 0 ? 'success' : ($successCount > 0 ? 'partial' : 'failed'),
        ]);

        return response()->json([
            'processed' => count($validated['transaksi']),
            'success'   => $successCount,
            'failed'    => $failCount,
            'results'   => $results,
        ]);
    }

    /**
     * Process satu transaksi: save TransaksiKasir + items, debit saldo, simpan offline log.
     */
    protected function processTransaction(array $row, $device): int
    {
        // Validasi saldo kalau bayar pakai saldo
        if ($row['metode_bayar'] === 'saldo' && !empty($row['santri_id'])) {
            $santri = Santri::lockForUpdate()->find($row['santri_id']);
            if (!$santri) {
                throw new \RuntimeException("Santri ID {$row['santri_id']} tidak ditemukan.");
            }
            if ($santri->saldo < $row['total']) {
                throw new \RuntimeException("Saldo santri {$santri->nama} tidak cukup (saldo: {$santri->saldo}, total: {$row['total']}).");
            }

            // Cek limit harian/mingguan
            try {
                $santri->checkLimitTransaksi($row['total']);
            } catch (\RuntimeException $e) {
                throw new \RuntimeException("Limit santri {$santri->nama} tercapai: " . $e->getMessage());
            }
        }

        // Buat header TransaksiKasir
        $transaksi = TransaksiKasir::create([
            'outlet_id'  => $device->outlet_id,
            'santri_id'  => $row['santri_id'] ?? null,
            'total'      => $row['total'],
            'created_at' => $row['transaksi_at'],
        ]);

        // Buat items
        foreach ($row['items'] as $item) {
            TransaksiKasirItem::create([
                'transaksi_kasir_id' => $transaksi->id,
                'produk_id'          => $item['produk_id'],
                'nama_produk'        => $item['nama'],
                'qty'                => $item['qty'],
                'harga_satuan'       => $item['harga'],
                'subtotal'           => $item['qty'] * $item['harga'],
            ]);

            // Kurangi stok produk
            Produk::where('id', $item['produk_id'])->decrement('stok', $item['qty']);
        }

        // Debit saldo & catat wallet transaction
        if ($row['metode_bayar'] === 'saldo' && !empty($row['santri_id'])) {
            $santri = Santri::find($row['santri_id']);
            $saldoSebelum = (int) $santri->saldo;
            $santri->decrement('saldo', $row['total']);
            $saldoSesudah = $saldoSebelum - $row['total'];

            WalletTransaction::create([
                'santri_id'      => $santri->id,
                'jenis'          => 'debit',
                'tipe'           => 'kantin',
                'nominal'        => $row['total'],
                'saldo_sebelum'  => $saldoSebelum,
                'saldo_sesudah'  => $saldoSesudah,
                'referensi_id'   => $transaksi->id,
                'referensi_tipe' => TransaksiKasir::class,
                'keterangan'     => 'Transaksi kantin offline #' . substr($row['client_uuid'], 0, 8),
            ]);
        }

        // Simpan log offline
        KantinOfflineTransaksi::create([
            'kantin_device_id'   => $device->id,
            'client_uuid'        => $row['client_uuid'],
            'transaksi_kasir_id' => $transaksi->id,
            'santri_id'          => $row['santri_id'] ?? null,
            'total'              => $row['total'],
            'items'              => $row['items'],
            'metode_bayar'       => $row['metode_bayar'],
            'transaksi_at'       => $row['transaksi_at'],
            'sync_status'        => 'synced',
            'synced_at'          => now(),
        ]);

        return $transaksi->id;
    }
}
