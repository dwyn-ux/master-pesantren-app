@extends('layouts.app')

@section('title', 'Dashboard Kepala Pondok')
@section('page-title', 'Dashboard Kepala Pondok')

@section('sidebar')
    @include('partials.sidebar-kepala')
@endsection

@section('content')
    <div class="flex flex-col gap-2 mb-7 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-gray-800">Ringkasan Pondok</h2>
            <p class="text-sm text-gray-500">Pantau kondisi santri, halaqah, dan administrasi dari satu tempat.</p>
        </div>
        <a href="{{ route('kepala-pondok.laporan') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-indigo-700 bg-indigo-50 rounded-xl hover:bg-indigo-100">
            <i class="fa-solid fa-chart-column"></i> Lihat laporan lengkap
        </a>
    </div>

    <div class="grid grid-cols-1 gap-5 mb-7 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('kepala-pondok.santri') }}" class="glass-panel group rounded-2xl p-5 border-l-4 border-indigo-500 hover:-translate-y-0.5 transition-transform">
            <div class="flex items-center justify-between mb-4"><span class="text-sm font-semibold text-gray-500">Santri aktif</span><i class="fa-solid fa-user-graduate text-indigo-500"></i></div>
            <p class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['santri_aktif']) }}</p>
            <p class="mt-2 text-xs font-semibold text-indigo-600">Buka data santri <i class="fa-solid fa-arrow-right ml-1"></i></p>
        </a>
        <a href="{{ route('kepala-pondok.laporan') }}" class="glass-panel group rounded-2xl p-5 border-l-4 border-amber-500 hover:-translate-y-0.5 transition-transform">
            <div class="flex items-center justify-between mb-4"><span class="text-sm font-semibold text-gray-500">Tagihan beredar</span><i class="fa-solid fa-file-invoice-dollar text-amber-500"></i></div>
            <p class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['tagihan_belum_bayar']) }}</p>
            <p class="mt-2 text-xs text-gray-500">Belum lunas atau sebagian dibayar</p>
        </a>
        @feature('halaqah')
            <a href="{{ route('kepala-pondok.halaqah') }}" class="glass-panel group rounded-2xl p-5 border-l-4 border-emerald-500 hover:-translate-y-0.5 transition-transform">
                <div class="flex items-center justify-between mb-4"><span class="text-sm font-semibold text-gray-500">Setoran hari ini</span><i class="fa-solid fa-book-quran text-emerald-500"></i></div>
                <p class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['setoran_hari_ini']) }}</p>
                <p class="mt-2 text-xs font-semibold text-emerald-600">Buka monitoring halaqah <i class="fa-solid fa-arrow-right ml-1"></i></p>
            </a>
        @endfeature
        <a href="{{ route('kepala-pondok.keuangan') }}" class="glass-panel group rounded-2xl p-5 border-l-4 border-sky-500 hover:-translate-y-0.5 transition-transform">
            <div class="flex items-center justify-between mb-4"><span class="text-sm font-semibold text-gray-500">Pembayaran bulan ini</span><i class="fa-solid fa-wallet text-sky-500"></i></div>
            <p class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($stats['pembayaran_bulan_ini']) }}</p>
            <p class="mt-2 text-xs font-semibold text-sky-600">Buka ringkasan keuangan <i class="fa-solid fa-arrow-right ml-1"></i></p>
        </a>
    </div>

    @if($stats['approval_menunggu'] > 0)
        <div class="flex gap-3 p-4 mb-7 border border-amber-200 bg-amber-50 rounded-2xl text-amber-800">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <p class="text-sm"><span class="font-bold">{{ $stats['approval_menunggu'] }} pengajuan biaya</span> sedang menunggu persetujuan Kepala Pondok.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="glass-panel overflow-hidden rounded-2xl">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <div><h3 class="font-extrabold text-gray-800">Setoran terbaru</h3><p class="text-xs text-gray-500 mt-0.5">Aktivitas halaqah terakhir</p></div>
                @feature('halaqah')
                    <a href="{{ route('kepala-pondok.halaqah') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">Lihat semua</a>
                @endfeature
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($setoranTerbaru as $setoran)
                    <div class="flex items-center gap-3 px-5 py-4">
                        <div class="flex items-center justify-center w-9 h-9 text-emerald-600 bg-emerald-50 rounded-xl"><i class="fa-solid fa-book-quran"></i></div>
                        <div class="min-w-0 flex-1"><p class="font-bold text-sm text-gray-800 truncate">{{ $setoran->santri?->nama ?? '-' }}</p><p class="text-xs text-gray-500">{{ ucfirst($setoran->jenis) }} · {{ $setoran->tanggal?->translatedFormat('d M Y') }}</p></div>
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $setoran->status === 'maqbul' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ str_replace('_', ' ', ucfirst($setoran->status)) }}</span>
                    </div>
                @empty
                    <p class="p-6 text-sm text-center text-gray-500">Belum ada setoran untuk ditampilkan.</p>
                @endforelse
            </div>
        </section>

        <section class="glass-panel overflow-hidden rounded-2xl">
            <div class="p-5 border-b border-gray-100"><h3 class="font-extrabold text-gray-800">Perizinan terbaru</h3><p class="text-xs text-gray-500 mt-0.5">Informasi izin santri terakhir</p></div>
            <div class="divide-y divide-gray-100">
                @forelse($perizinanTerbaru as $izin)
                    <div class="flex items-center gap-3 px-5 py-4">
                        <div class="flex items-center justify-center w-9 h-9 text-sky-600 bg-sky-50 rounded-xl"><i class="fa-solid fa-person-walking-arrow-right"></i></div>
                        <div class="min-w-0 flex-1"><p class="font-bold text-sm text-gray-800 truncate">{{ $izin->santri?->nama ?? '-' }}</p><p class="text-xs text-gray-500">{{ $izin->tanggal_mulai?->translatedFormat('d M Y') ?? $izin->created_at?->translatedFormat('d M Y') }}</p></div>
                        <span class="px-2.5 py-1 text-xs font-bold text-gray-600 bg-gray-100 rounded-full">{{ ucfirst($izin->status) }}</span>
                    </div>
                @empty
                    <p class="p-6 text-sm text-center text-gray-500">Belum ada perizinan untuk ditampilkan.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
