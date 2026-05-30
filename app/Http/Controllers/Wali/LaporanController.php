<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    public function __construct(protected ReportService $reports) {}

    public function show(Request $request, Santri $santri)
    {
        $wali = auth()->user()->wali;

        if (!$wali || !$wali->santri()->where('santri.id', $santri->id)->exists()) {
            abort(403, 'Akses ditolak. Santri ini bukan anak Anda.');
        }

        $dari   = Carbon::parse($request->dari   ?? now()->startOfMonth()->toDateString());
        $sampai = Carbon::parse($request->sampai ?? now()->toDateString());

        $dari->startOfDay();
        $sampai->endOfDay();

        $mutasi = $santri->walletTransactions()
            ->whereBetween('created_at', [$dari, $sampai])
            ->latest()
            ->get();

        $setoran = $santri->setoran()
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->with(['penerima', 'surahAwal', 'surahAkhir'])
            ->latest('tanggal')
            ->get();

        $laundry = $santri->laundryOrders()
            ->whereBetween('created_at', [$dari, $sampai])
            ->latest()
            ->get();

        $kantin = $santri->transaksiKasir()
            ->whereBetween('created_at', [$dari, $sampai])
            ->with('outlet')
            ->latest()
            ->get();

        $totalBelanja  = $mutasi->where('jenis', 'debit')->sum('nominal');
        $totalTopUp    = $mutasi->where('jenis', 'kredit')->sum('nominal');
        $totalHafalan  = $setoran->where('jenis', 'ziyadah')->where('status', 'maqbul')->sum('jumlah_halaman');
        $totalLaundry  = $laundry->sum('total');

        return view('wali.laporan.show', compact(
            'santri', 'dari', 'sampai',
            'mutasi', 'setoran', 'laundry', 'kantin',
            'totalBelanja', 'totalTopUp', 'totalHafalan', 'totalLaundry'
        ));
    }

    public function bulanan(Request $request, Santri $santri)
    {
        $wali = auth()->user()->wali;
        if (!$wali || !$wali->santri()->where('santri.id', $santri->id)->exists()) {
            abort(403);
        }

        $month = (int) ($request->month ?? now()->month);
        $year  = (int) ($request->year  ?? now()->year);

        $report = $this->reports->generateMonthlyReport($santri, $month, $year);

        return view('wali.laporan.bulanan', compact('santri', 'report', 'month', 'year'));
    }

    public function semester(Request $request, Santri $santri)
    {
        $wali = auth()->user()->wali;
        if (!$wali || !$wali->santri()->where('santri.id', $santri->id)->exists()) {
            abort(403);
        }

        $semester = (int) ($request->semester ?? (now()->month <= 6 ? 1 : 2));
        $year     = (int) ($request->year ?? now()->year);

        $report = $this->reports->generateSemesterReport($santri, $semester, $year);

        return view('wali.laporan.semester', compact('santri', 'report', 'semester', 'year'));
    }

    public function tahunan(Request $request, Santri $santri)
    {
        $wali = auth()->user()->wali;
        if (!$wali || !$wali->santri()->where('santri.id', $santri->id)->exists()) {
            abort(403);
        }

        $year = (int) ($request->year ?? now()->year);

        $report = $this->reports->generateYearlyReport($santri, $year);

        return view('wali.laporan.tahunan', compact('santri', 'report', 'year'));
    }
}
