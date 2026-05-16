<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Akademik\JadwalPelajaran;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\MataPelajaran;
use App\Models\Akademik\TahunAjaran;
use App\Models\Ustadz;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $kelas = Kelas::with('tingkat')->orderBy('tingkat_id')->get();

        $jadwal = collect();
        if ($request->filled('tahun_ajaran_id') && $request->filled('kelas_id')) {
            $jadwal = JadwalPelajaran::where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->where('kelas_id', $request->kelas_id)
                ->with(['mataPelajaran', 'ustadz.user'])
                ->orderByRaw("FIELD(hari, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu')")
                ->orderBy('jam_mulai')
                ->get();
        }

        return view('admin.akademik.jadwal-pelajaran.index', compact('tahunAjaran', 'kelas', 'jadwal', 'request'));
    }

    public function create(Request $request)
    {
        $tahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $kelas = Kelas::with('tingkat')->orderBy('tingkat_id')->get();
        $mapel = MataPelajaran::orderBy('urutan')->get();
        $ustadz = Ustadz::with('user')->get();

        return view('admin.akademik.jadwal-pelajaran.create', compact('tahunAjaran', 'kelas', 'mapel', 'ustadz', 'request'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id'          => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'ustadz_id'         => 'required|exists:ustadz,id',
            'tahun_ajaran_id'   => 'required|exists:tahun_ajaran,id',
            'hari'              => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jam_mulai'         => 'required|date_format:H:i',
            'jam_selesai'       => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'           => 'nullable|string|max:50',
        ]);

        JadwalPelajaran::create($validated);

        return redirect()->route('admin.akademik.jadwal-pelajaran.index', [
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'kelas_id'        => $validated['kelas_id'],
        ])->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        $tahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $kelas = Kelas::with('tingkat')->orderBy('tingkat_id')->get();
        $mapel = MataPelajaran::orderBy('urutan')->get();
        $ustadz = Ustadz::with('user')->get();

        return view('admin.akademik.jadwal-pelajaran.edit', compact('jadwalPelajaran', 'tahunAjaran', 'kelas', 'mapel', 'ustadz'));
    }

    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $validated = $request->validate([
            'kelas_id'          => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'ustadz_id'         => 'required|exists:ustadz,id',
            'tahun_ajaran_id'   => 'required|exists:tahun_ajaran,id',
            'hari'              => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jam_mulai'         => 'required|date_format:H:i',
            'jam_selesai'       => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'           => 'nullable|string|max:50',
        ]);

        $jadwalPelajaran->update($validated);

        return redirect()->route('admin.akademik.jadwal-pelajaran.index', [
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'kelas_id'        => $validated['kelas_id'],
        ])->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        $taId = $jadwalPelajaran->tahun_ajaran_id;
        $klsId = $jadwalPelajaran->kelas_id;

        $jadwalPelajaran->delete();

        return redirect()->route('admin.akademik.jadwal-pelajaran.index', [
            'tahun_ajaran_id' => $taId,
            'kelas_id'        => $klsId,
        ])->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
