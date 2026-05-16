<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\NilaiAkhir;
use App\Models\Akademik\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RaportController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $santri = collect();

        if ($request->filled('tahun_ajaran_id')) {
            $santri = \App\Models\Santri::whereHas('kelasSantri', function ($q) use ($request) {
                $q->where('tahun_ajaran_id', $request->tahun_ajaran_id);
            })->with(['kelasSantri.kelas', 'wali'])->get();
        }

        return view('admin.akademik.raport.index', compact('tahunAjaran', 'santri', 'request'));
    }

    public function preview($santriId, $tahunAjaranId)
    {
        return $this->render($santriId, $tahunAjaranId, false);
    }

    public function download($santriId, $tahunAjaranId)
    {
        return $this->render($santriId, $tahunAjaranId, true);
    }

    protected function render($santriId, $tahunAjaranId, bool $download)
    {
        $santri = \App\Models\Santri::with(['wali', 'kelasSantri.kelas.waliKelas.user', 'kelasSantri.kelas.tingkat'])->findOrFail($santriId);
        $tahunAjaran = TahunAjaran::findOrFail($tahunAjaranId);

        $kelasSantri = $santri->kelasSantri->firstWhere('tahun_ajaran_id', $tahunAjaran->id);
        $kelas       = $kelasSantri?->kelas;
        $waliKelas   = $kelas?->waliKelas;

        $nilaiAkhir = NilaiAkhir::where('santri_id', $santriId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->with('mataPelajaran')
            ->get();

        $nilaiSikap = \App\Models\Akademik\NilaiSikap::where('santri_id', $santriId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->get();

        $catatanWaliKelas = \App\Models\Akademik\CatatanWaliKelas::where('santri_id', $santriId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->with('ustadz.user')
            ->first();

        $absensi = \App\Models\Akademik\AbsensiPelajaran::where('absensi_pelajaran.santri_id', $santriId)
            ->whereHas('jadwal', fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get();

        $settings = RaportSettingController::all();

        $data = compact('santri', 'tahunAjaran', 'kelas', 'waliKelas', 'nilaiAkhir', 'nilaiSikap', 'catatanWaliKelas', 'absensi', 'settings');

        $pdf = Pdf::loadView('admin.akademik.raport.pdf', $data)->setPaper('a4', 'portrait');

        $filename = sprintf(
            'raport-%s-%s-%s.pdf',
            \Illuminate\Support\Str::slug($santri->nama),
            str_replace('/', '-', $tahunAjaran->nama),
            $tahunAjaran->semester
        );

        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }

    public function bulkDownload(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'santri'          => 'required|array',
            'santri.*'        => 'required|exists:santri,id',
        ]);

        $pdfs = [];
        foreach ($validated['santri'] as $santriId) {
            $pdfs[] = $this->download($santriId, $validated['tahun_ajaran_id'])->getOriginalContent();
        }

        // Zip multiple PDFs (simplified - in production use ZipArchive)
        return response()->download(storage_path('app/temp/raport.zip'));
    }
}
