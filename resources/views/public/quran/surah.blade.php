<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $surah['namaLatin'] }} — Ash-Shiddiq Apps</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-arabic { font-family: 'Amiri', serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800 antialiased">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 py-4 shadow-sm">
        <div class="max-w-3xl mx-auto px-6 flex items-center justify-between">
            <a href="{{ route('public.quran.index') }}" class="flex items-center gap-2 font-bold text-gray-700 hover:text-emerald-700 transition-colors text-sm">
                <i class="fa-solid fa-arrow-left"></i> Semua Surat
            </a>
            <div class="flex gap-2">
                @if($surah['nomor'] > 1)
                    <a href="{{ route('public.quran.surah', $surah['nomor'] - 1) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full text-sm font-bold transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-arrow-left text-xs"></i> Prev
                    </a>
                @endif
                @if($surah['nomor'] < 114)
                    <a href="{{ route('public.quran.surah', $surah['nomor'] + 1) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full text-sm font-bold transition-colors flex items-center gap-1">
                        Next <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-6 py-10">
        <!-- Surah Header -->
        <div class="bg-gradient-to-br from-emerald-900 to-teal-900 rounded-3xl p-8 text-center text-white mb-8 shadow-xl relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')]"></div>
            <div class="relative z-10">
                <div class="font-arabic text-5xl mb-3 text-emerald-100" style="font-family: 'Amiri', serif;">{{ $surah['nama'] }}</div>
                <h1 class="text-3xl font-black mb-1">{{ $surah['namaLatin'] }}</h1>
                <p class="text-emerald-200 text-lg font-medium">{{ $surah['arti'] }} &bull; {{ $surah['jumlahAyat'] }} Ayat</p>
            </div>
        </div>

        <!-- Ayat List -->
        <div class="space-y-4">
            @foreach($surah['ayat'] ?? [] as $ayat)
                @php
                    $nomorAyat = $ayat['nomorAyat'] ?? $ayat['nomor'] ?? $loop->iteration;
                    $teksArab = $ayat['teksArab'] ?? $ayat['ar'] ?? '';
                    $teksLatin = $ayat['teksLatin'] ?? $ayat['tr'] ?? '';
                    $teksIndonesia = $ayat['teksIndonesia'] ?? $ayat['idn'] ?? '';
                    $audio = $ayat['audio']['05'] ?? $ayat['audio']['04'] ?? null;
                @endphp
                <article class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                    <!-- Ayat Number + Actions -->
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl flex items-center justify-center font-black text-sm">
                            {{ $nomorAyat }}
                        </div>
                        @if($audio)
                            <button onclick="playAudio('{{ $audio }}')" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-full text-sm font-bold transition-colors border border-emerald-100">
                                <i class="fa-solid fa-play text-xs"></i> Audio
                            </button>
                        @endif
                    </div>
                    <!-- Arabic Text -->
                    <div class="font-arabic text-right text-3xl text-gray-800 leading-loose mb-5" dir="rtl" style="font-family: 'Amiri', serif;">
                        {{ $teksArab }}
                    </div>
                    <!-- Translation -->
                    <div class="border-l-4 border-emerald-200 pl-4 space-y-2">
                        @if($teksLatin)
                            <p class="text-sm text-emerald-700 italic leading-relaxed">{{ $teksLatin }}</p>
                        @endif
                        <p class="text-gray-700 leading-relaxed">{{ $teksIndonesia }}</p>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Bottom Navigation -->
        <div class="mt-8 flex gap-3 justify-center">
            @if($surah['nomor'] > 1)
                <a href="{{ route('public.quran.surah', $surah['nomor'] - 1) }}" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 hover:border-emerald-300 hover:text-emerald-700 rounded-full font-bold transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Surat Sebelumnya
                </a>
            @endif
            <a href="{{ route('public.quran.index') }}" class="px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-full font-bold transition-all shadow-sm">
                <i class="fa-solid fa-list mr-2"></i> Daftar Surat
            </a>
            @if($surah['nomor'] < 114)
                <a href="{{ route('public.quran.surah', $surah['nomor'] + 1) }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full font-bold transition-all shadow-sm">
                    Surat Selanjutnya <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            @endif
        </div>
    </div>

    <audio id="audioPlayer" class="hidden"></audio>

    <script>
    function playAudio(url) {
        const audio = document.getElementById('audioPlayer');
        audio.src = url;
        audio.play();
    }
    </script>
</body>
</html>
