<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\KasBank;
use App\Models\Finance\Kategori;
use App\Models\Finance\PettyCash;
use App\Services\Finance\PettyCashService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PettyCashController extends Controller
{
    public function __construct(protected PettyCashService $service) {}

    public function index(): View
    {
        $items = PettyCash::orderBy('is_active', 'desc')->orderBy('nama')->get();
        return view('finance.petty_cash.index', compact('items'));
    }

    public function create(): View
    {
        return view('finance.petty_cash.form', ['petty' => new PettyCash(['saldo_awal' => 0, 'is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['nomor'] = 'PC-' . now()->format('YmdHis');
        $data['tanggal'] = $data['tanggal'] ?? now()->toDateString();
        $data['saldo_berjalan'] = $data['saldo_awal'] ?? 0;
        PettyCash::create($data);
        return redirect()->route('finance.petty-cash.index')->with('success', 'Kas kecil dibuat.');
    }

    public function edit(PettyCash $pettyCash): View
    {
        return view('finance.petty_cash.form', ['petty' => $pettyCash]);
    }

    public function update(Request $request, PettyCash $pettyCash): RedirectResponse
    {
        $data = $this->validateData($request);
        $pettyCash->update($data);
        return redirect()->route('finance.petty-cash.index')->with('success', 'Diperbarui.');
    }

    public function show(PettyCash $pettyCash): View
    {
        $pettyCash->load(['transactions' => fn($q) => $q->latest('tanggal')->latest('id'), 'transactions.kategori']);
        $kasBanks = KasBank::aktif()->where('id', '!=', 0)->get();
        $kategoris = Kategori::pengeluaran()->aktif()->orderBy('nama')->get();
        return view('finance.petty_cash.show', compact('pettyCash', 'kasBanks', 'kategoris'));
    }

    public function topUp(Request $request, PettyCash $pettyCash): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'     => 'required|date',
            'nominal'     => 'required|numeric|min:0.01',
            'kas_bank_id' => 'required|exists:finance_kas_banks,id',
            'keterangan'  => 'nullable|string',
        ]);
        try {
            $this->service->topUp($pettyCash, $data);
            return back()->with('success', 'Kas kecil berhasil diisi.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function spend(Request $request, PettyCash $pettyCash): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'     => 'required|date',
            'nominal'     => 'required|numeric|min:0.01',
            'kategori_id' => 'required|exists:finance_kategoris,id',
            'keterangan'  => 'nullable|string',
            'bukti'       => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);
        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('finance/petty', 'public');
        }
        try {
            $this->service->spend($pettyCash, $data);
            return back()->with('success', 'Pengeluaran kas kecil tercatat.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'penanggung_jawab' => 'required|string|max:255',
            'saldo_awal'       => 'required|numeric|min:0',
            'tanggal'          => 'nullable|date',
            'is_active'        => 'sometimes|boolean',
            'keterangan'       => 'nullable|string',
        ]);
    }
}
