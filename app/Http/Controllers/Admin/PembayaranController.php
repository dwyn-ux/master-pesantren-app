<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TopUpRequest;
use App\Models\Wali;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    private string $tripayApiUrl;
    private ?string $tripayApiKey;
    private ?string $tripayPrivateKey;
    private ?string $tripayMerchantCode;

    public function __construct()
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
            "manual" => "Manual",
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
            "metode" => "required|in:va_bca,va_mandiri,qris,gopay,ovo,manual",
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

        if ($request->metode === "manual") {
            $pembayaran = DB::transaction(function () use (
                $tagihan,
                $wali,
                $request,
            ) {
                $pembayaran = Pembayaran::create([
                    "tagihan_id" => $tagihan->id,
                    "wali_id" => $wali->id,
                    "nominal" => $tagihan->nominal,
                    "metode" => $request->metode,
                    "status" => "paid",
                    "paid_at" => now(),
                ]);

                $tagihan->update(["status" => "lunas"]);

                return $pembayaran;
            });

            return redirect()
                ->route("admin.pembayaran.show", $pembayaran)
                ->with("success", "Pembayaran manual berhasil dicatat.");
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
            "tripay_ref" => "PESANTREN-" . $tagihan->id . "-" . time(),
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
        $pembayaran->load(["tagihan.santri", "tagihan.jenisTagihan", "wali"]);

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

    public function checkStatus(Pembayaran $pembayaran)
    {
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
                CURLOPT_SSL_VERIFYPEER => false,
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

        if ($this->tripayPrivateKey && $signature) {
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
            $this->applyPaymentStatus($pembayaran, strtoupper($status));
            return response()->json(["success" => true]);
        }

        $topUp = TopUpRequest::where("tripay_ref", $reference)->first();

        if ($topUp) {
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

        if ($setting && filled($setting->midtrans_server_key) && $signatureKey) {
            $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $setting->midtrans_server_key);
            if (!hash_equals($expected, $signatureKey)) {
                return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
            }
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
            $this->applyPaymentStatus($pembayaran, $rawStatus);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Pembayaran tidak ditemukan'], 404);
    }

    private function applyPaymentStatus(
        Pembayaran $pembayaran,
        string $tripayStatus,
    ): void {
        $statusMap = [
            "PAID" => "paid",
            "SETTLED" => "paid",
            "UNPAID" => "pending",
            "PENDING" => "pending",
            "EXPIRED" => "expired",
            "FAILED" => "failed",
            "REFUND" => "failed",
        ];

        $newStatus = $statusMap[strtoupper($tripayStatus)] ?? "pending";

        DB::transaction(function () use ($pembayaran, $newStatus) {
            $pembayaran->refresh();

            if ($pembayaran->status === "paid") {
                return;
            }

            $pembayaran->update([
                "status" => $newStatus,
                "paid_at" => $newStatus === "paid" ? now() : $pembayaran->paid_at,
            ]);

            if ($newStatus === "paid") {
                // Beri log biar keliatan di server
                \Log::info('Otomatis melunasi tagihan untuk pembayaran ID: ' . $pembayaran->id);

                // Batch tagihans
                if ($pembayaran->tagihan_ids) {
                    Tagihan::whereIn('id', $pembayaran->tagihan_ids)->update(['status' => 'lunas']);
                } elseif ($pembayaran->tagihan_id) {
                    Tagihan::where('id', $pembayaran->tagihan_id)->update(['status' => 'lunas']);
                }

                // Batch top-ups
                if ($pembayaran->topup_items) {
                    foreach ($pembayaran->topup_items as $item) {
                        $santri = Santri::lockForUpdate()->find($item['santri_id']);
                        if (!$santri) continue;

                        $saldoBefore = $santri->saldo;
                        $saldoAfter  = $saldoBefore + $item['nominal'];

                        $santri->update(['saldo' => $saldoAfter]);

                        WalletTransaction::create([
                            'santri_id'      => $santri->id,
                            'tipe'           => 'topup',
                            'referensi_id'   => $pembayaran->id,
                            'referensi_tipe' => Pembayaran::class,
                            'nominal'        => $item['nominal'],
                            'jenis'          => 'kredit',
                            'saldo_sebelum'  => $saldoBefore,
                            'saldo_sesudah'  => $saldoAfter,
                            'keterangan'     => 'Top up via checkout batch',
                        ]);
                    }
                }
            }
        });
    }

    private function applyTopUpStatus(TopUpRequest $topUp, string $tripayStatus): void
    {
        $statusMap = [
            "PAID"    => "paid",
            "SETTLED" => "paid",
            "UNPAID"  => "unpaid",
            "PENDING" => "unpaid",
            "EXPIRED" => "expired",
            "FAILED"  => "failed",
            "REFUND"  => "failed",
        ];

        $newStatus = $statusMap[$tripayStatus] ?? "unpaid";

        DB::transaction(function () use ($topUp, $newStatus) {
            $topUp->refresh();

            if ($topUp->status === "paid") {
                return;
            }

            $topUp->update([
                "status"  => $newStatus,
                "paid_at" => $newStatus === "paid" ? now() : $topUp->paid_at,
            ]);

            if ($newStatus === "paid") {
                $santri = Santri::lockForUpdate()->findOrFail($topUp->santri_id);

                $saldoBefore = $santri->saldo;
                $saldoAfter  = $saldoBefore + $topUp->nominal;

                $santri->update(["saldo" => $saldoAfter]);

                WalletTransaction::create([
                    "santri_id"      => $santri->id,
                    "tipe"           => "topup",
                    "referensi_id"   => $topUp->id,
                    "referensi_tipe" => TopUpRequest::class,
                    "nominal"        => $topUp->nominal,
                    "jenis"          => "kredit",
                    "saldo_sebelum"  => $saldoBefore,
                    "saldo_sesudah"  => $saldoAfter,
                    "keterangan"     => "Top up saldo via Tripay (" . ($topUp->tripay_channel ?? "online") . ")",
                ]);
            }
        });
    }

    private function hasTripayConfig(): bool
    {
        return filled($this->tripayApiKey) &&
            filled($this->tripayPrivateKey) &&
            filled($this->tripayMerchantCode);
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
