@extends('layouts.app')
@section('title', 'Jenis Tagihan')
@section('page-title', 'Jenis Tagihan')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <h6 class="text-lg font-bold text-gray-800">Daftar Jenis Tagihan</h6>
        <a href="{{ route('admin.jenis-tagihan.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Jenis
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Nama Tagihan</th>
                    <th class="px-6 py-4 font-medium">Kelompok</th>
                    <th class="px-6 py-4 font-medium">Nominal Default</th>
                    <th class="px-6 py-4 font-medium">Tipe Nominal</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($jenis as $j)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $j->nama }}</td>
                    <td class="px-6 py-4">
                        @php
                            $kelompokColor = match($j->kelompok ?? 'lainnya') {
                                'bulanan'    => 'bg-blue-100 text-blue-700 border-blue-200',
                                'semesteran' => 'bg-purple-100 text-purple-700 border-purple-200',
                                'tahunan'    => 'bg-amber-100 text-amber-700 border-amber-200',
                                'kegiatan'   => 'bg-green-100 text-green-700 border-green-200',
                                default      => 'bg-gray-100 text-gray-600 border-gray-200',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $kelompokColor }}">
                            {{ ucfirst($j->kelompok ?? 'lainnya') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium {{ $j->nominal > 0 ? 'text-gray-800' : 'text-gray-400 italic' }}">
                        {{ $j->nominal > 0 ? 'Rp '.number_format($j->nominal) : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($j->is_nominal_tetap)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">Tetap (Fix)</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">Bebas Isi</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($j->is_aktif)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">Non-aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.jenis-tagihan.edit', $j) }}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition-colors">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </a>
                            @if($j->is_aktif)
                            <form method="POST" action="{{ route('admin.jenis-tagihan.destroy', $j) }}" class="inline-block" onsubmit="return confirm('Nonaktifkan jenis tagihan {{ $j->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Nonaktifkan">
                                    <i class="fa-solid fa-ban text-sm"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-file-invoice-dollar text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada jenis tagihan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
