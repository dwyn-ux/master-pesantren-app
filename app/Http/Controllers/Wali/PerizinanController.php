<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use Illuminate\Http\Request;

class PerizinanController extends Controller
{
    public function index()
    {
        $wali = auth()->user()->wali;
        $santri = $wali->santri;
        $santriIds = $santri->pluck('id');

        $perizinan = Perizinan::whereIn('santri_id', $santriIds)->latest()->paginate(10);

        return view('wali.perizinan.index', compact('perizinan', 'santri'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santri,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
        ]);

        $wali = auth()->user()->wali;
        if (!$wali->santri->contains('id', $request->santri_id)) {
            abort(403, 'Akses ditolak.');
        }

        Perizinan::create([
            'santri_id' => $request->santri_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'status' => 'pending',
            'diajukan_oleh' => 'wali',
        ]);

        return back()->with('success', 'Pengajuan izin berhasil dikirim dan menunggu persetujuan.');
    }
}
