<?php

namespace App\Http\Controllers\Api\Kantin;

use App\Http\Controllers\Controller;
use App\Models\KantinSyncLog;
use App\Models\Produk;
use App\Models\Santri;
use App\Models\TarifLaundry;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    /**
     * PULL: ambil data master dari server ke device.
     * Optional query param: since (datetime ISO) untuk delta sync.
     */
    public function pull(Request $request)
    {
        $device = $request->attributes->get('kantin_device');
        $since = $request->query('since'); // ISO datetime

        // Ambil santri aktif dengan saldo + identifier
        $santriQuery = Santri::where('is_aktif', true)
            ->select('id', 'nis', 'nik', 'nama', 'kelas', 'jenis_kelamin',
                     'fingerprint_id', 'rfid_uid', 'saldo',
                     'tipe_limit', 'nominal_limit', 'updated_at');

        if ($since) {
            $santriQuery->where('updated_at', '>=', $since);
        }
        $santri = $santriQuery->get();

        // Produk milik outlet device ini
        $produkQuery = Produk::where('outlet_id', $device->outlet_id)
            ->select('id', 'nama', 'harga', 'stok', 'is_aktif', 'updated_at');

        if ($since) {
            $produkQuery->where('updated_at', '>=', $since);
        }
        $produk = $produkQuery->get();

        // Tarif laundry (kalau outlet tipe laundry)
        $tarif = collect();
        if ($device->outlet?->tipe === 'laundry') {
            $tarif = TarifLaundry::where('outlet_id', $device->outlet_id)->get();
        }

        $device->update(['last_sync_at' => now()]);

        KantinSyncLog::create([
            'kantin_device_id' => $device->id,
            'arah'             => 'pull',
            'jenis'            => 'full',
            'jumlah_record'    => $santri->count() + $produk->count() + $tarif->count(),
            'detail'           => [
                'santri'  => $santri->count(),
                'produk'  => $produk->count(),
                'tarif'   => $tarif->count(),
                'since'   => $since,
            ],
            'status'           => 'success',
        ]);

        return response()->json([
            'server_time' => now()->toIso8601String(),
            'data' => [
                'santri'  => $santri,
                'produk'  => $produk,
                'tarif'   => $tarif,
            ],
            'meta' => [
                'total_santri' => $santri->count(),
                'total_produk' => $produk->count(),
                'total_tarif'  => $tarif->count(),
            ],
        ]);
    }

    /**
     * Snapshot saldo santri (lightweight, dipanggil pas refresh kartu / scan).
     * Query: ?ids=1,2,3 atau ?nis=S001,S002 atau ?rfid=AABBCC
     */
    public function saldoSnapshot(Request $request)
    {
        $ids   = $request->query('ids');
        $nis   = $request->query('nis');
        $rfids = $request->query('rfid');

        $query = Santri::query()->where('is_aktif', true)
            ->select('id', 'nis', 'rfid_uid', 'fingerprint_id', 'nama', 'saldo', 'tipe_limit', 'nominal_limit');

        if ($ids) {
            $query->whereIn('id', explode(',', $ids));
        } elseif ($nis) {
            $query->whereIn('nis', explode(',', $nis));
        } elseif ($rfids) {
            $query->whereIn('rfid_uid', explode(',', $rfids));
        } else {
            return response()->json(['message' => 'Minimal 1 parameter: ids/nis/rfid'], 422);
        }

        return response()->json([
            'server_time' => now()->toIso8601String(),
            'data'        => $query->limit(500)->get(),
        ]);
    }
}
