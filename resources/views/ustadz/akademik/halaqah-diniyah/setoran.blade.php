@extends('layouts.app')

@section('title', 'Setoran Diniyah')
@section('page-title', 'Setoran Diniyah - ' . $halaqah->nama)

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Input Setoran Diniyah</h2>
            <a href="{{ route('ustadz.akademik.halaqah-diniyah.index') }}" class="text-sm text-gray-600 hover:underline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Santri</label>
                <select name="santri_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Santri</option>
                    @foreach($santri as $s)
                    <option value="{{ $s->santri_id }}" {{ request('santri_id') == $s->santri_id ? 'selected' : '' }}>
                        {{ $s->santri->nama }} ({{ $s->santri->wali->first()?->nama ?? '-' }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}" onchange="this.form.submit()"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
        </form>

        <form action="{{ route('ustadz.akademik.halaqah-diniyah.store-setoran', $halaqah) }}" method="POST">
            @csrf
            <input type="hidden" name="santri_id" value="{{ request('santri_id') }}">
            <input type="hidden" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}">

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Halaman Mulai</label>
                        <input type="text" name="halaman_mulai" placeholder="Hal 45" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                               value="{{ old('halaman_mulai', $setoran?->halaman_mulai ?? '') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Halaman Selesai</label>
                        <input type="text" name="halaman_selesai" placeholder="Hal 50" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                               value="{{ old('halaman_selesai', $setoran?->halaman_selesai ?? '') }}">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Setoran</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="jenis" value="bandongan" {{ old('jenis', $setoran?->jenis ?? 'bandongan') == 'bandongan' ? 'checked' : '' }}
                                   class="text-indigo-600 focus:ring-indigo-500">
                            Bandongan (bersama-sama)
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="jenis" value="sorogan" {{ old('jenis', $setoran?->jenis ?? 'bandongan') == 'sorogan' ? 'checked' : '' }}
                                   class="text-indigo-600 focus:ring-indigo-500">
                            Sorogan (sendiri)
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai</label>
                    <select name="nilai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">-</option>
                        <option value="mumtaz" {{ old('nilai', $setoran?->nilai) == 'mumtaz' ? 'selected' : '' }}>Mumtaz (Sangat Baik Sekali)</option>
                        <option value="jayyid_jiddan" {{ old('nilai', $setoran?->nilai) == 'jayyid_jiddan' ? 'selected' : '' }}>Jayyid Jiddan (Sangat Baik)</option>
                        <option value="jayyid" {{ old('nilai', $setoran?->nilai) == 'jayyid' ? 'selected' : '' }}>Jayyid (Baik)</option>
                        <option value="maqbul" {{ old('nilai', $setoran?->nilai) == 'maqbul' ? 'selected' : '' }}>Maqbul (Cukup Baik)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('catatan', $setoran?->catatan ?? '') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                    <i class="fa-solid fa-save mr-2"></i>Simpan Setoran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
