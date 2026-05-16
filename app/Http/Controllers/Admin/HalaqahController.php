<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Halaqah;
use App\Models\Santri;
use App\Models\Ustadz;
use Illuminate\Http\Request;

class HalaqahController extends Controller
{
    public function index()
    {
        $halaqah = Halaqah::with('ustadz')
            ->withCount('santri')
            ->latest()
            ->paginate(20);

        return view('admin.halaqah.index', compact('halaqah'));
    }

    public function create()
    {
        $ustadz = Ustadz::orderBy('nama')->get();
        return view('admin.halaqah.form', ['halaqah' => null, 'ustadz' => $ustadz]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:100',
            'ustadz_id' => 'required|exists:ustadz,id',
        ]);

        Halaqah::create($request->only('nama', 'ustadz_id'));

        return redirect()->route('admin.halaqah.index')
            ->with('success', "Halaqah {$request->nama} berhasil dibuat.");
    }

    public function edit(Halaqah $halaqah)
    {
        $ustadz = Ustadz::orderBy('nama')->get();
        $santri = Santri::where('is_aktif', true)->orderBy('nama')->get();
        $halaqah->load('santri');
        return view('admin.halaqah.form', compact('halaqah', 'ustadz', 'santri'));
    }

    public function update(Request $request, Halaqah $halaqah)
    {
        $request->validate([
            'nama'      => 'required|string|max:100',
            'ustadz_id' => 'required|exists:ustadz,id',
        ]);

        $halaqah->update($request->only('nama', 'ustadz_id'));

        // Sync santri
        if ($request->has('santri_ids')) {
            $sync = [];
            foreach ($request->santri_ids as $id) {
                $sync[$id] = ['tanggal_bergabung' => now()->toDateString()];
            }
            $halaqah->santri()->sync($sync);
        } else {
            $halaqah->santri()->detach();
        }

        return redirect()->route('admin.halaqah.index')
            ->with('success', "Halaqah {$halaqah->nama} berhasil diperbarui.");
    }

    public function destroy(Halaqah $halaqah)
    {
        $halaqah->delete();
        return back()->with('success', "Halaqah {$halaqah->nama} dihapus.");
    }
}
