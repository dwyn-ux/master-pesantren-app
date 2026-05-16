@extends('layouts.app')
@section('title', 'Halaqah')
@section('page-title', 'Data Halaqah')

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
        <h6 class="text-lg font-bold text-gray-800">Daftar Halaqah</h6>
        <a href="{{ route('admin.halaqah.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Buat Halaqah
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Nama Halaqah</th>
                    <th class="px-6 py-4 font-medium">Ustadz Penanggung Jawab</th>
                    <th class="px-6 py-4 font-medium">Jumlah Santri</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($halaqah as $h)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="px-6 py-4 font-bold text-indigo-900">{{ $h->nama }}</td>
                    <td class="px-6 py-4 text-gray-700">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                {{ substr($h->ustadz->nama, 0, 1) }}
                            </div>
                            <span>{{ $h->ustadz->nama }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                            <i class="fa-solid fa-users mr-1.5 opacity-70"></i> {{ $h->santri_count }} santri
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.halaqah.edit', $h) }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center gap-1.5 transition-colors text-sm font-medium">
                                <i class="fa-solid fa-pen-to-square"></i> Kelola
                            </a>
                            <form method="POST" action="{{ route('admin.halaqah.destroy', $h) }}" class="inline-block" onsubmit="return confirm('Hapus halaqah {{ $h->nama }} beserta seluruh anggotanya?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-users-rays text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada data halaqah dibuat.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($halaqah->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $halaqah->links() }}
    </div>
    @endif
</div>
@endsection
