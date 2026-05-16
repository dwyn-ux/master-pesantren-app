<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\KenaikanKelas;
use App\Models\Akademik\TahunAjaran;
use Illuminate\Http\Request;

class KenaikanKelasController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $kenaikan = collect();

        if ($request->filled('tahun_ajaran_id')) {
            $kenaikan = KenaikanKelas::where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->with(['santri.wali', 'kelasAsal', 'kelasTujuan'])
                ->get();
        }

        return view('admin.akademik.kenaikan.index', compact('tahunAjaran', 'kenaikan', 'request'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'santri'          => 'required|array',
            'santri.*'        => 'required|exists:santri,id',
            'keputusan'       => 'required|array',
            'keputusan.*'     => 'required|in:naik,tidak_naik,naik_bersyarat',
            'catatan'         => 'nullable|array',
        ]);

        foreach ($validated['santri'] as $santriId) {
            $keputusan = $validated['keputusan'][$santriId];
            $catatan = $validated['catatan'][$santriId] ?? null;

            $santri = \App\Models\Santri::find($santriId);
            $kelasAsal = $santri->kelasSantri->firstWhere('tahun_ajaran_id', $validated['tahun_ajaran_id'])?->kelas;

            KenaikanKelas::updateOrCreate(
                ['santri_id' => $santriId, 'tahun_ajaran_id' => $validated['tahun_ajaran_id']],
                [
                    'kelas_asal_id' => $kelasAsal?->id,
                    'keputusan'     => $keputusan,
                    'catatan'       => $catatan,
                ]
            );
        }

        return back()->with('success', 'Keputusan kenaikan kelas berhasil disimpan.');
    }

    public function execute(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_asal' => 'required|exists:tahun_ajaran,id',
            'tahun_ajaran_tujuan' => 'required|exists:tahun_ajaran,id',
        ]);

        if ($validated['tahun_ajaran_asal'] == $validated['tahun_ajaran_tujuan']) {
            return back()->with('error', 'Tahun ajaran asal dan tujuan tidak boleh sama.');
        }

        $kenaikan = KenaikanKelas::where('tahun_ajaran_id', $validated['tahun_ajaran_asal'])
            ->where('keputusan', 'naik')
            ->with('santri')
            ->get();

        foreach ($kenaikan as $k) {
            $santri = $k->santri;
            $kelasAsal = $k->kelasAsal;
            $tingkatTujuan = $kelasAsal->tingkat_id + 1;

            $kelasTujuan = \App\Models\Akademik\Kelas::where('tingkat_id', $tingkatTujuan)->first();
            if (!$kelasTujuan) {
                continue;
            }

            \App\Models\Akademik\KelasSantri::updateOrCreate(
                ['santri_id' => $santri->id, 'kelas_id' => $kelasTujuan->id, 'tahun_ajaran_id' => $validated['tahun_ajaran_tujuan']],
                ['created_at' => now()]
            );
        }

        return back()->with('success', 'Eksekusi kenaikan kelas berhasil.');
    }
}
