@extends('layouts.app')

@section('title', 'Cari Ayat - ' . $query)
@section('page-title', 'Hasil Pencarian')

@section('sidebar')
    <div class="px-4 py-3 mt-4">
        <p class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider mb-3 ml-2">Spiritual</p>
        <div class="space-y-1">
            <a href="{{ route('quran.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all bg-indigo-600 text-white shadow-md shadow-indigo-200">
                <i class="fa-solid fa-book-open-reader w-5 text-center text-lg"></i>
                <span class="font-bold text-sm">Al-Qur'an</span>
            </a>
            <a href="{{ route('prayer.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-indigo-100 hover:bg-white/10 hover:text-white">
                <i class="fa-solid fa-clock w-5 text-center text-lg"></i>
                <span class="font-bold text-sm">Jadwal Sholat</span>
            </a>
        </div>
        <div class="mt-6 pt-6 border-t border-indigo-400/30">
            <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-indigo-100 hover:bg-white/10 hover:text-white group">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors"><i class="fa-solid fa-house text-sm"></i></div>
                <span class="font-bold text-sm">Dashboard Utama</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back + Search Info -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 px-1">
        <a href="{{ route('quran.index') }}" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar Surat
        </a>
        <div class="bg-indigo-50 border border-indigo-100 px-4 py-2 rounded-full text-sm font-bold text-indigo-700">
            <i class="fa-solid fa-magnifying-glass mr-1"></i>
            Hasil untuk: <span class="text-indigo-900">"{{ $query }}"</span>
        </div>
    </div>

    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
            <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass text-indigo-500"></i>
                Hasil Pencarian
                <span class="text-sm font-normal text-gray-500 ml-2">({{ count($results) }} ditemukan)</span>
            </h6>
        </div>

        @if(count($results) > 0)
            <div class="divide-y divide-gray-50">
                @foreach($results as $result)
                    <a href="{{ route('quran.ayat', [$result['surah']['nomor'], $result['ayat']['nomor']]) }}"
                       class="flex items-start justify-between gap-4 px-6 py-5 hover:bg-indigo-50/40 transition-colors group">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <div class="w-12 h-12 flex-shrink-0 bg-indigo-50 group-hover:bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold transition-colors border border-indigo-100">
                                {{ $result['surah']['nomor'] }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="font-extrabold text-gray-800 group-hover:text-indigo-700 transition-colors mb-1">
                                    {{ $result['surah']['namaLatin'] }} &mdash; Ayat {{ $result['ayat']['nomor'] }}
                                </h5>
                                <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit($result['ayat']['idn'] ?? $result['ayat']['tr'] ?? '-', 160) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex-shrink-0 flex flex-col items-end gap-1">
                            <span class="font-arabic text-xl text-emerald-700 group-hover:text-emerald-600 transition-colors" style="font-family: 'Amiri', serif;">
                                {{ $result['surah']['nama'] ?? '' }}
                            </span>
                            <i class="fa-solid fa-arrow-right text-gray-300 group-hover:text-indigo-400 transition-colors text-sm"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <i class="fa-solid fa-magnifying-glass text-4xl text-gray-300"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-700 mb-2">Tidak Ada Hasil</h4>
                <p class="text-gray-500 max-w-sm mx-auto mb-6">
                    Tidak ditemukan ayat untuk kata kunci <strong>"{{ $query }}"</strong>. Coba gunakan kata lain atau periksa ejaan.
                </p>
                <a href="{{ route('quran.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl font-bold transition-colors">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Surat
                </a>
            </div>
        @endif
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
.font-arabic { font-family: 'Amiri', serif; }
</style>
@endsection
