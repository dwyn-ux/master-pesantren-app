@extends('layouts.app')

@section('title', 'Ayat ' . $ayat['nomor'] . ' - ' . ($ayat['surah']['namaLatin'] ?? 'Quran'))

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Navigation -->
    <div class="flex flex-wrap gap-3 mb-6 px-1">
        <a href="{{ route('quran.surah', $ayat['surah']['nomor']) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke {{ $ayat['surah']['namaLatin'] ?? 'Surah' }}
        </a>
        <span class="text-gray-300">|</span>
        <a href="{{ route('quran.tafsir', [$ayat['surah']['nomor'], $ayat['nomor']]) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
            <i class="fa-solid fa-book-open text-xs"></i> Lihat Tafsir
        </a>
    </div>

    <!-- Header Card -->
    <div class="glass-panel rounded-3xl overflow-hidden shadow-sm mb-6">
        <div class="p-6 bg-gradient-to-br from-emerald-50 to-teal-50 border-b border-emerald-100 flex flex-col sm:flex-row justify-between items-start gap-4">
            <div>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1">{{ $ayat['surah']['namaLatin'] ?? '' }}</p>
                <h4 class="text-2xl font-extrabold text-gray-800">Ayat {{ $ayat['nomor'] }}</h4>
                <p class="text-gray-500 text-sm mt-1 font-arabic" style="font-family: 'Amiri', serif;">{{ $ayat['surah']['nama'] ?? '' }}</p>
            </div>
            <div class="text-2xl font-extrabold text-emerald-700 bg-emerald-100/80 px-4 py-2 rounded-xl border border-emerald-200">
                {{ $ayat['surah']['nomor'] ?? '' }}:{{ $ayat['nomor'] }}
            </div>
        </div>

        <!-- Arabic Text -->
        <div class="p-8 bg-white">
            <div class="text-right font-arabic leading-loose text-4xl text-gray-800 mb-8" dir="rtl" style="font-family: 'Amiri', serif;">
                {{ $ayat['ar'] }}
            </div>

            <div class="pl-5 border-l-4 border-emerald-200 space-y-4">
                @if(!empty($ayat['tr']))
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Transliterasi</span>
                        <p class="text-emerald-800 italic leading-relaxed">{{ $ayat['tr'] }}</p>
                    </div>
                @endif
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Terjemahan</span>
                    <p class="text-gray-700 leading-relaxed text-lg">{{ $ayat['idn'] }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/50 flex flex-wrap gap-3">
            <button onclick="playAudio({{ $ayat['surah']['nomor'] }}, {{ $ayat['nomor'] }})"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all shadow-sm">
                <i class="fa-solid fa-play"></i> Putar Audio
            </button>
            <button onclick="copyText()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl font-bold transition-all shadow-sm">
                <i class="fa-regular fa-copy"></i> Salin Ayat
            </button>
        </div>
    </div>
</div>

<!-- Hidden audio player -->
<audio id="quranAudio" class="hidden" controls></audio>

<!-- Hidden copyable text -->
<div id="copyAyatContent" class="hidden">{{ $ayat['ar'] }}

{{ $ayat['idn'] }}</div>

<!-- Toast container -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
.font-arabic { font-family: 'Amiri', serif; }
</style>
@endsection

@section('scripts')
<script>
function playAudio(surahNumber, ayatNumber) {
    const pad = num => String(num).padStart(3, '0');
    const audioUrl = `https://equran.id/content/audio/${pad(surahNumber)}${pad(ayatNumber)}.mp3`;
    const audio = document.getElementById('quranAudio');
    audio.src = audioUrl;
    audio.play();
    showToast('Memutar Audio', 'Audio ayat sedang diputar.');
}

function copyText() {
    const text = document.getElementById('copyAyatContent').textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        showToast('Berhasil Disalin', 'Ayat dan terjemahan telah disalin.');
    }).catch(() => {
        showToast('Gagal', 'Tidak bisa menyalin ayat.', 'error');
    });
}

function showToast(title, message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    const iconClass = type === 'success' ? 'fa-circle-check text-green-500' : 'fa-circle-exclamation text-red-500';
    toast.className = `flex items-center p-4 bg-white border border-gray-200 rounded-xl shadow-lg transform transition-all duration-300 translate-y-full opacity-0`;
    toast.innerHTML = `<i class="fa-solid ${iconClass} text-xl mr-3"></i><div><div class="font-bold text-gray-800 text-sm">${title}</div><div class="text-sm text-gray-600">${message}</div></div>`;
    container.appendChild(toast);
    setTimeout(() => toast.classList.remove('translate-y-full', 'opacity-0'), 10);
    setTimeout(() => { toast.classList.add('translate-y-full', 'opacity-0'); setTimeout(() => toast.remove(), 300); }, 3000);
}
</script>
@endsection
