<?php

namespace App\Http\Controllers\KepalaPondok;

use App\Http\Controllers\Controller;
use App\Models\Finance\ExpenseRequest;
use App\Models\Pembayaran;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Tagihan;
use App\Services\FeatureManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function __construct(private FeatureManager $features) {}

    public function dashboard(): View
    {
        return view('kepala-pondok.dashboard', $this->overview());
    }

    public function laporan(): View
    {
        return view('kepala-pondok.laporan', $this->overview());
    }

    public function santri(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $santri = Santri::query()
            ->with('halaqah')
            ->withCount('setoran')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('is_aktif')
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        return view('kepala-pondok.santri', compact('santri', 'search'));
    }

    public function halaqah(Request $request): View
    {
        $status = $request->query('status');

        $setoran = Setoran::query()
            ->with(['santri', 'penerima', 'surahAwal', 'surahAkhir'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('tanggal')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $summary = [
            'hari_ini' => Setoran::whereDate('tanggal', today())->count(),
            'maqbul_bulan_ini' => Setoran::where('status', 'maqbul')->whereBetween('tanggal', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'perlu_latihan' => Setoran::whereIn('status', ['perbaikan', 'ulang', 'perlu_latihan', 'banyak_salah', 'dhaif', 'kurang'])->count(),
        ];

        return view('kepala-pondok.halaqah', compact('setoran', 'status', 'summary'));
    }

    public function keuangan(): View
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $summary = [
            'pembayaran_bulan_ini' => Pembayaran::paid()->whereBetween('paid_at', [$monthStart, $monthEnd])->sum('nominal'),
            'tagihan_beredar' => Tagihan::whereIn('status', ['belum_bayar', 'sebagian'])->sum('nominal'),
            'manual_menunggu' => Pembayaran::where('status', 'pending')->where('metode', 'manual')->count(),
            'approval_menunggu' => $this->features->enabled('finance')
                ? ExpenseRequest::where('status', 'pending_kepala')->count()
                : 0,
        ];

        $pembayaran = Pembayaran::with(['wali', 'tagihan.santri'])
            ->latest()
            ->take(12)
            ->get();
        $approval = $this->features->enabled('finance')
            ? ExpenseRequest::with(['kategori', 'creator'])->where('status', 'pending_kepala')->latest('tanggal')->take(10)->get()
            : collect();

        return view('kepala-pondok.keuangan', compact('summary', 'pembayaran', 'approval'));
    }

    private function overview(): array
    {
        $tagihanEnabled = $this->features->enabled('tagihan');
        $halaqahEnabled = $this->features->enabled('halaqah');
        $financeEnabled = $this->features->enabled('finance');

        $stats = [
            'santri_aktif' => Santri::where('is_aktif', true)->count(),
            'tagihan_belum_bayar' => $tagihanEnabled ? Tagihan::whereIn('status', ['belum_bayar', 'sebagian'])->count() : 0,
            'setoran_hari_ini' => $halaqahEnabled ? Setoran::whereDate('tanggal', today())->count() : 0,
            'pembayaran_bulan_ini' => $tagihanEnabled ? Pembayaran::paid()->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('nominal') : 0,
            'approval_menunggu' => $financeEnabled ? ExpenseRequest::where('status', 'pending_kepala')->count() : 0,
        ];

        $setoranTerbaru = $halaqahEnabled
            ? Setoran::with(['santri', 'penerima'])->latest('tanggal')->latest('id')->take(6)->get()
            : collect();
        $perizinanTerbaru = Perizinan::with('santri')->latest()->take(6)->get();

        return compact('stats', 'setoranTerbaru', 'perizinanTerbaru');
    }
}
