@extends('layouts.app')

@section('title', 'Data Santri')
@section('page-title', 'Data Santri')

@section('sidebar')
    @include('partials.sidebar-kepala')
@endsection

@section('content')
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-end sm:justify-between">
        <div><h2 class="text-xl font-extrabold text-gray-800">Data Santri</h2><p class="text-sm text-gray-500">Tampilan monitoring; data hanya dapat dilihat dari halaman ini.</p></div>
        <form method="GET" class="flex gap-2">
            <label class="sr-only" for="search">Cari santri</label>
            <input id="search" name="search" value="{{ $search }}" placeholder="Nama atau NIS..." class="w-full sm:w-64 px-4 py-2.5 text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
            <button class="px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700"><i class="fa-solid fa-magnifying-glass"></i><span class="hidden sm:inline ml-2">Cari</span></button>
        </form>
    </div>

    <div class="glass-panel overflow-hidden rounded-2xl">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="px-5 py-4 text-left">Santri</th><th class="px-5 py-4 text-left">NIS</th><th class="px-5 py-4 text-left">Kelas</th><th class="px-5 py-4 text-left">Halaqah</th><th class="px-5 py-4 text-center">Setoran</th><th class="px-5 py-4 text-center">Status</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($santri as $item)
                        <tr class="hover:bg-gray-50/80"><td class="px-5 py-4 font-bold text-gray-800">{{ $item->nama }}</td><td class="px-5 py-4 text-gray-600">{{ $item->nis ?? '-' }}</td><td class="px-5 py-4 text-gray-600">{{ $item->kelas ?? '-' }}</td><td class="px-5 py-4 text-gray-600">{{ $item->halaqah->pluck('nama')->filter()->join(', ') ?: '-' }}</td><td class="px-5 py-4 text-center font-semibold text-gray-700">{{ number_format($item->setoran_count) }}</td><td class="px-5 py-4 text-center"><span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full {{ $item->is_aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">{{ $item->is_aktif ? 'Aktif' : 'Tidak aktif' }}</span></td></tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-gray-500">Data santri tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($santri->hasPages())<div class="px-5 py-4 border-t border-gray-100">{{ $santri->links() }}</div>@endif
    </div>
@endsection
