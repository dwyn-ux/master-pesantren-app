<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\KunjunganKlinik;
use App\Models\Santri;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KesehatanController extends Controller
{
    public function __construct(protected NotificationService $notifier) {}

    public function index()
    {
        $wali = Auth::user()->wali;
        if (!$wali) abort(403, 'Profil wali tidak ditemukan.');

        $santriIds = $wali->santri->pluck('id');

        $kunjungan = KunjunganKlinik::with(['patient', 'pemeriksa'])
            ->where('patient_type', Santri::class)
            ->whereIn('patient_id', $santriIds)
            ->latest('tanggal_kunjungan')
            ->paginate(20);

        return view('wali.kesehatan.index', compact('kunjungan'));
    }

    public function show(KunjunganKlinik $kunjungan)
    {
        $wali = Auth::user()->wali;
        if (!$wali) abort(403, 'Profil wali tidak ditemukan.');
        if ($kunjungan->patient_type !== Santri::class) abort(404);
        if (!$wali->santri->contains('id', $kunjungan->patient_id)) abort(403, 'Santri ini bukan anak Anda.');

        $kunjungan->load(['patient', 'pemeriksa', 'voiceNotes.pengirimUstadz']);

        return view('wali.kesehatan.show', compact('kunjungan'));
    }

    public function konfirmasi(Request $request, KunjunganKlinik $kunjungan)
    {
        $wali = Auth::user()->wali;
        if (!$wali) abort(403);
        if ($kunjungan->patient_type !== Santri::class) abort(404);
        if (!$wali->santri->contains('id', $kunjungan->patient_id)) abort(403);

        $request->validate([
            'status'  => 'required|in:otw',
            'pesan'   => 'nullable|string|max:300',
        ]);

        $kunjungan->update([
            'wali_konfirmasi'        => $request->status,
            'wali_konfirmasi_at'     => now(),
            'wali_konfirmasi_pesan'  => $request->pesan,
        ]);

        // Label untuk notifikasi
        $santri   = $kunjungan->patient;
        $labelStatus = $request->status === 'otw'
            ? '🚗 Sedang dalam perjalanan'
            : '✅ Sudah ditangani';
        $pesan = $request->pesan ? " — \"{$request->pesan}\"" : '';

        // Kirim notif ke ustadz pemeriksa
        if ($kunjungan->pemeriksa?->user) {
            $this->notifier->send(
                user: $kunjungan->pemeriksa->user,
                type: 'konfirmasi_kesehatan',
                title: "Konfirmasi Wali: {$santri->nama}",
                body: "{$wali->nama}: {$labelStatus}{$pesan}",
                data: [
                    'kunjungan_id' => (string) $kunjungan->id,
                    'santri_id'    => (string) $santri->id,
                    'status'       => $request->status,
                ],
            );
        }

        // Kirim notif ke semua admin juga
        User::role('admin')->each(function ($adminUser) use ($kunjungan, $santri, $wali, $labelStatus, $pesan, $request) {
            $this->notifier->send(
                user: $adminUser,
                type: 'konfirmasi_kesehatan',
                title: "Konfirmasi Wali: {$santri->nama}",
                body: "{$wali->nama}: {$labelStatus}{$pesan}",
                data: [
                    'kunjungan_id' => (string) $kunjungan->id,
                    'santri_id'    => (string) $santri->id,
                    'status'       => $request->status,
                ],
            );
        });

        $msg = $request->status === 'otw'
            ? 'Konfirmasi terkirim. Ustadz sudah diberitahu bahwa Anda sedang dalam perjalanan.'
            : 'Konfirmasi terkirim. Terima kasih atas responnya.';

        return back()->with('success', $msg);
    }
}
