@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard ' . $role)

@section('sidebar')
    @if(Auth::user()->hasRole('ustadz'))
        @include('partials.sidebar-ustadz')
    @elseif(Auth::user()->hasRole('kepala_pondok'))
        @include('partials.sidebar-kepala')
    @else
        <div class="px-4 py-2 mt-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Menu Utama</div>
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-2 rounded-xl transition-all bg-indigo-600/50 text-white shadow-sm">
            <i class="fa-solid fa-gauge-high w-5 text-center"></i>
            <span class="font-medium text-sm">Dashboard</span>
        </a>
    @endif
@endsection

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-l-4 border-indigo-500 flex items-center gap-4 group hover:-translate-y-1 transition-transform">
        <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-users text-2xl"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-0.5">Role Aktif</div>
            <div class="text-lg font-extrabold text-gray-800">{{ $role }}</div>
        </div>
    </div>
</div>

<div class="glass-panel rounded-2xl shadow-sm overflow-hidden min-h-[400px] flex items-center justify-center">
    <div class="text-center p-8 max-w-md mx-auto">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-400">
            <i class="fa-solid fa-screwdriver-wrench text-4xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">Dashboard {{ $role }}</h3>
        <p class="text-gray-500 leading-relaxed">
            Modul untuk peran ini masih dalam tahap pengembangan. Silakan kembali lagi nanti untuk melihat pembaruan fitur.
        </p>
    </div>
</div>
@endsection
