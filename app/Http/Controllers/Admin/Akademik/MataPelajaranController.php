<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\Kitab;
use App\Models\Akademik\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mataPelajaran = MataPelajaran::withCount('kitab')->orderBy('urutan')->get();
        return view('admin.akademik.mata-pelajaran.index', compact('mataPelajaran'));
    }

    public function create()
    {
        return view('admin.akademik.mata-pelajaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
            'kode'      => 'required|string|max:20|unique:mata_pelajaran,kode',
            'deskripsi' => 'nullable|string',
            'urutan'    => 'required|integer|min:0',
        ]);

        MataPelajaran::create($validated);

        return redirect()->route('admin.akademik.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        return view('admin.akademik.mata-pelajaran.edit', compact('mataPelajaran'));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
            'kode'      => 'required|string|max:20|unique:mata_pelajaran,kode,' . $mataPelajaran->id,
            'deskripsi' => 'nullable|string',
            'urutan'    => 'required|integer|min:0',
        ]);

        $mataPelajaran->update($validated);

        return redirect()->route('admin.akademik.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();

        return redirect()->route('admin.akademik.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function kitab(MataPelajaran $mataPelajaran)
    {
        $kitab = $mataPelajaran->kitab;
        return view('admin.akademik.mata-pelajaran.kitab', compact('mataPelajaran', 'kitab'));
    }

    public function storeKitab(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:200',
            'pengarang'     => 'nullable|string|max:200',
            'total_halaman' => 'nullable|integer|min:1',
        ]);

        $mataPelajaran->kitab()->create($validated);

        return back()->with('success', 'Kitab berhasil ditambahkan.');
    }

    public function destroyKitab(MataPelajaran $mataPelajaran, Kitab $kitab)
    {
        $kitab->delete();

        return back()->with('success', 'Kitab berhasil dihapus.');
    }
}
