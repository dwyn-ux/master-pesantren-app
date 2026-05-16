@extends('layouts.app')

@section('title', 'Dashboard Ustadz')
@section('page-title', 'Dashboard Ustadz')

@section('sidebar')
    <a href="{{ route('ustadz.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('ustadz.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Welcome banner --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
        <h1 class="text-2xl font-bold">Halo, {{ auth()->user()->name }}</h1>
        <p class="text-indigo-100 mt-1">
            Hari ini: {{ now()->locale('id')->translatedFormat('l, d F Y') }}
            @if($taActive)
                · Tahun Ajaran {{ $taActive->nama }} ({{ ucfirst($taActive->semester) }})
            @endif
        </p>
    </div>

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Kelas Diampu</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['kelas_diampu'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i class="fa-solid fa-school text-indigo-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Jadwal Hari Ini</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['jadwal_hari_ini'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-day text-amber-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Wali Kelas</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['kelas_walikan'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-100 flex items-center justify-center">
                    <i class="fa-solid fa-user-tie text-purple-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Absensi Minggu Ini</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['absensi_minggu_ini'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fa-solid fa-clipboard-check text-green-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Jadwal hari ini --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fa-solid fa-calendar-day text-amber-500 mr-2"></i>Jadwal Mengajar Hari Ini
            </h2>

            @if($jadwalHariIni->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <i class="fa-regular fa-calendar text-4xl mb-2"></i>
                    <p class="text-sm">Tidak ada jadwal mengajar hari ini.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($jadwalHariIni as $j)
                    <div class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:border-indigo-200 hover:bg-indigo-50/30 transition">
                        <div class="text-center min-w-[64px]">
                            <p class="text-xs text-gray-500">{{ substr($j->jam_mulai, 0, 5) }}</p>
                            <p class="text-xs font-bold text-indigo-600">{{ substr($j->jam_selesai, 0, 5) }}</p>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $j->mataPelajaran->nama }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $j->kelas->tingkat->nama ?? '' }} - {{ $j->kelas->nama }}
                                @if($j->ruangan) · {{ $j->ruangan }} @endif
                            </p>
                        </div>
                        @feature('akademik_absensi')
                        <a href="{{ route('ustadz.akademik.absensi.index', ['jadwal_id' => $j->id, 'tanggal' => date('Y-m-d')]) }}" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg whitespace-nowrap">
                            Absen
                        </a>
                        @endfeature
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Kelas yang diwali --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fa-solid fa-user-tie text-purple-500 mr-2"></i>Kelas Yang Saya Walikan
            </h2>

            @if($kelasWalikan->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <i class="fa-regular fa-user text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada kelas yang Anda walikan.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($kelasWalikan as $k)
                    <a href="{{ route('ustadz.akademik.wali-kelas.show', $k) }}" class="flex items-center justify-between p-3 border border-gray-100 rounded-xl hover:border-purple-200 hover:bg-purple-50/30 transition">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $k->tingkat->nama }} - {{ $k->nama }}</p>
                            <p class="text-xs text-gray-500">{{ $k->santri->count() }} santri terdaftar</p>
                        </div>
                        <i class="fa-solid fa-arrow-right text-purple-500"></i>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
