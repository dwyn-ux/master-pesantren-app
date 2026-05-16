<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Periode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeriodeController extends Controller
{
    public function index(): View
    {
        $items = Periode::orderByDesc('tahun')->orderByDesc('bulan')->paginate(36);

        $existing = $items->pluck('id', fn($p) => "{$p->tahun}-{$p->bulan}");

        // Generate list 24 bulan terakhir
        $bulanList = [];
        for ($i = 0; $i < 24; $i++) {
            $d = now()->subMonths($i);
            $key = $d->year . '-' . $d->month;
            $bulanList[] = [
                'tahun'  => $d->year,
                'bulan'  => $d->month,
                'label'  => $d->translatedFormat('F Y'),
                'periode'=> Periode::where(['tahun' => $d->year, 'bulan' => $d->month])->first(),
            ];
        }

        return view('finance.periode.index', compact('items', 'bulanList'));
    }

    public function close(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        $p = Periode::firstOrCreate(['tahun' => $data['tahun'], 'bulan' => $data['bulan']]);
        $p->update([
            'status'    => 'closed',
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);

        return back()->with('success', "Periode {$data['bulan']}/{$data['tahun']} ditutup. Transaksi pada periode ini tidak bisa lagi diubah/ditambah.");
    }

    public function reopen(Periode $periode): RedirectResponse
    {
        $periode->update([
            'status'    => 'open',
            'closed_at' => null,
            'closed_by' => null,
        ]);
        return back()->with('success', 'Periode dibuka kembali.');
    }
}
