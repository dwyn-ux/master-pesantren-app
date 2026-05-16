<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\Tingkat;
use App\Models\Ustadz;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with(['tingkat', 'waliKelas'])->orderBy('tingkat_id')->get();
        return view('admin.akademik.kelas.index', compact('kelas'));
    }

    public function create()
    {
        $tingkat = Tingkat::orderBy('urutan')->get();
        $ustadz  = Ustadz::with('user')->get();
        return view('admin.akademik.kelas.create', compact('tingkat', 'ustadz'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tingkat_id'     => 'required|exists:tingkat,id',
            'nama'           => 'required|string|max:100',
            'wali_kelas_id'  => 'nullable|exists:ustadz,id',
            'kapasitas'      => 'required|integer|min:1',
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.akademik.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        $tingkat = Tingkat::orderBy('urutan')->get();
        $ustadz  = Ustadz::with('user')->get();
        return view('admin.akademik.kelas.edit', compact('kelas', 'tingkat', 'ustadz'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'tingkat_id'     => 'required|exists:tingkat,id',
            'nama'           => 'required|string|max:100',
            'wali_kelas_id'  => 'nullable|exists:ustadz,id',
            'kapasitas'      => 'required|integer|min:1',
        ]);

        $kelas->update($validated);

        return redirect()->route('admin.akademik.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()->route('admin.akademik.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
