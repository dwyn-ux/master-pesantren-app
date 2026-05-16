<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Models\Finance\Asset;
use App\Services\Finance\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function __construct(protected AssetService $service) {}

    public function index(Request $request): View
    {
        $query = Asset::with('account')->orderBy('kode');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($w) => $w->where('kode', 'like', "%$q%")->orWhere('nama', 'like', "%$q%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $items = $query->paginate(25)->withQueryString();

        $summary = [
            'total_aset'          => (float) Asset::sum('harga_perolehan'),
            'total_akumulasi'     => (float) Asset::sum('akumulasi_penyusutan'),
            'nilai_buku'          => (float) Asset::sum('nilai_buku'),
            'jumlah_aset_aktif'   => Asset::where('status', 'aktif')->count(),
        ];

        return view('finance.assets.index', compact('items', 'summary'));
    }

    public function create(): View
    {
        $accounts = Account::aktif()->where('tipe', 'asset')->orderBy('kode')->get();
        return view('finance.assets.form', ['asset' => new Asset(), 'accounts' => $accounts]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['nilai_buku'] = $data['harga_perolehan'];
        Asset::create($data);
        return redirect()->route('finance.assets.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function edit(Asset $asset): View
    {
        $accounts = Account::aktif()->where('tipe', 'asset')->orderBy('kode')->get();
        return view('finance.assets.form', compact('asset', 'accounts'));
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $data = $this->validateData($request, $asset->id);
        $asset->update($data);
        return redirect()->route('finance.assets.index')->with('success', 'Aset diperbarui.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        if ($asset->depreciations()->exists()) {
            return back()->with('error', 'Aset sudah punya riwayat penyusutan, ubah status saja jadi "dihapus".');
        }
        $asset->delete();
        return back()->with('success', 'Aset dihapus.');
    }

    public function show(Asset $asset): View
    {
        $asset->load(['depreciations' => fn($q) => $q->orderBy('tahun')->orderBy('bulan')]);
        return view('finance.assets.show', compact('asset'));
    }

    public function postDepreciation(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
            'bulan' => 'required|integer|min:1|max:12',
        ]);
        try {
            $r = $this->service->postDepreciation($data['tahun'], $data['bulan']);
            return back()->with('success', "Penyusutan diposting: {$r['processed']} aset diproses, {$r['skipped']} dilewati (sudah ada).");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'kode'                => "required|string|max:30|unique:finance_assets,kode" . ($id ? ",$id" : ''),
            'nama'                => 'required|string|max:255',
            'kategori'            => 'nullable|string|max:255',
            'tanggal_perolehan'   => 'required|date',
            'harga_perolehan'     => 'required|numeric|min:0',
            'nilai_residu'        => 'required|numeric|min:0',
            'umur_ekonomis_bulan' => 'required|integer|min:1',
            'metode_penyusutan'   => 'required|in:garis_lurus,saldo_menurun',
            'status'              => 'required|in:aktif,rusak,dijual,dihapus',
            'lokasi'              => 'nullable|string|max:255',
            'keterangan'          => 'nullable|string',
            'account_id'          => 'nullable|exists:finance_accounts,id',
        ]);
    }
}
