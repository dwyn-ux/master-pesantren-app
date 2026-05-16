<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        
        if (!$user) {
            \Log::warning('FCM Token Update Gagal: User tidak terautentikasi.');
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user->fcm_token = $request->fcm_token;
        $user->save();

        \Log::info('FCM Token Berhasil Diupdate untuk User: ' . $user->name);

        return response()->json([
            'success' => true,
            'message' => 'FCM Token updated successfully.',
        ]);
    }
}
