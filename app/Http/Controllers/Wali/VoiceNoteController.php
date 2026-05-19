<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\User;
use App\Models\VoiceNote;
use App\Models\WalletTransaction;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VoiceNoteController extends Controller
{
    public function __construct(protected NotificationService $notifier) {}
    public function index()
    {
        $wali = Auth::user()->wali;

        if (!$wali) {
            abort(403, "Profil wali tidak ditemukan.");
        }

        $santriList = $wali->santri;

        $sentVoiceNotes = VoiceNote::where("pengirim_wali_id", $wali->id)
            ->with(["penerimaSantri", "penerimaWali"])
            ->orderByDesc("created_at")
            ->get();

        $receivedVoiceNotes = VoiceNote::where("penerima_wali_id", $wali->id)
            ->with(["pengirimSantri", "pengirimWali"])
            ->orderByDesc("created_at")
            ->get();

        return view(
            "wali.voice-note.index",
            compact("santriList", "sentVoiceNotes", "receivedVoiceNotes"),
        );
    }

    public function create()
    {
        $wali = Auth::user()->wali;

        if (!$wali) {
            abort(403, "Profil wali tidak ditemukan.");
        }

        $santriList = $wali->santri;

        return view("wali.voice-note.create", compact("santriList"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "audio" => "required|file|mimes:mp3,wav,ogg,m4a|max:5120",
            "penerima_type" => "required|in:santri,wali",
            "penerima_id" => "required|integer",
            "durasi_detik" => "required|integer|min:1|max:300",
        ]);

        $wali = Auth::user()->wali;

        if (!$wali) {
            abort(403, "Profil wali tidak ditemukan.");
        }

        if ($request->penerima_type !== "santri") {
            return back()->with(
                "error",
                "Fitur kirim ke wali lain belum tersedia.",
            );
        }

        $santri = Santri::findOrFail($request->penerima_id);

        if (!$wali->santri->contains($santri)) {
            return back()->with(
                "error",
                "Santri tidak ditemukan dalam daftar anak Anda.",
            );
        }

        $biayaPerDetik = 100;
        $totalBiaya = $request->integer("durasi_detik") * $biayaPerDetik;

        $audioPath = $request->file("audio")->store("voice-notes", "public");
        $audioUrl = Storage::disk("public")->url($audioPath);

        try {
            $voiceNote = DB::transaction(function () use (
                $request,
                $wali,
                $santri,
                $totalBiaya,
                $audioUrl,
            ) {
                $santriWallet = Santri::whereKey($santri->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($santriWallet->saldo < $totalBiaya) {
                    throw new \RuntimeException(
                        "Saldo uang saku santri tidak mencukupi. Biaya voice note: Rp " .
                            number_format($totalBiaya),
                    );
                }

                $saldoSebelum = $santriWallet->saldo;
                $saldoSesudah = $saldoSebelum - $totalBiaya;

                $voiceNote = VoiceNote::create([
                    "pengirim_wali_id" => $wali->id,
                    "penerima_santri_id" => $santriWallet->id,
                    "audio_url" => $audioUrl,
                    "durasi_detik" => $request->integer("durasi_detik"),
                    "biaya" => $totalBiaya,
                    "is_read" => false,
                ]);

                $santriWallet->update(["saldo" => $saldoSesudah]);

                WalletTransaction::create([
                    "santri_id" => $santriWallet->id,
                    "tipe" => "voice_note",
                    "referensi_id" => $voiceNote->id,
                    "referensi_tipe" => VoiceNote::class,
                    "nominal" => $totalBiaya,
                    "jenis" => "debit",
                    "saldo_sebelum" => $saldoSebelum,
                    "saldo_sesudah" => $saldoSesudah,
                    "keterangan" =>
                        "Voice note dari wali untuk " . $santriWallet->nama,
                ]);

                return $voiceNote;
            });

            // Notifikasi ke ustadz halaqah santri (DB + FCM)
            $santri->loadMissing('halaqah.ustadz.user');
            foreach ($santri->halaqah as $halaqah) {
                if ($halaqah->ustadz?->user) {
                    $this->notifier->send(
                        user: $halaqah->ustadz->user,
                        type: 'voice_note_wali',
                        title: "Pesan suara dari wali {$santri->nama}",
                        body: "{$wali->nama} mengirim pesan suara untuk ananda {$santri->nama}.",
                        data: [
                            'voice_note_id' => (string) $voiceNote->id,
                            'santri_id'     => (string) $santri->id,
                        ],
                    );
                }
            }

            // Notifikasi ke admin
            $adminUsers = User::role('admin')->get();
            foreach ($adminUsers as $adminUser) {
                $this->notifier->send(
                    user: $adminUser,
                    type: 'voice_note_wali',
                    title: "Pesan suara dari wali {$santri->nama}",
                    body: "{$wali->nama} mengirim pesan suara untuk ananda {$santri->nama}.",
                    data: [
                        'voice_note_id' => (string) $voiceNote->id,
                        'santri_id'     => (string) $santri->id,
                    ],
                );
            }

            return redirect()
                ->route("wali.voice-note.success", $voiceNote)
                ->with("success", "Voice note berhasil dikirim.");
        } catch (\Throwable $e) {
            Storage::disk("public")->delete($audioPath);

            return back()->with("error", $e->getMessage());
        }
    }

    public function success(VoiceNote $voiceNote)
    {
        if ($voiceNote->pengirim_wali_id !== Auth::user()->wali?->id) {
            abort(403);
        }

        $voiceNote->load(["penerimaSantri", "penerimaWali"]);

        return view("wali.voice-note.success", compact("voiceNote"));
    }

    public function show(VoiceNote $voiceNote)
    {
        $waliId = Auth::user()->wali?->id;

        if (
            $voiceNote->pengirim_wali_id !== $waliId &&
            $voiceNote->penerima_wali_id !== $waliId
        ) {
            abort(403);
        }

        if ($voiceNote->penerima_wali_id === $waliId && !$voiceNote->is_read) {
            $voiceNote->update(["is_read" => true]);
        }

        $voiceNote->load([
            "pengirimSantri",
            "pengirimWali",
            "penerimaSantri",
            "penerimaWali",
        ]);

        return view("wali.voice-note.show", compact("voiceNote"));
    }

    public function markAsRead(VoiceNote $voiceNote)
    {
        if ($voiceNote->penerima_wali_id !== Auth::user()->wali?->id) {
            abort(403);
        }

        $voiceNote->update(["is_read" => true]);

        return response()->json(["success" => true]);
    }
}
