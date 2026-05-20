<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Santri;
use App\Models\TransaksiKasir;
use App\Models\TransaksiKasirItem;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirKantinController extends Controller
{
    public function index()
    {
        $outlet = $this->kantinOutlet();

        $produk = Produk::where('outlet_id', $outlet->id)
            ->where('is_aktif', true)
            ->where('stok', '>', 0)
            ->orderBy('nama')
            ->get();

        $santriList = Santri::where('is_aktif', true)->orderBy('nama')->get();

        return view('outlet.kasir.kantin.index', compact('outlet', 'produk', 'santriList'));
    }

    public function store(Request $request)
    {
        $outlet = $this->kantinOutlet();

        $request->validate([
            'auth_method' => 'required|in:fingerprint,rfid,manual',
            'auth_value' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            $transaksi = DB::transaction(function () use ($request, $outlet) {
                $santriQuery = Santri::where('is_aktif', true)->lockForUpdate();
                
                if ($request->auth_method === 'fingerprint') {
                    $santri = $santriQuery->where('fingerprint_id', $request->auth_value)->first();
                } elseif ($request->auth_method === 'rfid') {
                    $santri = $santriQuery->where('rfid_uid', $request->auth_value)->first();
                } else {
                    $santri = $santriQuery->where('id', $request->auth_value)->first();
                }

                if (! $santri) {
                    throw new \RuntimeException('Identitas santri tidak dikenali atau santri tidak aktif.');
                }

                $total = 0;
                $items = [];

                foreach ($request->items as $item) {
                    $produk = Produk::whereKey($item['produk_id'])
                        ->where('outlet_id', $outlet->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (! $produk->is_aktif) {
                        throw new \RuntimeException("Produk {$produk->nama} sedang nonaktif.");
                    }

                    if ($produk->stok < $item['qty']) {
                        throw new \RuntimeException("Stok {$produk->nama} tidak mencukupi.");
                    }

                    $subtotal = $produk->harga * $item['qty'];
                    $total += $subtotal;

                    $items[] = [
                        'produk' => $produk,
                        'qty' => $item['qty'],
                        'nama_produk' => $produk->nama,
                        'harga_satuan' => $produk->harga,
                        'subtotal' => $subtotal,
                    ];
                }

                if ($santri->saldo < $total) {
                    throw new \RuntimeException('Saldo uang saku tidak cukup. Saldo saat ini Rp '.number_format($santri->saldo).', total transaksi Rp '.number_format($total).'.');
                }

                $santri->checkLimitTransaksi($total);

                $saldoSebelum = $santri->saldo;
                $saldoSesudah = $saldoSebelum - $total;

                $transaksi = TransaksiKasir::create([
                    'santri_id' => $santri->id,
                    'outlet_id' => $outlet->id,
                    'total' => $total,
                    'fingerprint_verified' => true,
                ]);

                foreach ($items as $item) {
                    TransaksiKasirItem::create([
                        'transaksi_kasir_id' => $transaksi->id,
                        'produk_id' => $item['produk']->id,
                        'nama_produk' => $item['nama_produk'],
                        'harga_satuan' => $item['harga_satuan'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    $item['produk']->decrement('stok', $item['qty']);
                }

                $santri->update(['saldo' => $saldoSesudah]);

                WalletTransaction::create([
                    'santri_id' => $santri->id,
                    'tipe' => 'kantin',
                    'referensi_id' => $transaksi->id,
                    'referensi_tipe' => TransaksiKasir::class,
                    'nominal' => $total,
                    'jenis' => 'debit',
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                    'keterangan' => 'Belanja kantin di '.$outlet->nama,
                ]);

                return $transaksi->load(['santri', 'items']);
            });

            return redirect()->route('outlet.kasir.kantin.show', $transaksi)
                ->with('success', 'Transaksi kantin berhasil.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(TransaksiKasir $transaksi)
    {
        $outlet = $this->kantinOutlet();

        if ((int) $transaksi->outlet_id !== (int) $outlet->id) {
            abort(403);
        }

        $transaksi->load(['santri', 'items.produk']);

        return view('outlet.kasir.kantin.show', compact('outlet', 'transaksi'));
    }

    public function history(Request $request)
    {
        $outlet = $this->kantinOutlet();

        $transaksi = TransaksiKasir::with(['santri', 'items'])
            ->where('outlet_id', $outlet->id)
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->whereHas('santri', fn ($q) => $q->where('nama', 'ilike', "%{$search}%")->orWhere('nis', 'ilike', "%{$search}%"));
            })
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('outlet.kasir.kantin.history', compact('outlet', 'transaksi'));
    }

    private function kantinOutlet(): Outlet
    {
        $outlet = Auth::user()->outlet;

        if (! $outlet || $outlet->tipe !== 'kantin' || ! $outlet->is_aktif) {
            abort(403, 'Hanya admin outlet kantin yang bisa mengakses kasir kantin.');
        }

        return $outlet;
    }
}
