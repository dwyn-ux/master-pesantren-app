@extends('layouts.app')

@section('title', 'Tafsir Ayat ' . ($tafsir['ayat'] ?? '') . ' - ' . ($tafsir['surah']['namaLatin'] ?? 'Quran'))

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Navigation -->
    <div class="mb-6 px-1">
        <a href="{{ route('quran.surah', $tafsir['surah']['nomor'] ?? 1) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Surah {{ $tafsir['surah']['namaLatin'] ?? '' }}
        </a>
    </div>

    <!-- Header -->
    <div class="glass-panel rounded-3xl overflow-hidden shadow-sm mb-6">
        <div class="p-6 bg-gradient-to-br from-teal-50 to-emerald-50 border-b border-emerald-100">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-book-open mr-1"></i> Tafsir Al-Qur'an
                    </p>
                    <h4 class="text-2xl font-extrabold text-gray-800">Ayat {{ $tafsir['ayat'] ?? '-' }}</h4>
                    <p class="text-gray-500 text-sm mt-1">
                        Surah {{ $tafsir['surah']['namaLatin'] ?? '' }}
                        <span class="font-arabic ml-2 text-emerald-700" style="font-family: 'Amiri', serif;">{{ $tafsir['surah']['nama'] ?? '' }}</span>
                    </p>
                </div>
                <div class="text-xl font-extrabold text-emerald-700 bg-emerald-100/80 px-4 py-2 rounded-xl border border-emerald-200 shrink-0">
                    {{ $tafsir['surah']['nomor'] ?? '' }}:{{ $tafsir['ayat'] ?? '' }}
                </div>
            </div>
        </div>

        <!-- Tafsir Content -->
        <div class="p-8">
            <h5 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-4 h-0.5 bg-emerald-300 inline-block"></span> Penjelasan Tafsir
            </h5>
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6">
                <p class="text-gray-700 leading-loose text-lg">
                    {!! nl2br(e($tafsir['text'] ?? $tafsir['tafsir'] ?? $tafsir['isi'] ?? 'Tafsir tidak tersedia untuk ayat ini.')) !!}
                </p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/50 flex flex-wrap gap-3">
            <a href="{{ route('quran.surah', $tafsir['surah']['nomor'] ?? 1) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl font-bold transition-all border border-indigo-100">
                <i class="fa-solid fa-book-open"></i> Baca Surat Lengkap
            </a>
            <a href="{{ route('quran.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl font-bold transition-all">
                <i class="fa-solid fa-list"></i> Daftar Surat
            </a>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
.font-arabic { font-family: 'Amiri', serif; }
</style>
@endsection
