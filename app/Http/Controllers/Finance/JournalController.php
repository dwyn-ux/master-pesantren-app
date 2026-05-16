<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Models\Finance\Journal;
use App\Services\Finance\JournalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function __construct(protected JournalService $service) {}

    public function show(Journal $journal): View
    {
        $journal->load('lines.account', 'lines.kasBank');
        return view('finance.journal.show', compact('journal'));
    }

    public function reverse(Request $request, Journal $journal): RedirectResponse
    {
        $request->validate(['tanggal' => 'nullable|date', 'alasan' => 'required|string|max:500']);
        try {
            $reversed = $this->service->reverse($journal, $request->tanggal);
            return redirect()->route('finance.journal.show', $reversed)
                ->with('success', "Jurnal pembalik dibuat: {$reversed->nomor}. Reverse dari: {$journal->nomor}");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function void(Request $request, Journal $journal): RedirectResponse
    {
        $request->validate(['alasan' => 'required|string|max:500']);
        try {
            $this->service->void($journal, $request->alasan);
            return back()->with('success', 'Jurnal divoid.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function createManual(): View
    {
        $accounts = Account::aktif()->orderBy('kode')->get();
        return view('finance.journal.manual', compact('accounts'));
    }

    public function storeManual(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'keterangan' => 'required|string|max:500',
            'lines'      => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:finance_accounts,id',
            'lines.*.debit'      => 'nullable|numeric|min:0',
            'lines.*.kredit'     => 'nullable|numeric|min:0',
            'lines.*.keterangan' => 'nullable|string|max:255',
        ]);

        try {
            $journal = $this->service->post([
                'tanggal'    => $request->tanggal,
                'tipe'       => 'penyesuaian',
                'keterangan' => $request->keterangan,
            ], $request->lines);
            return redirect()->route('finance.journal.show', $journal)->with('success', 'Jurnal manual diposting: ' . $journal->nomor);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
