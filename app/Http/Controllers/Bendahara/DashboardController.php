<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $summary = [
            'total_tagihan'      => Tagihan::sum('nominal'),
            'tagihan_belum_bayar'=> Tagihan::where('status', 'belum_bayar')->sum('nominal'),
            'total_pembayaran'   => Pembayaran::paid()->sum('nominal'),
            'jumlah_pembayaran'  => Pembayaran::paid()->count(),
            'pending_pembayaran' => Pembayaran::where('status', 'pending')->sum('nominal'),
        ];

        $recentPayments = Pembayaran::with(['wali', 'tagihan.santri'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        $tagihanByStatus = [
            'belum_bayar' => Tagihan::where('status', 'belum_bayar')->count(),
            'lunas'      => Tagihan::where('status', 'lunas')->count(),
        ];

        return view('bendahara.dashboard', compact('summary', 'recentPayments', 'tagihanByStatus'));
    }
}
