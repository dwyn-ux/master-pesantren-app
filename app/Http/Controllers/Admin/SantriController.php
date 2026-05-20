<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $santri = Santri::query()
            ->when($request->search, fn($q) => $q
                ->where('nama', 'ilike', "%{$request->search}%")
                ->orWhere('nis', 'ilike', "%{$request->search}%")
                ->orWhere('nik', 'ilike', "%{$request->search}%"))
            ->when($request->status === 'aktif', fn($q) => $q->where('is_aktif', true))
            ->when($request->status === 'nonaktif', fn($q) => $q->where('is_aktif', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.santri.index', compact('santri'));
    }

    public function create()
    {
        return view('admin.santri.form', ['santri' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
        ]);

        // Cek duplikat nama + tgl lahir
        $exists = Santri::where('nama', $request->nama)
            ->whereDate('tanggal_lahir', $request->tanggal_lahir)
            ->exists();
        
        if ($exists) {
            return back()->withInput()->withErrors(['nama' => 'Santri dengan nama dan tanggal lahir ini sudah terdaftar.']);
        }

        $validated = $request->validate([
            'nis'           => 'required|string|max:20|unique:santri,nis',
            'nik'           => 'nullable|string|max:20|unique:santri,nik',
            'nama'          => 'required|string|max:100',
            'nama_ar'       => 'nullable|string|max:200',
            'kelas'         => 'nullable|string|max:10',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string',
            'no_hp_ortu'    => 'nullable|string|max:20',
        ]);

        Santri::create($validated);

        return redirect()->route('admin.santri.index')
            ->with('success', "Santri {$validated['nama']} berhasil ditambahkan.");
    }

    public function edit(Santri $santri)
    {
        return view('admin.santri.form', compact('santri'));
    }

    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'nis'           => "required|string|max:20|unique:santri,nis,{$santri->id}",
            'nik'           => "nullable|string|max:20|unique:santri,nik,{$santri->id}",
            'nama'          => 'required|string|max:100',
            'nama_ar'       => 'nullable|string|max:200',
            'kelas'         => 'nullable|string|max:10',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string',
            'no_hp_ortu'    => 'nullable|string|max:20',
            'is_aktif'      => 'boolean',
        ]);

        $santri->update($validated);

        return redirect()->route('admin.santri.index')
            ->with('success', "Data {$santri->nama} berhasil diperbarui.");
    }

    public function destroy(Santri $santri)
    {
        $santri->update(['is_aktif' => false]);

        return back()->with('success', "{$santri->nama} dinonaktifkan.");
    }

    public function toggleStatus(Santri $santri)
    {
        $santri->update(['is_aktif' => !$santri->is_aktif]);
        $status = $santri->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "{$santri->nama} berhasil {$status}.");
    }

    public function forceDestroy(Santri $santri)
    {
        $nama = $santri->nama;

        \DB::transaction(function () use ($santri) {
            // Detach relasi pivot
            $santri->wali()->detach();
            $santri->halaqah()->detach();

            // Hapus record terkait yang punya FK restrictOnDelete / nullOnDelete
            \App\Models\Setoran::where('santri_id', $santri->id)->delete();
            \App\Models\Tagihan::where('santri_id', $santri->id)->delete();
            \App\Models\Pembayaran::whereHas('tagihan', fn($q) => $q->where('santri_id', $santri->id))->delete();
            \App\Models\WalletTransaction::where('santri_id', $santri->id)->delete();
            \App\Models\TransaksiKasir::where('santri_id', $santri->id)->delete();
            \App\Models\LaundryOrder::where('santri_id', $santri->id)->delete();
            \App\Models\VoiceNote::where('pengirim_santri_id', $santri->id)->orWhere('penerima_santri_id', $santri->id)->delete();
            \App\Models\Perizinan::where('santri_id', $santri->id)->delete();
            \App\Models\KepulanganSantri::where('santri_id', $santri->id)->delete();
            \App\Models\KunjunganKlinik::where('patient_type', \App\Models\Santri::class)
                ->where('patient_id', $santri->id)->delete();

            $santri->delete();
        });

        return redirect()->route('admin.santri.index')
            ->with('success', "Santri {$nama} berhasil dihapus permanen.");
    }

    public function naikKelas()
    {
        $kelasList = Santri::where('is_aktif', true)
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->get()
            ->groupBy('kelas')
            ->map(fn($s) => $s->count())
            ->sortKeys();

        $preview = $kelasList->map(fn($count, $kelas) => [
            'dari'  => $kelas,
            'ke'    => $this->nextKelas($kelas),
            'count' => $count,
        ]);

        return view('admin.santri.naik-kelas', compact('preview'));
    }

    public function doNaikKelas(Request $request)
    {
        $santriList = Santri::where('is_aktif', true)
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->get();

        $updated = 0;
        foreach ($santriList as $santri) {
            $next = $this->nextKelas($santri->kelas);
            if ($next !== $santri->kelas) {
                $santri->update(['kelas' => $next]);
                $updated++;
            }
        }

        return redirect()->route('admin.santri.index')
            ->with('success', "{$updated} santri berhasil dinaikkan kelasnya.");
    }

    private function nextKelas(string $kelas): string
    {
        if (!preg_match('/^(\d+)([A-Za-z]*)$/', trim($kelas), $m)) {
            return $kelas;
        }

        $angka = (int) $m[1];
        $huruf = $m[2];

        if ($angka >= 12) {
            return $kelas;
        }

        return ($angka + 1) . $huruf;
    }
}
