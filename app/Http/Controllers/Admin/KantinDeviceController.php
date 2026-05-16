<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KantinDevice;
use App\Models\Outlet;
use Illuminate\Http\Request;

class KantinDeviceController extends Controller
{
    public function index()
    {
        $devices = KantinDevice::with('outlet')->orderBy('created_at', 'desc')->get();
        $outlets = Outlet::where('tipe', 'kantin')->where('is_aktif', true)->get();

        return view('admin.kantin-device.index', compact('devices', 'outlets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id'   => 'required|exists:outlets,id',
            'nama'        => 'required|string|max:100',
            'device_code' => 'required|string|max:50|unique:kantin_devices,device_code',
        ]);

        $token = KantinDevice::generateToken();

        KantinDevice::create([
            'outlet_id'   => $validated['outlet_id'],
            'nama'        => $validated['nama'],
            'device_code' => $validated['device_code'],
            'token_hash'  => $token['hash'],
            'is_active'   => true,
        ]);

        return back()->with('credential', "Token device <code class='font-mono'>{$token['plain']}</code><br><strong class='text-red-600'>SIMPAN sekarang juga, token tidak akan ditampilkan ulang!</strong>");
    }

    public function regenerate(KantinDevice $device)
    {
        $token = KantinDevice::generateToken();
        $device->update(['token_hash' => $token['hash']]);

        return back()->with('credential', "Token baru untuk <strong>{$device->nama}</strong>: <code class='font-mono'>{$token['plain']}</code><br><strong class='text-red-600'>SIMPAN sekarang juga!</strong>");
    }

    public function toggleStatus(KantinDevice $device)
    {
        $device->update(['is_active' => !$device->is_active]);
        $status = $device->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Device {$device->nama} berhasil {$status}.");
    }

    public function destroy(KantinDevice $device)
    {
        $device->delete();
        return back()->with('success', 'Device berhasil dihapus.');
    }

    public function logs(KantinDevice $device)
    {
        $logs = $device->syncLogs()->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.kantin-device.logs', compact('device', 'logs'));
    }
}
