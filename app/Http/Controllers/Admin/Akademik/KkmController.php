<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\Kkm;
use App\Models\Akademik\MataPelajaran;
use App\Models\Akademik\TahunAjaran;
use App\Models\Akademik\Tingkat;
use Illuminate\Http\Request;

class KkmController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $tingkat = Tingkat::orderBy('urutan')->get();
        $mapel = MataPelajaran::orderBy('urutan')->get();

        $kkmData = [];
        if ($request->filled('tahun_ajaran_id')) {
            $rows = Kkm::where('tahun_ajaran_id', $request->tahun_ajaran_id)->get();
            foreach ($rows as $r) {
                $kkmData["{$r->tingkat_id}_{$r->mata_pelajaran_id}"] = $r->nilai_kkm;
            }
        }

        return view('admin.akademik.kkm.index', compact('tahunAjaran', 'tingkat', 'mapel', 'kkmData', 'request'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kkm'             => 'required|array',
        ]);

        foreach ($validated['kkm'] as $key => $nilai) {
            if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) continue;

            [$tingkatId, $mapelId] = explode('_', $key);

            Kkm::updateOrCreate(
                [
                    'tahun_ajaran_id'   => $validated['tahun_ajaran_id'],
                    'tingkat_id'        => $tingkatId,
                    'mata_pelajaran_id' => $mapelId,
                ],
                ['nilai_kkm' => $nilai]
            );
        }

        return back()->with('success', 'KKM berhasil disimpan.');
    }
}
