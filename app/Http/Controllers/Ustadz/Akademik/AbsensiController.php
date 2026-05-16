<?php

namespace App\Http\Controllers\Ustadz\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\AbsensiPelajaran;
use App\Models\Akademik\JadwalPelajaran;
use App\Models\Akademik\JurnalMengajar;
use App\Models\Santri;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $ustadzId = auth()->user()->ustadz?->id;

        $jadwal = JadwalPelajaran::where('ustadz_id', $ustadzId)
            ->where('tahun_ajaran_id', \App\Models\Akademik\TahunAjaran::active()?->id)
            ->with('kelas', 'mataPelajaran')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $absensi = collect();
        if ($request->filled('jadwal_id') && $request->filled('tanggal')) {
            $absensi = AbsensiPelajaran::where('jadwal_pelajaran_id', $request->jadwal_id)
                ->where('tanggal', $request->tanggal)
                ->with('santri')
                ->get();
        }

        return view('ustadz.akademik.absensi.index', compact('jadwal', 'absensi', 'request'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal'   => 'required|date',
            'santri'    => 'required|array',
            'santri.*'  => 'required|exists:santri,id',
        ]);

        foreach ($validated['santri'] as $santriId) {
            AbsensiPelajaran::updateOrCreate(
                ['jadwal_pelajaran_id' => $validated['jadwal_id'], 'santri_id' => $santriId, 'tanggal' => $validated['tanggal']],
                ['status' => $request->input('status.' . $santriId, 'hadir')]
            );
        }

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function jurnal(Request $request)
    {
        $ustadzId = auth()->user()->ustadz?->id;

        $jadwal = JadwalPelajaran::where('ustadz_id', $ustadzId)
            ->where('tahun_ajaran_id', \App\Models\Akademik\TahunAjaran::active()?->id)
            ->with('kelas', 'mataPelajaran')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $jurnal = collect();
        if ($request->filled('jadwal_id') && $request->filled('tanggal')) {
            $jurnal = JurnalMengajar::where('jadwal_pelajaran_id', $request->jadwal_id)
                ->where('tanggal', $request->tanggal)
                ->with('ustadz')
                ->first();
        }

        return view('ustadz.akademik.jurnal.index', compact('jadwal', 'jurnal', 'request'));
    }

    public function storeJurnal(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id'     => 'required|exists:jadwal_pelajaran,id',
            'tanggal'       => 'required|date',
            'materi'        => 'required|string',
            'halaman_kitab' => 'nullable|string|max:50',
            'pr'            => 'nullable|string',
            'catatan'       => 'nullable|string',
        ]);

        JurnalMengajar::updateOrCreate(
            ['jadwal_pelajaran_id' => $validated['jadwal_id'], 'ustadz_id' => auth()->user()->ustadz?->id, 'tanggal' => $validated['tanggal']],
            $validated
        );

        return back()->with('success', 'Jurnal mengajar berhasil disimpan.');
    }
}
