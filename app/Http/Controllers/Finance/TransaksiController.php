<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\KasBank;
use App\Models\Finance\Kategori;
use App\Models\Finance\Transaksi;
use App\Services\Finance\TransaksiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    public function __construct(protected TransaksiService $service) {}

    public function index(Request $request): View
    {
        $query = Transaksi::with(['kategori', 'kasBank', 'kasBankTujuan'])
            ->latest('tanggal')->latest('id');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('kas_bank_id')) {
            $query->where(fn($q) => $q->where('kas_bank_id', $request->kas_bank_id)
                ->orWhere('kas_bank_tujuan_id', $request->kas_bank_id));
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($w) => $w->where('nomor', 'like', "%$q%")
                ->orWhere('pihak', 'like', "%$q%")
                ->orWhere('keterangan', 'like', "%$q%"));
        }

        $items    = $query->paginate(25)->withQueryString();
        $kasBanks = KasBank::aktif()->orderBy('kode')->get();

        return view('finance.transaksi.index', compact('items', 'kasBanks'));
    }

    public function createMasuk(): View
    {
        return view('finance.transaksi.form', [
            'transaksi' => new Transaksi(['tipe' => 'masuk', 'tanggal' => now()->toDateString()]),
            'kategoris' => Kategori::pemasukan()->aktif()->orderBy('nama')->get(),
            'kasBanks'  => KasBank::aktif()->orderBy('kode')->get(),
            'tipe'      => 'masuk',
        ]);
    }

    public function createKeluar(): View
    {
        return view('finance.transaksi.form', [
            'transaksi' => new Transaksi(['tipe' => 'keluar', 'tanggal' => now()->toDateString()]),
            'kategoris' => Kategori::pengeluaran()->aktif()->orderBy('nama')->get(),
            'kasBanks'  => KasBank::aktif()->orderBy('kode')->get(),
            'tipe'      => 'keluar',
        ]);
    }

    public function createTransfer(): View
    {
        return view('finance.transaksi.transfer', [
            'transaksi' => new Transaksi(['tipe' => 'transfer', 'tanggal' => now()->toDateString()]),
            'kasBanks'  => KasBank::aktif()->orderBy('kode')->get(),
        ]);
    }

    public function storeMasuk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'     => 'required|date',
            'kategori_id' => 'required|exists:finance_kategoris,id',
            'kas_bank_id' => 'required|exists:finance_kas_banks,id',
            'nominal'     => 'required|numeric|min:0.01',
            'pihak'       => 'nullable|string|max:255',
            'keterangan'  => 'nullable|string',
            'bukti'       => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('finance/bukti', 'public');
        }

        try {
            $trx = $this->service->masuk($data);
            return redirect()->route('finance.transaksi.show', $trx)->with('success', 'Pemasukan dicatat: ' . $trx->nomor);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function storeKeluar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'     => 'required|date',
            'kategori_id' => 'required|exists:finance_kategoris,id',
            'kas_bank_id' => 'required|exists:finance_kas_banks,id',
            'nominal'     => 'required|numeric|min:0.01',
            'pihak'       => 'nullable|string|max:255',
            'keterangan'  => 'nullable|string',
            'bukti'       => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('finance/bukti', 'public');
        }

        try {
            $trx = $this->service->keluar($data);
            return redirect()->route('finance.transaksi.show', $trx)->with('success', 'Pengeluaran dicatat: ' . $trx->nomor);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'            => 'required|date',
            'kas_bank_id'        => 'required|exists:finance_kas_banks,id',
            'kas_bank_tujuan_id' => 'required|exists:finance_kas_banks,id|different:kas_bank_id',
            'nominal'            => 'required|numeric|min:0.01',
            'keterangan'         => 'nullable|string',
            'bukti'              => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('finance/bukti', 'public');
        }

        try {
            $trx = $this->service->transfer($data);
            return redirect()->route('finance.transaksi.show', $trx)->with('success', 'Transfer dicatat: ' . $trx->nomor);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Transaksi $transaksi): View
    {
        $transaksi->load(['kategori.account', 'kasBank.account', 'kasBankTujuan.account', 'journal.lines.account', 'creator']);
        return view('finance.transaksi.show', compact('transaksi'));
    }

    public function void(Request $request, Transaksi $transaksi): RedirectResponse
    {
        $request->validate(['alasan' => 'required|string|max:500']);
        try {
            $this->service->void($transaksi, $request->alasan);
            return back()->with('success', 'Transaksi divoid.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
