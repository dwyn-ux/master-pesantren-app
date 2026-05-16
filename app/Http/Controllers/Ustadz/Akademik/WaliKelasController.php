<?php

namespace App\Http\Controllers\Ustadz\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\CatatanWaliKelas;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\NilaiSikap;
use App\Models\Akademik\TahunAjaran;
use App\Models\Santri;
use Illuminate\Http\Request;

class WaliKelasController extends Controller
{
    public function index()
    {
        $ustadzId = auth()->user()->ustadz?->id;

        $kelas = Kelas::where('wali_kelas_id', $ustadzId)
            ->with(['tingkat', 'santri.santri'])
            ->get();

        return view('ustadz.akademik.wali-kelas.index', compact('kelas'));
    }

    public function show(Request $request, Kelas $kelas)
    {
        $ustadzId = auth()->user()->ustadz?->id;
        if ($kelas->wali_kelas_id !== $ustadzId) {
            abort(403, 'Anda bukan wali kelas ini.');
        }

        $tahunAjaran = TahunAjaran::active();
        if (!$tahunAjaran) {
            return view('ustadz.akademik.wali-kelas.show', [
                'kelas' => $kelas,
                'santri' => collect(),
                'tahunAjaran' => null,
                'noTahunAjaran' => true,
            ]);
        }

        $santri = Santri::whereHas('kelasSantri', function ($q) use ($kelas, $tahunAjaran) {
            $q->where('kelas_id', $kelas->id)->where('tahun_ajaran_id', $tahunAjaran->id);
        })->with(['wali'])->get();

        return view('ustadz.akademik.wali-kelas.show', compact('kelas', 'santri', 'tahunAjaran'));
    }

    public function sikap(Request $request, Kelas $kelas, Santri $santri)
    {
        $ustadzId = auth()->user()->ustadz?->id;
        if ($kelas->wali_kelas_id !== $ustadzId) {
            abort(403);
        }

        $tahunAjaran = TahunAjaran::active();
        if (!$tahunAjaran) {
            return back()->with('error', 'Belum ada tahun ajaran aktif.');
        }

        $sikapDefault = ['Kejujuran', 'Kedisiplinan', 'Ibadah', 'Kebersihan', 'Sopan Santun'];
        $existing = NilaiSikap::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->get()
            ->keyBy('aspek');

        $catatan = CatatanWaliKelas::where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->first();

        return view('ustadz.akademik.wali-kelas.sikap', compact('kelas', 'santri', 'tahunAjaran', 'sikapDefault', 'existing', 'catatan'));
    }

    public function storeSikap(Request $request, Kelas $kelas, Santri $santri)
    {
        $ustadzId = auth()->user()->ustadz?->id;
        if ($kelas->wali_kelas_id !== $ustadzId) {
            abort(403);
        }

        $tahunAjaran = TahunAjaran::active();
        if (!$tahunAjaran) {
            return back()->with('error', 'Belum ada tahun ajaran aktif.');
        }

        $validated = $request->validate([
            'sikap'    => 'array',
            'catatan'  => 'nullable|string',
        ]);

        // Save nilai sikap
        if (!empty($validated['sikap'])) {
            foreach ($validated['sikap'] as $aspek => $data) {
                if (empty($data['predikat']) && empty($data['deskripsi'])) continue;
                NilaiSikap::updateOrCreate(
                    ['santri_id' => $santri->id, 'tahun_ajaran_id' => $tahunAjaran->id, 'aspek' => $aspek],
                    ['predikat' => $data['predikat'] ?? null, 'deskripsi' => $data['deskripsi'] ?? null]
                );
            }
        }

        // Save catatan wali kelas
        CatatanWaliKelas::updateOrCreate(
            ['santri_id' => $santri->id, 'tahun_ajaran_id' => $tahunAjaran->id],
            ['ustadz_id' => $ustadzId, 'catatan' => $validated['catatan'] ?? null]
        );

        return back()->with('success', 'Penilaian sikap & catatan berhasil disimpan.');
    }
}
