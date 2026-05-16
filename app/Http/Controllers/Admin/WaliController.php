<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\User;
use App\Models\Wali;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WaliController extends Controller
{
    public function index(Request $request)
    {
        $wali = Wali::with('user', 'santri')
            ->when($request->search, fn($q) => $q
                ->where('nama', 'like', "%{$request->search}%")
                ->orWhere('no_hp', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.wali.index', compact('wali'));
    }

    public function create()
    {
        $santri = Santri::where('is_aktif', true)->orderBy('nama')->get();
        return view('admin.wali.form', ['wali' => null, 'santri' => $santri]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:100',
            'no_hp'       => 'required|string|max:20',
            'santri_ids'  => 'required|array|min:1',
            'santri_ids.*'=> 'exists:santri,id',
            'hubungan'    => 'nullable|array',
        ]);

        // Cek duplikat Wali + Anak
        foreach ($request->santri_ids as $santriId) {
            $exists = Wali::where('nama', $request->nama)
                ->whereHas('santri', fn($q) => $q->where('santri_id', $santriId))
                ->exists();
            
            if ($exists) {
                $santri = Santri::find($santriId);
                return back()->withInput()->withErrors(['nama' => "Wali {$request->nama} sudah terhubung dengan santri {$santri->nama}."]);
            }
        }

        DB::transaction(function () use ($request) {
            // Auto-generate username & password
            $namaDepan = strtolower(preg_replace('/[^a-zA-Z]/', '', explode(' ', $request->nama)[0]));
            $username  = $namaDepan . rand(100, 999);
            while (User::where('username', $username)->exists()) {
                $username = $namaDepan . rand(100, 999);
            }
            $password = Str::random(8);

            $user = User::create([
                'name'           => $request->nama,
                'username'       => $username,
                'password'       => Hash::make($password),
                'must_change_pw' => true,
                'is_active'      => true,
            ]);
            $user->assignRole('wali');

            $wali = Wali::create([
                'user_id' => $user->id,
                'nama'    => $request->nama,
                'no_hp'   => $request->no_hp,
            ]);

            // Link ke santri
            if ($request->santri_ids) {
                foreach ($request->santri_ids as $santriId) {
                    $wali->santri()->attach($santriId, [
                        'hubungan' => $request->hubungan[$santriId] ?? 'wali',
                    ]);
                }
            }

            // Simpan kredensial ke file
            $line = "[wali] nama: {$request->nama} | username: {$username} | password: {$password}\n";
            \Storage::disk('local')->append('credentials-wali.txt', $line);

            session()->flash('credential', "Username: <strong>{$username}</strong> | Password: <strong>{$password}</strong>");
        });

        return redirect()->route('admin.wali.index')
            ->with('success', "Wali {$request->nama} berhasil ditambahkan. Catat kredensial berikut!");
    }

    public function edit(Wali $wali)
    {
        $santri = Santri::where('is_aktif', true)->orderBy('nama')->get();
        $wali->load('santri', 'user');
        return view('admin.wali.form', compact('wali', 'santri'));
    }

    public function update(Request $request, Wali $wali)
    {
        $request->validate([
            'nama'  => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
        ]);

        $wali->update(['nama' => $request->nama, 'no_hp' => $request->no_hp]);
        $wali->user->update(['name' => $request->nama]);

        return redirect()->route('admin.wali.index')
            ->with('success', "Data {$wali->nama} berhasil diperbarui.");
    }

    public function destroy(Wali $wali)
    {
        $wali->user->update(['is_active' => false]);
        return back()->with('success', "{$wali->nama} dinonaktifkan.");
    }

    public function toggleStatus(Wali $wali)
    {
        $wali->user->update(['is_active' => !$wali->user->is_active]);
        $status = $wali->user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "{$wali->nama} berhasil {$status}.");
    }

    public function forceDestroy(Wali $wali)
    {
        $nama = $wali->nama;

        DB::transaction(function () use ($wali) {
            // Detach pivot santri
            $wali->santri()->detach();

            // Hapus record terkait
            \App\Models\VoiceNote::where('pengirim_wali_id', $wali->id)
                ->orWhere('penerima_wali_id', $wali->id)
                ->delete();
            \App\Models\Pembayaran::where('wali_id', $wali->id)->delete();
            \App\Models\Order::where('wali_id', $wali->id)->delete();

            $user = $wali->user;
            $wali->delete();

            if ($user) {
                \App\Models\AppNotification::where('user_id', $user->id)->delete();
                $user->delete();
            }
        });

        return redirect()->route('admin.wali.index')
            ->with('success', "Wali {$nama} berhasil dihapus permanen.");
    }

    public function resetPassword(Wali $wali)
    {
        $password = Str::random(8);
        $wali->user->update([
            'password'       => Hash::make($password),
            'must_change_pw' => true,
        ]);

        \Storage::disk('local')->append('credentials-wali.txt',
            "[reset] nama: {$wali->nama} | username: {$wali->user->username} | password baru: {$password}\n"
        );

        return back()->with('credential', "Password baru: <strong>{$password}</strong>");
    }
}
