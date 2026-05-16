@extends('layouts.app')
@section('title', $jenis ? 'Edit Jenis Tagihan' : 'Tambah Jenis Tagihan')
@section('page-title', $jenis ? 'Edit Jenis Tagihan' : 'Tambah Jenis Tagihan')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-2xl">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
        <h6 class="text-lg font-bold text-gray-800">{{ $jenis ? 'Edit Jenis Tagihan' : 'Tambah Jenis Tagihan Baru' }}</h6>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ $jenis ? route('admin.jenis-tagihan.update', $jenis) : route('admin.jenis-tagihan.store') }}"
              x-data="{ bebas: {{ old('is_nominal_tetap', $jenis?->is_nominal_tetap ?? 1) == 0 ? 'true' : 'false' }} }">
            @csrf
            @if($jenis) @method('PUT') @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Tagihan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $jenis?->nama) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('nama') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="cth: SPP, Infak, Daftar Ulang">
                    @error('nama')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kelompok <span class="text-red-500">*</span></label>
                    <select name="kelompok" class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('kelompok') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all bg-white appearance-none">
                        @foreach(['bulanan' => 'Bulanan', 'semesteran' => 'Semesteran', 'tahunan' => 'Tahunan', 'kegiatan' => 'Kegiatan', 'lainnya' => 'Lainnya'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('kelompok', $jenis?->kelompok ?? 'bulanan') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('kelompok')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Nominal <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center p-3 border rounded-xl cursor-pointer transition-colors" :class="!bebas ? 'border-indigo-500 bg-indigo-50/50' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="is_nominal_tetap" value="1" id="tetap" 
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                   @checked(old('is_nominal_tetap', $jenis?->is_nominal_tetap ?? 1) == 1)
                                   @change="bebas = false">
                            <span class="ml-3 block text-sm font-medium text-gray-800">Nominal Tetap <span class="block text-xs font-normal text-gray-500">Admin menentukan nominal</span></span>
                        </label>

                        <label class="flex items-center p-3 border rounded-xl cursor-pointer transition-colors" :class="bebas ? 'border-indigo-500 bg-indigo-50/50' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="is_nominal_tetap" value="0" id="bebas" 
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                   @checked(old('is_nominal_tetap', $jenis?->is_nominal_tetap) == 0)
                                   @change="bebas = true">
                            <span class="ml-3 block text-sm font-medium text-gray-800">Bebas Isi <span class="block text-xs font-normal text-gray-500">Wali tentukan saat bayar</span></span>
                        </label>
                    </div>
                </div>

                <div x-show="!bebas" x-transition>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal (Rp)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-medium">Rp</span>
                        </div>
                        <input type="number" name="nominal" value="{{ old('nominal', $jenis?->nominal ?? 0) }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border {{ $errors->has('nominal') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                               min="0" step="1000" placeholder="0">
                    </div>
                    @error('nominal')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                <input type="hidden" name="nominal" value="0" x-show="bebas" :disabled="!bebas">

                @if($jenis)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="hidden" name="is_aktif" value="0">
                            <input type="checkbox" name="is_aktif" value="1" class="sr-only" @checked(old('is_aktif', $jenis->is_aktif))>
                            <div class="block bg-gray-300 w-10 h-6 rounded-full transition-colors" id="toggle-bg"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform" id="toggle-dot"></div>
                        </div>
                        <div class="ml-3 text-sm font-medium text-gray-700">Status jenis tagihan aktif</div>
                    </label>
                </div>
                @endif
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.jenis-tagihan.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors text-center">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> {{ $jenis ? 'Simpan Perubahan' : 'Tambah Jenis' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@if($jenis)
@push('styles')
<style>
    /* Custom Toggle Switch */
    input:checked ~ #toggle-bg { background-color: #4f46e5; }
    input:checked ~ #toggle-dot { transform: translateX(100%); }
</style>
@endpush
@endif
