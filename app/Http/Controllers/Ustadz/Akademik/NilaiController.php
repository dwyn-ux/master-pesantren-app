<?php

namespace App\Http\Controllers\Ustadz\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\KomponenNilai;
use App\Models\Akademik\Kkm;
use App\Models\Akademik\Nilai;
use App\Models\Akademik\NilaiAkhir;
use App\Models\Akademik\TahunAjaran;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::active();

        if (!$tahunAjaran) {
            return view('ustadz.akademik.nilai.index', [
                'komponen' => collect(),
                'santri'   => collect(),
                'request'  => $request,
                'noTahunAjaran' => true,
            ]);
        }

        $komponen = KomponenNilai::where('tahun_ajaran_id', $tahunAjaran->id)
            ->with('mataPelajaran')
            ->get()
            ->groupBy('mata_pelajaran_id');

        $santri = collect();
        if ($request->filled('mata_pelajaran_id')) {
            $santri = \App\Models\Santri::whereHas('kelasSantri', function ($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran_id', $tahunAjaran->id);
            })->with(['kelasSantri.kelas', 'wali'])->get();
        }

        return view('ustadz.akademik.nilai.index', compact('komponen', 'santri', 'request'));
    }

    public function store(Request $request)
    {
        $tahunAjaran = TahunAjaran::active();
        if (!$tahunAjaran) {
            return back()->with('error', 'Belum ada tahun ajaran aktif.');
        }

        $validated = $request->validate([
            'komponen_id' => 'required|exists:komponen_nilai,id',
            'santri'      => 'required|array',
            'santri.*'    => 'required|exists:santri,id',
        ]);

        foreach ($validated['santri'] as $santriId) {
            $nilai = $request->input('nilai.' . $santriId, 0);
            Nilai::updateOrCreate(
                ['santri_id' => $santriId, 'komponen_nilai_id' => $validated['komponen_id'], 'tahun_ajaran_id' => $tahunAjaran->id],
                ['nilai' => $nilai]
            );
        }

        $this->hitungNilaiAkhir($validated['komponen_id']);

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    protected function hitungNilaiAkhir($komponenId)
    {
        $komponen = KomponenNilai::with('mataPelajaran')->find($komponenId);
        $tahunAjaran = TahunAjaran::active();

        if (!$tahunAjaran || !$komponen) return;

        $komponenList = KomponenNilai::where('mata_pelajaran_id', $komponen->mata_pelajaran_id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->get();

        $totalBobot = $komponenList->sum('bobot');
        if ($totalBobot == 0) return;

        $santriIds = $komponenList->pluck('nilai.santri_id')->flatten()->unique();

        foreach ($santriIds as $santriId) {
            $nilaiTotal = 0;
            foreach ($komponenList as $k) {
                $n = Nilai::where('santri_id', $santriId)
                    ->where('komponen_nilai_id', $k->id)
                    ->where('tahun_ajaran_id', $tahunAjaran->id)
                    ->first();
                if ($n) {
                    $nilaiTotal += ($n->nilai * $k->bobot) / 100;
                }
            }

            $predikat = NilaiAkhir::predikatFor($nilaiTotal);

            NilaiAkhir::updateOrCreate(
                ['santri_id' => $santriId, 'mata_pelajaran_id' => $komponen->mata_pelajaran_id, 'tahun_ajaran_id' => $tahunAjaran->id],
                ['nilai_akhir' => $nilaiTotal, 'predikat' => $predikat]
            );
        }
    }
}
