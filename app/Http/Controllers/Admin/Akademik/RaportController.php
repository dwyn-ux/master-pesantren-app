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
        $santri = \App\Models\Santri::with(['wali', 'kelasSantri.kelas'])->findOrFail($santriId);
        $tahunAjaran = TahunAjaran::findOrFail($tahunAjaranId);

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

        $absensi = \App\Models\Akademik\AbsensiPelajaran::where('santri_id', $santriId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get();

        return view('admin.akademik.raport.preview', compact('santri', 'tahunAjaran', 'nilaiAkhir', 'nilaiSikap', 'catatanWaliKelas', 'absensi'));
    }

    public function download($santriId, $tahunAjaranId)
    {
        $santri = \App\Models\Santri::with(['wali', 'kelasSantri.kelas'])->findOrFail($santriId);
        $tahunAjaran = TahunAjaran::findOrFail($tahunAjaranId);

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

        $absensi = \App\Models\Akademik\AbsensiPelajaran::where('santri_id', $santriId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get();

        $pdf = Pdf::loadView('admin.akademik.raport.pdf', compact('santri', 'tahunAjaran', 'nilaiAkhir', 'nilaiSikap', 'catatanWaliKelas', 'absensi'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("raport-{$santri->nama}-{$tahunAjaran->nama}.pdf");
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
