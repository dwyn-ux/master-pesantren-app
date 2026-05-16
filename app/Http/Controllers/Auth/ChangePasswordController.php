<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();
        $user->update([
            'password'       => Hash::make($request->password),
            'must_change_pw' => false,
        ]);

        return redirect($this->dashboardRoute($user))
            ->with('success', 'Password berhasil diubah.');
    }

    private function dashboardRoute($user): string
    {
        return match (true) {
            $user->hasRole('superadmin')    => route('superadmin.dashboard'),
            $user->hasRole('admin')         => route('admin.dashboard'),
            $user->hasRole('bendahara')     => route('bendahara.dashboard'),
            $user->hasRole('kepala_pondok') => route('kepala-pondok.dashboard'),
            $user->hasRole('ustadz')        => route('ustadz.dashboard'),
            $user->hasRole('wali')          => route('wali.dashboard'),
            $user->hasRole('outlet')        => route('outlet.dashboard'),
            default                         => route('login'),
        };
    }
}
