@extends('layouts.app')
@section('title', 'Detail Kepulangan Santri')
@section('page-title', 'Detail Sesi: ' . $sesi_kepulangan->nama_sesi)

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
        <h6 class="text-lg font-bold text-gray-800">Daftar Santri & Status Kepulangan</h6>
        <a href="{{ route('admin.sesi-kepulangan.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Santri</th>
                    <th class="px-6 py-4 font-medium">Status Administrasi</th>
                    <th class="px-6 py-4 font-medium">Janji Bayar (Jika ACC)</th>
                    <th class="px-6 py-4 font-medium">Waktu Keluar</th>
                    <th class="px-6 py-4 font-medium">Waktu Kembali</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kepulangan as $k)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $k->santri->nama }}</div>
                        <div class="text-xs text-gray-500">NIS: {{ $k->santri->nis }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($k->status_administrasi === 'lunas')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Lunas / Siap Pulang</span>
                        @elseif($k->status_administrasi === 'acc_bendahara')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">ACC Bendahara (Surat Kesanggupan)</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Belum Lunas</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $k->tanggal_janji_bayar ? $k->tanggal_janji_bayar->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        @if($k->waktu_keluar)
                            <span class="text-green-600"><i class="fa-solid fa-check"></i> {{ $k->waktu_keluar->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="text-gray-400">Belum keluar</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        @if($k->waktu_kembali)
                            <span class="text-green-600"><i class="fa-solid fa-check"></i> {{ $k->waktu_kembali->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="text-gray-400">Belum kembali</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">Data santri tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kepulangan->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $kepulangan->links() }}
    </div>
    @endif
</div>
@endsection
