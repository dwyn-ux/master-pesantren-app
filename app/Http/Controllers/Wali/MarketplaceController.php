<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produk;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketplaceController extends Controller
{
    public function index()
    {
        $produk = Produk::aktif()
            ->where("stok", ">", 0)
            ->whereHas(
                "outlet",
                fn($query) => $query
                    ->where("tipe", "kantin")
                    ->where("is_aktif", true),
            )
            ->orderBy("nama")
            ->get();

        $santriList = Auth::user()->wali?->santri ?? collect();

        return view("wali.marketplace.index", compact("produk", "santriList"));
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            "santri_id" => "required|exists:santri,id",
            "items" => "required|array|min:1",
            "items.*.produk_id" => "required|exists:produk,id",
            "items.*.qty" => "required|integer|min:1",
        ]);

        $wali = Auth::user()->wali;

        if (!$wali) {
            abort(403, "Profil wali tidak ditemukan.");
        }

        $santri = Santri::findOrFail($request->santri_id);

        // Validasi santri milik wali ini
        if (!$wali->santri->contains($santri)) {
            return back()->with(
                "error",
                "Santri tidak ditemukan dalam daftar anak Anda.",
            );
        }

        DB::beginTransaction();
        try {
            $total = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item["produk_id"]);

                $produk->loadMissing("outlet");

                if (
                    !$produk->is_aktif ||
                    !$produk->outlet ||
                    $produk->outlet->tipe !== "kantin" ||
                    !$produk->outlet->is_aktif
                ) {
                    throw new \Exception(
                        "Produk {$produk->nama} tidak tersedia di marketplace kantin.",
                    );
                }

                if ($produk->stok < $item["qty"]) {
                    throw new \Exception(
                        "Stok {$produk->nama} tidak mencukupi.",
                    );
                }

                $subtotal = $produk->harga * $item["qty"];
                $total += $subtotal;

                $orderItems[] = [
                    "produk" => $produk,
                    "produk_id" => $produk->id,
                    "nama_produk" => $produk->nama,
                    "harga" => $produk->harga,
                    "qty" => $item["qty"],
                    "subtotal" => $subtotal,
                ];
            }

            // Buat order dari wali. Pembayaran online/manual bisa diproses admin pada sprint pembayaran marketplace.
            $order = Order::create([
                "wali_id" => $wali->id,
                "tujuan_santri_id" => $santri->id,
                "total" => $total,
                "opsi_terima" => "kirim_ke_santri",
                "status" => "pending",
            ]);

            // Buat order items dan reserve stok supaya tidak overselling.
            foreach ($orderItems as $item) {
                $produk = $item["produk"];
                unset($item["produk"]);

                $item["order_id"] = $order->id;
                OrderItem::create($item);

                $produk->decrement("stok", $item["qty"]);
            }

            DB::commit();

            return redirect()
                ->route("wali.marketplace.success", $order)
                ->with("success", "Order berhasil dibuat!");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with("error", $e->getMessage());
        }
    }

    public function orderSuccess(Order $order)
    {
        // Validasi order milik wali yang login
        if ($order->wali_id !== Auth::user()->wali->id) {
            abort(403);
        }

        $order->load(["items.produk", "tujuanSantri"]);

        return view("wali.marketplace.success", compact("order"));
    }

    public function orders()
    {
        $orders = Order::where("wali_id", Auth::user()->wali->id)
            ->with(["items.produk", "tujuanSantri"])
            ->orderBy("created_at", "desc")
            ->paginate(10);

        return view("wali.marketplace.orders", compact("orders"));
    }

    public function orderDetail(Order $order)
    {
        // Validasi order milik wali yang login
        if ($order->wali_id !== Auth::user()->wali->id) {
            abort(403);
        }

        $order->load(["items.produk", "tujuanSantri"]);

        return view("wali.marketplace.detail", compact("order"));
    }
}
