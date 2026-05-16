@extends('layouts.app')

@section('title', 'Jadwal Sholat')
@section('page-title', 'Jadwal Sholat')

@section('sidebar')
    <div class="px-4 py-3 mt-4">
        <p class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider mb-3 ml-2">Spiritual</p>
        <div class="space-y-1">
            <a href="{{ route('quran.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-indigo-100 hover:bg-white/10 hover:text-white">
                <i class="fa-solid fa-book-open-reader w-5 text-center text-lg"></i>
                <span class="font-bold text-sm">Al-Qur'an</span>
            </a>
            <a href="{{ route('prayer.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all bg-indigo-600 text-white shadow-md shadow-indigo-200">
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
<div class="max-w-5xl mx-auto pb-12">
    
    <!-- Hero Header -->
    <div class="relative rounded-3xl overflow-hidden mb-8 shadow-sm">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-900 to-blue-800"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-20 mix-blend-overlay"></div>
        
        <div class="relative p-8 sm:p-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-white text-center md:text-left">
                <h2 class="text-3xl sm:text-4xl font-extrabold mb-2 tracking-tight">
                    Jadwal Sholat <span class="text-amber-300">{{ ($latitude ?? null) ? 'di Lokasi Anda' : 'di ' . ($city ?? 'Jakarta') }}</span>
                </h2>
                <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-4 text-indigo-200">
                    <div class="flex items-center gap-2" id="locationStatusContainer">
                        <i class="fa-solid fa-location-dot"></i>
                        <span id="locationStatus" class="font-medium text-sm">Mendeteksi lokasi otomatis...</span>
                    </div>
                    <button type="button" id="useLocationBtn" class="text-amber-300 hover:text-amber-200 text-sm font-bold underline decoration-amber-300/30 hover:decoration-amber-300 transition-colors">
                        Perbarui Lokasi GPS
                    </button>
                </div>
            </div>
            
            <div class="text-right shrink-0 bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/20 shadow-inner">
                <div class="text-white/80 text-sm font-medium mb-1">{{ $prayerTimes['date']['readable'] ?? now()->translatedFormat('d F Y') }}</div>
                <div class="text-white text-lg font-bold font-arabic" style="font-family: 'Amiri', serif;">
                    {{ $prayerTimes['date']['hijri']['day'] ?? '' }} {{ $prayerTimes['date']['hijri']['month']['en'] ?? '' }}, {{ $prayerTimes['date']['hijri']['year'] ?? '' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Main Panel -->
    <div class="glass-panel rounded-3xl shadow-sm overflow-hidden bg-white/80">
        
        <!-- Search Form -->
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <form method="GET" class="flex flex-col sm:flex-row gap-4 max-w-3xl">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <input type="text" name="city" value="{{ $city ?? 'Jakarta' }}" class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm font-medium" placeholder="Kota">
                </div>
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-globe"></i>
                    </div>
                    <input type="text" name="country" value="{{ $country ?? 'Indonesia' }}" class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm font-medium" placeholder="Negara">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl font-bold transition-colors shadow-sm whitespace-nowrap">
                    Cari Jadwal
                </button>
            </form>
        </div>

        <div class="p-6 sm:p-8">
            @if($prayerTimes)
                @php
                    $prayers = [
                        'Fajr' => ['name' => 'Subuh', 'icon' => 'fa-moon'],
                        'Sunrise' => ['name' => 'Terbit', 'icon' => 'fa-sun'],
                        'Dhuhr' => ['name' => 'Dzuhur', 'icon' => 'fa-sun'],
                        'Asr' => ['name' => 'Ashar', 'icon' => 'fa-cloud-sun'],
                        'Maghrib' => ['name' => 'Maghrib', 'icon' => 'fa-cloud-moon'],
                        'Isha' => ['name' => 'Isya', 'icon' => 'fa-moon'],
                    ];
                @endphp
                
                <!-- Prayer Times Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                    @foreach($prayers as $key => $prayer)
                        <div class="time-card bg-gray-50 border border-gray-100 rounded-2xl p-5 text-center flex flex-col items-center justify-center transition-all duration-300 relative overflow-hidden group" data-prayer="{{ $key }}">
                            <!-- Background decoration -->
                            <div class="absolute -right-4 -bottom-4 text-gray-100 group-hover:text-indigo-50 transition-colors duration-300">
                                <i class="fa-solid {{ $prayer['icon'] }} text-6xl"></i>
                            </div>
                            
                            <div class="relative z-10">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2 time-name">{{ $prayer['name'] }}</h4>
                                <div class="text-2xl font-black text-gray-800 time-value">{{ substr($prayerTimes['timings'][$key] ?? '--:--', 0, 5) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Next Prayer Highlight -->
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-100 rounded-2xl p-6 sm:p-8 flex flex-col md:flex-row justify-between items-center gap-6 shadow-sm">
                    <div class="flex items-center gap-5 text-center md:text-left">
                        <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-3xl shadow-inner shrink-0">
                            <i class="fa-regular fa-bell"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-indigo-400 uppercase tracking-wider mb-1">Sholat Selanjutnya</p>
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-indigo-900" id="nextPrayerName">Menghitung...</h3>
                        </div>
                    </div>
                    
                    <div class="text-center md:text-right bg-white/60 px-6 py-4 rounded-xl border border-indigo-50 backdrop-blur-sm min-w-[200px]">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Waktu Tersisa</p>
                        <div class="text-3xl sm:text-4xl font-black text-indigo-600 font-mono tracking-tight" id="nextPrayerCountdown">--:--</div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-medium text-gray-400">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-globe"></i> Zona Waktu: {{ $prayerTimes['meta']['timezone'] ?? '-' }}
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-calculator"></i> Metode: {{ $prayerTimes['meta']['method']['name'] ?? 'Kemenag / Sihat' }}
                    </div>
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-700 mb-2">Jadwal Tidak Tersedia</h4>
                    <p class="text-gray-500 max-w-sm mx-auto mb-6">Tidak dapat memuat jadwal sholat. Silakan coba cari kota lain atau izinkan akses lokasi.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
.font-arabic { font-family: 'Amiri', serif; }

/* Styles for the "Next Prayer" highlighted card */
.time-card.next-style {
    background-color: #1e1b4b !important; /* text-indigo-950 */
    border-color: #3730a3 !important; /* indigo-800 */
    transform: scale(1.05);
    box-shadow: 0 10px 25px -5px rgba(49, 46, 129, 0.5);
    z-index: 10;
}
.time-card.next-style .time-name { color: #818cf8 !important; /* indigo-400 */ }
.time-card.next-style .time-value { color: #ffffff !important; }
.time-card.next-style i { color: rgba(255, 255, 255, 0.1) !important; }

@media (max-width: 768px) {
    .time-card.next-style { transform: scale(1.02); }
}
</style>
@endsection

@section('scripts')
<script>
const prayerNameMap = { 
    Fajr: 'Subuh', 
    Sunrise: 'Terbit',
    Dhuhr: 'Dzuhur', 
    Asr: 'Ashar', 
    Maghrib: 'Maghrib', 
    Isha: 'Isya' 
};

const currentLat = @json($latitude ?? null);
const currentLng = @json($longitude ?? null);

function setLocationStatus(text) {
    const el = document.getElementById('locationStatus');
    if (el) el.textContent = text;
}

function requestBrowserLocation(auto = false) {
    if (!navigator.geolocation) {
        setLocationStatus('Browser tidak mendukung deteksi lokasi.');
        return;
    }
    if (window.location.protocol !== 'https:' && !['localhost', '127.0.0.1'].includes(window.location.hostname)) {
        setLocationStatus('Deteksi otomatis butuh HTTPS. Gunakan pencarian manual.');
        return;
    }
    if (auto && (new URLSearchParams(window.location.search)).has('lat')) {
        setLocationStatus('Jadwal menggunakan lokasi GPS.');
        return;
    }
    
    setLocationStatus('Meminta izin lokasi browser...');
    
    navigator.geolocation.getCurrentPosition((position) => {
        const lat = position.coords.latitude.toFixed(6);
        const lng = position.coords.longitude.toFixed(6);
        const params = new URLSearchParams(window.location.search);
        
        // Don't reload if coordinates are practically the same
        if (params.get('lat') == lat && params.get('lng') == lng) {
            setLocationStatus('Lokasi sudah diperbarui.');
            return;
        }
        
        params.set('lat', lat);
        params.set('lng', lng);
        params.delete('city'); // Clear city text search when using coords
        params.delete('country');
        
        setLocationStatus('Lokasi ditemukan. Memuat jadwal...');
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }, (error) => {
        const message = error.code === 1 ? 'Izin lokasi ditolak. Gunakan pencarian manual.' : 'Lokasi gagal dideteksi.';
        setLocationStatus(message);
    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
}

function updateNextPrayer() {
    fetch('{{ route("prayer.next-prayer") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            city: @json($city ?? 'Jakarta'), 
            country: @json($country ?? 'Indonesia'), 
            lat: currentLat, 
            lng: currentLng
        })
    })
    .then(r => r.json())
    .then(data => {
        if(!data.next_prayer) return;
        
        const hours = Math.floor(data.time_remaining / 60);
        const minutes = Math.abs(data.time_remaining % 60);
        
        document.getElementById('nextPrayerName').textContent = prayerNameMap[data.next_prayer] || data.next_prayer;
        
        // Format countdown string
        let countdownText = '';
        if (hours > 0) {
            countdownText = `${hours}j ${minutes}m`;
        } else {
            countdownText = `${minutes}m`;
        }
        document.getElementById('nextPrayerCountdown').textContent = countdownText;
        
        // Highlight active card
        document.querySelectorAll('.time-card').forEach(card => card.classList.remove('next-style'));
        const activeCard = document.querySelector(`[data-prayer="${data.next_prayer}"]`);
        if(activeCard) activeCard.classList.add('next-style');
    })
    .catch(err => console.error("Error fetching next prayer:", err));
}

document.addEventListener('DOMContentLoaded', () => { 
    document.getElementById('useLocationBtn')?.addEventListener('click', () => requestBrowserLocation(false));
    requestBrowserLocation(true); 
    
    // Initial fetch
    updateNextPrayer(); 
    // Update countdown every minute
    setInterval(updateNextPrayer, 60000); 
});
</script>
@endsection
