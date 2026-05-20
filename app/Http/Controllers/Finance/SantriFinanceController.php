<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TopUpRequest;
use App\Models\TransaksiKasir;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SantriFinanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Santri::query()->orderBy('nama');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($w) => $w->where('nama', 'ilike', "%$q%")->orWhere('nis', 'ilike', "%$q%"));
        }
        $items = $query->paginate(50)->withQueryString();

        $summary = [
            'total_saldo'     => (float) Santri::sum('saldo'),
            'total_tagihan'   => (float) Tagihan::where('status', 'belum_bayar')->sum('nominal'),
            'total_pembayaran_bulan' => (float) Pembayaran::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('nominal'),
            'total_topup_bulan' => (float) TopUpRequest::whereIn('status', ['paid', 'success'])
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->sum('nominal'),
        ];

        return view('finance.santri.index', compact('items', 'summary'));
    }

    public function show(Santri $santri, Request $request): View
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $start = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $tagihan = Tagihan::where('santri_id', $santri->id)
            ->with('jenisTagihan')
            ->latest('id')->limit(20)->get();

        $pembayaran = Pembayaran::where('status', 'paid')
            ->whereHas('tagihan', fn($q) => $q->where('santri_id', $santri->id))
            ->with('tagihan.jenisTagihan')
            ->latest('paid_at')->limit(20)->get();

        $topups = TopUpRequest::where('santri_id', $santri->id)
            ->whereIn('status', ['paid', 'success'])
            ->latest('updated_at')->limit(20)->get();

        $kantin = TransaksiKasir::where('santri_id', $santri->id)
            ->whereBetween('created_at', [$start, $end])
            ->with(['outlet', 'items'])
            ->latest()->limit(50)->get();

        $walletTrx = WalletTransaction::where('santri_id', $santri->id)
            ->whereBetween('created_at', [$start, $end])
            ->latest()->limit(100)->get();

        $stats = [
            'saldo_sekarang'      => (float) $santri->saldo,
            'tagihan_belum_bayar' => (float) Tagihan::where('santri_id', $santri->id)->where('status', 'belum_bayar')->sum('nominal'),
            'tagihan_lunas'       => (float) Tagihan::where('santri_id', $santri->id)->where('status', 'lunas')->sum('nominal'),
            'belanja_kantin_bulan'=> (float) TransaksiKasir::where('santri_id', $santri->id)
                ->whereBetween('created_at', [$start, $end])->sum('total'),
            'topup_bulan'         => (float) TopUpRequest::where('santri_id', $santri->id)
                ->whereIn('status', ['paid', 'success'])
                ->whereBetween('updated_at', [$start, $end])->sum('nominal'),
            'pembayaran_bulan'    => (float) Pembayaran::where('status', 'paid')
                ->whereHas('tagihan', fn($q) => $q->where('santri_id', $santri->id))
                ->whereBetween('paid_at', [$start, $end])->sum('nominal'),
        ];

        return view('finance.santri.show', compact('santri', 'stats', 'tagihan', 'pembayaran', 'topups', 'kantin', 'walletTrx', 'bulan', 'tahun'));
    }
}
