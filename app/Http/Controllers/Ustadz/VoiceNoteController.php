<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\KunjunganKlinik;
use App\Models\Santri;
use App\Models\VoiceNote;
use App\Models\Wali;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VoiceNoteController extends Controller
{
    public function __construct(protected NotificationService $notifier) {}

    public function index()
    {
        $ustadz = Auth::user()->ustadz;
        if (!$ustadz) {
            abort(403, 'Profil ustadz tidak ditemukan.');
        }

        $sent = VoiceNote::where('pengirim_ustadz_id', $ustadz->id)
            ->with(['penerimaWali', 'penerimaSantri', 'kunjunganKlinik'])
            ->latest('created_at')
            ->paginate(20);

        return view('ustadz.voice-note.index', compact('sent'));
    }

    public function create(Request $request)
    {
        $ustadz = Auth::user()->ustadz;
        if (!$ustadz) {
            abort(403, 'Profil ustadz tidak ditemukan.');
        }

        $kunjungan = null;
        $santri = null;

        if ($request->filled('kunjungan_id')) {
            $kunjungan = KunjunganKlinik::with('patient.wali')->find($request->kunjungan_id);
            if ($kunjungan && $kunjungan->patient_type === Santri::class) {
                $santri = $kunjungan->patient;
            }
        }

        if (!$santri && $request->filled('santri_id')) {
            $santri = Santri::with('wali')->find($request->santri_id);
        }

        $santriList = Santri::where('is_aktif', true)
            ->orderBy('nama')
            ->select('id', 'nama', 'nis', 'kelas')
            ->get();

        return view('ustadz.voice-note.create', compact('santri', 'santriList', 'kunjungan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a,webm|max:5120',
            'santri_id' => 'required|integer|exists:santri,id',
            'durasi_detik' => 'required|integer|min:1|max:300',
            'konteks' => 'nullable|in:umum,kesehatan,akademik',
            'kunjungan_klinik_id' => 'nullable|integer|exists:kunjungan_klinik,id',
            'pesan_teks' => 'nullable|string|max:500',
        ]);

        $ustadz = Auth::user()->ustadz;
        if (!$ustadz) {
            abort(403, 'Profil ustadz tidak ditemukan.');
        }

        $santri = Santri::with('wali.user')->findOrFail($request->santri_id);

        if ($santri->wali->isEmpty()) {
            return back()->with('error', 'Santri belum memiliki wali terdaftar.');
        }

        $audioPath = $request->file('audio')->store('voice-notes', 'public');
        $audioUrl = Storage::disk('public')->url($audioPath);

        $konteks = $request->input('konteks', 'umum');
        $firstVoiceNote = null;

        foreach ($santri->wali as $wali) {
            $voiceNote = VoiceNote::create([
                'pengirim_ustadz_id'  => $ustadz->id,
                'penerima_wali_id'    => $wali->id,
                'penerima_santri_id'  => $santri->id,
                'audio_url'           => $audioUrl,
                'durasi_detik'        => $request->integer('durasi_detik'),
                'biaya'               => 0,
                'is_read'             => false,
                'konteks'             => $konteks,
                'kunjungan_klinik_id' => $request->input('kunjungan_klinik_id'),
            ]);

            $firstVoiceNote ??= $voiceNote;

            if ($wali->user) {
                $this->notifier->send(
                    user: $wali->user,
                    type: 'voice_note_ustadz',
                    title: "Pesan suara dari {$ustadz->nama}",
                    body: $request->input('pesan_teks')
                        ?: "Ustadz {$ustadz->nama} mengirim pesan suara tentang {$santri->nama}.",
                    data: [
                        'voice_note_id' => (string) $voiceNote->id,
                        'santri_id'     => (string) $santri->id,
                        'konteks'       => $konteks,
                    ],
                    actionUrl: route('wali.voice-note.show', $voiceNote->id),
                );
            }
        }

        return redirect()
            ->route('ustadz.voice-note.index')
            ->with('success', 'Voice note berhasil dikirim ke wali santri (gratis).');
    }

    public function show(VoiceNote $voiceNote)
    {
        $ustadz = Auth::user()->ustadz;
        if (!$ustadz || $voiceNote->pengirim_ustadz_id !== $ustadz->id) {
            abort(403);
        }

        $voiceNote->load(['penerimaWali', 'penerimaSantri', 'kunjunganKlinik']);

        return view('ustadz.voice-note.show', compact('voiceNote'));
    }
}
