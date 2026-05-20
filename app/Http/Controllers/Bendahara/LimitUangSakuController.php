<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class LimitUangSakuController extends Controller
{
    public function index(Request $request)
    {
        $santri = Santri::query()
            ->when($request->search, fn($q) => $q
                ->where('nama', 'ilike', "%{$request->search}%")
                ->orWhere('nis', 'ilike', "%{$request->search}%"))
            ->where('is_aktif', true)
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        return view('bendahara.limit_uang_saku.index', compact('santri'));
    }

    public function update(Request $request, Santri $santri)
    {
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
