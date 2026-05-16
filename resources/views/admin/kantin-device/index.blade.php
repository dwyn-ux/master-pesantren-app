@extends('layouts.app')

@section('title', 'Device Kantin')
@section('page-title', 'Device Kantin Offline')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    @if(session('credential'))
    <div class="bg-yellow-50 border-2 border-yellow-300 rounded-2xl p-5 shadow-lg">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-key text-yellow-600 text-2xl mt-1"></i>
            <div class="flex-1">
                <h3 class="font-bold text-yellow-900 mb-2">Token Device Generated</h3>
                <div class="text-sm text-yellow-800">{!! session('credential') !!}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Form tambah device --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Daftar Device Kantin</h2>
        <p class="text-sm text-gray-500 mb-4">Setiap laptop/komputer kasir harus didaftarkan di sini untuk mendapatkan token API. Token hanya ditampilkan sekali saat dibuat.</p>

        <form action="{{ route('admin.kantin-device.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6 p-4 bg-indigo-50/30 rounded-xl border border-indigo-100">
            @csrf
            <div>
                <label class="block text-xs text-gray-600 mb-1">Outlet Kantin</label>
                <select name="outlet_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Pilih Outlet</option>
                    @foreach($outlets as $o)
                    <option value="{{ $o->id }}">{{ $o->nama }}</option>
                    @endforeach
                </select>
                @error('outlet_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Nama Device</label>
                <input type="text" name="nama" placeholder="Kasir Kantin Lt 1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Kode Device</label>
                <input type="text" name="device_code" placeholder="KASIR-01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono uppercase">
                @error('device_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fa-solid fa-plus mr-1"></i>Daftarkan & Generate Token
                </button>
            </div>
        </form>

        @if($devices->isEmpty())
            <div class="text-center py-8 text-gray-400">
                <i class="fa-solid fa-laptop text-4xl mb-2"></i>
                <p class="text-sm">Belum ada device kantin terdaftar.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Device</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Outlet</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Last Seen</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Last Sync</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($devices as $d)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            <div class="font-semibold text-gray-800">{{ $d->nama }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $d->device_code }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $d->outlet->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $d->last_seen_at?->diffForHumans() ?? 'Belum pernah' }}
                            @if($d->last_ip)<br><span class="font-mono">{{ $d->last_ip }}</span>@endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $d->last_sync_at?->diffForHumans() ?? 'Belum pernah' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($d->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-sm space-x-2">
                            <a href="{{ route('admin.kantin-device.logs', $d) }}" class="text-blue-600 hover:text-blue-800" title="Lihat log sync">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </a>
                            <form action="{{ route('admin.kantin-device.regenerate', $d) }}" method="POST" class="inline" onsubmit="return confirm('Generate token baru? Token lama akan langsung tidak valid.')">
                                @csrf
                                <button type="submit" class="text-orange-600 hover:text-orange-800" title="Regenerate token">
                                    <i class="fa-solid fa-key"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.kantin-device.toggle-status', $d) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-{{ $d->is_active ? 'gray' : 'green' }}-600 hover:text-{{ $d->is_active ? 'gray' : 'green' }}-800" title="{{ $d->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fa-solid fa-{{ $d->is_active ? 'pause' : 'play' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.kantin-device.destroy', $d) }}" method="POST" class="inline" onsubmit="return confirm('Hapus device permanen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
