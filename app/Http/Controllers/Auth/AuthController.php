<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->dashboardRoute(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $request->username)
            ->where('is_active', true)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Username atau password salah.']);
        }

        // Wali auto-remember supaya tidak perlu login ulang di HP.
        // Role lain (admin/bendahara/dll) tetap bisa opt-in lewat checkbox.
        $remember = $user->hasRole('wali') ? true : $request->boolean('remember');

        Auth::login($user, $remember);

        if ($user->must_change_pw) {
            return redirect()->route('auth.change-password');
        }

        return redirect($this->dashboardRoute($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Hapus FCM token saat logout supaya notifikasi tidak dikirim ke device ini lagi
        if ($user && $user->fcm_token) {
            $user->update(['fcm_token' => null]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function dashboardRoute(User $user): string
    {
        return match (true) {
            $user->hasRole('superadmin')    => route('superadmin.dashboard'),
            $user->hasRole('admin')         => route('admin.dashboard'),
            $user->hasRole('bendahara')     => route('bendahara.dashboard'),
            $user->hasRole('kepala_pondok') => route('kepala-pondok.dashboard'),
            $user->hasRole('ustadz')        => route('ustadz.dashboard'),
            $user->hasRole('wali')          => route('wali.dashboard'),
            $user->hasRole('outlet')        => route('outlet.dashboard'),
            default                         => '/',
        };
    }
}
