<?php

namespace App\Http\Controllers\Admin;

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
                $q->where(function ($inner) use ($request) {
                    $inner->where('nama', 'like', "%{$request->search}%")
                          ->orWhere('nis', 'like', "%{$request->search}%");
                });
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $perizinan = $query->latest()->paginate(20)->withQueryString();
        return view('admin.perizinan.index', compact('perizinan'));
    }

    public function updateStatus(Request $request, Perizinan $perizinan)
    {
        $request->validate([
            'status' => 'required|in:pending,disetujui_ustadz,disetujui_kesantrian,ditolak,sedang_keluar,selesai,terlambat'
        ]);

        $perizinan->update([
            'status' => $request->status,
            'disetujui_oleh' => auth()->id()
        ]);

        if ($request->status === 'sedang_keluar' && !$perizinan->waktu_keluar_aktual) {
            $perizinan->update(['waktu_keluar_aktual' => now()]);
        } elseif (($request->status === 'selesai' || $request->status === 'terlambat') && !$perizinan->waktu_kembali_aktual) {
            $perizinan->update(['waktu_kembali_aktual' => now()]);
        }

        return back()->with('success', 'Status perizinan berhasil diperbarui.');
    }
}
