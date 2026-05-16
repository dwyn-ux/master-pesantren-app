@extends('layouts.app')

@section('title', 'Superadmin Dashboard')
@section('page-title', 'Superadmin')

@section('sidebar')
    @include('partials.sidebar-superadmin')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
        <h1 class="text-2xl font-bold">Halo, {{ auth()->user()->name }}</h1>
        <p class="text-indigo-100 mt-1">Kelola fitur aplikasi pesantren dari sini.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Fitur</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_fitur'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i class="fa-solid fa-toggle-on text-indigo-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Fitur Aktif</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['fitur_aktif'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Fitur Nonaktif</p>
                    <p class="text-2xl font-bold text-gray-500 mt-1">{{ $stats['fitur_nonaktif'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center">
                    <i class="fa-solid fa-circle-minus text-gray-500 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total User</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_user'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fa-solid fa-users text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ route('superadmin.features.index') }}" class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition">
                <i class="fa-solid fa-sliders text-indigo-600 text-xl w-6"></i>
                <div>
                    <p class="font-semibold text-gray-800">Kelola Fitur</p>
                    <p class="text-xs text-gray-500">Aktifkan / nonaktifkan modul aplikasi</p>
                </div>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition">
                <i class="fa-solid fa-gauge text-indigo-600 text-xl w-6"></i>
                <div>
                    <p class="font-semibold text-gray-800">Buka Dashboard Admin</p>
                    <p class="text-xs text-gray-500">Pantau operasional pesantren</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
