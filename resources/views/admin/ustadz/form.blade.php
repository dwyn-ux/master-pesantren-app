@extends('layouts.app')
@section('title', $ustadz ? 'Edit Ustadz' : 'Tambah Ustadz')
@section('page-title', $ustadz ? 'Edit Ustadz' : 'Tambah Ustadz')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-2xl">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
        <h6 class="text-lg font-bold text-gray-800">{{ $ustadz ? 'Edit Data Ustadz' : 'Tambah Ustadz Baru' }}</h6>
        @if(!$ustadz)
        <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full border border-amber-200">
            Username & Password otomatis
        </span>
        @endif
    </div>
    <div class="p-6">
        <form method="POST" action="{{ $ustadz ? route('admin.ustadz.update', $ustadz) : route('admin.ustadz.store') }}">
            @csrf
            @if($ustadz) @method('PUT') @endif

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $ustadz?->nik) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('nik') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="Nomor Induk Kependudukan (16 digit)">
                    @error('nik')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap Ustadz <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $ustadz?->nama) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('nama') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="Nama lengkap ustadz">
                    @error('nama')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">No HP / WhatsApp</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $ustadz?->no_hp) }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"
                               placeholder="08xxxxxxxxxx">
                        <p class="text-xs text-gray-500 mt-1">Opsional, digunakan untuk komunikasi jika diperlukan.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $ustadz?->tanggal_lahir?->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                        @error('tanggal_lahir')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('alamat') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="Alamat lengkap ustadz/ustadzah">{{ old('alamat', $ustadz?->alamat) }}</textarea>
                    @error('alamat')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                

            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.ustadz.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors text-center">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> {{ $ustadz ? 'Simpan Perubahan' : 'Simpan & Buat Akun' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
