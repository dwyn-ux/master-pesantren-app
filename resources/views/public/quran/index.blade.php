<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Al-Qur'an — Ash-Shiddiq Apps</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-arabic { font-family: 'Amiri', serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800 antialiased">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 font-extrabold text-xl text-gray-900">
                <div class="w-9 h-9 bg-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-book-quran text-white text-lg"></i>
                </div>
                Ash-Shiddiq
            </a>
            <div class="flex items-center gap-6 text-sm font-bold">
                <a href="/" class="text-gray-500 hover:text-gray-800 transition-colors hidden sm:block">Beranda</a>
                <a href="{{ route('public.prayer.index') }}" class="text-gray-500 hover:text-gray-800 transition-colors hidden sm:block">Jadwal Sholat</a>
                <a href="{{ route('public.quran.index') }}" class="text-white bg-emerald-600 px-4 py-2 rounded-full">
                    <i class="fa-solid fa-book-open mr-1"></i> Al-Qur'an
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 py-10">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl sm:text-5xl font-black text-gray-900 mb-3">Al-Qur'an Digital</h1>
            <p class="text-gray-500 text-lg">114 Surat — terjemahan Indonesia & audio tajwid</p>
        </div>

        @if(count($surahList) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($surahList as $surah)
                    <a href="{{ route('public.quran.surah', $surah['nomor']) }}"
                       class="group bg-white border border-gray-100 hover:border-emerald-200 rounded-2xl p-5 flex items-center justify-between gap-4 transition-all hover:shadow-lg hover:-translate-y-1 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity pointer-events-none">
                            <i class="fa-solid fa-book-quran text-7xl"></i>
                        </div>
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <div class="w-11 h-11 shrink-0 rounded-xl bg-gray-50 group-hover:bg-emerald-100 text-gray-600 group-hover:text-emerald-700 font-black flex items-center justify-center transition-colors border border-gray-100 group-hover:border-emerald-200 text-sm">
                                {{ $surah['nomor'] }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-extrabold text-gray-900 group-hover:text-emerald-700 transition-colors truncate">{{ $surah['namaLatin'] }}</div>
                                <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-0.5">{{ $surah['arti'] ?? '-' }} &bull; {{ $surah['jumlahAyat'] }} Ayat</div>
                            </div>
                        </div>
                        <div class="font-arabic text-2xl text-gray-500 group-hover:text-emerald-600 shrink-0 transition-colors">{{ $surah['nama'] }}</div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-20 bg-white rounded-3xl border border-gray-100">
                <i class="fa-solid fa-cloud-arrow-down text-5xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-500 font-bold">Data Al-Qur'an sedang dimuat...</p>
            </div>
        @endif
    </div>

    <footer class="text-center py-8 text-gray-400 text-sm border-t border-gray-100 mt-10">
        &copy; {{ date('Y') }} Ash-Shiddiq Apps &mdash; Data dari <span class="font-medium">equran.id</span>
    </footer>
</body>
</html>
