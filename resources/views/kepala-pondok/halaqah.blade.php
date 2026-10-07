@extends('layouts.app')

@section('title', 'Monitoring Halaqah')
@section('page-title', 'Monitoring Halaqah')

@section('sidebar')
    @include('partials.sidebar-kepala')
@endsection

@section('content')
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-end sm:justify-between">
        <div><h2 class="text-xl font-extrabold text-gray-800">Monitoring Halaqah</h2><p class="text-sm text-gray-500">Pantau setoran tahfidz tanpa mengubah data.</p></div>
        <form method="GET"><label class="sr-only" for="status">Status setoran</label><select id="status" name="status" onchange="this.form.submit()" class="px-4 py-2.5 text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500"><option value="">Semua status</option>@foreach(['maqbul', 'perbaikan', 'ulang', 'perlu_latihan', 'banyak_salah', 'dhaif', 'kurang'] as $option)<option value="{{ $option }}" @selected($status === $option)>{{ str_replace('_', ' ', ucfirst($option)) }}</option>@endforeach</select></form>
    </div>

    <div class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-3">
        <div class="glass-panel rounded-2xl p-5"><p class="text-sm text-gray-500">Setoran hari ini</p><p class="mt-2 text-3xl font-extrabold text-gray-800">{{ number_format($summary['hari_ini']) }}</p></div>
        <div class="glass-panel rounded-2xl p-5"><p class="text-sm text-gray-500">Maqbul bulan ini</p><p class="mt-2 text-3xl font-extrabold text-emerald-600">{{ number_format($summary['maqbul_bulan_ini']) }}</p></div>
        <div class="glass-panel rounded-2xl p-5"><p class="text-sm text-gray-500">Perlu perhatian</p><p class="mt-2 text-3xl font-extrabold text-amber-600">{{ number_format($summary['perlu_latihan']) }}</p></div>
    </div>

    <div class="glass-panel overflow-hidden rounded-2xl"><div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="px-5 py-4 text-left">Tanggal</th><th class="px-5 py-4 text-left">Santri</th><th class="px-5 py-4 text-left">Ustadz</th><th class="px-5 py-4 text-left">Setoran</th><th class="px-5 py-4 text-center">Halaman</th><th class="px-5 py-4 text-center">Status</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($setoran as $item)<tr class="hover:bg-gray-50/80"><td class="px-5 py-4 text-gray-600 whitespace-nowrap">{{ $item->tanggal?->translatedFormat('d M Y') }}</td><td class="px-5 py-4 font-bold text-gray-800">{{ $item->santri?->nama ?? '-' }}</td><td class="px-5 py-4 text-gray-600">{{ $item->penerima?->nama ?? '-' }}</td><td class="px-5 py-4 text-gray-600">{{ ucfirst($item->jenis) }} · {{ $item->surahAwal?->nama_latin ?? $item->surahAwal?->nama ?? '-' }}@if($item->surahAkhir && $item->surahAkhir->id !== $item->surahAwal?->id) – {{ $item->surahAkhir->nama_latin ?? $item->surahAkhir->nama }}@endif</td><td class="px-5 py-4 text-center text-gray-600">{{ $item->jumlah_halaman ?: '-' }}</td><td class="px-5 py-4 text-center"><span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full {{ $item->status === 'maqbul' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ str_replace('_', ' ', ucfirst($item->status)) }}</span></td></tr>@empty<tr><td colspan="6" class="px-5 py-12 text-center text-gray-500">Belum ada data setoran.</td></tr>@endforelse</tbody></table></div>@if($setoran->hasPages())<div class="px-5 py-4 border-t border-gray-100">{{ $setoran->links() }}</div>@endif</div>
@endsection
