@extends('layouts.app')

@section('title', 'Laporan Monitoring')
@section('page-title', 'Laporan Monitoring')

@section('sidebar')
    @include('partials.sidebar-kepala')
@endsection

@section('content')
    <div class="mb-7">
        <h2 class="text-xl font-extrabold text-gray-800">Laporan Ringkas Pondok</h2>
        <p class="text-sm text-gray-500">Ringkasan data operasional saat ini. Gunakan menu di bawah untuk melihat rincian.</p>
    </div>

    <div class="grid grid-cols-1 gap-5 mb-7 md:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('kepala-pondok.santri') }}" class="glass-panel rounded-2xl p-5 hover:-translate-y-0.5 transition-transform"><i class="fa-solid fa-user-graduate text-2xl text-indigo-500"></i><p class="mt-4 text-sm text-gray-500">Santri aktif</p><p class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['santri_aktif']) }}</p></a>
        <a href="{{ route('kepala-pondok.keuangan') }}" class="glass-panel rounded-2xl p-5 hover:-translate-y-0.5 transition-transform"><i class="fa-solid fa-money-bill-wave text-2xl text-sky-500"></i><p class="mt-4 text-sm text-gray-500">Pembayaran bulan ini</p><p class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($stats['pembayaran_bulan_ini']) }}</p></a>
        <div class="glass-panel rounded-2xl p-5"><i class="fa-solid fa-file-invoice text-2xl text-amber-500"></i><p class="mt-4 text-sm text-gray-500">Tagihan belum lunas</p><p class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['tagihan_belum_bayar']) }}</p></div>
        @feature('halaqah')
            <a href="{{ route('kepala-pondok.halaqah') }}" class="glass-panel rounded-2xl p-5 hover:-translate-y-0.5 transition-transform"><i class="fa-solid fa-book-quran text-2xl text-emerald-500"></i><p class="mt-4 text-sm text-gray-500">Setoran hari ini</p><p class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['setoran_hari_ini']) }}</p></a>
        @endfeature
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <a href="{{ route('kepala-pondok.santri') }}" class="glass-panel group rounded-2xl p-6 hover:border-indigo-200 border border-transparent">
            <div class="flex items-center justify-between"><div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center"><i class="fa-solid fa-users"></i></div><i class="fa-solid fa-arrow-right text-gray-300 group-hover:text-indigo-600"></i></div>
            <h3 class="mt-5 font-extrabold text-gray-800">Data Santri</h3><p class="mt-1 text-sm leading-relaxed text-gray-500">Cari santri, lihat kelas/halaqah, status aktif, dan jumlah setoran.</p>
        </a>
        @feature('halaqah')
            <a href="{{ route('kepala-pondok.halaqah') }}" class="glass-panel group rounded-2xl p-6 hover:border-emerald-200 border border-transparent">
                <div class="flex items-center justify-between"><div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center"><i class="fa-solid fa-book-quran"></i></div><i class="fa-solid fa-arrow-right text-gray-300 group-hover:text-emerald-600"></i></div>
                <h3 class="mt-5 font-extrabold text-gray-800">Monitoring Halaqah</h3><p class="mt-1 text-sm leading-relaxed text-gray-500">Pantau setoran terbaru, capaian maqbul, dan setoran yang perlu latihan.</p>
            </a>
        @endfeature
        <a href="{{ route('kepala-pondok.keuangan') }}" class="glass-panel group rounded-2xl p-6 hover:border-sky-200 border border-transparent">
            <div class="flex items-center justify-between"><div class="w-11 h-11 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center"><i class="fa-solid fa-wallet"></i></div><i class="fa-solid fa-arrow-right text-gray-300 group-hover:text-sky-600"></i></div>
            <h3 class="mt-5 font-extrabold text-gray-800">Monitoring Keuangan</h3><p class="mt-1 text-sm leading-relaxed text-gray-500">Lihat pembayaran, tagihan beredar, serta antrean konfirmasi manual.</p>
        </a>
    </div>
@endsection
