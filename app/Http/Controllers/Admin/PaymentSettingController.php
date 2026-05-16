<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $setting = PaymentSetting::first() ?? new PaymentSetting();
        return view('admin.payment_settings.index', compact('setting'));
    }

    public function testConnection(): JsonResponse
    {
        $setting = PaymentSetting::first();

        if (!$setting) {
            return response()->json(['success' => false, 'message' => 'Pengaturan belum dikonfigurasi.']);
        }

        $gateway = $setting->active_gateway;

        if ($gateway !== 'tripay') {
            return response()->json(['success' => false, 'message' => ucfirst($gateway) . ' belum terintegrasi penuh. Tes koneksi tidak tersedia.']);
        }

        if (!filled($setting->tripay_api_key)) {
            return response()->json(['success' => false, 'message' => 'API Key Tripay belum diisi.']);
        }

        $mode   = $setting->tripay_mode ?? 'sandbox';
        $apiUrl = $mode === 'production'
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $apiUrl . '/merchant/profile',
                CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $setting->tripay_api_key],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
            ]);
            $response = curl_exec($ch);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return response()->json(['success' => false, 'message' => 'Koneksi gagal: ' . $error]);
            }

            $result = json_decode((string) $response, true);

            if ($result['success'] ?? false) {
                $merchant = $result['data']['name'] ?? 'Unknown';
                return response()->json(['success' => true, 'message' => 'Koneksi berhasil! Merchant: ' . $merchant]);
            }

            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Respon tidak dikenali dari Tripay.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'active_gateway' => 'required|in:tripay,midtrans,xendit',
            'tripay_api_key' => 'nullable|string',
            'tripay_private_key' => 'nullable|string',
            'tripay_merchant_code' => 'nullable|string',
            'tripay_mode' => 'required|in:sandbox,production',
            'midtrans_client_key' => 'nullable|string',
            'midtrans_server_key' => 'nullable|string',
            'midtrans_is_production' => 'boolean',
            'xendit_secret_key' => 'nullable|string',
        ]);

        $setting = PaymentSetting::first() ?? new PaymentSetting();
        
        $setting->fill([
            'active_gateway' => $request->active_gateway,
            'tripay_api_key' => $request->tripay_api_key,
            'tripay_private_key' => $request->tripay_private_key,
            'tripay_merchant_code' => $request->tripay_merchant_code,
            'tripay_mode' => $request->tripay_mode,
            'midtrans_client_key' => $request->midtrans_client_key,
            'midtrans_server_key' => $request->midtrans_server_key,
            'midtrans_is_production' => $request->has('midtrans_is_production'),
            'xendit_secret_key' => $request->xendit_secret_key,
        ]);
        
        $setting->save();

        return redirect()->route('admin.payment-settings.index')
            ->with('success', 'Pengaturan Payment Gateway berhasil disimpan.');
    }
}
