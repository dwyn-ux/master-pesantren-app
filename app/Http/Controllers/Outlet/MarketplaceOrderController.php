<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketplaceOrderController extends Controller
{
    public function index(Request $request)
    {
        $outlet = $this->kantinOutlet();

        $orders = Order::with(['wali', 'tujuanSantri', 'items.produk'])
            ->whereHas('items.produk', fn ($query) => $query->where('outlet_id', $outlet->id))
            ->when($request->status, fn ($query) => $query->where('status', $request->status))
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;

                $query->where('id', $search)
                    ->orWhereHas('wali', fn ($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('tujuanSantri', fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statusOptions = $this->statusOptions();

        return view('outlet.orders.index', compact('outlet', 'orders', 'statusOptions'));
    }

    public function show(Order $order)
    {
        $outlet = $this->kantinOutlet();
        $this->ensureOwnOrder($order, $outlet);

        $order->load(['wali.user', 'tujuanSantri', 'items.produk']);
        $statusOptions = $this->statusOptions();

        return view('outlet.orders.show', compact('outlet', 'order', 'statusOptions'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $outlet = $this->kantinOutlet();
        $this->ensureOwnOrder($order, $outlet);

        $request->validate([
            'status' => 'required|in:pending,diproses,selesai,dibatalkan',
        ]);

        $newStatus = $request->status;

        if ($order->status === 'dibatalkan') {
            return back()->with('error', 'Order yang sudah dibatalkan tidak bisa diubah lagi.');
        }

        if ($order->status === 'selesai' && $newStatus !== 'selesai') {
            return back()->with('error', 'Order yang sudah selesai tidak bisa dikembalikan statusnya.');
        }

        DB::transaction(function () use ($order, $newStatus, $outlet) {
            $order->load('items.produk');

            if ($newStatus === 'dibatalkan' && $order->status !== 'dibatalkan') {
                foreach ($order->items as $item) {
                    if ($item->produk && (int) $item->produk->outlet_id === (int) $outlet->id) {
                        $item->produk->increment('stok', $item->qty);
                    }
                }
            }

            $order->update(['status' => $newStatus]);
        });

        return back()->with('success', 'Status order berhasil diperbarui.');
    }

    private function kantinOutlet(): Outlet
    {
        $outlet = Auth::user()->outlet;

        if (! $outlet || $outlet->tipe !== 'kantin' || ! $outlet->is_aktif) {
            abort(403, 'Hanya admin outlet kantin yang bisa mengakses marketplace.');
        }

        return $outlet;
    }

    private function ensureOwnOrder(Order $order, Outlet $outlet): void
    {
        $belongsToOutlet = $order->items()
            ->whereHas('produk', fn ($query) => $query->where('outlet_id', $outlet->id))
            ->exists();

        if (! $belongsToOutlet) {
            abort(403);
        }
    }

    private function statusOptions(): array
    {
        return [
            'pending' => 'Menunggu',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
    }
}
