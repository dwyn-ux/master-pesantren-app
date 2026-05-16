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
@endsection
