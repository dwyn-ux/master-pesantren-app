@extends('layouts.app')
@section('title', $santri ? 'Edit Santri' : 'Tambah Santri')
@section('page-title', $santri ? 'Edit Santri' : 'Tambah Santri')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-3xl">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
        <h6 class="text-lg font-bold text-gray-800">{{ $santri ? 'Edit Data Santri' : 'Tambah Santri Baru' }}</h6>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ $santri ? route('admin.santri.update', $santri) : route('admin.santri.store') }}">
            @csrf
            @if($santri) @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-6">

                {{-- NIS --}}
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NIS <span class="text-red-500">*</span></label>
                    <input type="text" name="nis" value="{{ old('nis', $santri?->nis) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('nis') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="cth: 2024001">
                    @error('nis')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- NIK --}}
                <div class="sm:col-span-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $santri?->nik) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('nik') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="Nomor Induk Kependudukan (16 digit)">
                    @error('nik')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Nama --}}
                <div class="sm:col-span-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $santri?->nama) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('nama') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="Nama lengkap santri">
                    @error('nama')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Kelas --}}
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                    <input type="text" name="kelas" value="{{ old('kelas', $santri?->kelas) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('kelas') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="cth: 7A">
                    @error('kelas')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Jenis Kelamin --}}
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kelamin</label>
                    <div class="flex gap-4 mt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="L" class="w-4 h-4 accent-indigo-600"
                                   @checked(old('jenis_kelamin', $santri?->jenis_kelamin) === 'L')>
                            <span class="text-sm text-gray-700 font-medium">Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="P" class="w-4 h-4 accent-indigo-600"
                                   @checked(old('jenis_kelamin', $santri?->jenis_kelamin) === 'P')>
                            <span class="text-sm text-gray-700 font-medium">Perempuan</span>
                        </label>
                    </div>
                    @error('jenis_kelamin')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Tanggal Lahir --}}
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir"
                           value="{{ old('tanggal_lahir', $santri?->tanggal_lahir?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('tanggal_lahir') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all">
                    @error('tanggal_lahir')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Alamat --}}
                <div class="sm:col-span-12">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                    <textarea name="alamat" rows="2"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('alamat') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="Alamat lengkap asal santri">{{ old('alamat', $santri?->alamat) }}</textarea>
                    @error('alamat')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- No HP Orang Tua --}}
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">No HP Orang Tua</label>
                    <input type="text" name="no_hp_ortu" value="{{ old('no_hp_ortu', $santri?->no_hp_ortu) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('no_hp_ortu') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="cth: 08123456789">
                    @error('no_hp_ortu')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                @if($santri)
                {{-- Status Aktif --}}
                <div class="sm:col-span-6 flex items-end">
                    <label class="flex items-center cursor-pointer p-3 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 transition-colors w-full">
                        <div class="relative">
                            <input type="hidden" name="is_aktif" value="0">
                            <input type="checkbox" name="is_aktif" value="1" id="is_aktif" class="sr-only" @checked(old('is_aktif', $santri->is_aktif))>
                            <div class="block bg-gray-300 w-10 h-6 rounded-full transition-colors" id="toggle-bg"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform" id="toggle-dot"></div>
                        </div>
                        <div class="ml-3 text-sm font-medium text-gray-700">Santri aktif</div>
                    </label>
                </div>
                @endif

            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.santri.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors text-center">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> {{ $santri ? 'Simpan Perubahan' : 'Tambah Santri' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@if($santri)
@push('styles')
<style>
    input:checked ~ #toggle-bg { background-color: #4f46e5; }
    input:checked ~ #toggle-dot { transform: translateX(100%); }
</style>
@endpush
@endif
