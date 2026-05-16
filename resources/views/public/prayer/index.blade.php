<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Sholat — Ash-Shiddiq Apps</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-indigo-950 min-h-screen text-gray-800 antialiased">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-indigo-900/90 backdrop-blur-md border-b border-white/10 py-4">
        <div class="max-w-6xl mx-auto px-6 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 text-white font-extrabold text-xl">
                <img src="/logo-nobg.png" alt="Ash-Shiddiq" class="w-9 h-9">
                Ash-Shiddiq
            </a>
            <div class="flex items-center gap-6">
                <a href="/" class="text-indigo-200 hover:text-white text-sm font-bold transition-colors hidden sm:block">
                    <i class="fa-solid fa-house mr-1"></i> Beranda
                </a>
                <a href="{{ route('public.prayer.index') }}" class="text-white text-sm font-bold bg-indigo-600 px-4 py-2 rounded-full">
                    <i class="fa-solid fa-mosque mr-1"></i> Jadwal Sholat
                </a>
            </div>
        </div>
    </nav>

    <div class="pt-24 pb-12 min-h-screen bg-gradient-to-b from-indigo-950 via-indigo-900 to-gray-900">
        <div class="max-w-4xl mx-auto px-6">
            
            <!-- Page Header -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-800/60 rounded-2xl border border-indigo-700 mb-4">
                    <i class="fa-solid fa-mosque text-3xl text-amber-300"></i>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-white mb-2">Jadwal Sholat</h1>
                <p class="text-indigo-200 text-lg">Cari waktu sholat untuk kota Anda di seluruh dunia</p>
            </div>

            <!-- Search Form -->
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-6 mb-8 shadow-xl">
                <form method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-indigo-300">
                            <i class="fa-solid fa-city"></i>
                        </div>
                        <input type="text" name="city" placeholder="Nama Kota (cth: Surabaya)" value="{{ $city }}"
                            class="w-full pl-11 pr-4 py-3 bg-white/20 border border-white/30 rounded-xl text-white placeholder-indigo-200 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition-all font-medium">
                    </div>
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-indigo-300">
                            <i class="fa-solid fa-globe"></i>
                        </div>
                        <input type="text" name="country" placeholder="Negara (cth: Indonesia)" value="{{ $country }}"
                            class="w-full pl-11 pr-4 py-3 bg-white/20 border border-white/30 rounded-xl text-white placeholder-indigo-200 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition-all font-medium">
                    </div>
                    <button type="submit" class="px-8 py-3 bg-amber-400 hover:bg-amber-300 text-indigo-950 rounded-xl font-extrabold transition-all shadow-lg shadow-amber-400/20 hover:scale-105 whitespace-nowrap flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Cari
                    </button>
                </form>
            </div>

            @if($prayerTimes)
                <!-- Date Info -->
                <div class="flex flex-col sm:flex-row justify-between items-center text-center sm:text-left gap-2 mb-8 px-2">
                    <div>
                        <p class="text-indigo-300 text-sm font-bold uppercase tracking-wider">Waktu Sholat untuk</p>
                        <h3 class="text-white text-2xl font-extrabold">{{ $city }}, {{ $country }}</h3>
                    </div>
                    <div class="bg-white/10 border border-white/20 px-5 py-3 rounded-xl text-right">
                        <p class="text-indigo-200 text-sm font-medium">{{ $prayerTimes['date']['readable'] }}</p>
                        <p class="text-white font-bold">Zona: {{ $prayerTimes['meta']['timezone'] }}</p>
                    </div>
                </div>

                <!-- Prayer Cards -->
                @php
                    $prayers = [
                        'Fajr'    => ['name' => 'Subuh',   'icon' => 'fa-moon',       'colors' => 'from-blue-800 to-indigo-800'],
                        'Dhuhr'   => ['name' => 'Dzuhur',  'icon' => 'fa-sun',        'colors' => 'from-amber-600 to-orange-600'],
                        'Asr'     => ['name' => 'Ashar',   'icon' => 'fa-cloud-sun',  'colors' => 'from-orange-700 to-red-700'],
                        'Maghrib' => ['name' => 'Maghrib', 'icon' => 'fa-cloud-moon', 'colors' => 'from-purple-700 to-indigo-800'],
                        'Isha'    => ['name' => 'Isya',    'icon' => 'fa-moon',       'colors' => 'from-gray-800 to-indigo-900'],
                    ];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
                    @foreach($prayers as $key => $prayer)
                        <div class="bg-gradient-to-br {{ $prayer['colors'] }} rounded-2xl p-6 text-center text-white border border-white/10 shadow-lg hover:-translate-y-2 transition-transform duration-300">
                            <div class="text-3xl mb-3 opacity-90"><i class="fa-solid {{ $prayer['icon'] }}"></i></div>
                            <div class="text-sm font-bold uppercase tracking-wider text-white/80 mb-2">{{ $prayer['name'] }}</div>
                            <div class="text-2xl font-black font-mono">{{ substr($prayerTimes['timings'][$key] ?? '--:--', 0, 5) }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- Info Box -->
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-5 flex flex-col sm:flex-row gap-5">
                    <div class="text-amber-300 text-2xl shrink-0 mt-0.5"><i class="fa-solid fa-circle-info"></i></div>
                    <div class="text-sm text-indigo-100 space-y-1">
                        <p><span class="font-bold text-white">Metode:</span> {{ $prayerTimes['meta']['method']['name'] ?? 'Kemenag Indonesia' }}</p>
                        <p><span class="font-bold text-white">Lokasi:</span> {{ $city }}, {{ $country }}</p>
                        <p><span class="font-bold text-white">Koordinat:</span> {{ $prayerTimes['meta']['latitude'] }}, {{ $prayerTimes['meta']['longitude'] }}</p>
                    </div>
                </div>
            @else
                <div class="bg-white/10 backdrop-blur-md border border-red-400/30 rounded-3xl p-10 text-center">
                    <i class="fa-solid fa-triangle-exclamation text-4xl text-amber-400 mb-4 block"></i>
                    <h4 class="text-xl font-bold text-white mb-2">Jadwal Tidak Ditemukan</h4>
                    <p class="text-indigo-200">Tidak dapat memuat jadwal sholat untuk <strong>{{ $city }}, {{ $country }}</strong>.</p>
                    <p class="text-indigo-300 text-sm mt-1">Periksa ejaan nama kota atau coba kota lain.</p>
                </div>
            @endif

            <div class="text-center mt-10">
                <a href="/" class="text-indigo-300 hover:text-white text-sm font-medium transition-colors">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>
