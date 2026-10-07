@extends('layouts.app')

@section('title', 'Monitoring Keuangan')
@section('page-title', 'Monitoring Keuangan')

@section('sidebar')
    @include('partials.sidebar-kepala')
@endsection

@section('content')
    <div class="mb-7"><h2 class="text-xl font-extrabold text-gray-800">Monitoring Keuangan</h2><p class="text-sm text-gray-500">Ringkasan tagihan dan pembayaran. Halaman ini bersifat hanya-baca.</p></div>

    <div class="grid grid-cols-1 gap-5 mb-7 sm:grid-cols-2 xl:grid-cols-4">
        <div class="glass-panel rounded-2xl p-5"><p class="text-sm text-gray-500">Pembayaran bulan ini</p><p class="mt-2 text-2xl font-extrabold text-emerald-600">Rp {{ number_format($summary['pembayaran_bulan_ini']) }}</p></div>
        <div class="glass-panel rounded-2xl p-5"><p class="text-sm text-gray-500">Tagihan beredar</p><p class="mt-2 text-2xl font-extrabold text-amber-600">Rp {{ number_format($summary['tagihan_beredar']) }}</p></div>
        <div class="glass-panel rounded-2xl p-5"><p class="text-sm text-gray-500">Konfirmasi manual</p><p class="mt-2 text-3xl font-extrabold text-gray-800">{{ number_format($summary['manual_menunggu']) }}</p><p class="mt-1 text-xs text-gray-500">Menunggu bendahara/admin</p></div>
        <div class="glass-panel rounded-2xl p-5"><p class="text-sm text-gray-500">Pengajuan biaya</p><p class="mt-2 text-3xl font-extrabold text-gray-800">{{ number_format($summary['approval_menunggu']) }}</p><p class="mt-1 text-xs text-gray-500">Menunggu persetujuan kepala</p></div>
    </div>

    <section class="glass-panel overflow-hidden rounded-2xl mb-6"><div class="p-5 border-b border-gray-100"><h3 class="font-extrabold text-gray-800">Pembayaran terbaru</h3><p class="text-xs text-gray-500 mt-0.5">12 transaksi terakhir</p></div><div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="px-5 py-4 text-left">Waktu</th><th class="px-5 py-4 text-left">Santri / Wali</th><th class="px-5 py-4 text-left">Metode</th><th class="px-5 py-4 text-right">Nominal</th><th class="px-5 py-4 text-center">Status</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($pembayaran as $item)<tr class="hover:bg-gray-50/80"><td class="px-5 py-4 text-gray-600 whitespace-nowrap">{{ $item->paid_at?->translatedFormat('d M Y H:i') ?? $item->created_at?->translatedFormat('d M Y H:i') }}</td><td class="px-5 py-4"><p class="font-bold text-gray-800">{{ $item->tagihan?->santri?->nama ?? '-' }}</p><p class="text-xs text-gray-500">{{ $item->wali?->nama ?? 'Wali tidak tercatat' }}</p></td><td class="px-5 py-4 text-gray-600">{{ $item->metode === 'manual' ? 'Manual ' . ucfirst($item->manual_type ?? '') : strtoupper($item->metode ?? '-') }}</td><td class="px-5 py-4 text-right font-bold text-gray-800 whitespace-nowrap">Rp {{ number_format($item->nominal) }}</td><td class="px-5 py-4 text-center"><span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full {{ $item->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($item->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">{{ ucfirst($item->status) }}</span></td></tr>@empty<tr><td colspan="5" class="px-5 py-12 text-center text-gray-500">Belum ada transaksi pembayaran.</td></tr>@endforelse</tbody></table></div></section>

    @if($approval->isNotEmpty())
        <section class="glass-panel overflow-hidden rounded-2xl"><div class="p-5 border-b border-gray-100"><h3 class="font-extrabold text-gray-800">Pengajuan biaya menunggu persetujuan</h3><p class="text-xs text-gray-500 mt-0.5">Pantau pengajuan yang memerlukan keputusan Kepala Pondok.</p></div><div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="px-5 py-4 text-left">Nomor</th><th class="px-5 py-4 text-left">Pengajuan</th><th class="px-5 py-4 text-left">Kategori</th><th class="px-5 py-4 text-right">Nominal</th></tr></thead><tbody class="divide-y divide-gray-100">@foreach($approval as $item)<tr><td class="px-5 py-4 text-gray-600">{{ $item->nomor }}</td><td class="px-5 py-4"><p class="font-bold text-gray-800">{{ $item->judul }}</p><p class="text-xs text-gray-500">{{ $item->creator?->name ?? '-' }} · {{ $item->tanggal?->translatedFormat('d M Y') }}</p></td><td class="px-5 py-4 text-gray-600">{{ $item->kategori?->nama ?? '-' }}</td><td class="px-5 py-4 text-right font-bold text-gray-800">Rp {{ number_format($item->nominal) }}</td></tr>@endforeach</tbody></table></div></section>
    @endif
@endsection
