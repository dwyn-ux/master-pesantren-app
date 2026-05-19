<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffPasswordController extends Controller
{
    /**
     * Daftar role staff yang bisa di-manage passwordnya dari superadmin.
     * Wali tidak termasuk — sudah ada di admin/wali.
     */
    private array $staffRoles = ['admin', 'bendahara', 'kepala_pondok', 'kesantrian', 'ustadz', 'outlet'];

    public function index(Request $request)
    {
        $users = User::with('roles')
            ->whereHas('roles', fn($q) => $q->whereIn('name', $this->staffRoles))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('username', 'like', "%{$request->search}%"))
            ->when($request->role, fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $request->role)))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $roleLabels = [
            'admin'        => 'Admin',
            'bendahara'    => 'Bendahara',
            'kepala_pondok'=> 'Kepala Pondok',
            'kesantrian'   => 'Kesantrian',
            'ustadz'       => 'Ustadz',
            'outlet'       => 'Outlet',
        ];

        return view('superadmin.staff-password.index', compact('users', 'roleLabels'));
    }

    public function update(Request $request, User $user)
    {
        // Pastikan user yang di-edit adalah staff (bukan superadmin lain)
        if ($user->hasRole('superadmin') && $user->id !== auth()->id()) {
            return back()->with('error', 'Tidak bisa mengubah password superadmin lain.');
        }

        $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::min(6)],
        ]);

        $user->update([
            'password'       => Hash::make($request->password),
            'must_change_pw' => $request->boolean('must_change_pw', true),
        ]);

        return back()->with('success', "Password {$user->name} ({$user->username}) berhasil diubah.");
    }

    public function resetToDefault(User $user)
    {
        // Reset ke password default: username + "@1234"
        $defaultPassword = $user->username . '@1234';

        $user->update([
            'password'       => Hash::make($defaultPassword),
            'must_change_pw' => true,
        ]);

        return back()->with('credential', "Password <strong>{$user->name}</strong> direset ke: <strong>{$defaultPassword}</strong> (wajib ganti saat login)");
    }
}
