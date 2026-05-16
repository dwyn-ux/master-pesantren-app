<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->paginate(20);
        return view('admin.akademik.tahun-ajaran.index', compact('tahunAjaran'));
    }

    public function create()
    {
        return view('admin.akademik.tahun-ajaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:50',
            'semester'        => 'required|in:ganjil,genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active'       => 'boolean',
        ]);

        if (!empty($validated['is_active'])) {
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        }

        TahunAjaran::create($validated);

        return redirect()->route('admin.akademik.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.akademik.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:50',
            'semester'        => 'required|in:ganjil,genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active'       => 'boolean',
        ]);

        if (!empty($validated['is_active']) && !$tahunAjaran->is_active) {
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        }

        $tahunAjaran->update($validated);

        return redirect()->route('admin.akademik.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->is_active) {
            return back()->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif.');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.akademik.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
