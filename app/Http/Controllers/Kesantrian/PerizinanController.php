<?php

namespace App\Http\Controllers\Kesantrian;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use Illuminate\Http\Request;

class PerizinanController extends Controller
{
    public function index(Request $request)
    {
        $query = Perizinan::with('santri');

        if ($request->search) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nis', 'like', "%{$request->search}%");
            });
        }

        $status = $request->status ?: 'disetujui_ustadz';
        $query->where('status', $status);

        $perizinan = $query->latest()->paginate(20);
        return view('kesantrian.perizinan.index', compact('perizinan', 'status'));
    }

    public function updateStatus(Request $request, Perizinan $perizinan)
    {
        $request->validate([
            'status' => 'required|in:disetujui_kesantrian,ditolak',
        ]);

        if ($perizinan->status !== 'disetujui_ustadz') {
            return back()->with('error', 'Hanya pengajuan yang sudah ACC Ustadz yang bisa diproses.');
        }

        $perizinan->update([
            'status'        => $request->status,
            'disetujui_oleh' => auth()->id(),
        ]);

        return back()->with('success', 'Status perizinan berhasil diperbarui.');
    }
}
