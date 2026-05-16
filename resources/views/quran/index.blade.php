@extends('layouts.app')

@section('title', 'Al-Qur\'an Digital')
@section('page-title', 'Al-Qur\'an')

@section('sidebar')
    <div class="px-4 py-3 mt-4">
        <p class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider mb-3 ml-2">Spiritual</p>
        <div class="space-y-1">
            <a href="{{ route('quran.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('quran*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-indigo-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-book-open-reader w-5 text-center text-lg"></i>
                <span class="font-bold text-sm">Al-Qur'an</span>
            </a>
            <a href="{{ route('prayer.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('prayer.index') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-indigo-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-clock w-5 text-center text-lg"></i>
                <span class="font-bold text-sm">Jadwal Sholat</span>
            </a>
            <a href="{{ route('prayer.qibla') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('prayer.qibla') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-indigo-100 hover:bg-white/10 hover:text-white' }}">
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
<div class="max-w-6xl mx-auto">
    
    <!-- Hero Banner -->
    <div class="relative rounded-3xl overflow-hidden mb-8 shadow-sm">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-800 to-teal-600"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')] opacity-10"></div>
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        
        <div class="relative p-8 sm:p-12 text-white flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="text-center sm:text-left z-10">
                <h2 class="text-3xl sm:text-4xl font-extrabold mb-2 tracking-tight">Al-Qur'an Digital</h2>
                <p class="text-emerald-100 text-lg">"Sebaik-baik kalian adalah yang mempelajari Al-Qur'an dan mengajarkannya."</p>
            </div>
            <div class="w-24 h-24 sm:w-32 sm:h-32 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 flex items-center justify-center shrink-0 shadow-xl rotate-3 hover:rotate-0 transition-transform duration-500">
                <i class="fa-solid fa-book-quran text-5xl sm:text-6xl text-emerald-100 drop-shadow-md"></i>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="glass-panel rounded-3xl shadow-sm p-6 sm:p-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Daftar Surat</h3>
                <p class="text-sm text-gray-500 mt-1">Pilih surat untuk mulai membaca.</p>
            </div>
            
            <form method="GET" action="{{ route('quran.search') }}" class="w-full md:w-96 relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" 
                    class="w-full pl-11 pr-24 py-3 bg-gray-50/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all shadow-sm font-medium" 
                    placeholder="Cari surat atau ayat...">
                <button type="submit" class="absolute inset-y-1.5 right-1.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold transition-colors text-sm shadow-sm">
                    Cari
                </button>
            </form>
        </div>

        @if(empty($surahList))
            <div class="py-16 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <i class="fa-solid fa-wifi text-4xl text-gray-300 relative">
                        <div class="absolute inset-0 bg-white/80 w-full h-full rotate-45 flex items-center justify-center">
                            <div class="w-1 h-full bg-gray-300"></div>
                        </div>
                    </i>
                </div>
                <h4 class="text-xl font-bold text-gray-700 mb-2">Koneksi Terputus</h4>
                <p class="text-gray-500 max-w-sm mx-auto mb-6">Tidak dapat memuat daftar surat saat ini. Pastikan Anda terhubung ke internet.</p>
                <button onclick="window.location.reload()" class="px-6 py-2.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-xl font-semibold transition-colors">
                    <i class="fa-solid fa-rotate-right mr-1"></i> Muat Ulang
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($surahList as $surah)
                    <a href="{{ route('quran.surah', $surah['nomor']) }}" class="group relative overflow-hidden bg-white border border-gray-100 hover:border-emerald-300 rounded-2xl p-5 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 flex items-center gap-4">
                        <div class="absolute -right-4 -bottom-4 text-emerald-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none transform group-hover:scale-110">
                            <i class="fa-solid fa-book-quran text-7xl"></i>
                        </div>
                        
                        <div class="relative w-12 h-12 flex-shrink-0 flex items-center justify-center bg-gray-50 group-hover:bg-emerald-50 rounded-xl font-bold text-lg text-gray-700 group-hover:text-emerald-600 border border-gray-100 group-hover:border-emerald-200 transition-colors z-10">
                            {{ $surah['nomor'] }}
                        </div>
                        
                        <div class="flex-1 relative z-10">
                            <div class="font-extrabold text-gray-800 text-lg group-hover:text-emerald-700 transition-colors mb-0.5">{{ $surah['namaLatin'] }}</div>
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $surah['arti'] }} &bull; {{ $surah['jumlahAyat'] }} Ayat</div>
                        </div>
                        
                        <div class="text-2xl text-emerald-800 opacity-80 group-hover:opacity-100 transition-opacity font-arabic relative z-10" style="font-family: 'Amiri', serif;">
                            {{ $surah['nama'] }}
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
.font-arabic {
    font-family: 'Amiri', serif;
}
</style>
@endsection
