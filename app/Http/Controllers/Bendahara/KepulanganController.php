<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\SesiKepulangan;
use App\Models\KepulanganSantri;
use Illuminate\Http\Request;

class KepulanganController extends Controller
{
    public function index(Request $request)
    {
        $sesiAktif = SesiKepulangan::where('is_active', true)->latest()->first();
        if (!$sesiAktif) {
            return view('bendahara.kepulangan.index', ['sesiAktif' => null, 'kepulangan' => collect()]);
        }

        $query = $sesiAktif->kepulanganSantri()->with('santri');
        if ($request->search) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nis', 'like', "%{$request->search}%");
            });
        }
        
        $kepulangan = $query->paginate(20);
        return view('bendahara.kepulangan.index', compact('sesiAktif', 'kepulangan'));
    }

    public function acc(Request $request, KepulanganSantri $kepulangan)
    {
        $request->validate([
            'tanggal_janji_bayar' => 'required|date|after_or_equal:today',
            'keterangan_bendahara' => 'nullable|string'
        ]);

        $kepulangan->update([
            'status_administrasi' => 'acc_bendahara',
            'tanggal_janji_bayar' => $request->tanggal_janji_bayar,
            'keterangan_bendahara' => $request->keterangan_bendahara
        ]);

        return back()->with('success', 'Surat Kesanggupan telah di-ACC. Santri diizinkan pulang.');
    }
}
