@extends('layouts.app')

@section('title', 'Kalender Sholat Bulanan')
@section('page-title', 'Kalender Sholat Bulanan')

@section('sidebar')
    <div class="px-4 py-3 mt-4">
        <p class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider mb-3 ml-2">Spiritual</p>
        <div class="space-y-1">
            <a href="{{ route('quran.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-indigo-100 hover:bg-white/10 hover:text-white">
                <i class="fa-solid fa-book-open-reader w-5 text-center"></i>
                <span class="font-bold text-sm">Al-Qur'an</span>
            </a>
            <a href="{{ route('prayer.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-indigo-100 hover:bg-white/10 hover:text-white">
                <i class="fa-solid fa-clock w-5 text-center"></i>
                <span class="font-bold text-sm">Jadwal Sholat</span>
            </a>
            <a href="{{ route('prayer.calendar') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all bg-indigo-600 text-white shadow-md shadow-indigo-200">
                <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                <span class="font-bold text-sm">Kalender Sholat</span>
            </a>
        </div>
        <div class="mt-6 pt-6 border-t border-indigo-400/30">
            <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-indigo-100 hover:bg-white/10 hover:text-white group">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors"><i class="fa-solid fa-house text-sm"></i></div>
                <span class="font-bold text-sm">Dashboard Utama</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6 px-1">
        <a href="{{ route('prayer.index') }}" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Jadwal Sholat
        </a>
    </div>

    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-indigo-500"></i> Kalender Sholat Bulanan
                </h6>
                <p class="text-sm text-gray-500 mt-1">{{ $month }}/{{ $year }} &mdash; {{ $city }}, {{ $country }}</p>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="p-6 border-b border-gray-100 bg-gray-50/30">
            <form method="GET" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fa-solid fa-city text-sm"></i></div>
                    <input type="text" name="city" class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm focus:border-indigo-500 transition-all" placeholder="Kota" value="{{ $city }}">
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fa-solid fa-globe text-sm"></i></div>
                    <input type="text" name="country" class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm focus:border-indigo-500 transition-all" placeholder="Negara" value="{{ $country }}">
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fa-solid fa-calendar text-sm"></i></div>
                    <input type="number" min="1" max="12" name="month" class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm focus:border-indigo-500 transition-all" placeholder="Bulan (1-12)" value="{{ $month }}">
                </div>
                <button type="submit" class="py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate-right"></i> Muat Ulang
                </button>
            </form>
        </div>

        <!-- Calendar Table -->
        @if(count($calendar) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                            <th class="px-4 py-4 font-medium">Tanggal</th>
                            <th class="px-4 py-4 font-medium"><i class="fa-solid fa-moon text-blue-400 mr-1"></i> Subuh</th>
                            <th class="px-4 py-4 font-medium"><i class="fa-solid fa-sun text-yellow-400 mr-1"></i> Dzuhur</th>
                            <th class="px-4 py-4 font-medium"><i class="fa-solid fa-cloud-sun text-orange-400 mr-1"></i> Ashar</th>
                            <th class="px-4 py-4 font-medium"><i class="fa-solid fa-cloud-moon text-purple-400 mr-1"></i> Maghrib</th>
                            <th class="px-4 py-4 font-medium"><i class="fa-solid fa-moon text-indigo-400 mr-1"></i> Isya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($calendar as $day)
                            <tr class="hover:bg-indigo-50/20 transition-colors">
                                <td class="px-4 py-3 font-bold text-gray-800">{{ $day['date']['gregorian']['date'] ?? $day['date']['readable'] ?? '-' }}</td>
                                <td class="px-4 py-3 font-mono text-sm text-gray-700">{{ substr($day['timings']['Fajr'] ?? '-', 0, 5) }}</td>
                                <td class="px-4 py-3 font-mono text-sm text-gray-700">{{ substr($day['timings']['Dhuhr'] ?? '-', 0, 5) }}</td>
                                <td class="px-4 py-3 font-mono text-sm text-gray-700">{{ substr($day['timings']['Asr'] ?? '-', 0, 5) }}</td>
                                <td class="px-4 py-3 font-mono text-sm text-gray-700">{{ substr($day['timings']['Maghrib'] ?? '-', 0, 5) }}</td>
                                <td class="px-4 py-3 font-mono text-sm text-gray-700">{{ substr($day['timings']['Isha'] ?? '-', 0, 5) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-700 mb-2">Kalender Tidak Tersedia</h4>
                <p class="text-gray-500 max-w-sm mx-auto">Kalender sholat tidak tersedia untuk pengaturan ini. Coba ubah kota atau tanggal.</p>
            </div>
        @endif
    </div>
</div>
@endsection
