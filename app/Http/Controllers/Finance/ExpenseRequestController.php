<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\ExpenseRequest;
use App\Models\Finance\KasBank;
use App\Models\Finance\Kategori;
use App\Models\Finance\Setting;
use App\Models\Finance\Vendor;
use App\Services\Finance\ExpenseRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseRequestController extends Controller
{
    public function __construct(protected ExpenseRequestService $service) {}

    public function index(Request $request): View
    {
        $query = ExpenseRequest::with(['kategori', 'kasBank', 'vendor', 'creator'])->latest('tanggal');
        if ($request->filled('status')) $query->where('status', $request->status);
        $items = $query->paginate(25)->withQueryString();

        $summary = [
            'pending_kepala'  => ExpenseRequest::where('status', 'pending_kepala')->count(),
            'pending_yayasan' => ExpenseRequest::where('status', 'pending_yayasan')->count(),
            'approved'        => ExpenseRequest::where('status', 'approved')->count(),
        ];

        return view('finance.expense_requests.index', compact('items', 'summary'));
    }

    public function create(): View
    {
        $kategoris = Kategori::pengeluaran()->aktif()->orderBy('nama')->get();
        $kasBanks  = KasBank::aktif()->orderBy('kode')->get();
        $vendors   = Vendor::where('is_active', true)->orderBy('nama')->get();
        $thresholdK = (float) Setting::get('approval.threshold_kepala_pondok', 5000000);
        $thresholdY = (float) Setting::get('approval.threshold_yayasan', 50000000);

        return view('finance.expense_requests.form', compact('kategoris', 'kasBanks', 'vendors', 'thresholdK', 'thresholdY'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'     => 'required|date',
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'nominal'     => 'required|numeric|min:0.01',
            'kategori_id' => 'required|exists:finance_kategoris,id',
            'kas_bank_id' => 'required|exists:finance_kas_banks,id',
            'vendor_id'   => 'nullable|exists:finance_vendors,id',
            'bukti'       => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('finance/expense', 'public');
        }
        $data['nomor'] = ExpenseRequest::generateNomor();
        $data['status'] = 'draft';
        $data['created_by'] = auth()->id();
        $req = ExpenseRequest::create($data);
        $this->service->submit($req);

        return redirect()->route('finance.expense-requests.show', $req)->with('success', 'Permintaan disubmit: ' . $req->nomor);
    }

    public function show(ExpenseRequest $expenseRequest): View
    {
        $expenseRequest->load(['kategori', 'kasBank', 'vendor', 'creator', 'transaksi']);
        return view('finance.expense_requests.show', ['req' => $expenseRequest]);
    }

    public function approve(ExpenseRequest $expenseRequest, string $level): RedirectResponse
    {
        try {
            $this->service->approve($expenseRequest, $level);
            return back()->with('success', 'Approval berhasil.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, ExpenseRequest $expenseRequest): RedirectResponse
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $this->service->reject($expenseRequest, $request->reason);
        return back()->with('success', 'Permintaan ditolak.');
    }

    public function execute(ExpenseRequest $expenseRequest): RedirectResponse
    {
        try {
            $this->service->execute($expenseRequest);
            return back()->with('success', 'Pengeluaran dieksekusi & jurnal otomatis dibuat.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
