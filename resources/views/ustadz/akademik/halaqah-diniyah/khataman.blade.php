@extends('layouts.app')

@section('title', 'Khataman Kitab')
@section('page-title', 'Khataman Kitab - ' . $halaqah->nama)

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Catat Khataman Kitab</h2>
            <a href="{{ route('ustadz.akademik.halaqah-diniyah.index') }}" class="text-sm text-gray-600 hover:underline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-6">
            <h3 class="font-semibold text-indigo-800 mb-2">Kitab: {{ $kitab->nama }}</h3>
            <p class="text-sm text-indigo-600">{{ $kitab->pengarang ?? '-' }} • {{ $kitab->total_halaman ?? '-' }} halaman</p>
        </div>

        <form action="{{ route('ustadz.akademik.halaqah-diniyah.store-khataman', $halaqah) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Santri</label>
                    <select name="santri_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih Santri</option>
                        @foreach($santri as $s)
                        <option value="{{ $s->santri_id }}" {{ old('santri_id') == $s->santri_id ? 'selected' : '' }}>
                            {{ $s->santri->nama }} ({{ $s->santri->wali->first()?->nama ?? '-' }})
                        </option>
                        @endforeach
                    </select>
                    @error('santri_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Khatam</label>
                    <input type="date" name="tanggal_khatam" value="{{ old('tanggal_khatam', date('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @error('tanggal_khatam')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg">
                    <i class="fa-solid fa-certificate mr-2"></i>Catat Khataman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
