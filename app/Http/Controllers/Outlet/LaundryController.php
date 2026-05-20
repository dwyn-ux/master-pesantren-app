<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use App\Models\LaundryOrder;
use App\Models\Outlet;
use App\Models\Santri;
use App\Models\TarifLaundry;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaundryController extends Controller
{
    public function index(Request $request)
    {
        $outlet = $this->laundryOutlet();
        $tarif = TarifLaundry::aktif();

        $orders = LaundryOrder::with('santri')
            ->where('outlet_id', $outlet->id)
            ->when($request->status, fn ($query) => $query->where('status', $request->status))
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->where('nomor_tiket', 'ilike', "%{$search}%")
                    ->orWhereHas('santri', fn ($q) => $q->where('nama', 'ilike', "%{$search}%")->orWhere('nis', 'ilike', "%{$search}%"));
            })
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $statusOptions = $this->statusOptions();

        return view('outlet.laundry.index', compact('outlet', 'tarif', 'orders', 'statusOptions'));
    }

    public function create()
    {
        $outlet = $this->laundryOutlet();
        $tarif = TarifLaundry::aktif();

        return view('outlet.laundry.form', compact('outlet', 'tarif'));
    }

    public function store(Request $request)
    {
        $outlet = $this->laundryOutlet();
        $tarif = TarifLaundry::aktif();

        $request->validate([
            'fingerprint_id' => 'required|string|max:100',
            'berat_kg' => 'required|numeric|min:1|max:99.99',
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            $order = DB::transaction(function () use ($request, $outlet, $tarif) {
                $santri = Santri::where('fingerprint_id', $request->fingerprint_id)
                    ->where('is_aktif', true)
                    ->lockForUpdate()
                    ->first();

                if (! $santri) {
                    throw new \RuntimeException('Fingerprint tidak dikenali atau santri tidak aktif.');
                }

                $berat = round((float) $request->berat_kg, 2);
                $total = (int) ceil($berat * $tarif->harga_per_kg);

                if ($santri->saldo < $total) {
                    throw new \RuntimeException('Saldo uang saku tidak cukup. Saldo saat ini Rp '.number_format($santri->saldo).', total laundry Rp '.number_format($total).'.');
                }

                $santri->checkLimitTransaksi($total);

                $saldoSebelum = $santri->saldo;
                $saldoSesudah = $saldoSebelum - $total;

                $order = LaundryOrder::create([
                    'santri_id' => $santri->id,
                    'outlet_id' => $outlet->id,
                    'nomor_tiket' => $this->generateTicketNumber(),
                    'berat_kg' => $berat,
                    'harga_per_kg' => $tarif->harga_per_kg,
                    'total' => $total,
                    'status' => 'diterima',
                    'fingerprint_verified' => true,
                    'catatan' => $request->catatan,
                    'tanggal_antar' => now(),
                ]);

                $santri->update(['saldo' => $saldoSesudah]);

                WalletTransaction::create([
                    'santri_id' => $santri->id,
                    'tipe' => 'laundry',
                    'referensi_id' => $order->id,
                    'referensi_tipe' => LaundryOrder::class,
                    'nominal' => $total,
                    'jenis' => 'debit',
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                    'keterangan' => 'Laundry '.$order->nomor_tiket,
                ]);

                return $order->load('santri');
            });

            return redirect()->route('outlet.laundry.show', $order)
                ->with('success', 'Order laundry berhasil dibuat.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(LaundryOrder $laundry)
    {
        $outlet = $this->laundryOutlet();

        if ((int) $laundry->outlet_id !== (int) $outlet->id) {
            abort(403);
        }

        $laundry->load('santri');
        $statusOptions = $this->statusOptions();

        return view('outlet.laundry.show', compact('outlet', 'laundry', 'statusOptions'));
    }

    public function updateStatus(Request $request, LaundryOrder $laundry)
    {
        $outlet = $this->laundryOutlet();

        if ((int) $laundry->outlet_id !== (int) $outlet->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:diterima,dicuci,selesai,diambil',
        ]);

        $updates = ['status' => $request->status];

        if ($request->status === 'selesai' && ! $laundry->tanggal_selesai) {
            $updates['tanggal_selesai'] = now();
        }

        if ($request->status === 'diambil' && ! $laundry->tanggal_diambil) {
            $updates['tanggal_diambil'] = now();
        }

        $laundry->update($updates);

        return back()->with('success', 'Status laundry berhasil diperbarui.');
    }

    private function laundryOutlet(): Outlet
    {
        $outlet = Auth::user()->outlet;

        if (! $outlet || $outlet->tipe !== 'laundry' || ! $outlet->is_aktif) {
            abort(403, 'Hanya admin outlet laundry yang bisa mengakses modul laundry.');
        }

        return $outlet;
    }

    private function generateTicketNumber(): string
    {
        $prefix = 'LDR-'.now()->format('Ymd').'-';
        $countToday = LaundryOrder::whereDate('created_at', now()->toDateString())->count() + 1;

        return $prefix.str_pad((string) $countToday, 3, '0', STR_PAD_LEFT);
    }

    private function statusOptions(): array
    {
        return [
            'diterima' => 'Diterima',
            'dicuci' => 'Dicuci',
            'selesai' => 'Selesai',
            'diambil' => 'Diambil',
        ];
    }
}
