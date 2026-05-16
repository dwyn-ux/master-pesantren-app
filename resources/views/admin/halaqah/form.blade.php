@extends('layouts.app')
@section('title', $halaqah ? 'Edit Halaqah' : 'Buat Halaqah')
@section('page-title', $halaqah ? 'Edit Halaqah' : 'Buat Halaqah')

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
        <h6 class="text-lg font-bold text-gray-800">{{ $halaqah ? 'Edit Data Halaqah' : 'Buat Halaqah Baru' }}</h6>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ $halaqah ? route('admin.halaqah.update', $halaqah) : route('admin.halaqah.store') }}">
            @csrf
            @if($halaqah) @method('PUT') @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Halaqah <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $halaqah?->nama) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('nama') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                           placeholder="cth: Halaqah Mujahidin">
                    @error('nama')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ustadz Penanggung Jawab <span class="text-red-500">*</span></label>
                    <select name="ustadz_id" class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('ustadz_id') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all bg-white appearance-none">
                        <option value="">-- Pilih Ustadz --</option>
                        @foreach($ustadz as $u)
                        <option value="{{ $u->id }}" @selected(old('ustadz_id', $halaqah?->ustadz_id) == $u->id)>
                            {{ $u->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('ustadz_id')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                @if($halaqah && isset($santri))
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center justify-between">
                        <span>Anggota Santri (Checklist untuk memasukkan ke Halaqah ini)</span>
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs border border-indigo-100">{{ $santri->count() }} Tersedia</span>
                    </label>
                    
                    <div class="border border-gray-200 rounded-xl overflow-hidden bg-gray-50/50">
                        <div class="max-h-72 overflow-y-auto p-2">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @forelse($santri as $s)
                                <label class="flex items-center cursor-pointer p-3 bg-white rounded-lg border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-colors">
                                    <input type="checkbox" name="santri_ids[]" value="{{ $s->id }}"
                                           class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                           @checked($halaqah->santri->contains($s->id))>
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-800">{{ $s->nama }}</span>
                                        <span class="block text-xs text-gray-500 mt-0.5 font-mono">{{ $s->nis }}</span>
                                    </div>
                                </label>
                                @empty
                                <div class="col-span-1 sm:col-span-2 py-8 text-center text-gray-500">
                                    <p class="text-sm">Tidak ada santri yang dapat dimasukkan.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.halaqah.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors text-center">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> {{ $halaqah ? 'Simpan Perubahan' : 'Buat Halaqah Baru' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
