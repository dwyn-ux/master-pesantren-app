<?php

namespace App\Http\Middleware;

use App\Models\KantinDevice;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateKantinDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Bearer token required.',
            ], 401);
        }

        $device = KantinDevice::findByToken($token);

        if (!$device) {
            return response()->json([
                'message' => 'Invalid or revoked device token.',
            ], 401);
        }

        $device->touchLastSeen($request->ip());

        $request->attributes->set('kantin_device', $device);

        return $next($request);
    }
}
