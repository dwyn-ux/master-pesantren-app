<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FingerprintController extends Controller
{
    /**
     * Daftar santri dan status fingerprint mereka
     */
    public function index(Request $request)
    {
        $santri = Santri::query()
            ->when($request->search, fn($q) => $q
                ->where('nama', 'like', "%{$request->search}%")
                ->orWhere('nis', 'like', "%{$request->search}%"))
            ->when($request->status === 'no-fingerprint', fn($q) => $q->whereNull('fingerprint_id'))
            ->when($request->status === 'with-fingerprint', fn($q) => $q->whereNotNull('fingerprint_id'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.fingerprint.index', compact('santri'));
    }

    /**
     * Form edit/assign fingerprint
     */
    public function edit(Santri $santri)
    {
        return view('admin.fingerprint.form', compact('santri'));
    }

    /**
     * Update fingerprint ID
     */
    public function update(Request $request, Santri $santri)
    {
        $request->validate([
            'fingerprint_id' => "required|string|max:100|unique:santri,fingerprint_id,{$santri->id}",
        ]);

        $santri->update(['fingerprint_id' => $request->fingerprint_id]);

        return redirect()->route('admin.fingerprint.index')
            ->with('success', "Fingerprint untuk {$santri->nama} berhasil diupdate.");
    }

    /**
     * API endpoint untuk hardware reader
     * Hardware mengirim: POST /api/fingerprint/sync
     * Body: { "device_id": "...", "fingerprint_id": "...", "action": "register|scan" }
     */
    public function apiSync(Request $request): JsonResponse
    {
        $request->validate([
            'device_id'      => 'required|string',
            'fingerprint_id' => 'required|string',
            'action'         => 'required|in:register,scan',
            'timestamp'      => 'required|integer',
        ]);

        // TODO: Verify device signature/token untuk security

        if ($request->action === 'register') {
            return $this->registerFingerprintFromDevice($request);
        }

        if ($request->action === 'scan') {
            return $this->scanFingerprintFromDevice($request);
        }

        return response()->json(['success' => false, 'message' => 'Unknown action'], 400);
    }

    /**
     * Register fingerprint baru dari hardware
     */
    private function registerFingerprintFromDevice(Request $request): JsonResponse
    {
        $santri = Santri::where('nis', $request->nis)->first();

        if (!$santri) {
            return response()->json(['success' => false, 'message' => 'Santri tidak ditemukan'], 404);
        }

        if ($santri->fingerprint_id) {
            return response()->json(['success' => false, 'message' => 'Santri sudah punya fingerprint'], 400);
        }

        $santri->update(['fingerprint_id' => $request->fingerprint_id]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint berhasil terdaftar',
            'data'    => ['santri_id' => $santri->id, 'fingerprint_id' => $santri->fingerprint_id],
        ]);
    }

    /**
     * Scan fingerprint dari hardware (untuk transaksi kasir dll)
     */
    private function scanFingerprintFromDevice(Request $request): JsonResponse
    {
        $santri = Santri::where('fingerprint_id', $request->fingerprint_id)->first();

        if (!$santri) {
            return response()->json(['success' => false, 'message' => 'Fingerprint tidak dikenali'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Santri teridentifikasi',
            'data'    => [
                'santri_id' => $santri->id,
                'nama'      => $santri->nama,
                'nis'       => $santri->nis,
                'saldo'     => $santri->saldo,
            ],
        ]);
    }

    /**
     * Reset fingerprint (hapus)
     */
    public function destroy(Santri $santri)
    {
        $nama = $santri->nama;
        $santri->update(['fingerprint_id' => null]);

        return back()->with('success', "Fingerprint {$nama} berhasil dihapus.");
    }
}
