<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\PaymentSetting;
use App\Models\Tagihan;
use App\Models\TopUpRequest;
use App\Models\Wali;
use App\Services\PaymentSettlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PembayaranController extends Controller
{
    private string $tripayApiUrl;
    private ?string $tripayApiKey;
    private ?string $tripayPrivateKey;
    private ?string $tripayMerchantCode;

    public function __construct(private PaymentSettlementService $settlement)
    {
        $mode = config("services.tripay.mode", "sandbox");
        $this->tripayApiUrl =
            $mode === "production"
                ? "https://tripay.co.id/api"
                : "https://tripay.co.id/api-sandbox";
        $this->tripayApiKey = config("services.tripay.api_key");
        $this->tripayPrivateKey = config("services.tripay.private_key");
        $this->tripayMerchantCode = config("services.tripay.merchant_code");
    }

    public function index(Request $request)
    {
        $pembayaran = Pembayaran::with([
            "tagihan.santri",
            "tagihan.jenisTagihan",
            "wali",
        ])
            ->when(
                $request->status,
                fn($q) => $q->where("status", $request->status),
            )
            ->when(
                $request->metode,
                fn($q) => $q->where("metode", $request->metode),
            )
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->whereHas(
                    "wali",
                    fn($query) => $query->where("nama", "like", "%{$search}%"),
                )->orWhereHas(
                    "tagihan.santri",
                    fn($query) => $query
                        ->where("nama", "like", "%{$search}%")
                        ->orWhere("nis", "like", "%{$search}%"),
                );
            })
            ->latest("created_at")
            ->paginate(20)
            ->withQueryString();

        $statusOptions = [
            "pending" => "Pending",
            "paid" => "Dibayar",
            "failed" => "Gagal",
            "expired" => "Kadaluarsa",
        ];
        $metodeOptions = [
            "va_bca" => "VA BCA",
            "va_mandiri" => "VA Mandiri",
            "qris" => "QRIS",
            "gopay" => "Gopay",
            "ovo" => "OVO",
            "manual" => "Manual",
        ];

        return view(
            "admin.pembayaran.index",
            compact("pembayaran", "statusOptions", "metodeOptions"),
        );
    }

    public function create(Request $request)
    {
        $request->validate(["tagihan_id" => "required|exists:tagihan,id"]);

        $tagihan = Tagihan::with(["santri.wali", "jenisTagihan"])->findOrFail(
            $request->tagihan_id,
        );
        $wali = $this->resolveWaliForTagihan($tagihan);

        if (!$wali) {
            return redirect()
                ->route("admin.tagihan.index")
                ->with(
                    "error",
                    "Tagihan belum punya wali terkait. Hubungkan santri dengan wali terlebih dahulu.",
                );
        }

        $metodeOptions = [
            "va_bca" => "VA BCA",
            "va_mandiri" => "VA Mandiri",
            "qris" => "QRIS",
            "gopay" => "Gopay",
            "ovo" => "OVO",
            "manual_cash" => "Tunai / Cash",
            "manual_transfer" => "Transfer Bank + Bukti",
        ];

        return view(
            "admin.pembayaran.create",
            compact("tagihan", "wali", "metodeOptions"),
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            "tagihan_id" => "required|exists:tagihan,id",
            "metode" => "required|in:va_bca,va_mandiri,qris,gopay,ovo,manual_cash,manual_transfer",
            "proof" => "required_if:metode,manual_transfer|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120",
            "manual_note" => "nullable|string|max:500",
        ]);

        $tagihan = Tagihan::with(["santri.wali", "jenisTagihan"])->findOrFail(
            $request->tagihan_id,
        );
        $wali = $this->resolveWaliForTagihan($tagihan);

        if (!$wali) {
            return back()->with(
                "error",
                "Wali untuk tagihan ini belum terdaftar.",
            );
        }

        if ($tagihan->status === "lunas") {
            return back()->with("error", "Tagihan sudah lunas.");
        }

        if (in_array($request->metode, ["manual_cash", "manual_transfer"], true)) {
            $proofPath = null;

            if ($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('payment-proofs', 'local');
            }

            try {
                $pembayaran = DB::transaction(function () use ($tagihan, $wali, $request, $proofPath) {
                    $pembayaran = Pembayaran::create([
                        "tagihan_id" => $tagihan->id,
                        "wali_id" => $wali->id,
                        "nominal" => $tagihan->nominal,
                        "metode" => "manual",
                        "manual_type" => $request->metode === 'manual_cash' ? 'cash' : 'transfer',
                        "tripay_ref" => 'MANUAL-' . Str::ulid(),
                        "proof_path" => $proofPath,
                        "proof_original_name" => $request->file('proof')?->getClientOriginalName(),
                        "submitted_at" => now(),
                        "confirmed_by" => auth()->id(),
                        "confirmed_at" => now(),
                        "manual_note" => $request->manual_note,
                        "status" => "paid",
                        "paid_at" => now(),
                    ]);

                    $tagihan->update(["status" => "lunas"]);

                    return $pembayaran;
                });
            } catch (\Throwable $e) {
                if ($proofPath) {
                    Storage::disk('local')->delete($proofPath);
                }

                throw $e;
            }

            return redirect()
                ->route("admin.pembayaran.show", $pembayaran)
                ->with("success", "Pembayaran manual berhasil dicatat dan tagihan sudah lunas.");
        }

        if (!$this->hasTripayConfig()) {
            return back()->with(
                "error",
                "Konfigurasi Tripay belum lengkap. Isi TRIPAY_API_KEY, TRIPAY_PRIVATE_KEY, dan TRIPAY_MERCHANT_CODE di .env.",
            );
        }

        $pembayaran = Pembayaran::create([
            "tagihan_id" => $tagihan->id,
            "wali_id" => $wali->id,
            "nominal" => $tagihan->nominal,
            "metode" => $request->metode,
            "tripay_ref" => "PESANTREN-" . Str::ulid(),
            "tripay_channel" => $this->mapChannelCode($request->metode),
            "status" => "pending",
        ]);

        $response = $this->createTripayInvoice($pembayaran);

        if (!$response["success"]) {
            $pembayaran->delete();

            return back()->with(
                "error",
                "Gagal membuat invoice Tripay: " . $response["message"],
            );
        }

        $pembayaran->update([
            "tripay_ref" => $response["reference"],
            "tripay_channel" =>
                $response["channel"] ?? $pembayaran->tripay_channel,
        ]);

        return redirect()->to($response["payment_url"]);
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(["tagihan.santri", "tagihan.jenisTagihan", "wali", "confirmer"]);

        return view("admin.pembayaran.show", compact("pembayaran"));
    }

    public function cancel(Pembayaran $pembayaran)
    {
        if ($pembayaran->status !== 'pending') {
            return back()->with('error', 'Hanya transaksi pending yang bisa dibatalkan.');
        }

        $pembayaran->update(['status' => 'failed']);

        return back()->with('success', 'Transaksi berhasil dibatalkan. Tagihan sekarang dapat dipilih kembali.');
    }

    public function confirmManual(Request $request, Pembayaran $pembayaran)
    {
        $data = $request->validate([
            'manual_note' => 'nullable|string|max:500',
        ]);

        try {
            $this->settlement->confirmManual($pembayaran, $request->user(), $data['manual_note'] ?? null);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bukti transfer dikonfirmasi. Tagihan sudah dilunasi.');
    }

    public function rejectManual(Request $request, Pembayaran $pembayaran)
    {
        $data = $request->validate([
            'rejection_note' => 'required|string|max:500',
        ]);

        try {
            $this->settlement->rejectManual($pembayaran, $request->user(), $data['rejection_note']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bukti transfer ditolak. Wali dapat mengunggah bukti baru.');
    }

    public function proof(Pembayaran $pembayaran)
    {
        abort_unless($pembayaran->proof_path && Storage::disk('local')->exists($pembayaran->proof_path), 404);

        return Storage::disk('local')->download(
            $pembayaran->proof_path,
            $pembayaran->proof_original_name ?: basename($pembayaran->proof_path),
        );
    }

    public function checkStatus(Pembayaran $pembayaran)
    {
        if ($pembayaran->metode === 'manual') {
            return back()->with('error', 'Pembayaran manual dikonfirmasi dari tombol konfirmasi bukti, bukan gateway.');
        }
        if ($pembayaran->metode === 'midtrans_snap' || $pembayaran->metode === 'midtrans') {
            return $this->checkMidtransStatus($pembayaran);
        }

        if (!$pembayaran->tripay_ref) {
            return back()->with("error", "Pembayaran ini bukan dari gateway online.");
        }

        if (!$this->hasTripayConfig()) {
            return back()->with("error", "Konfigurasi Tripay belum lengkap.");
        }

        $response = $this->checkTripayStatus($pembayaran->tripay_ref);

        if (!$response["success"]) {
            return back()->with(
                "error",
                "Gagal cek status: " . $response["message"],
            );
        }

        $this->applyPaymentStatus($pembayaran, $response["status"]);

        return back()->with(
            "info",
            "Status pembayaran: " . ucfirst(strtolower($response["status"])),
        );
    }

    private function checkMidtransStatus(Pembayaran $pembayaran)
    {
        $setting = \App\Models\PaymentSetting::where('active_gateway', 'midtrans')->first() 
            ?? \App\Models\PaymentSetting::first();

        if (!$setting || !filled($setting->midtrans_server_key)) {
            return back()->with('error', 'Konfigurasi Midtrans belum lengkap.');
        }

        $isProduction = $setting->midtrans_is_production ?? false;
        $url = ($isProduction ? 'https://api.midtrans.com/v2/' : 'https://api.sandbox.midtrans.com/v2/') . $pembayaran->tripay_ref . '/status';
        $authHeader = 'Basic ' . base64_encode($setting->midtrans_server_key . ':');

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => ['Authorization: ' . $authHeader, 'Accept: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) return back()->with('error', 'Midtrans API error: ' . $error);
            
            $result = json_decode((string)$response, true);
            $txStatus = $result['transaction_status'] ?? null;

            if (!$txStatus) return back()->with('error', 'Transaksi tidak ditemukan di Midtrans.');

            $rawStatus = match (true) {
                in_array($txStatus, ['settlement', 'capture']) && ($result['fraud_status'] ?? '') !== 'deny' => 'PAID',
                $txStatus === 'expire' => 'EXPIRED',
                $txStatus === 'cancel' || $txStatus === 'deny' => 'FAILED',
                default => 'PENDING',
            };

            $this->applyPaymentStatus($pembayaran, $rawStatus);
            return back()->with('success', 'Status Midtrans berhasil disinkronkan: ' . $rawStatus);

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function tripayCallback(Request $request): JsonResponse
    {
        $rawBody = $request->getContent();
        $signature = $request->header("X-Callback-Signature");
        $this->loadTripayConfig();

        if (! filled($this->tripayPrivateKey)) {
            return response()->json(
                ["success" => false, "message" => "Tripay callback secret is not configured"],
                503,
            );
        }

        if (! is_string($signature) || $signature === '') {
            return response()->json(
                ["success" => false, "message" => "Missing signature"],
                403,
            );
        }

        $expectedSignature = hash_hmac(
            "sha256",
            $rawBody,
            $this->tripayPrivateKey,
        );

        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json(
                ["success" => false, "message" => "Invalid signature"],
                403,
            );
        }

        $reference =
            $request->input("reference") ?? $request->input("merchant_ref");
        $status =
            $request->input("status") ?? $request->input("payment_status");

        if (!$reference || !$status) {
            return response()->json(
                ["success" => false, "message" => "Payload tidak lengkap"],
                422,
            );
        }

        $pembayaran = Pembayaran::where("tripay_ref", $reference)->first();

        if ($pembayaran) {
            if ($request->filled('total_amount') && (int) $request->input('total_amount') !== (int) $pembayaran->nominal) {
                return response()->json(["success" => false, "message" => "Nominal callback tidak cocok"], 422);
            }

            $this->applyPaymentStatus($pembayaran, strtoupper($status));
            return response()->json(["success" => true]);
        }

        $topUp = TopUpRequest::where("tripay_ref", $reference)->first();

        if ($topUp) {
            if ($request->filled('total_amount') && (int) $request->input('total_amount') !== (int) $topUp->nominal) {
                return response()->json(["success" => false, "message" => "Nominal callback tidak cocok"], 422);
            }

            $this->applyTopUpStatus($topUp, strtoupper($status));
            return response()->json(["success" => true]);
        }

        return response()->json(
            ["success" => false, "message" => "Pembayaran tidak ditemukan"],
            404,
        );
    }

    private function createTripayInvoice(Pembayaran $pembayaran): array
    {
        $this->loadTripayConfig();
        $pembayaran->load(["tagihan.santri", "tagihan.jenisTagihan", "wali"]);
        $tagihan = $pembayaran->tagihan;

        $merchantRef = $pembayaran->tripay_ref;
        $amount = (int) $pembayaran->nominal;
        $signature = hash_hmac(
            "sha256",
            $this->tripayMerchantCode . $merchantRef . $amount,
            $this->tripayPrivateKey,
        );

        $payload = [
            "method" => $this->mapChannelCode($pembayaran->metode),
            "merchant_ref" => $merchantRef,
            "amount" => $amount,
            "customer_name" => $pembayaran->wali->nama,
            "customer_email" =>
                $pembayaran->wali->user->email ?? "wali@example.test",
            "customer_phone" => $pembayaran->wali->no_hp,
            "order_items" => [
                [
                    "sku" => "TAGIHAN-" . $tagihan->id,
                    "name" =>
                        $tagihan->jenisTagihan->nama .
                        " - " .
                        $tagihan->santri->nama,
                    "price" => $amount,
                    "quantity" => 1,
                ],
            ],
            "return_url" => route("admin.pembayaran.show", $pembayaran),
            "callback_url" => route("api.tripay-callback"),
            "expired_time" => now()->addDay()->timestamp,
            "signature" => $signature,
        ];

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $this->tripayApiUrl . "/transaction/create",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $this->tripayApiKey,
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return ["success" => false, "message" => $error];
            }

            $result = json_decode((string) $response, true);

            if ($result["success"] ?? false) {
                return [
                    "success" => true,
                    "reference" => $result["data"]["reference"] ?? $merchantRef,
                    "channel" => $payload["method"],
                    "payment_url" =>
                        $result["data"]["checkout_url"] ??
                        ($result["data"]["pay_url"] ??
                            route("admin.pembayaran.show", $pembayaran)),
                ];
            }

            return [
                "success" => false,
                "message" => $result["message"] ?? "Unknown error",
            ];
        } catch (\Throwable $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    private function checkTripayStatus(string $reference): array
    {
        $this->loadTripayConfig();
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL =>
                    $this->tripayApiUrl .
                    "/transaction/detail?reference=" .
                    urlencode($reference),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $this->tripayApiKey,
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return ["success" => false, "message" => $error];
            }

            $result = json_decode((string) $response, true);

            if ($result["success"] ?? false) {
                return [
                    "success" => true,
                    "status" => $result["data"]["status"] ?? "UNPAID",
                ];
            }

            return [
                "success" => false,
                "message" => $result["message"] ?? "Unknown error",
            ];
        } catch (\Throwable $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    private function resolveWaliForTagihan(Tagihan $tagihan): ?Wali
    {
        return $tagihan->santri
            ?->wali()
            ->orderByRaw("CASE WHEN hubungan = 'ayah' THEN 1 WHEN hubungan = 'ibu' THEN 2 WHEN hubungan = 'wali' THEN 3 ELSE 4 END")
            ->first();
    }

    public function midtransCallback(Request $request): JsonResponse
    {
        $orderId       = $request->input('order_id');
        $statusCode    = $request->input('status_code');
        $grossAmount   = $request->input('gross_amount');
        $txStatus      = $request->input('transaction_status');
        $fraudStatus   = $request->input('fraud_status');
        $signatureKey  = $request->input('signature_key');

        if (!$orderId || !$txStatus) {
            return response()->json(['success' => false, 'message' => 'Payload tidak lengkap'], 422);
        }

        // Verify signature from Midtrans DB-based server_key
        $setting = \App\Models\PaymentSetting::where('active_gateway', 'midtrans')->first()
            ?? \App\Models\PaymentSetting::first();

        if (! $setting || ! filled($setting->midtrans_server_key)) {
            return response()->json(['success' => false, 'message' => 'Midtrans callback secret is not configured'], 503);
        }

        if (! is_string($signatureKey) || $signatureKey === '') {
            return response()->json(['success' => false, 'message' => 'Missing signature'], 403);
        }

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $setting->midtrans_server_key);
        if (!hash_equals($expected, $signatureKey)) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
        }

        $rawStatus = match (true) {
            in_array($txStatus, ['settlement', 'capture']) && $fraudStatus !== 'deny' => 'PAID',
            $txStatus === 'expire'  => 'EXPIRED',
            $txStatus === 'cancel'  => 'FAILED',
            $txStatus === 'deny'    => 'FAILED',
            default                 => 'PENDING',
        };

        $pembayaran = Pembayaran::where('tripay_ref', $orderId)->first();

        if ($pembayaran) {
            if ((int) round((float) $grossAmount) !== (int) $pembayaran->nominal) {
                return response()->json(['success' => false, 'message' => 'Nominal callback tidak cocok'], 422);
            }

            $this->applyPaymentStatus($pembayaran, $rawStatus);
            return response()->json(['success' => true]);
        }

        $topUp = TopUpRequest::where('tripay_ref', $orderId)->first();

        if ($topUp) {
            if ((int) round((float) $grossAmount) !== (int) $topUp->nominal) {
                return response()->json(['success' => false, 'message' => 'Nominal callback tidak cocok'], 422);
            }

            $this->applyTopUpStatus($topUp, $rawStatus);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Pembayaran tidak ditemukan'], 404);
    }

    private function applyPaymentStatus(
        Pembayaran $pembayaran,
        string $tripayStatus,
    ): void {
        $this->settlement->applyPaymentStatus($pembayaran, $tripayStatus);
    }

    private function applyTopUpStatus(TopUpRequest $topUp, string $tripayStatus): void
    {
        $this->settlement->applyTopUpStatus($topUp, $tripayStatus);
    }

    private function hasTripayConfig(): bool
    {
        $this->loadTripayConfig();

        return filled($this->tripayApiKey) &&
            filled($this->tripayPrivateKey) &&
            filled($this->tripayMerchantCode);
    }

    private function loadTripayConfig(): void
    {
        $setting = PaymentSetting::first();

        if (! $setting) {
            return;
        }

        $this->tripayApiKey = $setting->tripay_api_key ?: $this->tripayApiKey;
        $this->tripayPrivateKey = $setting->tripay_private_key ?: $this->tripayPrivateKey;
        $this->tripayMerchantCode = $setting->tripay_merchant_code ?: $this->tripayMerchantCode;
        $this->tripayApiUrl = ($setting->tripay_mode ?? 'sandbox') === 'production'
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';
    }

    private function mapChannelCode(string $metode): string
    {
        return match ($metode) {
            "va_bca" => "BCAVA",
            "va_mandiri" => "MANDIRIVA",
            "qris" => "QRIS",
            "gopay" => "GOPAY",
            "ovo" => "OVO",
            default => "BCAVA",
        };
    }
}
