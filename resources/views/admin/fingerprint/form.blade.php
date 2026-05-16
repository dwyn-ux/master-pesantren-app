@extends('layouts.app')
@section('title', 'Edit Fingerprint - ' . $santri->nama)
@section('page-title', 'Edit Fingerprint')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-4 px-1">
        <a href="{{ route('admin.fingerprint.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar Fingerprint
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Info Santri -->
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-child text-lg"></i>
                </div>
                <div>
                    <h6 class="font-bold text-gray-800">{{ $santri->nama }}</h6>
                    <p class="text-xs text-gray-500 font-mono">{{ $santri->nis }}</p>
                </div>
            </div>

            <div class="p-6 space-y-5">
                <div class="flex justify-between items-center py-3 border-b border-gray-50">
                    <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">NIS</span>
                    <span class="font-bold text-gray-800 font-mono">{{ $santri->nis }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Saldo Uang Saku</span>
                    <span class="font-extrabold text-emerald-600">Rp {{ number_format($santri->saldo) }}</span>
                </div>
            </div>
        </div>

        <!-- Update Fingerprint Form -->
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-fingerprint text-lg"></i>
                </div>
                <div>
                    <h6 class="font-bold text-gray-800">Update Fingerprint ID</h6>
                    <p class="text-xs text-gray-500">Daftarkan atau perbarui ID biometrik santri</p>
                </div>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.fingerprint.update', $santri) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Fingerprint ID
                            @if($santri->fingerprint_id)
                                <span class="ml-2 text-xs font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                    <i class="fa-solid fa-check mr-1"></i>Sudah Terdaftar
                                </span>
                            @else
                                <span class="ml-2 text-xs font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                                    <i class="fa-solid fa-clock mr-1"></i>Belum Terdaftar
                                </span>
                            @endif
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-fingerprint text-lg"></i>
                            </div>
                            <input type="text"
                                   name="fingerprint_id"
                                   value="{{ old('fingerprint_id', $santri->fingerprint_id) }}"
                                   class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm font-mono tracking-widest text-gray-800 @error('fingerprint_id') border-red-500 @enderror"
                                   placeholder="Scan atau input ID dari hardware reader...">
                        </div>
                        @error('fingerprint_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 leading-relaxed">
                            Fingerprint ID adalah identitas unik dari hardware fingerprint reader. Bisa di-scan dari device atau diinput manual.
                        </p>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                        <h6 class="text-sm font-bold text-blue-700 flex items-center gap-2 mb-3">
                            <i class="fa-solid fa-circle-info"></i> Cara Mendapatkan Fingerprint ID
                        </h6>
                        <ol class="space-y-1.5 text-sm text-blue-700">
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-blue-200 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold mt-0.5">1</span>
                                Santri melakukan scan sidik jari di kios fingerprint
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-blue-200 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold mt-0.5">2</span>
                                Hardware reader mengirim ID unik ke sistem secara otomatis
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-blue-200 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold mt-0.5">3</span>
                                Atau, admin dapat menginput ID secara manual dari hardware reader
                            </li>
                        </ol>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.fingerprint.index') }}" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-colors text-center">
                            Batal
                        </a>
                        <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200 flex justify-center items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
