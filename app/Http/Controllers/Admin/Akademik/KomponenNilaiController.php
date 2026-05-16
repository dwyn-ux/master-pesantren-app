<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\KomponenNilai;
use App\Models\Akademik\MataPelajaran;
use App\Models\Akademik\TahunAjaran;
use Illuminate\Http\Request;

class KomponenNilaiController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $mapel = MataPelajaran::orderBy('urutan')->get();

        $komponen = collect();
        $totalBobot = 0;
        if ($request->filled('tahun_ajaran_id') && $request->filled('mata_pelajaran_id')) {
            $komponen = KomponenNilai::where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
                ->orderBy('urutan')
                ->get();
            $totalBobot = $komponen->sum('bobot');
        }

        return view('admin.akademik.komponen-nilai.index', compact('tahunAjaran', 'mapel', 'komponen', 'totalBobot', 'request'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'tahun_ajaran_id'   => 'required|exists:tahun_ajaran,id',
            'nama'              => 'required|string|max:50',
            'bobot'             => 'required|integer|min:0|max:100',
            'urutan'            => 'required|integer|min:0',
        ]);

        KomponenNilai::create($validated);

        return back()->with('success', 'Komponen nilai berhasil ditambahkan.');
    }

    public function update(Request $request, KomponenNilai $komponenNilai)
    {
        $validated = $request->validate([
            'nama'   => 'required|string|max:50',
            'bobot'  => 'required|integer|min:0|max:100',
            'urutan' => 'required|integer|min:0',
        ]);

        $komponenNilai->update($validated);

        return back()->with('success', 'Komponen nilai berhasil diperbarui.');
    }

    public function destroy(KomponenNilai $komponenNilai)
    {
        $komponenNilai->delete();

        return back()->with('success', 'Komponen nilai berhasil dihapus.');
    }
}
