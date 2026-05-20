<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RfidController extends Controller
{
    public function index(Request $request)
    {
        $santri = Santri::query()
            ->when($request->search, fn($q) => $q
                ->where('nama', 'ilike', "%{$request->search}%")
                ->orWhere('nis', 'ilike', "%{$request->search}%")
                ->orWhere('rfid_uid', 'ilike', "%{$request->search}%"))
            ->when($request->status === 'no-rfid', fn($q) => $q->whereNull('rfid_uid'))
            ->when($request->status === 'with-rfid', fn($q) => $q->whereNotNull('rfid_uid'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.rfid.index', compact('santri'));
    }

    public function edit(Santri $santri)
    {
        return view('admin.rfid.form', compact('santri'));
    }

    public function update(Request $request, Santri $santri)
    {
        $request->validate([
            'rfid_uid' => "required|string|max:100|unique:santri,rfid_uid,{$santri->id}",
        ]);

        $santri->update(['rfid_uid' => $request->rfid_uid]);

        return redirect()->route('admin.rfid.index')
            ->with('success', "RFID untuk {$santri->nama} berhasil diupdate.");
    }

    public function apiSync(Request $request): JsonResponse
    {
        $request->validate([
            'rfid_uid' => 'required|string',
            'action'   => 'required|in:register,scan',
            'nis'      => 'required_if:action,register|string',
        ]);

        if ($request->action === 'register') {
            $santri = Santri::where('nis', $request->nis)->first();
            if (!$santri) return response()->json(['success' => false, 'message' => 'Santri tidak ditemukan'], 404);
            
            $santri->update(['rfid_uid' => $request->rfid_uid]);
            return response()->json(['success' => true, 'message' => 'RFID terdaftar', 'data' => $santri]);
        }

        $santri = Santri::where('rfid_uid', $request->rfid_uid)->first();
        if (!$santri) return response()->json(['success' => false, 'message' => 'RFID tidak dikenali'], 404);

        return response()->json([
            'success' => true,
            'message' => 'Santri teridentifikasi',
            'data'    => [
                'id'    => $santri->id,
                'nama'  => $santri->nama,
                'nis'   => $santri->nis,
                'saldo' => $santri->saldo,
            ],
        ]);
    }

    public function destroy(Santri $santri)
    {
        $santri->update(['rfid_uid' => null]);
        return back()->with('success', "RFID berhasil dihapus.");
    }
}
