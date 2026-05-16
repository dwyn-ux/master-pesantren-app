<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Halaqah;
use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerizinanController extends Controller
{
    private function getSantriIds($ustadz): \Illuminate\Support\Collection
    {
        $halaqahIds = Halaqah::where('ustadz_id', $ustadz->id)->pluck('id');
        return DB::table('halaqah_santri')
            ->whereIn('halaqah_id', $halaqahIds)
            ->pluck('santri_id');
    }

    public function index()
    {
        $ustadz = auth()->user()->ustadz;

        $santriIds = $this->getSantriIds($ustadz);

        $perizinan = Perizinan::with('santri')
            ->whereIn('santri_id', $santriIds)
            ->latest()
            ->paginate(15);

        return view('ustadz.perizinan.index', compact('perizinan'));
    }

    public function updateStatus(Request $request, Perizinan $perizinan)
    {
        $request->validate([
            'status' => 'required|in:pending,disetujui_ustadz,ditolak'
        ]);

        $ustadz    = auth()->user()->ustadz;
        $santriIds = $this->getSantriIds($ustadz);

        if (!$santriIds->contains($perizinan->santri_id)) {
            abort(403);
        }

        $perizinan->update([
            'status' => $request->status,
            'disetujui_oleh' => auth()->id()
        ]);

        return back()->with('success', 'Status perizinan santri halaqah berhasil diperbarui.');
    }
}
