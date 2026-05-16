<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $outlet = $this->kantinOutlet();

        $produk = Produk::where('outlet_id', $outlet->id)
            ->when($request->search, fn ($query) => $query->where('nama', 'like', "%{$request->search}%")->orWhere('barcode', 'like', "%{$request->search}%"))
            ->when($request->status === 'aktif', fn ($query) => $query->where('is_aktif', true))
            ->when($request->status === 'nonaktif', fn ($query) => $query->where('is_aktif', false))
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        return view('outlet.produk.index', compact('outlet', 'produk'));
    }

    public function create()
    {
        $outlet = $this->kantinOutlet();

        return view('outlet.produk.form', ['outlet' => $outlet, 'produk' => null]);
    }

    public function store(Request $request)
    {
        $outlet = $this->kantinOutlet();

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'barcode' => 'nullable|string|max:50|unique:produk,barcode',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'foto' => 'nullable|image|max:2048',
            'is_aktif' => 'nullable|boolean',
        ]);

        $validated['outlet_id'] = $outlet->id;
        $validated['is_aktif'] = $request->boolean('is_aktif', true);

        if ($request->hasFile('foto')) {
            $validated['foto'] = Storage::disk('public')->url($request->file('foto')->store('produk', 'public'));
        }

        Produk::create($validated);

        return redirect()->route('outlet.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $outlet = $this->kantinOutlet();
        $this->ensureOwnProduct($produk, $outlet);

        return view('outlet.produk.form', compact('outlet', 'produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        $outlet = $this->kantinOutlet();
        $this->ensureOwnProduct($produk, $outlet);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'barcode' => "nullable|string|max:50|unique:produk,barcode,{$produk->id}",
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'foto' => 'nullable|image|max:2048',
            'is_aktif' => 'nullable|boolean',
        ]);

        $validated['is_aktif'] = $request->boolean('is_aktif');

        if ($request->hasFile('foto')) {
            $validated['foto'] = Storage::disk('public')->url($request->file('foto')->store('produk', 'public'));
        }

        $produk->update($validated);

        return redirect()->route('outlet.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $outlet = $this->kantinOutlet();
        $this->ensureOwnProduct($produk, $outlet);

        $produk->update(['is_aktif' => false]);

        return back()->with('success', 'Produk dinonaktifkan.');
    }

    private function kantinOutlet(): Outlet
    {
        $outlet = Auth::user()->outlet;

        if (! $outlet || $outlet->tipe !== 'kantin' || ! $outlet->is_aktif) {
            abort(403, 'Hanya admin outlet kantin yang bisa mengakses marketplace.');
        }

        return $outlet;
    }

    private function ensureOwnProduct(Produk $produk, Outlet $outlet): void
    {
        if ((int) $produk->outlet_id !== (int) $outlet->id) {
            abort(403);
        }
    }
}
