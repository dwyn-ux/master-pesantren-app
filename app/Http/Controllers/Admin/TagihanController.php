<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisTagihan;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function __construct(protected NotificationService $notifier) {}

    public function index()
    {
        $tagihan = Tagihan::with(['santri', 'jenisTagihan'])
            ->latest('created_at')
            ->get();

        return view('admin.tagihan.index', compact('tagihan'));
    }

    public function create()
    {
        $santri = Santri::where('is_aktif', true)->orderBy('nama')->get();
        $jenis = JenisTagihan::where('is_aktif', true)->orderBy('nama')->get();

        return view('admin.tagihan.form', [
            'tagihan' => null,
            'santri'  => $santri,
            'jenis'   => $jenis,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'santri_ids'        => 'required|array|min:1',
            'santri_ids.*'      => 'exists:santri,id',
            'jenis_tagihan_id'  => 'required|exists:jenis_tagihan,id',
            'nominal'           => 'nullable|integer|min:0',
            'periode'           => 'required|string|max:7',
            'due_date'          => 'required|date',
        ]);

        $jenis = JenisTagihan::findOrFail($request->jenis_tagihan_id);
        $nominal = $jenis->is_nominal_tetap ? $jenis->nominal : $request->nominal;

        if (! $jenis->is_nominal_tetap && is_null($nominal)) {
            return back()->withInput()->withErrors(['nominal' => 'Nominal harus diisi untuk jenis bebas isi.']);
        }

        $created = 0;
        $skipped = 0;

        foreach ($request->santri_ids as $santriId) {
            $exists = Tagihan::where('santri_id', $santriId)
                ->where('jenis_tagihan_id', $jenis->id)
                ->where('periode', $request->periode)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            Tagihan::create([
                'santri_id'        => $santriId,
                'jenis_tagihan_id' => $jenis->id,
                'nominal'          => $nominal,
                'periode'          => $request->periode,
                'status'           => 'belum_bayar',
                'due_date'         => $request->due_date,
            ]);
            $created++;

            // Kirim Notifikasi ke Wali Santri (DB + FCM)
            $santriData = Santri::with('wali.user')->find($santriId);
            if ($santriData) {
                foreach ($santriData->wali as $wali) {
                    if (!$wali->user) continue;
                    $this->notifier->send(
                        user: $wali->user,
                        type: 'tagihan_baru',
                        title: 'Tagihan Baru: ' . $jenis->nama,
                        body: 'Terdapat tagihan baru untuk ananda ' . $santriData->nama . ' sebesar Rp ' . number_format($nominal, 0, ',', '.'),
                        data: ['type' => 'tagihan_baru', 'santri_id' => (string) $santriId],
                        actionUrl: route('wali.tagihan.index'),
                    );
                }
            }
        }

        $msg = "{$created} tagihan berhasil dibuat.";
        if ($skipped > 0) $msg .= " {$skipped} dilewati (sudah ada).";

        return redirect()->route('admin.tagihan.index')->with('success', $msg);
    }

    public function edit(Tagihan $tagihan)
    {
        $santri = Santri::where('is_aktif', true)->orderBy('nama')->get();
        $jenis = JenisTagihan::where('is_aktif', true)->orderBy('nama')->get();

        return view('admin.tagihan.form', compact('tagihan', 'santri', 'jenis'));
    }

    public function update(Request $request, Tagihan $tagihan)
    {
        $request->validate([
            'santri_id'         => 'required|exists:santri,id',
            'jenis_tagihan_id'  => 'required|exists:jenis_tagihan,id',
            'nominal'           => 'nullable|integer|min:0',
            'periode'           => 'required|string|max:7',
            'status'            => 'required|in:belum_bayar,lunas',
            'due_date'          => 'required|date',
        ]);

        $jenis = JenisTagihan::findOrFail($request->jenis_tagihan_id);
        $nominal = $jenis->is_nominal_tetap ? $jenis->nominal : $request->nominal;

        if (! $jenis->is_nominal_tetap && is_null($nominal)) {
            return back()->withInput()->withErrors(['nominal' => 'Nominal harus diisi untuk jenis bebas isi.']);
        }

        $tagihan->update([
            'santri_id'         => $request->santri_id,
            'jenis_tagihan_id'  => $jenis->id,
            'nominal'           => $nominal,
            'periode'           => $request->periode,
            'status'            => $request->status,
            'due_date'          => $request->due_date,
        ]);

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Tagihan $tagihan)
    {
        $tagihan->delete();

        return back()->with('success', 'Tagihan berhasil dihapus.');
    }
}
