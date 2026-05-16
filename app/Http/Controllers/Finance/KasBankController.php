<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Models\Finance\KasBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KasBankController extends Controller
{
    public function index(): View
    {
        $items = KasBank::with('account')->orderBy('kode')->get();
        $items->each->recalcSaldo();
        return view('finance.kas_banks.index', compact('items'));
    }

    public function create(): View
    {
        $accounts = Account::aktif()->where('tipe', 'asset')->where('is_kas_bank', true)->orderBy('kode')->get();
        return view('finance.kas_banks.form', ['kasBank' => new KasBank(), 'accounts' => $accounts]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['saldo_berjalan'] = $data['saldo_awal'] ?? 0;
        KasBank::create($data);
        return redirect()->route('finance.kas-banks.index')->with('success', 'Kas/Bank berhasil ditambahkan.');
    }

    public function edit(KasBank $kasBank): View
    {
        $accounts = Account::aktif()->where('tipe', 'asset')->where('is_kas_bank', true)->orderBy('kode')->get();
        return view('finance.kas_banks.form', compact('kasBank', 'accounts'));
    }

    public function update(Request $request, KasBank $kasBank): RedirectResponse
    {
        $data = $this->validateData($request, $kasBank->id);
        $kasBank->update($data);
        $kasBank->recalcSaldo();
        return redirect()->route('finance.kas-banks.index')->with('success', 'Kas/Bank diperbarui.');
    }

    public function destroy(KasBank $kasBank): RedirectResponse
    {
        if ($kasBank->transaksi()->exists()) {
            return back()->with('error', 'Kas/Bank sudah dipakai di transaksi, tidak bisa dihapus. Nonaktifkan saja.');
        }
        $kasBank->delete();
        return back()->with('success', 'Kas/Bank dihapus.');
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'kode'           => "required|string|max:20|unique:finance_kas_banks,kode" . ($id ? ",$id" : ''),
            'nama'           => 'required|string|max:255',
            'tipe'           => 'required|in:kas,bank',
            'nama_bank'      => 'nullable|string|max:255',
            'no_rekening'    => 'nullable|string|max:50',
            'atas_nama'      => 'nullable|string|max:255',
            'account_id'     => 'required|exists:finance_accounts,id',
            'saldo_awal'     => 'required|numeric|min:0',
            'is_active'      => 'sometimes|boolean',
            'keterangan'     => 'nullable|string',
        ]);
    }
}
