<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketplaceOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['wali', 'tujuanSantri', 'items'])
            ->when($request->status, fn ($query) => $query->where('status', $request->status))
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;

                $query->where('id', $search)
                    ->orWhereHas('wali', fn ($q) => $q->where('nama', 'ilike', "%{$search}%"))
                    ->orWhereHas('tujuanSantri', fn ($q) => $q->where('nama', 'ilike', "%{$search}%")->orWhere('nis', 'ilike', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statusOptions = $this->statusOptions();

        return view('admin.marketplace.orders.index', compact('orders', 'statusOptions'));
    }

    public function show(Order $order)
    {
        $order->load(['wali.user', 'tujuanSantri', 'items.produk']);
        $statusOptions = $this->statusOptions();

        return view('admin.marketplace.orders.show', compact('order', 'statusOptions'));
    }

    public function updateStatus(Request $request, Order $order)
    {
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

        DB::transaction(function () use ($order, $newStatus) {
            $order->load('items.produk');

            if ($newStatus === 'dibatalkan' && $order->status !== 'dibatalkan') {
                foreach ($order->items as $item) {
                    if ($item->produk) {
                        $item->produk->increment('stok', $item->qty);
                    }
                }
            }

            $order->update(['status' => $newStatus]);
        });

        return back()->with('success', 'Status order berhasil diperbarui.');
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
