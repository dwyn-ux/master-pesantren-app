<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class LimitUangSakuController extends Controller
{
    public function index()
    {
        $wali   = auth()->user()->wali;
        $santri = $wali->santri;

        return view('wali.limit_uang_saku.index', compact('santri'));
    }

    public function update(Request $request, Santri $santri)
    {
        $wali = auth()->user()->wali;

        if (! $wali->santri->contains('id', $santri->id)) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'tipe_limit'    => 'required|in:tidak_ada,harian,mingguan',
            'nominal_limit' => 'nullable|integer|min:1000|max:9999999',
        ]);

        $santri->update([
            'tipe_limit'    => $request->tipe_limit,
            'nominal_limit' => $request->tipe_limit === 'tidak_ada' ? null : $request->nominal_limit,
        ]);

        return back()->with('success', "Limit uang saku {$santri->nama} berhasil diperbarui.");
    }
}
