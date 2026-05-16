@extends('layouts.app')
@section('title', $wali ? 'Edit Wali' : 'Tambah Wali')
@section('page-title', $wali ? 'Edit Wali' : 'Tambah Wali')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-4xl">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
        <h6 class="text-lg font-bold text-gray-800">{{ $wali ? 'Edit Data Wali' : 'Tambah Wali Baru' }}</h6>
        @if(!$wali)
        <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full border border-amber-200">
            Username & Password otomatis
        </span>
        @endif
    </div>
    <div class="p-6">
        <form method="POST" action="{{ $wali ? route('admin.wali.update', $wali) : route('admin.wali.store') }}">
            @csrf
            @if($wali) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-7">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap Wali <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $wali?->nama) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('nama') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="Nama lengkap wali">
                    @error('nama')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                
                <div class="md:col-span-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">No HP / WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $wali?->no_hp) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('no_hp') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="08xxxxxxxxxx">
                    @error('no_hp')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                </div>

                @if(!$wali)
                <div class="md:col-span-12 mt-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Kaitkan ke Santri (Anak)</label>
                    <div class="border border-gray-200 rounded-xl overflow-hidden bg-gray-50/50">
                        <div class="max-h-64 overflow-y-auto p-2">
                            @forelse($santri as $s)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-white rounded-lg border border-gray-100 mb-2 hover:border-indigo-200 transition-colors gap-3">
                                <label class="flex items-center cursor-pointer flex-1">
                                    <input type="checkbox" name="santri_ids[]" value="{{ $s->id }}"
                                           class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                           @checked(in_array($s->id, old('santri_ids', [])))>
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-800">{{ $s->nama }}</span>
                                        <span class="block text-xs text-gray-500 mt-0.5">NIS: {{ $s->nis }}</span>
                                    </div>
                                </label>
                                <select name="hubungan[{{ $s->id }}]" class="w-full sm:w-32 px-3 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm bg-gray-50">
                                    <option value="ayah">Ayah</option>
                                    <option value="ibu">Ibu</option>
                                    <option value="wali">Wali Lain</option>
                                </select>
                            </div>
                            @empty
                            <div class="py-8 text-center text-gray-500">
                                <p class="text-sm">Belum ada data santri aktif untuk dikaitkan.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.wali.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors text-center">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> {{ $wali ? 'Simpan Perubahan' : 'Simpan & Buat Akun Wali' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
