<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ustadz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UstadzController extends Controller
{
    public function index(Request $request)
    {
        $ustadz = Ustadz::with('user')
            ->when($request->search, fn($q) => $q
                ->where('nama', 'ilike', "%{$request->search}%")
                ->orWhere('no_hp', 'ilike', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.ustadz.index', compact('ustadz'));
    }

    public function create()
    {
        return view('admin.ustadz.form', ['ustadz' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'           => 'nullable|string|max:20|unique:ustadz,nik',
            'nama'          => 'required|string|max:100',
            'nama_ar'       => 'nullable|string|max:200',
            'alamat'        => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'no_hp'         => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $namaDepan = strtolower(preg_replace('/[^a-zA-Z]/', '', explode(' ', $request->nama)[0]));
            $username  = 'ustadz.' . $namaDepan;
            $base      = $username;
            $i = 1;
            while (User::where('username', $username)->exists()) {
                $username = $base . $i++;
            }
            $password = Str::random(8);

            $user = User::create([
                'name'           => $request->nama,
                'username'       => $username,
                'password'       => Hash::make($password),
                'must_change_pw' => true,
                'is_active'      => true,
            ]);
            $user->assignRole('ustadz');

            Ustadz::create([
                'user_id'       => $user->id,
                'nik'           => $request->nik,
                'nama'          => $request->nama,
                'nama_ar'       => $request->nama_ar,
                'alamat'        => $request->alamat,
                'tanggal_lahir' => $request->tanggal_lahir,
                'no_hp'         => $request->no_hp,
            ]);

            \Storage::disk('local')->append('credentials-ustadz.txt',
                "[ustadz] nama: {$request->nama} | username: {$username} | password: {$password}\n"
            );

            session()->flash('credential', "Username: <strong>{$username}</strong> | Password: <strong>{$password}</strong>");
        });

        return redirect()->route('admin.ustadz.index')
            ->with('success', "Ustadz {$request->nama} berhasil ditambahkan. Catat kredensial!");
    }

    public function edit(Ustadz $ustadz)
    {
        return view('admin.ustadz.form', compact('ustadz'));
    }

    public function update(Request $request, Ustadz $ustadz)
    {
        $request->validate([
            'nik'           => "nullable|string|max:20|unique:ustadz,nik,{$ustadz->id}",
            'nama'          => 'required|string|max:100',
            'nama_ar'       => 'nullable|string|max:200',
            'alamat'        => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'no_hp'         => 'nullable|string|max:20',
        ]);

        $ustadz->update([
            'nik'           => $request->nik,
            'nama'          => $request->nama,
            'nama_ar'       => $request->nama_ar,
            'alamat'        => $request->alamat,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_hp'         => $request->no_hp
        ]);
        $ustadz->user->update(['name' => $request->nama]);

        return redirect()->route('admin.ustadz.index')
            ->with('success', "Data {$ustadz->nama} berhasil diperbarui.");
    }

    public function destroy(Ustadz $ustadz)
    {
        DB::transaction(function () use ($ustadz) {
            $ustadz->user->delete();
            $ustadz->delete();
        });
        return redirect()->route('admin.ustadz.index')->with('success', "Data {$ustadz->nama} berhasil dihapus permanen.");
    }

    public function toggleStatus(Ustadz $ustadz)
    {
        $ustadz->user->update(['is_active' => !$ustadz->user->is_active]);
        $status = $ustadz->user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "{$ustadz->nama} berhasil {$status}.");
    }

    public function resetPassword(Ustadz $ustadz)
    {
        // Reset ke username sebagai password default (mudah diingat, wajib ganti)
        $defaultPassword = $ustadz->user->username;
        $ustadz->user->update([
            'password'       => Hash::make($defaultPassword),
            'must_change_pw' => true,
        ]);

        \Storage::disk('local')->append('credentials-ustadz.txt',
            "[reset] nama: {$ustadz->nama} | username: {$ustadz->user->username} | password baru: {$defaultPassword}\n"
        );

        return back()->with('credential', "Password <strong>{$ustadz->nama}</strong> direset ke: <strong>{$defaultPassword}</strong> (wajib ganti saat login)");
    }

    public function downloadCredential(Ustadz $ustadz)
    {
        $username     = $ustadz->user->username;
        $noHp         = $ustadz->no_hp ?: '-';

        $content  = "=== KREDENSIAL LOGIN USTADZ ===\n\n";
        $content .= "Nama        : {$ustadz->nama}\n";
        $content .= "No HP       : {$noHp}\n\n";
        $content .= "Username    : {$username}\n";
        $content .= "Password    : {$username}\n";
        $content .= "             (password default = username, ubah setelah login pertama)\n\n";
        $content .= "URL Login   : " . url('/login') . "\n\n";
        $content .= "==============================\n";
        $content .= "Dicetak pada: " . now()->format('d/m/Y H:i') . "\n";

        $filename = 'kredensial-' . Str::slug($ustadz->nama) . '.txt';

        return response($content, 200, [
            'Content-Type'        => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function downloadAllCredentials()
    {
        $semuaUstadz = Ustadz::with('user')
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->orderBy('nama')
            ->get();

        $content  = "=== DAFTAR KREDENSIAL LOGIN USTADZ ===\n";
        $content .= "Dicetak pada : " . now()->format('d/m/Y H:i') . "\n";
        $content .= "Total Ustadz : " . $semuaUstadz->count() . " akun\n";
        $content .= "URL Login    : " . url('/login') . "\n";
        $content .= str_repeat("=", 50) . "\n\n";

        foreach ($semuaUstadz as $u) {
            $noHp = $u->no_hp ?: '-';
            $content .= "Nama       : {$u->nama}\n";
            $content .= "No HP      : {$noHp}\n";
            $content .= "Username   : {$u->user->username}\n";
            $content .= "Password   : {$u->user->username}\n";
            $content .= "           (password default = username)\n";
            $content .= str_repeat("-", 40) . "\n";
        }

        return response($content, 200, [
            'Content-Type'        => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="kredensial-semua-ustadz-' . now()->format('Ymd') . '.txt"',
        ]);
    }
}
