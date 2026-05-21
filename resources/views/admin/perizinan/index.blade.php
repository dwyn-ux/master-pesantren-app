@extends('layouts.app')
@section('title', 'Kelola Perizinan')
@section('page-title', 'Perizinan Santri')

@section('sidebar')
    @if(auth()->user()->hasRole('admin'))
        @include('partials.sidebar-admin')
    @elseif(auth()->user()->hasRole('kesantrian'))
        @include('partials.sidebar-kesantrian')
    @else
        @include('partials.sidebar-ustadz')
    @endif
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <h6 class="text-lg font-bold text-gray-800">Daftar Pengajuan Izin</h6>
    </div>

    {{-- Filter --}}
    <div class="p-6 border-b border-gray-100">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" 
                       placeholder="Cari nama santri atau NIS...">
            </div>
            <div class="w-full md:w-48">
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all bg-white appearance-none">
                    <option value="">Semua Status</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="disetujui_ustadz" @selected(request('status') === 'disetujui_ustadz')>ACC Ustadz</option>
                    <option value="disetujui_kesantrian" @selected(request('status') === 'disetujui_kesantrian')>ACC Kesantrian</option>
                    <option value="sedang_keluar" @selected(request('status') === 'sedang_keluar')>Sedang Keluar</option>
                    <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                    <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat</option>
                    <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm">
                    Filter
                </button>
                <a href="{{ route('admin.perizinan.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">
                    Reset
                </a>
            </div>
        </form>
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
                        <div class="text-[10px] bg-gray-100 text-gray-600 inline-block px-2 py-0.5 mt-1 rounded">Oleh: {{ ucfirst($p->diajukan_oleh) }}</div>
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
                        <form action="{{ route('admin.perizinan.update-status', $p) }}" method="POST" class="flex items-center justify-end gap-2">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="text-sm px-3 py-1.5 rounded border border-gray-200 focus:ring-indigo-500 outline-none cursor-pointer">
                                <option value="pending" @selected($p->status == 'pending')>Pending</option>
                                <option value="disetujui_ustadz" @selected($p->status == 'disetujui_ustadz')>ACC Ustadz</option>
                                <option value="disetujui_kesantrian" @selected($p->status == 'disetujui_kesantrian')>ACC Kesantrian</option>
                                <option value="sedang_keluar" @selected($p->status == 'sedang_keluar')>Sedang Keluar</option>
                                <option value="selesai" @selected($p->status == 'selesai')>Selesai</option>
                                <option value="terlambat" @selected($p->status == 'terlambat')>Terlambat</option>
                                <option value="ditolak" @selected($p->status == 'ditolak')>Ditolak</option>
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        Belum ada data perizinan.
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
