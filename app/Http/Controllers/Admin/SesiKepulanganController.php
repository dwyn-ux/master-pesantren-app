<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SesiKepulangan;
use App\Models\KepulanganSantri;
use App\Models\Santri;
use Illuminate\Http\Request;

class SesiKepulanganController extends Controller
{
    public function index()
    {
        $sesi = SesiKepulangan::withCount('kepulanganSantri')->latest()->paginate(10);
        return view('admin.sesi_kepulangan.index', compact('sesi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'tanggal_pulang' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pulang',
        ]);

        $sesi = SesiKepulangan::create($request->all());

        // Otomatis masukkan semua santri aktif ke sesi ini
        $santri = Santri::where('is_aktif', true)->get();
        $kepulanganData = [];
        foreach ($santri as $s) {
            $kepulanganData[] = [
                'sesi_kepulangan_id' => $sesi->id,
                'santri_id' => $s->id,
                'status_administrasi' => 'belum_lunas',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Chunk insert untuk performa
        foreach (array_chunk($kepulanganData, 500) as $chunk) {
            KepulanganSantri::insert($chunk);
        }

        return back()->with('success', 'Sesi Kepulangan berhasil dibuat dan seluruh santri aktif telah didaftarkan.');
    }

    public function show(SesiKepulangan $sesi_kepulangan)
    {
        $kepulangan = $sesi_kepulangan->kepulanganSantri()->with('santri')->paginate(50);
        return view('admin.sesi_kepulangan.show', compact('sesi_kepulangan', 'kepulangan'));
    }

    public function update(Request $request, SesiKepulangan $sesi_kepulangan)
    {
        $sesi_kepulangan->update(['is_active' => $request->has('is_active')]);
        return back()->with('success', 'Status sesi diperbarui.');
    }
}
