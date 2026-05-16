@extends('layouts.app')
@section('title', 'Dashboard Kesantrian')
@section('page-title', 'Dashboard Kesantrian')

@section('sidebar')
    @include('partials.sidebar-kesantrian')
@endsection

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
    <div class="glass-panel rounded-2xl shadow-sm p-6 border-l-4 border-yellow-400">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Menunggu ACC Kesantrian</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-1">{{ $pendingCount }}</h2>
            </div>
            <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                <i class="fa-solid fa-clock text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="glass-panel rounded-2xl shadow-sm p-6 border-l-4 border-emerald-400">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Disetujui Hari Ini</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-1">{{ $approvedToday }}</h2>
            </div>
            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
        <h6 class="text-lg font-bold text-gray-800">Perizinan Perlu ACC Kesantrian</h6>
        <a href="{{ route('kesantrian.perizinan.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua &rarr;</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Santri</th>
                    <th class="px-6 py-4 font-medium">Waktu Izin</th>
                    <th class="px-6 py-4 font-medium">Alasan</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pendingPerizinan as $p)
                <tr class="hover:bg-yellow-50/30 transition-colors">
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
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('kesantrian.perizinan.update-status', $p) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="disetujui_kesantrian">
                                <button type="submit" onclick="return confirm('ACC izin ini?')" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded text-sm font-medium transition-colors">
                                    <i class="fa-solid fa-check"></i> ACC
                                </button>
                            </form>
                            <form action="{{ route('kesantrian.perizinan.update-status', $p) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="ditolak">
                                <button type="submit" onclick="return confirm('Tolak izin ini?')" class="px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded text-sm font-medium transition-colors">
                                    <i class="fa-solid fa-times"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-3xl mb-2 block"></i>
                        Semua pengajuan sudah diproses.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
