@extends('layouts.app')
@section('title', 'Halaqah Saya')
@section('page-title', 'Halaqah Saya')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Welcome Strip -->
    <div class="glass-panel rounded-2xl p-6 mb-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-l-4 border-indigo-500">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-extrabold shrink-0 border-2 border-indigo-200">
                {{ strtoupper(substr($ustadz->nama, 0, 1)) }}
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ustadz / Musyrif</p>
                <h3 class="text-xl font-extrabold text-gray-800">{{ $ustadz->nama }}</h3>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="text-center">
                <div class="text-3xl font-black text-indigo-600">{{ $halaqah->count() }}</div>
                <div class="text-xs font-bold text-gray-500 uppercase">Halaqah</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-black text-emerald-600">{{ $halaqah->sum('santri_count') }}</div>
                <div class="text-xs font-bold text-gray-500 uppercase">Total Santri</div>
            </div>
        </div>
    </div>

    <h4 class="text-lg font-extrabold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-book-quran text-indigo-500"></i> Daftar Halaqah
    </h4>

    @if($halaqah->isEmpty())
        <div class="glass-panel rounded-2xl p-16 text-center">
            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-book-quran text-4xl text-indigo-300"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Halaqah</h4>
            <p class="text-gray-500 max-w-sm mx-auto">Anda belum ditugaskan ke halaqah manapun. Silakan hubungi admin.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($halaqah as $h)
                <a href="{{ route('ustadz.halaqah.show', $h) }}" class="glass-panel rounded-2xl p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group border border-white/60 hover:border-indigo-200">
                    <div class="flex items-start justify-between mb-5">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i class="fa-solid fa-book-quran"></i>
                        </div>
                        <span class="bg-emerald-100 text-emerald-700 text-xs font-extrabold px-3 py-1 rounded-full border border-emerald-200">
                            {{ $h->santri_count }} Santri
                        </span>
                    </div>
                    <h5 class="text-lg font-extrabold text-gray-800 group-hover:text-indigo-700 transition-colors mb-1">{{ $h->nama }}</h5>
                    <p class="text-sm text-gray-500 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-right text-xs group-hover:text-indigo-500 transition-colors"></i>
                        Klik untuk lihat detail dan daftar santri
                    </p>
                </a>
            @endforeach
        </div>
    @endif

    <!-- Quick Links -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8">
        <a href="{{ route('ustadz.santri.index') }}" class="glass-panel rounded-xl p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-transform group">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="font-extrabold text-gray-800">Semua Santri</div>
                <div class="text-xs text-gray-500">Daftar santri di halaqah Anda</div>
            </div>
        </a>
        <a href="{{ route('ustadz.setoran.index') }}" class="glass-panel rounded-xl p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-transform group">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl group-hover:bg-amber-600 group-hover:text-white transition-colors">
                <i class="fa-solid fa-scroll"></i>
            </div>
            <div>
                <div class="font-extrabold text-gray-800">Riwayat Setoran</div>
                <div class="text-xs text-gray-500">Catat dan pantau hafalan santri</div>
            </div>
        </a>
        <a href="{{ route('quran.index') }}" class="glass-panel rounded-xl p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-transform group">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                <i class="fa-solid fa-book-open-reader"></i>
            </div>
            <div>
                <div class="font-extrabold text-gray-800">Al-Qur'an Digital</div>
                <div class="text-xs text-gray-500">Buka Al-Qur'an & terjemahan</div>
            </div>
        </a>
    </div>
</div>
@endsection
