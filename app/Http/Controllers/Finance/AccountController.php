<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $query = Account::with('parent')->orderBy('kode');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($w) => $w->where('kode', 'ilike', "%$q%")->orWhere('nama', 'ilike', "%$q%"));
        }
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $accounts = $query->paginate(50)->withQueryString();

        return view('finance.accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        $parents = Account::orderBy('kode')->get();
        return view('finance.accounts.form', ['account' => new Account(), 'parents' => $parents]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Account::create($data);
        return redirect()->route('finance.accounts.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(Account $account): View
    {
        $parents = Account::where('id', '!=', $account->id)->orderBy('kode')->get();
        return view('finance.accounts.form', compact('account', 'parents'));
    }

    public function update(Request $request, Account $account): RedirectResponse
    {
        $data = $this->validateData($request, $account->id);
        $account->update($data);
        return redirect()->route('finance.accounts.index')->with('success', 'Akun diperbarui.');
    }

    public function destroy(Account $account): RedirectResponse
    {
        if ($account->journalLines()->exists()) {
            return back()->with('error', 'Akun ini sudah dipakai di jurnal, tidak bisa dihapus. Nonaktifkan saja.');
        }
        $account->delete();
        return back()->with('success', 'Akun dihapus.');
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'kode'         => "required|string|max:20|unique:finance_accounts,kode" . ($id ? ",$id" : ''),
            'nama'         => 'required|string|max:255',
            'tipe'         => 'required|in:asset,liability,equity,revenue,expense',
            'saldo_normal' => 'required|in:debit,kredit',
            'parent_id'    => 'nullable|exists:finance_accounts,id',
            'is_kas_bank'  => 'sometimes|boolean',
            'is_active'    => 'sometimes|boolean',
            'keterangan'   => 'nullable|string',
        ]);
    }
}
