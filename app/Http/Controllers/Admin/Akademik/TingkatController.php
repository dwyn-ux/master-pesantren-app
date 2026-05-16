<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\Tingkat;
use Illuminate\Http\Request;

class TingkatController extends Controller
{
    public function index()
    {
        $tingkat = Tingkat::orderBy('urutan')->get();
        return view('admin.akademik.tingkat.index', compact('tingkat'));
    }

    public function create()
    {
        return view('admin.akademik.tingkat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'    => 'required|string|max:100',
            'urutan'  => 'required|integer|min:0',
        ]);

        Tingkat::create($validated);

        return redirect()->route('admin.akademik.tingkat.index')
            ->with('success', 'Tingkat berhasil ditambahkan.');
    }

    public function edit(Tingkat $tingkat)
    {
        return view('admin.akademik.tingkat.edit', compact('tingkat'));
    }

    public function update(Request $request, Tingkat $tingkat)
    {
        $validated = $request->validate([
            'nama'    => 'required|string|max:100',
            'urutan'  => 'required|integer|min:0',
        ]);

        $tingkat->update($validated);

        return redirect()->route('admin.akademik.tingkat.index')
            ->with('success', 'Tingkat berhasil diperbarui.');
    }

    public function destroy(Tingkat $tingkat)
    {
        $tingkat->delete();

        return redirect()->route('admin.akademik.tingkat.index')
            ->with('success', 'Tingkat berhasil dihapus.');
    }
}
