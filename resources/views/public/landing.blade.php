<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Pesantren Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-arabic { font-family: 'Amiri', serif; }
        .bg-pattern {
            background-color: #ffffff;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="bg-gray-50/50 text-gray-800 antialiased overflow-x-hidden">

    <!-- Navigation -->
    <nav id="main-nav" class="fixed w-full z-50" style="transition: background 0.3s ease, box-shadow 0.3s ease, padding 0.3s ease;">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div id="nav-inner" class="flex items-center justify-between" style="padding-top: 1.25rem; padding-bottom: 1.25rem; transition: padding 0.3s ease;">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 group">
                    <img src="/logo-nobg.png" alt="Ash-Shiddiq" class="w-9 h-9 shadow-md transform group-hover:scale-105 transition-transform duration-300">
                    <span id="nav-logo-text" class="font-extrabold text-xl tracking-tight" style="color: #fff; transition: color 0.3s ease;">
                        Ash-Shiddiq<span class="text-indigo-400">.</span>
                    </span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#quran" class="nav-link font-semibold text-sm" style="color: rgba(255,255,255,0.8); transition: color 0.2s;">Al-Qur'an</a>
                    <a href="#prayer" class="nav-link font-semibold text-sm" style="color: rgba(255,255,255,0.8); transition: color 0.2s;">Jadwal Sholat</a>
                    <a href="#features" class="nav-link font-semibold text-sm" style="color: rgba(255,255,255,0.8); transition: color 0.2s;">Fitur</a>
                </div>

                <!-- Desktop Actions -->
                <div class="hidden md:flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" id="nav-cta"
                           class="px-5 py-2 rounded-full font-bold text-sm transition-all text-white"
                           style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); transition: background 0.3s, box-shadow 0.3s;">
                            Masuk Portal
                        </a>
                    @endguest
                    @auth
                        <a href="{{ route('dashboard') }}" id="nav-cta"
                           class="px-5 py-2 rounded-full font-bold text-sm transition-all text-white"
                           style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); transition: background 0.3s, box-shadow 0.3s;">
                            Dashboard
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden w-9 h-9 flex items-center justify-center rounded-lg" style="color: #fff; transition: color 0.3s;">
                    <i id="mobile-menu-icon" class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden absolute top-full left-0 w-full py-4 px-6 flex flex-col gap-1"
             style="background: rgba(255,255,255,0.98); backdrop-filter: blur(16px); border-top: 1px solid rgba(0,0,0,0.07); box-shadow: 0 8px 32px rgba(0,0,0,0.12);">
            <a href="#quran" class="mobile-nav-link font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 px-4 py-3 rounded-xl transition-colors">
                <i class="fa-solid fa-book-open w-5 text-center mr-2 text-indigo-400"></i>Al-Qur'an
            </a>
            <a href="#prayer" class="mobile-nav-link font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 px-4 py-3 rounded-xl transition-colors">
                <i class="fa-solid fa-clock w-5 text-center mr-2 text-indigo-400"></i>Jadwal Sholat
            </a>
            <a href="#features" class="mobile-nav-link font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 px-4 py-3 rounded-xl transition-colors">
                <i class="fa-solid fa-layer-group w-5 text-center mr-2 text-indigo-400"></i>Fitur Sistem
            </a>
            <div class="pt-3 mt-1 border-t border-gray-100">
                @guest
                    <a href="{{ route('login') }}" class="block w-full text-center px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors">
                        Masuk Portal
                    </a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="block w-full text-center px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-colors">
                        Ke Dashboard
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <script>
    (function() {
        const nav      = document.getElementById('main-nav');
        const inner    = document.getElementById('nav-inner');
        const logoText = document.getElementById('nav-logo-text');
        const navLinks = document.querySelectorAll('.nav-link');
        const navCta   = document.getElementById('nav-cta');
        const mobileBtn  = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileIcon = document.getElementById('mobile-menu-icon');

        // Mobile menu toggle
        mobileBtn.addEventListener('click', function() {
            const isOpen = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            mobileIcon.className = isOpen ? 'fa-solid fa-bars text-lg' : 'fa-solid fa-xmark text-lg';
        });

        // Close mobile menu on link click
        document.querySelectorAll('.mobile-nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
                mobileIcon.className = 'fa-solid fa-bars text-lg';
            });
        });

        // Scroll handler
        function onScroll() {
            var scrolled = window.scrollY > 60;

            if (scrolled) {
                nav.style.background    = 'rgba(255,255,255,0.96)';
                nav.style.backdropFilter = 'blur(16px)';
                nav.style.webkitBackdropFilter = 'blur(16px)';
                nav.style.boxShadow     = '0 4px 32px rgba(0,0,0,0.10)';
                inner.style.paddingTop    = '0.75rem';
                inner.style.paddingBottom = '0.75rem';
                logoText.style.color    = '#111827';
                navLinks.forEach(function(l) { l.style.color = '#4b5563'; });
                if (navCta) {
                    navCta.style.background = '#4f46e5';
                    navCta.style.border     = 'none';
                    navCta.style.boxShadow  = '0 2px 12px rgba(79,70,229,0.25)';
                }
                if (mobileBtn) mobileBtn.style.color = '#374151';
            } else {
                nav.style.background    = 'transparent';
                nav.style.backdropFilter = 'none';
                nav.style.webkitBackdropFilter = 'none';
                nav.style.boxShadow     = 'none';
                inner.style.paddingTop    = '1.25rem';
                inner.style.paddingBottom = '1.25rem';
                logoText.style.color    = '#ffffff';
                navLinks.forEach(function(l) { l.style.color = 'rgba(255,255,255,0.8)'; });
                if (navCta) {
                    navCta.style.background = 'rgba(255,255,255,0.15)';
                    navCta.style.border     = '1px solid rgba(255,255,255,0.3)';
                    navCta.style.boxShadow  = 'none';
                }
                if (mobileBtn) mobileBtn.style.color = '#ffffff';
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll(); // run once on load
    })();
    </script>

    <!-- Hero Section -->
    <div class="relative min-h-[90vh] flex items-center justify-center pt-24 pb-16 overflow-hidden bg-indigo-900">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover opacity-20 mix-blend-overlay" alt="Mosque">
            <div class="absolute inset-0 bg-gradient-to-b from-indigo-900/80 via-indigo-900/90 to-indigo-950"></div>
        </div>
        
        <div class="absolute top-1/4 left-0 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-40 animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-30 animate-pulse" style="animation-delay: 2s;"></div>

        <div class="relative z-10 max-w-5xl mx-auto px-6 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-500/20 border border-indigo-400/30 backdrop-blur-md mb-8">
                <span class="flex h-2 w-2 rounded-full bg-emerald-400"></span>
                <span class="text-xs font-bold text-indigo-100 tracking-wider uppercase">Sistem Informasi Pesantren Digital</span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-black text-white tracking-tight mb-6 leading-tight">
                Integrasi Modern untuk <br class="hidden md:block"> <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400">Pendidikan Islami</span>
            </h1>
            
            <p class="text-lg md:text-xl text-indigo-100/90 mb-10 max-w-2xl mx-auto leading-relaxed">
                Platform terpadu untuk kemudahan administrasi, pemantauan santri, transaksi cashless, dan fasilitas spiritual digital.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#quran" class="w-full sm:w-auto px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full font-bold text-lg transition-all shadow-lg shadow-emerald-500/30 hover:-translate-y-1 flex justify-center items-center gap-2">
                    <i class="fa-solid fa-book-open"></i> Baca Al-Qur'an
                </a>
                <a href="#prayer" class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-full font-bold text-lg transition-all flex justify-center items-center gap-2">
                    <i class="fa-regular fa-clock"></i> Jadwal Sholat
                </a>
            </div>
        </div>
        
        <!-- Scroll indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex flex-col items-center animate-bounce text-indigo-300/60">
            <span class="text-xs font-bold uppercase tracking-widest mb-2">Eksplorasi</span>
            <i class="fa-solid fa-arrow-down"></i>
        </div>
    </div>

    <!-- Quran Section -->
    <section id="quran" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-indigo-600 font-bold tracking-wide uppercase text-sm mb-2">Spiritual Digital</h2>
                <h3 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Baca Al-Qur'an Kapan Saja</h3>
                <p class="text-gray-500 text-lg">Akses 114 surat dengan terjemahan Bahasa Indonesia dan pemutar audio tajwid.</p>
            </div>

            @if(count($surahList ?? []) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    @foreach(array_slice($surahList, 0, 6) as $surah)
                        <a href="{{ route('public.quran.surah', $surah['nomor']) }}" class="group relative bg-white border border-gray-100 hover:border-emerald-200 rounded-3xl p-6 transition-all duration-300 hover:shadow-xl hover:shadow-emerald-900/5 hover:-translate-y-1 flex items-center justify-between overflow-hidden">
                            <!-- Bg decoration -->
                            <div class="absolute -right-6 -bottom-6 text-gray-50 group-hover:text-emerald-50 transition-colors duration-500 transform group-hover:scale-110 pointer-events-none">
                                <i class="fa-solid fa-book-quran text-8xl"></i>
                            </div>
                            
                            <div class="flex items-center gap-5 relative z-10">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 group-hover:bg-emerald-100 text-gray-600 group-hover:text-emerald-600 font-bold flex items-center justify-center transition-colors">
                                    {{ $surah['nomor'] }}
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-gray-900 text-lg group-hover:text-emerald-700 transition-colors">{{ $surah['namaLatin'] }}</h4>
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-0.5">{{ $surah['arti'] ?? '-' }} &bull; {{ $surah['jumlahAyat'] }} Ayat</p>
                                </div>
                            </div>
                            
                            <div class="font-arabic text-3xl text-emerald-800/60 group-hover:text-emerald-600 transition-colors relative z-10">
                                {{ $surah['nama'] }}
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="text-center">
                    <a href="{{ route('public.quran.index') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-gray-900 hover:bg-black text-white rounded-full font-bold transition-all shadow-md hover:shadow-lg">
                        Lihat Semua 114 Surat <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-3xl border border-gray-100">
                    <i class="fa-solid fa-cloud-arrow-down text-4xl text-gray-300 mb-4"></i>
                    <h4 class="text-lg font-bold text-gray-700">Data sedang disiapkan</h4>
                    <p class="text-gray-500">API Al-Qur'an sedang memuat data.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Prayer Section -->
    <section id="prayer" class="py-24 bg-gray-50 bg-pattern">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-indigo-900 rounded-[3rem] overflow-hidden relative shadow-2xl">
                <!-- Background decoration -->
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')] opacity-10 mix-blend-overlay"></div>
                <div class="absolute top-0 right-0 p-12 opacity-5">
                    <i class="fa-solid fa-mosque text-9xl"></i>
                </div>
                
                <div class="relative z-10 p-8 sm:p-16">
                    <div class="text-center mb-12">
                        <h2 class="text-amber-400 font-bold tracking-widest uppercase text-sm mb-2">Panduan Waktu</h2>
                        <h3 class="text-3xl md:text-5xl font-black text-white mb-4">Jadwal Sholat Hari Ini</h3>
                        <p class="text-indigo-200 text-lg">Waktu sholat untuk wilayah Jakarta dan sekitarnya.</p>
                    </div>

                    @if($prayerTimes)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-12">
                            @php
                                $prayers = [
                                    'Fajr' => ['name' => 'Subuh', 'icon' => 'fa-moon'],
                                    'Dhuhr' => ['name' => 'Dzuhur', 'icon' => 'fa-sun'],
                                    'Asr' => ['name' => 'Ashar', 'icon' => 'fa-cloud-sun'],
                                    'Maghrib' => ['name' => 'Maghrib', 'icon' => 'fa-cloud-moon'],
                                    'Isha' => ['name' => 'Isya', 'icon' => 'fa-moon'],
                                ];
                            @endphp

                            @foreach($prayers as $key => $prayer)
                                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-6 text-center text-white transform hover:-translate-y-2 transition-transform duration-300">
                                    <div class="w-12 h-12 mx-auto bg-indigo-800/50 rounded-2xl flex items-center justify-center text-xl mb-4 border border-white/10">
                                        <i class="fa-solid {{ $prayer['icon'] }} text-amber-300"></i>
                                    </div>
                                    <h4 class="font-bold uppercase tracking-wider text-sm mb-1 text-indigo-100">{{ $prayer['name'] }}</h4>
                                    <div class="text-3xl font-black tracking-tight">{{ substr($prayerTimes['timings'][$key] ?? '--:--', 0, 5) }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-center">
                            <a href="{{ route('public.prayer.index') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-amber-400 hover:bg-amber-300 text-indigo-950 rounded-full font-bold transition-all shadow-lg shadow-amber-400/20 hover:scale-105">
                                <i class="fa-solid fa-location-dot"></i> Sesuaikan dengan Lokasi Anda
                            </a>
                        </div>
                    @else
                        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-8 text-center text-white max-w-lg mx-auto">
                            <i class="fa-solid fa-circle-exclamation text-3xl text-amber-400 mb-4"></i>
                            <h4 class="text-xl font-bold mb-2">Gagal Memuat Jadwal</h4>
                            <p class="text-indigo-200">Silakan periksa koneksi internet Anda atau gunakan fitur lokasi untuk mendeteksi secara otomatis.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Features System -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-indigo-600 font-bold tracking-wide uppercase text-sm mb-2">Sistem Informasi</h2>
                <h3 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Terintegrasi dalam Satu Platform</h3>
                <p class="text-gray-500 text-lg">Solusi digital menyeluruh untuk manajemen pesantren, dari administrasi hingga aktivitas wali santri.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:shadow-xl hover:border-indigo-100 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-3">Sistem Uang Saku Digital</h4>
                    <p class="text-gray-600 leading-relaxed">Transaksi kantin dan laundry menggunakan biometrik sidik jari. Wali dapat memantau dan mengisi saldo secara real-time.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:shadow-xl hover:emerald-100 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-shop"></i>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-3">Marketplace Pesantren</h4>
                    <p class="text-gray-600 leading-relaxed">Wali santri dapat memesan kebutuhan sehari-hari santri langsung melalui aplikasi, yang akan disiapkan oleh pihak outlet koperasi.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:shadow-xl hover:border-blue-100 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-microphone-lines"></i>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-3">Komunikasi Voice Note</h4>
                    <p class="text-gray-600 leading-relaxed">Fasilitas kirim pesan suara berbayar yang aman antara wali dan santri, menggantikan fungsi wartel konvensional.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:shadow-xl hover:border-amber-100 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-3">Rekam Medis Klinik</h4>
                    <p class="text-gray-600 leading-relaxed">Pencatatan riwayat kesehatan santri secara digital. Wali akan mendapat notifikasi jika santri sedang dirawat.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:shadow-xl hover:border-purple-100 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-3">Perizinan & Absensi</h4>
                    <p class="text-gray-600 leading-relaxed">Digitalisasi sistem izin keluar/pulang santri dan absensi asrama yang terpantau langsung oleh musyrif dan wali.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:shadow-xl hover:border-rose-100 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-book-open-reader"></i>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-3">Rapor & Tahfidz</h4>
                    <p class="text-gray-600 leading-relaxed">Pantau perkembangan akademik dan hafalan (tahfidz) santri secara berkala melalui dashboard khusus wali.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 relative">
        <div class="absolute inset-0 bg-indigo-600"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        
        <div class="max-w-4xl mx-auto px-6 relative z-10 text-center">
            <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Siap Memantau Perkembangan Santri?</h2>
            <p class="text-indigo-100 text-lg mb-10 max-w-2xl mx-auto">Masuk ke portal wali untuk mengakses informasi akademik, keuangan, dan aktivitas santri secara real-time.</p>
            
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-10 py-4 bg-white text-indigo-700 hover:bg-gray-50 rounded-full font-extrabold text-lg transition-all shadow-xl hover:-translate-y-1">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Portal
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <img src="/logo-nobg.png" alt="Ash-Shiddiq" class="w-10 h-10">
                    <span class="font-extrabold text-xl tracking-tight text-white">Ash-Shiddiq<span class="text-indigo-500">.</span></span>
                </div>
                
                <div class="text-gray-400 text-sm font-medium">
                    &copy; {{ date('Y') }} Sistem Informasi Pesantren. Hak Cipta Dilindungi.
                </div>
                
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-indigo-600 hover:text-white transition-colors">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-indigo-600 hover:text-white transition-colors">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-indigo-600 hover:text-white transition-colors">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
