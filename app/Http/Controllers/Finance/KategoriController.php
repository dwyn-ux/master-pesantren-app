<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Models\Finance\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        $items = Kategori::with('account')->orderBy('tipe')->orderBy('kode')->paginate(50);
        return view('finance.kategoris.index', compact('items'));
    }

    public function create(): View
    {
        return view('finance.kategoris.form', [
            'kategori' => new Kategori(),
            'accounts' => Account::aktif()->whereIn('tipe', ['revenue', 'expense'])->orderBy('kode')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Kategori::create($data);
        return redirect()->route('finance.kategoris.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('finance.kategoris.form', [
            'kategori' => $kategori,
            'accounts' => Account::aktif()->whereIn('tipe', ['revenue', 'expense'])->orderBy('kode')->get(),
        ]);
    }

    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $data = $this->validateData($request, $kategori->id);
        $kategori->update($data);
        return redirect()->route('finance.kategoris.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        $kategori->delete();
        return back()->with('success', 'Kategori dihapus.');
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'kode'        => "required|string|max:20|unique:finance_kategoris,kode" . ($id ? ",$id" : ''),
            'nama'        => 'required|string|max:255',
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'account_id'  => 'required|exists:finance_accounts,id',
            'is_active'   => 'sometimes|boolean',
            'keterangan'  => 'nullable|string',
        ]);
    }
}
