@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="glass-panel rounded-2xl p-6 shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1">
        <div class="w-14 h-14 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 text-2xl shadow-inner">
            <i class="fa-solid fa-user-graduate"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Santri Aktif</p>
            <h4 class="text-2xl font-bold text-gray-800">{{ number_format($stats['santri_aktif']) }}</h4>
        </div>
    </div>
    
    <div class="glass-panel rounded-2xl p-6 shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1">
        <div class="w-14 h-14 rounded-xl bg-teal-100 flex items-center justify-center text-teal-600 text-2xl shadow-inner">
            <i class="fa-solid fa-user-group"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Wali</p>
            <h4 class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_wali']) }}</h4>
        </div>
    </div>
    
    <div class="glass-panel rounded-2xl p-6 shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1">
        <div class="w-14 h-14 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600 text-2xl shadow-inner">
            <i class="fa-solid fa-chalkboard-user"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Ustadz</p>
            <h4 class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_ustadz']) }}</h4>
        </div>
    </div>
    
    <div class="glass-panel rounded-2xl p-6 shadow-sm flex items-center gap-4 transition-transform hover:-translate-y-1">
        <div class="w-14 h-14 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 text-2xl shadow-inner">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Tagihan Belum Lunas</p>
            <h4 class="text-2xl font-bold text-gray-800">{{ number_format($stats['tagihan_belum_bayar']) }}</h4>
        </div>
    </div>
</div>

<div class="glass-panel rounded-3xl p-10 text-center shadow-sm max-w-4xl mx-auto">
    <div class="w-24 h-24 bg-green-100 text-green-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-6 shadow-inner">
        <i class="fa-solid fa-circle-check"></i>
    </div>
    <h3 class="text-2xl font-bold text-gray-800 mb-3">Sistem Berjalan Normal</h3>
    <p class="text-gray-500 max-w-lg mx-auto leading-relaxed">
        Selamat datang di Dashboard Administrator. Gunakan menu navigasi di sebelah kiri untuk mengelola master data santri, wali, tagihan keuangan, serta pengaturan aplikasi.
    </p>
</div>

@if($akademikStats)
<div class="mt-8 max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800">Akademik Diniyah</h3>
        @if($akademikStats['tahun_ajaran'])
            <span class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full">
                <i class="fa-solid fa-calendar-days mr-1"></i>
                {{ $akademikStats['tahun_ajaran']->nama }} - {{ ucfirst($akademikStats['tahun_ajaran']->semester) }}
            </span>
        @else
            <span class="text-sm bg-yellow-50 text-yellow-700 px-3 py-1 rounded-full">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>Belum ada tahun ajaran aktif
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-panel rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                <i class="fa-solid fa-school text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Kelas</p>
                <h4 class="text-xl font-bold text-gray-800">{{ $akademikStats['total_kelas'] }}</h4>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <i class="fa-solid fa-calendar-week text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Jadwal Pelajaran</p>
                <h4 class="text-xl font-bold text-gray-800">{{ $akademikStats['total_jadwal'] }}</h4>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <i class="fa-solid fa-user-graduate text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Santri Terdaftar di Kelas</p>
                <h4 class="text-xl font-bold text-gray-800">{{ $akademikStats['santri_terdaftar'] }}</h4>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
