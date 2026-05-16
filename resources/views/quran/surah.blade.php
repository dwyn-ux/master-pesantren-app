@extends('layouts.app')

@section('title', $surah['namaLatin'] ?? 'Surah')
@section('page-title', $surah['namaLatin'] ?? 'Surah')

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
            <a href="{{ route('prayer.qibla') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-indigo-100 hover:bg-white/10 hover:text-white">
                <i class="fa-solid fa-compass w-5 text-center text-lg"></i>
                <span class="font-bold text-sm">Arah Kiblat</span>
            </a>
        </div>
        
        <div class="mt-6 pt-6 border-t border-indigo-400/30">
            <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-indigo-100 hover:bg-white/10 hover:text-white group">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                    <i class="fa-solid fa-house text-sm"></i>
                </div>
                <span class="font-bold text-sm">Dashboard Utama</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    
    <!-- Surah Header Card -->
    <div class="relative rounded-3xl overflow-hidden mb-8 shadow-lg shadow-emerald-900/5 bg-gradient-to-br from-emerald-50 via-white to-teal-50 border border-emerald-100">
        <div class="absolute top-0 right-0 p-12 opacity-5 pointer-events-none">
            <i class="fa-solid fa-book-quran text-9xl"></i>
        </div>
        
        <div class="p-8 sm:p-12 text-center relative z-10">
            <div class="mb-6 flex justify-center">
                <a href="{{ route('quran.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-sm border border-emerald-200 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300 rounded-full text-sm font-bold transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar
                </a>
            </div>
            
            <div class="font-arabic text-5xl sm:text-6xl text-emerald-800 mb-4 drop-shadow-sm leading-tight" style="font-family: 'Amiri', serif;">
                {{ $surah['nama'] ?? '' }}
            </div>
            
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight mb-2">{{ $surah['namaLatin'] ?? '' }}</h2>
            <p class="text-gray-500 font-medium mb-6 uppercase tracking-wider text-sm">{{ $surah['arti'] ?? '' }}</p>
            
            <div class="flex flex-wrap justify-center gap-3">
                <span class="px-4 py-1.5 bg-white border border-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase tracking-widest shadow-sm">
                    Surat ke-{{ $surah['nomor'] ?? '-' }}
                </span>
                <span class="px-4 py-1.5 bg-white border border-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase tracking-widest shadow-sm">
                    {{ $surah['jumlahAyat'] ?? count($surah['ayat'] ?? []) }} Ayat
                </span>
                <span class="px-4 py-1.5 bg-white border border-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase tracking-widest shadow-sm">
                    {{ ($surah['tempatTurun'] ?? '') === 'Mekah' ? 'Makkiyah' : 'Madaniyah' }}
                </span>
            </div>

            @if(($surah['nomor'] ?? null) != 9 && ($surah['nomor'] ?? null) != 1)
                <div class="mt-10 font-arabic text-4xl text-emerald-800/90 leading-loose" style="font-family: 'Amiri', serif;">
                    بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                </div>
            @endif
        </div>
    </div>

    <!-- Controls -->
    <div class="flex justify-between items-center mb-6 px-2" x-data="{ translationVisible: true }">
        <h6 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Mode Baca</h6>
        <button @click="translationVisible = !translationVisible; document.querySelectorAll('.translation-block').forEach(el => el.style.display = translationVisible ? 'block' : 'none')" 
                class="px-4 py-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-full text-sm font-bold transition-all shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-language text-emerald-500 text-lg"></i>
            <span x-text="translationVisible ? 'Sembunyikan Arti' : 'Tampilkan Arti'"></span>
        </button>
    </div>

    <!-- Ayats -->
    <div class="space-y-6">
        @foreach($surah['ayat'] ?? [] as $ayat)
            @php
                $nomorAyat = $ayat['nomorAyat'] ?? $ayat['nomor'] ?? $loop->iteration;
                $teksArab = $ayat['teksArab'] ?? $ayat['ar'] ?? '';
                $teksLatin = $ayat['teksLatin'] ?? $ayat['tr'] ?? '';
                $teksIndonesia = $ayat['teksIndonesia'] ?? $ayat['idn'] ?? '';
                $audio = $ayat['audio']['05'] ?? $ayat['audio']['04'] ?? $ayat['audio']['03'] ?? $ayat['audio']['02'] ?? $ayat['audio']['01'] ?? null;
            @endphp
            
            <article class="glass-panel rounded-3xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 sm:p-8" id="ayat-{{ $nomorAyat }}">
                <div class="flex flex-col sm:flex-row gap-6">
                    
                    <!-- Verse Number Badge -->
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg border border-emerald-100 shadow-sm mx-auto sm:mx-0">
                            {{ $nomorAyat }}
                        </div>
                    </div>
                    
                    <div class="flex-grow">
                        <!-- Arabic Text -->
                        <div class="text-right font-arabic text-3xl sm:text-4xl text-gray-800 leading-loose sm:leading-loose mb-8 pr-2" dir="rtl" style="font-family: 'Amiri', serif;">
                            {{ $teksArab }}
                        </div>
                        
                        <!-- Translation Block -->
                        <div class="translation-block pl-4 sm:pl-6 border-l-4 border-emerald-200 space-y-3">
                            @if($teksLatin)
                                <div class="text-emerald-800/80 text-sm sm:text-base italic font-medium leading-relaxed">
                                    {{ $teksLatin }}
                                </div>
                            @endif
                            <div class="text-gray-700 text-base sm:text-lg leading-relaxed">
                                {{ $teksIndonesia }}
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                            @if($audio)
                                <button onclick="playAudio('{{ $audio }}')" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                                    <i class="fa-solid fa-play text-emerald-500"></i> Putar Audio
                                </button>
                            @endif
                            
                            <a href="{{ route('quran.tafsir', [$surah['nomor'], $nomorAyat]) }}" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                                <i class="fa-solid fa-book-open text-indigo-500"></i> Tafsir
                            </a>
                            
                            <button onclick="copyAyat('ayat-{{ $nomorAyat }}')" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-colors flex items-center gap-2 ml-auto">
                                <i class="fa-regular fa-copy"></i> Salin
                            </button>
                        </div>
                    </div>
                    
                </div>
            </article>
        @endforeach
    </div>
</div>

<!-- Hidden Audio Player -->
<audio id="quranAudio" class="hidden" controls></audio>

<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
.font-arabic {
    font-family: 'Amiri', serif;
}
</style>
@endsection

@section('scripts')
<script>
function playAudio(url) {
    const audio = document.getElementById('quranAudio');
    audio.src = url;
    audio.play();
    showToast('Memutar Audio', 'Audio ayat sedang diputar.', 'success');
}

function copyAyat(id) {
    const el = document.getElementById(id);
    
    // Extract text safely without getting button texts
    let textToCopy = '';
    
    // Arabic text
    const arabicEl = el.querySelector('[dir="rtl"]');
    if (arabicEl) textToCopy += arabicEl.innerText.trim() + '\n\n';
    
    // Translation
    const transBlock = el.querySelector('.translation-block');
    if (transBlock) {
        textToCopy += transBlock.innerText.trim() + '\n';
    }
    
    navigator.clipboard.writeText(textToCopy).then(() => {
        showToast('Berhasil Disalin', 'Ayat dan terjemahan telah disalin ke clipboard.', 'success');
    }).catch(err => {
        showToast('Gagal Menyalin', 'Terjadi kesalahan saat menyalin ayat.', 'error');
    });
}

function showToast(title, message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    
    let iconClass, bgClass, iconColorClass;
    if (type === 'success') {
        iconClass = 'fa-circle-check';
        bgClass = 'bg-white border-green-200';
        iconColorClass = 'text-green-500';
    } else {
        iconClass = 'fa-circle-exclamation';
        bgClass = 'bg-white border-red-200';
        iconColorClass = 'text-red-500';
    }

    toast.className = `flex items-center p-4 border rounded-xl shadow-lg transform transition-all duration-300 translate-y-full opacity-0 ${bgClass}`;
    toast.innerHTML = `
        <div class="mr-3 ${iconColorClass} text-xl"><i class="fa-solid ${iconClass}"></i></div>
        <div>
            <div class="font-bold text-gray-800 text-sm">${title}</div>
            <div class="text-sm text-gray-600">${message}</div>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-y-full', 'opacity-0');
    }, 10);

    // Animate out and remove
    setTimeout(() => {
        toast.classList.add('translate-y-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endsection
