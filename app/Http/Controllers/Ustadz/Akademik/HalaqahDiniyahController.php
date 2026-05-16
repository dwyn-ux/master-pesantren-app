<?php

namespace App\Http\Controllers\Ustadz\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\HalaqahDiniyah;
use App\Models\Akademik\HalaqahDiniyahSantri;
use App\Models\Akademik\Kitab;
use App\Models\Akademik\SetoranDiniyah;
use App\Models\Santri;
use Illuminate\Http\Request;

class HalaqahDiniyahController extends Controller
{
    public function index()
    {
        $halaqah = HalaqahDiniyah::where('ustadz_id', auth()->user()->ustadz?->id)
            ->with('kitab')
            ->withCount('santri')
            ->get();
        return view('ustadz.akademik.halaqah-diniyah.index', compact('halaqah'));
    }

    public function santri(HalaqahDiniyah $halaqah)
    {
        $santri = $halaqah->santri()->with('santri.wali')->get();
        return view('ustadz.akademik.halaqah-diniyah.santri', compact('halaqah', 'santri'));
    }

    public function setoran(Request $request, HalaqahDiniyah $halaqah)
    {
        $santri = $halaqah->santri()->with('santri')->get();

        $setoran = collect();
        if ($request->filled('santri_id') && $request->filled('tanggal')) {
            $setoran = SetoranDiniyah::where('halaqah_diniyah_id', $halaqah->id)
                ->where('santri_id', $request->santri_id)
                ->where('tanggal', $request->tanggal)
                ->first();
        }

        return view('ustadz.akademik.halaqah-diniyah.setoran', compact('halaqah', 'santri', 'setoran', 'request'));
    }

    public function storeSetoran(Request $request, HalaqahDiniyah $halaqah)
    {
        $validated = $request->validate([
            'santri_id'     => 'required|exists:santri,id',
            'tanggal'       => 'required|date',
            'halaman_mulai' => 'required|string|max:20',
            'halaman_selesai' => 'required|string|max:20',
            'jenis'         => 'required|in:bandongan,sorogan',
            'nilai'         => 'nullable|in:mumtaz,jayyid_jiddan,jayyid,maqbul',
            'catatan'       => 'nullable|string',
        ]);

        SetoranDiniyah::create([
            'halaqah_diniyah_id' => $halaqah->id,
            'santri_id'          => $validated['santri_id'],
            'ustadz_id'          => auth()->user()->ustadz?->id,
            'tanggal'            => $validated['tanggal'],
            'halaman_mulai'      => $validated['halaman_mulai'],
            'halaman_selesai'    => $validated['halaman_selesai'],
            'jenis'              => $validated['jenis'],
            'nilai'              => $validated['nilai'],
            'catatan'            => $validated['catatan'],
        ]);

        return back()->with('success', 'Setoran diniyah berhasil disimpan.');
    }

    public function khataman(Request $request, HalaqahDiniyah $halaqah)
    {
        $kitab = $halaqah->kitab;
        $santri = $halaqah->santri()->with('santri.wali')->get();

        return view('ustadz.akademik.halaqah-diniyah.khataman', compact('halaqah', 'kitab', 'santri'));
    }

    public function storeKhataman(Request $request, HalaqahDiniyah $halaqah)
    {
        $validated = $request->validate([
            'santri_id'       => 'required|exists:santri,id',
            'tanggal_khatam'  => 'required|date',
            'catatan'         => 'nullable|string',
        ]);

        \App\Models\Akademik\KhatamanKitab::create([
            'santri_id'       => $validated['santri_id'],
            'kitab_id'        => $halaqah->kitab_id,
            'ustadz_id'       => auth()->user()->ustadz?->id,
            'tanggal_khatam'  => $validated['tanggal_khatam'],
            'catatan'         => $validated['catatan'],
        ]);

        return back()->with('success', 'Khataman kitab berhasil dicatat.');
    }
}
