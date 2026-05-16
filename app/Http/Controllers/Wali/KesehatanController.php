<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\KunjunganKlinik;
use App\Models\Santri;
use Illuminate\Support\Facades\Auth;

class KesehatanController extends Controller
{
    public function index()
    {
        $wali = Auth::user()->wali;
        if (!$wali) {
            abort(403, 'Profil wali tidak ditemukan.');
        }

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
        if (!$wali) {
            abort(403, 'Profil wali tidak ditemukan.');
        }

        if ($kunjungan->patient_type !== Santri::class) {
            abort(404);
        }

        if (!$wali->santri->contains('id', $kunjungan->patient_id)) {
            abort(403, 'Santri ini bukan anak Anda.');
        }

        $kunjungan->load(['patient', 'pemeriksa', 'voiceNotes.pengirimUstadz']);

        return view('wali.kesehatan.show', compact('kunjungan'));
    }
}
