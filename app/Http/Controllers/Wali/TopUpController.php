<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use App\Models\TopUpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopUpController extends Controller
{
    public function index()
    {
        $wali = auth()->user()->wali;
        if (!$wali) abort(403);

        $requests = TopUpRequest::where('wali_id', $wali->id)
            ->with('santri')
            ->latest()
            ->paginate(15);

        return view('wali.topup.index', compact('requests', 'wali'));
    }

    public function create()
    {
        $wali = auth()->user()->wali;
        if (!$wali) abort(403);

        $santriList = $wali->santri;
        $setting    = PaymentSetting::first();

        return view('wali.topup.create', compact('santriList', 'setting'));
    }

    public function store(Request $request): JsonResponse
    {
        $wali = auth()->user()->wali;
        if (!$wali) return response()->json(['message' => 'Unauthorized'], 403);

        $request->validate([
            'santri_id' => 'required|integer',
            'nominal'   => 'required|integer|min:10000|max:10000000',
        ]);

        if (!$wali->santri()->where('santri.id', $request->santri_id)->exists()) {
            return response()->json(['message' => 'Santri tidak ditemukan.'], 422);
        }

        $setting = PaymentSetting::first();
        if (!$setting) {
            return response()->json(['message' => 'Pengaturan pembayaran belum dikonfigurasi.'], 503);
        }

        $gateway     = $setting->active_gateway ?? 'tripay';
        $merchantRef = 'TOPUP-' . time() . '-' . $request->santri_id;

        if ($gateway === 'midtrans') {
            return $this->storeMidtrans($request, $wali, $setting, $merchantRef);
        }

        return $this->storeTripay($request, $wali, $setting, $merchantRef);
    }

    public function show(TopUpRequest $topUp)
    {
        $wali = auth()->user()->wali;
        if (!$wali || $topUp->wali_id !== $wali->id) abort(403);

        $topUp->load('santri');

        return view('wali.topup.show', compact('topUp'));
    }

    // ──────────────────────────────────────────────
    // Tripay QRIS
    // ──────────────────────────────────────────────

    private function storeTripay(Request $request, $wali, PaymentSetting $setting, string $merchantRef): JsonResponse
    {
        if (!$this->hasTripayConfig($setting)) {
            return response()->json(['message' => 'Konfigurasi Tripay belum lengkap.'], 503);
        }

        $topUp = TopUpRequest::create([
            'wali_id'        => $wali->id,
            'santri_id'      => $request->santri_id,
            'nominal'        => $request->nominal,
            'status'         => 'unpaid',
            'tripay_channel' => 'QRIS',
            'tripay_ref'     => $merchantRef,
        ]);

        $response = $this->createTripayInvoice($topUp, $setting, $merchantRef);

        if (!$response['success']) {
            $topUp->delete();
            return response()->json(['message' => 'Gagal membuat tagihan: ' . $response['message']], 502);
        }

        $topUp->update([
            'tripay_ref'  => $response['reference'],
            'payment_url' => $response['payment_url'],
        ]);

        return response()->json(['type' => 'redirect', 'url' => $response['payment_url']]);
    }

    private function createTripayInvoice(TopUpRequest $topUp, PaymentSetting $setting, string $merchantRef): array
    {
        $topUp->load(['santri', 'wali']);
        $user   = auth()->user();
        $amount = (int) $topUp->nominal;

        $mode   = $setting->tripay_mode ?? 'sandbox';
        $apiUrl = $mode === 'production' ? 'https://tripay.co.id/api' : 'https://tripay.co.id/api-sandbox';

        $signature = hash_hmac('sha256', $setting->tripay_merchant_code . $merchantRef . $amount, $setting->tripay_private_key);

        $payload = [
            'method'        => 'QRIS',
            'merchant_ref'  => $merchantRef,
            'amount'        => $amount,
            'customer_name' => $topUp->wali->nama,
            'customer_email'=> $user->email ?? 'wali@example.test',
            'customer_phone'=> $topUp->wali->no_hp ?? '',
            'order_items'   => [[
                'sku'      => 'TOPUP-' . $topUp->santri->nis,
                'name'     => 'Top Up Saldo Santri ' . $topUp->santri->nama,
                'price'    => $amount,
                'quantity' => 1,
            ]],
            'return_url'   => route('wali.topup.show', $topUp),
            'callback_url' => route('api.tripay-callback'),
            'expired_time' => now()->addDay()->timestamp,
            'signature'    => $signature,
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
            ]);
            $response = curl_exec($ch);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) return ['success' => false, 'message' => $error];

            $result = json_decode((string) $response, true);

            if ($result['success'] ?? false) {
                return [
                    'success'     => true,
                    'reference'   => $result['data']['reference'] ?? $merchantRef,
                    'payment_url' => $result['data']['checkout_url'] ?? ($result['data']['pay_url'] ?? route('wali.topup.show', $topUp)),
                ];
            }

            return ['success' => false, 'message' => $result['message'] ?? 'Unknown error'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ──────────────────────────────────────────────
    // Midtrans Snap
    // ──────────────────────────────────────────────

    private function storeMidtrans(Request $request, $wali, PaymentSetting $setting, string $merchantRef): JsonResponse
    {
        if (!filled($setting->midtrans_server_key)) {
            return response()->json(['message' => 'Konfigurasi Midtrans belum lengkap.'], 503);
        }

        $topUp = TopUpRequest::create([
            'wali_id'   => $wali->id,
            'santri_id' => $request->santri_id,
            'nominal'   => $request->nominal,
            'status'    => 'unpaid',
            'tripay_ref'=> $merchantRef,
        ]);

        $santri  = $topUp->santri;
        $user    = auth()->user();
        $amount  = (int) $request->nominal;

        $payload = [
            'transaction_details' => ['order_id' => $merchantRef, 'gross_amount' => $amount],
            'customer_details'    => [
                'first_name' => $wali->nama,
                'email'      => $user->email ?? 'wali@pesantren.id',
                'phone'      => $wali->no_hp ?? '',
            ],
            'item_details' => [[
                'id'       => 'TOPUP-' . $santri->nis,
                'price'    => $amount,
                'quantity' => 1,
                'name'     => 'Top Up Saldo ' . $santri->nama,
            ]],
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
            ]);
            $response = curl_exec($ch);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) {
                $topUp->delete();
                return response()->json(['message' => 'Midtrans error: ' . $error], 502);
            }

            $result = json_decode((string) $response, true);

            if (!empty($result['token'])) {
                return response()->json([
                    'type'       => 'snap',
                    'token'      => $result['token'],
                    'return_url' => route('wali.topup.show', $topUp),
                ]);
            }

            $topUp->delete();
            return response()->json(['message' => $result['error_messages'][0] ?? 'Midtrans error'], 502);
        } catch (\Throwable $e) {
            $topUp->delete();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function hasTripayConfig(PaymentSetting $setting): bool
    {
        return filled($setting->tripay_api_key)
            && filled($setting->tripay_private_key)
            && filled($setting->tripay_merchant_code);
    }
}
