<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisTagihan;
use Illuminate\Http\Request;

class JenisTagihanController extends Controller
{
    public function index()
    {
        $jenis = JenisTagihan::latest('id')->get();
        return view('admin.jenis-tagihan.index', compact('jenis'));
    }

    public function create()
    {
        return view('admin.jenis-tagihan.form', ['jenis' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'             => 'required|string|max:100',
            'kelompok'         => 'required|in:bulanan,semesteran,tahunan,kegiatan,lainnya',
            'nominal'          => 'required|integer|min:0',
            'is_nominal_tetap' => 'required|boolean',
        ]);

        JenisTagihan::create([
            'nama'             => $request->nama,
            'kelompok'         => $request->kelompok,
            'nominal'          => $request->nominal,
            'is_nominal_tetap' => $request->is_nominal_tetap,
            'is_aktif'         => true,
        ]);

        return redirect()->route('admin.jenis-tagihan.index')
            ->with('success', "Jenis tagihan {$request->nama} berhasil ditambahkan.");
    }

    public function edit(JenisTagihan $jenisTagihan)
    {
        return view('admin.jenis-tagihan.form', ['jenis' => $jenisTagihan]);
    }

    public function update(Request $request, JenisTagihan $jenisTagihan)
    {
        $request->validate([
            'nama'             => 'required|string|max:100',
            'kelompok'         => 'required|in:bulanan,semesteran,tahunan,kegiatan,lainnya',
            'nominal'          => 'required|integer|min:0',
            'is_nominal_tetap' => 'required|boolean',
            'is_aktif'         => 'boolean',
        ]);

        $jenisTagihan->update([
            'nama'             => $request->nama,
            'kelompok'         => $request->kelompok,
            'nominal'          => $request->nominal,
            'is_nominal_tetap' => $request->is_nominal_tetap,
            'is_aktif'         => $request->boolean('is_aktif'),
        ]);

        return redirect()->route('admin.jenis-tagihan.index')
            ->with('success', "Jenis tagihan {$jenisTagihan->nama} diperbarui.");
    }

    public function destroy(JenisTagihan $jenisTagihan)
    {
        $jenisTagihan->update(['is_aktif' => false]);
        return back()->with('success', "{$jenisTagihan->nama} dinonaktifkan.");
    }
}
