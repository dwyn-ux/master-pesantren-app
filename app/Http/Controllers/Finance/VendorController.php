<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Vendor::orderBy('nama');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($w) => $w->where('nama', 'ilike', "%$q%")->orWhere('kode', 'ilike', "%$q%"));
        }
        $items = $query->paginate(25)->withQueryString();
        return view('finance.vendors.index', compact('items'));
    }

    public function create(): View
    {
        return view('finance.vendors.form', ['vendor' => new Vendor()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Vendor::create($this->validateData($request));
        return redirect()->route('finance.vendors.index')->with('success', 'Vendor ditambahkan.');
    }

    public function edit(Vendor $vendor): View
    {
        return view('finance.vendors.form', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update($this->validateData($request, $vendor->id));
        return redirect()->route('finance.vendors.index')->with('success', 'Vendor diperbarui.');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        if ($vendor->payables()->exists()) {
            return back()->with('error', 'Vendor sudah punya hutang, tidak bisa dihapus.');
        }
        $vendor->delete();
        return back()->with('success', 'Vendor dihapus.');
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'kode'           => "required|string|max:20|unique:finance_vendors,kode" . ($id ? ",$id" : ''),
            'nama'           => 'required|string|max:255',
            'kontak_person'  => 'nullable|string|max:255',
            'telepon'        => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'alamat'         => 'nullable|string',
            'npwp'           => 'nullable|string|max:30',
            'bank_nama'      => 'nullable|string|max:50',
            'bank_rekening'  => 'nullable|string|max:30',
            'bank_atas_nama' => 'nullable|string|max:255',
            'is_active'      => 'sometimes|boolean',
        ]);
    }
}
