<?php

namespace App\Http\Controllers\Api\Kantin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    /**
     * Lookup santri real-time via RFID/fingerprint/NIS/nama.
     * GET /api/kantin/santri/lookup?rfid=AABBCC
     * GET /api/kantin/santri/lookup?nis=S001
     * GET /api/kantin/santri/lookup?fingerprint=FP_001
     * GET /api/kantin/santri/lookup?q=ahmad   (fuzzy nama)
     */
    public function lookup(Request $request)
    {
        $rfid        = $request->query('rfid');
        $nis         = $request->query('nis');
        $fingerprint = $request->query('fingerprint');
        $q           = $request->query('q');

        $query = Santri::query()->where('is_aktif', true)
            ->select('id', 'nis', 'nik', 'nama', 'kelas', 'jenis_kelamin',
                     'fingerprint_id', 'rfid_uid', 'saldo',
                     'tipe_limit', 'nominal_limit');

        if ($rfid) {
            $santri = $query->where('rfid_uid', $rfid)->first();
            return $santri
                ? response()->json(['matched_by' => 'rfid', 'santri' => $santri])
                : response()->json(['matched_by' => null, 'santri' => null], 404);
        }

        if ($fingerprint) {
            $santri = $query->where('fingerprint_id', $fingerprint)->first();
            return $santri
                ? response()->json(['matched_by' => 'fingerprint', 'santri' => $santri])
                : response()->json(['matched_by' => null, 'santri' => null], 404);
        }

        if ($nis) {
            $santri = $query->where('nis', $nis)->first();
            return $santri
                ? response()->json(['matched_by' => 'nis', 'santri' => $santri])
                : response()->json(['matched_by' => null, 'santri' => null], 404);
        }

        if ($q) {
            $results = $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('nis', 'like', "%{$q}%")
                  ->orWhere('kelas', 'like', "%{$q}%");
            })->limit(20)->get();

            return response()->json([
                'matched_by' => 'search',
                'count'      => $results->count(),
                'santri'     => $results,
            ]);
        }

        return response()->json([
            'message' => 'Minimal 1 parameter: rfid, fingerprint, nis, atau q',
        ], 422);
    }
}
