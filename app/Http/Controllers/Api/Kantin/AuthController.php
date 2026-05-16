<?php

namespace App\Http\Controllers\Api\Kantin;

use App\Http\Controllers\Controller;
use App\Models\KantinDevice;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Verify token & return device info.
     * Body: token disertakan via Bearer header.
     */
    public function me(Request $request)
    {
        $device = $request->attributes->get('kantin_device');

        return response()->json([
            'device' => [
                'id'           => $device->id,
                'nama'         => $device->nama,
                'device_code'  => $device->device_code,
                'outlet'       => [
                    'id'   => $device->outlet->id ?? null,
                    'nama' => $device->outlet->nama ?? '-',
                    'tipe' => $device->outlet->tipe ?? null,
                ],
                'last_sync_at' => $device->last_sync_at,
                'server_time'  => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Verifikasi token only - dipakai pas startup app electron.
     */
    public function verify(Request $request)
    {
        $device = KantinDevice::findByToken($request->bearerToken() ?? '');

        if (!$device) {
            return response()->json([
                'valid'   => false,
                'message' => 'Token tidak valid atau sudah dicabut.',
            ], 401);
        }

        return response()->json([
            'valid'        => true,
            'device_code'  => $device->device_code,
            'nama'         => $device->nama,
        ]);
    }
}
