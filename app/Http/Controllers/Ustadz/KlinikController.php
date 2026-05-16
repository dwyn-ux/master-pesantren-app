<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\KunjunganKlinik;
use App\Models\Santri;
use App\Models\Ustadz;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class KlinikController extends Controller
{
    public function __construct(protected NotificationService $notifier) {}

    public function index()
    {
        $kunjungan = KunjunganKlinik::with(['patient', 'pemeriksa'])
            ->latest()
            ->paginate(15);
        return view('ustadz.klinik.index', compact('kunjungan'));
    }

    public function searchPatient(Request $request)
    {
        $term = trim((string) $request->q);
        if ($term === '') {
            // Tidak ada keyword: kembalikan 20 santri aktif pertama supaya dropdown tetap bisa dibuka
            $santri = Santri::where('is_aktif', true)
                ->orderBy('nama')
                ->select('id', 'nama', 'nik', 'nis', 'kelas')
                ->limit(20)
                ->get()
                ->map(fn($s) => [
                    'id'    => $s->id,
                    'nama'  => $s->nama,
                    'nik'   => $s->nik,
                    'nis'   => $s->nis,
                    'kelas' => $s->kelas,
                    'type'  => 'App\\Models\\Santri',
                    'badge' => 'Santri',
                ]);
            return response()->json($santri->toArray());
        }

        $santri = Santri::where('is_aktif', true)
            ->where(function ($q) use ($term) {
                $q->where('nama', 'like', "%{$term}%")
                  ->orWhere('nik',  'like', "%{$term}%")
                  ->orWhere('nis',  'like', "%{$term}%")
                  ->orWhere('kelas','like', "%{$term}%");
            })
            ->select('id', 'nama', 'nik', 'nis', 'kelas')
            ->orderBy('nama')
            ->limit(15)
            ->get()
            ->map(fn($s) => [
                'id'    => $s->id,
                'nama'  => $s->nama,
                'nik'   => $s->nik,
                'nis'   => $s->nis,
                'kelas' => $s->kelas,
                'type'  => 'App\\Models\\Santri',
                'badge' => 'Santri',
            ]);

        $ustadz = Ustadz::where(function ($q) use ($term) {
                $q->where('nama', 'like', "%{$term}%")
                  ->orWhere('nik', 'like', "%{$term}%");
            })
            ->select('id', 'nama', 'nik')
            ->orderBy('nama')
            ->limit(10)
            ->get()
            ->map(fn($u) => [
                'id'    => $u->id,
                'nama'  => $u->nama,
                'nik'   => $u->nik,
                'nis'   => null,
                'kelas' => null,
                'type'  => 'App\\Models\\Ustadz',
                'badge' => 'Ustadz',
            ]);

        return response()->json(array_merge($santri->toArray(), $ustadz->toArray()));
    }

    public function history(Request $request)
    {
        $history = KunjunganKlinik::where('patient_type', $request->type)
            ->where('patient_id', $request->id)
            ->with('pemeriksa')
            ->latest()
            ->get();
        return response()->json($history);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_type' => 'required|string',
            'patient_id' => 'required|integer',
            'keluhan' => 'required|string',
            'diagnosa' => 'required|string',
            'tindakan_obat' => 'required|string',
            'status_pengobatan' => 'required|in:aktifitas_normal,istirahat,dirujuk,dirawat_orang_tua',
            'lama_istirahat_hari' => 'nullable|integer|min:1',
            'catatan_ortu' => 'nullable|string',
        ]);

        $kunjungan = new KunjunganKlinik($validated);
        $kunjungan->tanggal_kunjungan = now();
        $kunjungan->pemeriksa_id = auth()->user()->ustadz->id;
        $kunjungan->perlu_rujuk = $validated['status_pengobatan'] === 'dirujuk';
        $kunjungan->perlu_dirawat_ortu = $validated['status_pengobatan'] === 'dirawat_orang_tua';
        $kunjungan->catatan_ortu = $request->input('catatan_ortu');
        $kunjungan->save();

        if ($kunjungan->perlu_rujuk || $kunjungan->perlu_dirawat_ortu) {
            $this->notifyOrangTua($kunjungan);
        }

        return back()->with('success', 'Data rekam medis berhasil disimpan.');
    }

    protected function notifyOrangTua(KunjunganKlinik $kunjungan): void
    {
        if ($kunjungan->patient_type !== Santri::class) {
            return;
        }

        $santri = Santri::with('wali.user')->find($kunjungan->patient_id);
        if (!$santri) return;

        $statusLabel = $kunjungan->perlu_rujuk ? 'perlu dirujuk ke faskes' : 'disarankan dirawat di rumah';
        $title = "Pemeriksaan Kesehatan: {$santri->nama}";
        $body = "Hasil pemeriksaan: {$kunjungan->diagnosa}. Status: {$statusLabel}."
            . ($kunjungan->catatan_ortu ? " Catatan: {$kunjungan->catatan_ortu}" : '');

        $pemeriksa = $kunjungan->pemeriksa?->nama ?? 'Klinik Pondok';

        foreach ($santri->wali as $wali) {
            if (!$wali->user) continue;
            $this->notifier->send(
                user: $wali->user,
                type: 'kesehatan',
                title: $title,
                body: $body,
                data: [
                    'kunjungan_klinik_id' => (string) $kunjungan->id,
                    'santri_id' => (string) $santri->id,
                    'perlu_rujuk' => $kunjungan->perlu_rujuk ? '1' : '0',
                    'perlu_dirawat_ortu' => $kunjungan->perlu_dirawat_ortu ? '1' : '0',
                    'pemeriksa' => $pemeriksa,
                ],
                actionUrl: route('wali.kesehatan.show', $kunjungan->id),
            );
        }

        $kunjungan->update(['notified_at' => now()]);
    }
}
