<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Halaqah;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\Ustadz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HalaqahController extends Controller
{
    /** Ambil profil ustadz yang login */
    private function getUstadz(): Ustadz
    {
        $ustadz = Ustadz::where('user_id', auth()->id())->first();
        if (!$ustadz) {
            abort(403, 'Profil ustadz tidak ditemukan untuk akun ini.');
        }
        return $ustadz;
    }

    /** IDs santri di semua halaqah ustadz yang login */
    private function getSantriIds(Ustadz $ustadz): \Illuminate\Support\Collection
    {
        $halaqahIds = Halaqah::where('ustadz_id', $ustadz->id)->pluck('id');
        return \DB::table('halaqah_santri')
            ->whereIn('halaqah_id', $halaqahIds)
            ->pluck('santri_id');
    }

    // ─────────────────────────────────────────────────────────────────
    // Halaqah
    // ─────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $ustadz  = $this->getUstadz();
        $halaqah = Halaqah::where('ustadz_id', $ustadz->id)
            ->withCount('santri')
            ->get();

        return view('ustadz.halaqah.index', compact('ustadz', 'halaqah'));
    }

    public function show(Halaqah $halaqah): View
    {
        $ustadz = $this->getUstadz();
        abort_if($halaqah->ustadz_id !== $ustadz->id, 403);
        $halaqah->load('santri');
        return view('ustadz.halaqah.show', compact('ustadz', 'halaqah'));
    }

    // ─────────────────────────────────────────────────────────────────
    // Santri (di halaqah ustadz)
    // ─────────────────────────────────────────────────────────────────

    public function santri(Request $request): View
    {
        $ustadz     = $this->getUstadz();
        $santriIds  = $this->getSantriIds($ustadz);
        $halaqahList = Halaqah::where('ustadz_id', $ustadz->id)->get();

        $santri = Santri::whereIn('id', $santriIds)
            ->when($request->search, fn($q, $s) =>
                $q->where('nama', 'like', "%$s%")->orWhere('nis', 'like', "%$s%"))
            ->when($request->halaqah_id, fn($q, $id) =>
                $q->whereHas('halaqah', fn($qh) => $qh->where('halaqah.id', $id)))
            ->with('halaqah')
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        return view('ustadz.halaqah.santri', compact('ustadz', 'santri', 'halaqahList'));
    }

    // ─────────────────────────────────────────────────────────────────
    // Setoran Halaqah (santri di halaqah ustadz)
    // ─────────────────────────────────────────────────────────────────

    public function setoran(Request $request): View
    {
        $ustadz    = $this->getUstadz();
        $santriIds = $this->getSantriIds($ustadz);

        // List santri untuk dropdown (dari halaqah ustadz)
        $santriList = Santri::whereIn('id', $santriIds)
            ->orderBy('nama')
            ->get(['id', 'nis', 'nama']);

        $setoran = Setoran::whereIn('santri_id', $santriIds)
            ->when($request->jenis, fn($q, $j) => $q->where('jenis', $j))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->santri_id, fn($q, $id) => $q->where('santri_id', $id))
            ->with(['santri', 'surahAwal', 'surahAkhir'])
            ->latest('tanggal')
            ->paginate(25)
            ->withQueryString();

        $surahList = Surah::orderBy('id')->get(['id', 'nama_latin', 'jumlah_ayat', 'juz_awal']);

        return view('ustadz.halaqah.setoran', compact('ustadz', 'santriList', 'setoran', 'surahList'));
    }

    /** API: posisi terakhir setoran santri (untuk auto-fill form) */
    public function lastPosition(Request $request): JsonResponse
    {
        $request->validate(['santri_id' => 'required|integer|exists:santri,id']);

        $last = Setoran::where('santri_id', $request->santri_id)
            ->where('status', 'maqbul')
            ->where('jenis', 'ziyadah')
            ->latest('tanggal')
            ->first();

        if (!$last) {
            return response()->json(['has_last' => false]);
        }

        $surahAkhir = Surah::find($last->surah_akhir);
        $nextSurahId = $last->surah_akhir;
        $nextAyat    = $last->ayat_akhir + 1;

        // Jika sudah mencapai akhir surah, lanjut ke surah berikutnya
        if ($surahAkhir && $last->ayat_akhir >= $surahAkhir->jumlah_ayat) {
            $nextSurahId = $last->surah_akhir + 1;
            $nextAyat    = 1;
        }

        // Jangan melebihi surah ke-114
        if ($nextSurahId > 114) {
            $nextSurahId = 114;
            $nextAyat    = $surahAkhir?->jumlah_ayat ?? 1;
        }

        return response()->json([
            'has_last'     => true,
            'surah_awal'   => $nextSurahId,
            'ayat_awal'    => $nextAyat,
            'surah_akhir'  => $nextSurahId,
            'last_tanggal' => $last->tanggal->format('d/m/Y'),
            'last_info'    => ($last->surahAwal?->nama_latin ?? 'Surah '.$last->surah_awal)
                              . ' ' . $last->ayat_awal . '-'
                              . ($last->surahAkhir?->nama_latin ?? 'Surah '.$last->surah_akhir)
                              . ' ' . $last->ayat_akhir,
        ]);
    }

    /** Simpan setoran (halaqah ustadz) */
    public function storeSetoran(Request $request): \Illuminate\Http\RedirectResponse
    {
        $ustadz    = $this->getUstadz();
        $santriIds = $this->getSantriIds($ustadz);

        $data = $request->validate([
            'santri_id'      => ['required', 'integer', \Illuminate\Validation\Rule::in($santriIds)],
            'jenis'          => 'required|in:ziyadah,murojaah',
            'tipe_halaqah'   => 'nullable|string',
            'surah_awal'     => 'required|integer|between:1,114',
            'ayat_awal'      => 'required|integer|min:1',
            'surah_akhir'    => 'required|integer|between:1,114',
            'ayat_akhir'     => 'required|integer|min:1',
            'jumlah_halaman' => 'nullable|numeric|min:0',
            'status'         => 'required|in:maqbul,dhaif,kurang',
            'catatan'        => 'nullable|string|max:500',
            'tanggal'        => 'required|date',
        ]);

        $data['penerima_id'] = $ustadz->id;
        $data['tipe_halaqah'] = $data['tipe_halaqah'] ?? 'halaqah';
        Setoran::create($data);

        return redirect()->route('ustadz.setoran.index')
            ->with('success', 'Setoran berhasil dicatat.');
    }

    // ─────────────────────────────────────────────────────────────────
    // Setoran Umum (semua santri, termasuk di luar halaqah)
    // ─────────────────────────────────────────────────────────────────

    public function setoranUmum(Request $request): View
    {
        $ustadz = $this->getUstadz();

        $santriList = Santri::where('is_aktif', true)
            ->orderBy('nama')
            ->get(['id', 'nis', 'nama']);

        $setoran = Setoran::where('penerima_id', $ustadz->id)
            ->when($request->jenis, fn($q, $j) => $q->where('jenis', $j))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->santri_id, fn($q, $id) => $q->where('santri_id', $id))
            ->with(['santri', 'surahAwal', 'surahAkhir'])
            ->latest('tanggal')
            ->paginate(25)
            ->withQueryString();

        $surahList = Surah::orderBy('id')->get(['id', 'nama_latin', 'jumlah_ayat', 'juz_awal']);

        return view('ustadz.setoran-umum', compact('ustadz', 'santriList', 'setoran', 'surahList'));
    }

    public function storeSetoranUmum(Request $request): \Illuminate\Http\RedirectResponse
    {
        $ustadz = $this->getUstadz();

        $data = $request->validate([
            'santri_id'      => 'required|integer|exists:santri,id',
            'jenis'          => 'required|in:ziyadah,murojaah',
            'tipe_halaqah'   => 'nullable|string',
            'surah_awal'     => 'required|integer|between:1,114',
            'ayat_awal'      => 'required|integer|min:1',
            'surah_akhir'    => 'required|integer|between:1,114',
            'ayat_akhir'     => 'required|integer|min:1',
            'jumlah_halaman' => 'nullable|numeric|min:0',
            'status'         => 'required|in:maqbul,dhaif,kurang',
            'catatan'        => 'nullable|string|max:500',
            'tanggal'        => 'required|date',
        ]);

        $data['penerima_id'] = $ustadz->id;
        $data['tipe_halaqah'] = $data['tipe_halaqah'] ?? 'umum';
        Setoran::create($data);

        return redirect()->route('ustadz.setoran-umum.index')
            ->with('success', 'Setoran umum berhasil dicatat.');
    }
}
