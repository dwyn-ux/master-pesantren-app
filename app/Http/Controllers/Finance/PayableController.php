<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Models\Finance\KasBank;
use App\Models\Finance\Payable;
use App\Models\Finance\Vendor;
use App\Services\Finance\PayableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayableController extends Controller
{
    public function __construct(protected PayableService $service) {}

    public function index(Request $request): View
    {
        $query = Payable::with(['vendor'])->latest('tanggal');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('vendor_id')) $query->where('vendor_id', $request->vendor_id);

        $items = $query->paginate(25)->withQueryString();

        $summary = [
            'total_hutang'   => (float) Payable::whereNotIn('status', ['void'])->sum('sisa'),
            'jatuh_tempo_7'  => (float) Payable::where('status', '!=', 'lunas')->whereDate('jatuh_tempo', '<=', now()->addDays(7))->sum('sisa'),
            'overdue'        => (float) Payable::where('status', '!=', 'lunas')->whereDate('jatuh_tempo', '<', today())->sum('sisa'),
        ];

        $vendors = Vendor::orderBy('nama')->get();

        return view('finance.payables.index', compact('items', 'summary', 'vendors'));
    }

    public function create(): View
    {
        $vendors = Vendor::where('is_active', true)->orderBy('nama')->get();
        $accounts = Account::aktif()->whereIn('tipe', ['expense', 'asset'])->orderBy('kode')->get();
        return view('finance.payables.form', ['payable' => new Payable(), 'vendors' => $vendors, 'accounts' => $accounts]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'     => 'required|date',
            'jatuh_tempo' => 'required|date|after_or_equal:tanggal',
            'vendor_id'   => 'required|exists:finance_vendors,id',
            'nominal'     => 'required|numeric|min:0.01',
            'account_id'  => 'required|exists:finance_accounts,id',
            'keterangan'  => 'nullable|string',
        ]);

        try {
            $payable = $this->service->create($data);
            return redirect()->route('finance.payables.show', $payable)->with('success', 'Hutang dicatat: ' . $payable->nomor);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Payable $payable): View
    {
        $payable->load(['vendor', 'payments.kasBank', 'journal.lines.account']);
        $kasBanks = KasBank::aktif()->get();
        return view('finance.payables.show', compact('payable', 'kasBanks'));
    }

    public function pay(Request $request, Payable $payable): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'     => 'required|date',
            'nominal'     => 'required|numeric|min:0.01',
            'kas_bank_id' => 'required|exists:finance_kas_banks,id',
            'keterangan'  => 'nullable|string',
        ]);

        try {
            $this->service->pay($payable, $data);
            return back()->with('success', 'Pembayaran tercatat.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
