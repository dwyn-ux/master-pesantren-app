<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureMustChangePw
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->must_change_pw) {
            // Biarkan akses ke route ganti password & logout saja
            if (! $request->routeIs('auth.change-password', 'auth.change-password.update', 'auth.logout')) {
                return redirect()->route('auth.change-password');
            }
        }

        return $next($request);
    }
}
