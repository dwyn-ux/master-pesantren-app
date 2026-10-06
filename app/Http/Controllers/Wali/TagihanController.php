<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\PaymentSetting;
use App\Models\Santri;
use App\Models\Tagihan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TagihanController extends Controller
{
    public function index()
    {
        $wali = auth()->user()->wali;
        if (!$wali) abort(403);

        $santriIds = $wali->santri->pluck('id');

        $belumBayar = Tagihan::with(['santri', 'jenisTagihan'])
            ->whereIn('santri_id', $santriIds)
            ->where('status', 'belum_bayar')
            ->orderBy('due_date')
            ->get()
            ->map(function ($t) use ($wali) {
                // Cek apakah ada pembayaran pending untuk tagihan ini (baik single maupun batch)
                $pPending = Pembayaran::where('status', 'pending')
                    ->where('wali_id', $wali->id)
                    ->where(function($q) use ($t) {
                        $q->where('tagihan_id', $t->id)
                          ->orWhereJsonContains('tagihan_ids', $t->id);
                    })->first();
                
                $t->payment_status = $pPending ? 'pending' : null;
                $t->payment_url = $pPending ? $pPending->payment_url : null;
                $t->pending_payment_id = $pPending?->id;
                return $t;
            });

        $riwayat = Tagihan::with(['santri', 'jenisTagihan'])
            ->whereIn('santri_id', $santriIds)
            ->whereIn('status', ['lunas', 'sebagian'])
            ->latest('updated_at')
            ->paginate(15);

        $totalTunggakan = $belumBayar->sum('nominal');

        return view('wali.tagihan.index', compact('belumBayar', 'riwayat', 'totalTunggakan', 'wali'));
    }

    public function checkout(Request $request): JsonResponse
    {
        $wali = auth()->user()->wali;
        if (!$wali) return response()->json(['message' => 'Unauthorized'], 403);

        $request->validate([
            'tagihan_ids'          => 'required|array|min:1',
            'tagihan_ids.*'        => 'integer|exists:tagihan,id',
            'topup_items'          => 'nullable|array',
            'topup_items.*.santri_id' => 'required|integer',
            'topup_items.*.nominal'   => 'required|integer|min:10000|max:10000000',
        ]);

        $santriIds = $wali->santri->pluck('id');

        // Authorize tagihans
        $tagihans = Tagihan::with(['santri', 'jenisTagihan'])
            ->whereIn('id', $request->tagihan_ids)
            ->whereIn('santri_id', $santriIds)
            ->where('status', 'belum_bayar')
            ->get();

        if ($tagihans->count() !== count($request->tagihan_ids)) {
            return response()->json(['message' => 'Beberapa tagihan tidak valid atau sudah lunas.'], 422);
        }

        // Authorize topup santri
        $topupItems = collect($request->topup_items ?? [])
            ->filter(fn($i) => ($i['nominal'] ?? 0) > 0)
            ->values();

        foreach ($topupItems as $item) {
            if (!$santriIds->contains($item['santri_id'])) {
                return response()->json(['message' => 'Santri untuk top up tidak valid.'], 422);
            }
        }

        $tagihanTotal = $tagihans->sum('nominal');
        $topupTotal   = $topupItems->sum('nominal');
        $totalNominal = $tagihanTotal + $topupTotal;

        $setting = PaymentSetting::first();
        if (!$setting) {
            return response()->json(['message' => 'Pengaturan pembayaran belum dikonfigurasi.'], 503);
        }

        $gateway = $setting->active_gateway ?? 'tripay';

        $merchantRef = 'BATCH-' . Str::ulid();

        if ($gateway === 'midtrans') {
            return $this->checkoutMidtrans($wali, $tagihans, $topupItems, $totalNominal, $merchantRef, $setting);
        }

        if ($gateway !== 'tripay') {
            return response()->json(['message' => 'Gateway pembayaran yang dipilih belum didukung.'], 503);
        }

        // Default: Tripay QRIS
        return $this->checkoutTripay($wali, $tagihans, $topupItems, $totalNominal, $merchantRef, $setting);
    }

    public function show(Tagihan $tagihan)
    {
        $wali = auth()->user()->wali;
        $this->authorizeTagihan($tagihan, $wali?->id);

        $tagihan->load(['santri', 'jenisTagihan']);

        $pembayaran = Pembayaran::query()
            ->where('wali_id', $wali->id)
            ->where(function ($query) use ($tagihan) {
                $query->where('tagihan_id', $tagihan->id)
                    ->orWhereJsonContains('tagihan_ids', $tagihan->id);
            })
            ->latest()
            ->first();

        return view('wali.tagihan.show', compact('tagihan', 'pembayaran'));
    }

    public function pay(Tagihan $tagihan)
    {
        $wali = auth()->user()->wali;
        $this->authorizeTagihan($tagihan, $wali?->id);

        if ($tagihan->status === 'lunas') {
            return redirect()->route('wali.tagihan.show', $tagihan)
                ->with('error', 'Tagihan ini sudah lunas.');
        }

        $pending = $this->pendingPaymentFor($tagihan, $wali->id);

        if ($pending) {
            return redirect()->route('wali.tagihan.show', $tagihan)
                ->with('error', 'Tagihan ini masih memiliki pembayaran yang menunggu proses.');
        }

        return view('wali.tagihan.pay', compact('tagihan'));
    }

    public function processPayment(Request $request, Tagihan $tagihan)
    {
        $wali = auth()->user()->wali;
        $this->authorizeTagihan($tagihan, $wali?->id);

        $request->validate([
            'metode' => 'required|in:manual_transfer',
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'manual_note' => 'nullable|string|max:500',
        ]);

        if ($tagihan->status === 'lunas') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        if ($this->pendingPaymentFor($tagihan, $wali->id)) {
            return back()->with('error', 'Masih ada pembayaran yang menunggu proses untuk tagihan ini.');
        }

        $proofPath = $request->file('proof')->store('payment-proofs', 'local');

        try {
            $payment = Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'wali_id' => $wali->id,
                'nominal' => $tagihan->nominal,
                'metode' => 'manual',
                'manual_type' => 'transfer',
                'tripay_ref' => 'MANUAL-' . Str::ulid(),
                'proof_path' => $proofPath,
                'proof_original_name' => $request->file('proof')->getClientOriginalName(),
                'submitted_at' => now(),
                'manual_note' => $request->manual_note,
                'status' => 'pending',
            ]);
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($proofPath);
            throw $e;
        }

        return redirect()->route('wali.tagihan.show', $tagihan)
            ->with('success', 'Bukti transfer berhasil dikirim dan menunggu konfirmasi admin.');
    }

    public function cancelPayment(Pembayaran $pembayaran)
    {
        $wali = auth()->user()->wali;

        if (! $wali || (int) $pembayaran->wali_id !== (int) $wali->id) {
            abort(403);
        }

        if ($pembayaran->status !== 'pending') {
            return back()->with('error', 'Hanya pembayaran yang masih menunggu yang dapat dibatalkan.');
        }

        $pembayaran->update(['status' => 'failed']);

        return back()->with('success', 'Pembayaran dibatalkan. Anda dapat memilih metode lain.');
    }

    public function proof(Pembayaran $pembayaran)
    {
        $wali = auth()->user()->wali;

        if (! $wali || (int) $pembayaran->wali_id !== (int) $wali->id) {
            abort(403);
        }

        abort_unless($pembayaran->proof_path && Storage::disk('local')->exists($pembayaran->proof_path), 404);

        return Storage::disk('local')->download(
            $pembayaran->proof_path,
            $pembayaran->proof_original_name ?: basename($pembayaran->proof_path),
        );
    }

    // ──────────────────────────────────────────────
    // Gateway: Tripay QRIS
    // ──────────────────────────────────────────────

    private function checkoutTripay($wali, $tagihans, $topupItems, int $totalNominal, string $merchantRef, PaymentSetting $setting): JsonResponse
    {
        if (!$this->hasTripayConfig($setting)) {
            return response()->json(['message' => 'Konfigurasi Tripay belum lengkap.'], 503);
        }

        $pembayaran = DB::transaction(function () use ($wali, $tagihans, $topupItems, $totalNominal, $merchantRef, $setting) {
            $p = Pembayaran::create([
                'tagihan_id'     => null,
                'tagihan_ids'    => $tagihans->pluck('id')->toArray(),
                'topup_items'    => $topupItems->toArray(),
                'wali_id'        => $wali->id,
                'nominal'        => $totalNominal,
                'metode'         => 'qris',
                'tripay_ref'     => $merchantRef,
                'tripay_channel' => 'QRIS',
                'status'         => 'pending',
            ]);
            return $p;
        });

        $orderItems = $tagihans->map(fn($t) => [
            'sku'      => 'TAGIHAN-' . $t->id,
            'name'     => ($t->jenisTagihan->nama ?? 'Tagihan') . ' ' . $t->periode . ' - ' . $t->santri->nama,
            'price'    => (int) $t->nominal,
            'quantity' => 1,
        ])->toArray();

        foreach ($topupItems as $item) {
            $orderItems[] = [
                'sku'      => 'TOPUP-' . $item['santri_id'],
                'name'     => 'Top Up Saldo',
                'price'    => (int) $item['nominal'],
                'quantity' => 1,
            ];
        }

        $mode   = $setting->tripay_mode ?? 'sandbox';
        $apiUrl = $mode === 'production' ? 'https://tripay.co.id/api' : 'https://tripay.co.id/api-sandbox';

        $amount    = (int) $totalNominal;
        $signature = hash_hmac('sha256', $setting->tripay_merchant_code . $merchantRef . $amount, $setting->tripay_private_key);

        $payload = [
            'method'        => 'QRIS',
            'merchant_ref'  => $merchantRef,
            'amount'        => $amount,
            'customer_name' => $wali->nama,
            'customer_email'=> auth()->user()->email ?? 'wali@pesantren.id',
            'customer_phone'=> $wali->no_hp ?? '',
            'order_items'   => $orderItems,
            'return_url'    => route('wali.tagihan.index'),
            'callback_url'  => route('api.tripay-callback'),
            'expired_time'  => now()->addDay()->timestamp,
            'signature'     => $signature,
        ];

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $apiUrl . '/transaction/create',
                CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $setting->tripay_api_key],
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $response = curl_exec($ch);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) {
                $pembayaran->delete();
                return response()->json(['message' => 'Tripay error: ' . $error], 502);
            }

            $result = json_decode((string) $response, true);

            if ($result['success'] ?? false) {
                $pembayaran->update([
                    'tripay_ref'  => $result['data']['reference'] ?? $merchantRef,
                    'payment_url' => $result['data']['checkout_url'] ?? ($result['data']['pay_url'] ?? ''),
                ]);

                return response()->json([
                    'type' => 'redirect',
                    'url'  => $result['data']['checkout_url'] ?? ($result['data']['pay_url'] ?? route('wali.tagihan.index')),
                ]);
            }

            $pembayaran->delete();
            return response()->json(['message' => $result['message'] ?? 'Tripay error'], 502);
        } catch (\Throwable $e) {
            $pembayaran->delete();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ──────────────────────────────────────────────
    // Gateway: Midtrans Snap
    // ──────────────────────────────────────────────

    private function checkoutMidtrans($wali, $tagihans, $topupItems, int $totalNominal, string $merchantRef, PaymentSetting $setting): JsonResponse
    {
        if (!filled($setting->midtrans_server_key)) {
            return response()->json(['message' => 'Konfigurasi Midtrans belum lengkap.'], 503);
        }

        $pembayaran = DB::transaction(function () use ($wali, $tagihans, $topupItems, $totalNominal, $merchantRef) {
            return Pembayaran::create([
                'tagihan_id'  => null,
                'tagihan_ids' => $tagihans->pluck('id')->toArray(),
                'topup_items' => $topupItems->toArray(),
                'wali_id'     => $wali->id,
                'nominal'     => $totalNominal,
                'metode'      => 'midtrans_snap',
                'tripay_ref'  => $merchantRef,
                'status'      => 'pending',
            ]);
        });

        $itemDetails = $tagihans->map(fn($t) => [
            'id'       => 'TAGIHAN-' . $t->id,
            'price'    => (int) $t->nominal,
            'quantity' => 1,
            'name'     => substr(($t->jenisTagihan->nama ?? 'Tagihan') . ' ' . $t->periode . ' - ' . $t->santri->nama, 0, 50),
        ])->toArray();

        foreach ($topupItems as $item) {
            $itemDetails[] = [
                'id'       => 'TOPUP-' . $item['santri_id'],
                'price'    => (int) $item['nominal'],
                'quantity' => 1,
                'name'     => 'Top Up Saldo Santri',
            ];
        }

        $user    = auth()->user();
        $payload = [
            'transaction_details' => [
                'order_id'     => $merchantRef,
                'gross_amount' => (int) $totalNominal,
            ],
            'customer_details' => [
                'first_name' => $wali->nama,
                'email'      => $user->email ?? 'wali@pesantren.id',
                'phone'      => $wali->no_hp ?? '',
            ],
            'item_details' => $itemDetails,
        ];

        $isProduction = $setting->midtrans_is_production ?? false;
        $snapUrl      = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $authHeader = 'Basic ' . base64_encode($setting->midtrans_server_key . ':');

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $snapUrl,
                CURLOPT_HTTPHEADER     => ['Authorization: ' . $authHeader, 'Content-Type: application/json'],
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $response = curl_exec($ch);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) {
                $pembayaran->delete();
                return response()->json(['message' => 'Midtrans error: ' . $error], 502);
            }

            $result = json_decode((string) $response, true);

            if (!empty($result['token'])) {
                $pembayaran->update(['snap_token' => $result['token']]);

                return response()->json([
                    'type'       => 'snap',
                    'token'      => $result['token'],
                    'return_url' => route('wali.tagihan.index'),
                ]);
            }

            $pembayaran->delete();
            return response()->json(['message' => $result['error_messages'][0] ?? 'Midtrans error'], 502);
        } catch (\Throwable $e) {
            $pembayaran->delete();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function pollStatus(Request $request): JsonResponse
    {
        $wali = auth()->user()->wali;
        if (!$wali) return response()->json([], 403);

        $ids = $request->input('ids', []);
        if (empty($ids)) return response()->json([]);

        $santriIds = $wali->santri->pluck('id');

        $tagihans = Tagihan::whereIn('id', $ids)
            ->whereIn('santri_id', $santriIds)
            ->get(['id', 'status']);

        return response()->json(
            $tagihans->mapWithKeys(fn($t) => [$t->id => $t->status])
        );
    }

    private function hasTripayConfig(PaymentSetting $setting): bool
    {
        return filled($setting->tripay_api_key)
            && filled($setting->tripay_private_key)
            && filled($setting->tripay_merchant_code);
    }

    private function authorizeTagihan(Tagihan $tagihan, ?int $waliId): void
    {
        $authorized = $waliId && Santri::query()
            ->whereKey($tagihan->santri_id)
            ->whereHas('wali', fn ($query) => $query->whereKey($waliId))
            ->exists();

        if (! $authorized) {
            abort(403);
        }
    }

    private function pendingPaymentFor(Tagihan $tagihan, int $waliId): ?Pembayaran
    {
        return Pembayaran::query()
            ->where('wali_id', $waliId)
            ->where('status', 'pending')
            ->where(function ($query) use ($tagihan) {
                $query->where('tagihan_id', $tagihan->id)
                    ->orWhereJsonContains('tagihan_ids', $tagihan->id);
            })
            ->latest()
            ->first();
    }
}
