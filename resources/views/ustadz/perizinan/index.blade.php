@extends('layouts.app')
@section('title', 'Perizinan Santri Halaqah')
@section('page-title', 'Perizinan Halaqah')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
        <h6 class="text-lg font-bold text-gray-800">Daftar Pengajuan Izin Santri Halaqah</h6>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Santri</th>
                    <th class="px-6 py-4 font-medium">Waktu Izin</th>
                    <th class="px-6 py-4 font-medium">Alasan</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($perizinan as $p)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $p->santri->nama }}</div>
                        <div class="text-xs text-gray-500">NIS: {{ $p->santri->nis }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="text-gray-800">K: {{ $p->tanggal_mulai->format('d/m/Y H:i') }}</div>
                        <div class="text-gray-800">M: {{ $p->tanggal_selesai->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $p->alasan }}">
                        {{ $p->alasan }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $color = match($p->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'disetujui_ustadz' => 'bg-blue-100 text-blue-800',
                                'disetujui_kesantrian' => 'bg-emerald-100 text-emerald-800',
                                'ditolak' => 'bg-red-100 text-red-800',
                                'sedang_keluar' => 'bg-indigo-100 text-indigo-800',
                                'selesai' => 'bg-gray-100 text-gray-800',
                                'terlambat' => 'bg-red-200 text-red-900',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            $label = str_replace('_', ' ', strtoupper($p->status));
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $color }}">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($p->status === 'pending')
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('ustadz.perizinan.update-status', $p) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="disetujui_ustadz">
                                <button type="submit" onclick="return confirm('Yakin ACC izin ini?')" class="px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded text-sm font-medium transition-colors">
                                    <i class="fa-solid fa-check"></i> ACC
                                </button>
                            </form>
                            <form action="{{ route('ustadz.perizinan.update-status', $p) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="ditolak">
                                <button type="submit" onclick="return confirm('Yakin tolak izin ini?')" class="px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded text-sm font-medium transition-colors">
                                    <i class="fa-solid fa-times"></i> Tolak
                                </button>
                            </form>
                        </div>
                        @else
                            <span class="text-xs text-gray-400 italic">Sudah diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        Belum ada data perizinan dari santri halaqah Anda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($perizinan->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $perizinan->links() }}
    </div>
    @endif
</div>
@endsection
