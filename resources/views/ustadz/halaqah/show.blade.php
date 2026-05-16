@extends('layouts.app')
@section('title', 'Detail Halaqah - ' . $halaqah->nama)
@section('page-title', 'Detail Halaqah')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-5 px-1">
        <a href="{{ route('ustadz.halaqah.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar Halaqah
        </a>
    </div>

    <!-- Header Halaqah -->
    <div class="glass-panel rounded-2xl p-6 mb-6 bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-2xl shadow-lg">
                    <i class="fa-solid fa-book-quran"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-extrabold text-gray-800">{{ $halaqah->nama }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Musyrif: <strong class="text-indigo-600">{{ $ustadz->nama }}</strong></p>
                </div>
            </div>
            <div class="bg-white/80 rounded-2xl px-6 py-4 text-center border border-indigo-50 shadow-sm">
                <div class="text-3xl font-black text-indigo-600">{{ $halaqah->santri->count() }}</div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Santri Aktif</div>
            </div>
        </div>
    </div>

    <!-- Daftar Santri -->
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-users text-indigo-500"></i> Daftar Santri
            </h6>
            <a href="{{ route('ustadz.setoran.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm transition-colors shadow-sm">
                <i class="fa-solid fa-plus"></i> Catat Setoran
            </a>
        </div>

        @if($halaqah->santri->isEmpty())
            <div class="p-12 text-center">
                <i class="fa-solid fa-user-xmark text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">Belum ada santri di halaqah ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                            <th class="px-6 py-4 font-medium">NIS</th>
                            <th class="px-6 py-4 font-medium">Nama Santri</th>
                            <th class="px-6 py-4 font-medium">Bergabung</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($halaqah->santri as $santri)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-indigo-700 text-sm">{{ $santri->nis }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-extrabold text-sm shrink-0">
                                            {{ strtoupper(substr($santri->nama, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-gray-800">{{ $santri->nama }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-sm">
                                    {{ $santri->pivot->tanggal_bergabung ? \Carbon\Carbon::parse($santri->pivot->tanggal_bergabung)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($santri->is_aktif)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-circle text-[6px] mr-1.5"></i> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            Tidak Aktif
                                        </span>
                                    @endif
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
