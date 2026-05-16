<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Budget;
use App\Models\Finance\Kategori;
use App\Models\Finance\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan');

        $query = Budget::with('kategori')->where('tahun', $tahun);
        if ($bulan) $query->where('bulan', $bulan);
        $items = $query->orderBy('bulan')->get();

        // Hitung realisasi otomatis dari transaksi
        foreach ($items as $b) {
            $start = \Carbon\Carbon::create($b->tahun, $b->bulan ?? 1, 1)->startOfMonth();
            $end   = $b->bulan
                ? $start->copy()->endOfMonth()
                : \Carbon\Carbon::create($b->tahun, 12, 31)->endOfDay();

            $tipe = $b->kategori->tipe;
            $realisasi = (float) DB::table('finance_transaksi')
                ->where('kategori_id', $b->kategori_id)
                ->where('status', 'posted')
                ->where('tipe', $tipe == 'pemasukan' ? 'masuk' : 'keluar')
                ->whereBetween('tanggal', [$start, $end])
                ->sum('nominal');

            if (abs((float) $b->nominal_realisasi - $realisasi) > 0.01) {
                $b->update(['nominal_realisasi' => $realisasi]);
            }
        }

        $kategoris = Kategori::aktif()->orderBy('tipe')->orderBy('nama')->get();
        $alertPercent = (int) Setting::get('reminder.budget_warning', 80);

        return view('finance.budgets.index', compact('items', 'kategoris', 'tahun', 'bulan', 'alertPercent'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun'            => 'required|integer|min:2020|max:2099',
            'bulan'            => 'nullable|integer|min:1|max:12',
            'kategori_id'      => 'required|exists:finance_kategoris,id',
            'nominal_anggaran' => 'required|numeric|min:0',
            'catatan'          => 'nullable|string',
        ]);

        $existing = Budget::where('tahun', $data['tahun'])
            ->where('bulan', $data['bulan'] ?? null)
            ->where('kategori_id', $data['kategori_id'])
            ->first();
        if ($existing) {
            $existing->update($data);
            return back()->with('success', 'Anggaran diperbarui.');
        }

        $data['created_by'] = auth()->id();
        Budget::create($data);
        return back()->with('success', 'Anggaran ditambahkan.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $budget->delete();
        return back()->with('success', 'Anggaran dihapus.');
    }
}
