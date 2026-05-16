@extends('layouts.app')

@section('title', 'Arah Kiblat Otomatis')

@section('sidebar')
    <div class="px-4 py-3 mt-4">
        <p class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider mb-3 ml-2">Spiritual</p>
        <div class="space-y-1">
            <a href="{{ route('quran.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-indigo-100 hover:bg-white/10 hover:text-white">
                <i class="fa-solid fa-book-open-reader w-5 text-center text-lg"></i>
                <span class="font-bold text-sm">Al-Qur'an</span>
            </a>
            <a href="{{ route('prayer.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-indigo-100 hover:bg-white/10 hover:text-white">
                <i class="fa-solid fa-clock w-5 text-center text-lg"></i>
                <span class="font-bold text-sm">Jadwal Sholat</span>
            </a>
            <a href="{{ route('prayer.qibla') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all bg-indigo-600 text-white shadow-md shadow-indigo-200">
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
<div class="max-w-md mx-auto" x-data="qiblaCompass()">
    <div class="glass-panel rounded-[2.5rem] overflow-hidden shadow-xl shadow-indigo-900/5 border border-white/60 bg-white">
        
        <!-- Header -->
        <div class="bg-gradient-to-br from-indigo-900 to-blue-900 text-white p-8 relative overflow-hidden">
            <!-- Decorative background -->
            <div class="absolute -right-6 -bottom-6 opacity-10 transform -rotate-12">
                <i class="fa-solid fa-kaaba text-9xl"></i>
            </div>
            <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-30 mix-blend-overlay"></div>
            
            <div class="flex justify-between items-center relative z-10 mb-6">
                <div>
                    <h4 class="text-2xl font-extrabold tracking-tight mb-1">Arah Kiblat</h4>
                    <p class="text-indigo-200 text-sm font-medium">Panduan akurat dengan GPS</p>
                </div>
                <a href="{{ route('prayer.index') }}" class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center hover:bg-white/20 transition-colors shadow-sm">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
            
            <div class="relative z-10 flex items-center gap-4 bg-black/20 p-4 rounded-2xl backdrop-blur-md border border-white/10 shadow-inner">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/80 shadow-inner flex items-center justify-center border border-indigo-400/50">
                    <i class="fa-solid fa-location-dot text-xl text-white drop-shadow-md" x-show="!isLoadingLocation"></i>
                    <i class="fa-solid fa-circle-notch fa-spin text-xl text-white" x-show="isLoadingLocation"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-indigo-200 font-bold uppercase tracking-wider mb-0.5">Lokasi Anda</p>
                    <p class="font-bold text-sm text-white line-clamp-1" x-text="locationName"></p>
                </div>
            </div>
        </div>
        
        <!-- Compass Area -->
        <div class="p-8 text-center bg-gray-50/50 min-h-[450px] flex flex-col items-center justify-center relative">
            
            <!-- Messages / Errors -->
            <div x-show="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm w-full font-bold flex items-start gap-2 text-left shadow-sm" x-transition>
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> 
                <span x-text="error" class="flex-1"></span>
            </div>
            
            <!-- Permission Request UI -->
            <div x-show="needsPermission" class="mb-6 w-full" x-transition>
                <div class="p-6 bg-white rounded-3xl shadow-sm border border-indigo-50 text-center">
                    <div class="w-20 h-20 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl border border-indigo-100">
                        <i class="fa-regular fa-compass"></i>
                    </div>
                    <h5 class="text-lg font-extrabold text-gray-800 mb-2">Akses Sensor Kompas</h5>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">Aplikasi memerlukan izin untuk mengakses sensor orientasi perangkat agar jarum kompas dapat berfungsi.</p>
                    <button @click="requestOrientationPermission" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-indigo-200 transition-all active:scale-95 flex justify-center items-center gap-2">
                        <i class="fa-solid fa-check"></i> Izinkan Akses
                    </button>
                </div>
            </div>

            <!-- Compass UI -->
            <div x-show="!needsPermission && qiblaAngle !== null" x-transition class="w-full max-w-[280px] relative mx-auto my-4 aspect-square">
                
                <!-- Compass Base (Outer Ring) -->
                <div class="absolute inset-0 rounded-full border-[6px] border-white shadow-[0_10px_30px_rgba(0,0,0,0.1),inset_0_4px_10px_rgba(0,0,0,0.05)] bg-gray-50 flex items-center justify-center overflow-hidden">
                    <!-- Cardinal Directions -->
                    <div class="absolute top-3 font-extrabold text-red-500 text-sm z-10 bg-white/80 w-6 h-6 flex items-center justify-center rounded-full">U</div>
                    <div class="absolute bottom-3 font-bold text-gray-400 text-sm z-10 bg-white/80 w-6 h-6 flex items-center justify-center rounded-full">S</div>
                    <div class="absolute right-3 font-bold text-gray-400 text-sm z-10 bg-white/80 w-6 h-6 flex items-center justify-center rounded-full">T</div>
                    <div class="absolute left-3 font-bold text-gray-400 text-sm z-10 bg-white/80 w-6 h-6 flex items-center justify-center rounded-full">B</div>
                    
                    <!-- Tick marks -->
                    <template x-for="i in 36">
                        <div class="absolute w-[1px] h-full" :style="'transform: rotate(' + (i * 10) + 'deg)'">
                            <div class="w-full" :class="i % 3 === 0 ? 'h-3 bg-gray-300' : 'h-1.5 bg-gray-200'"></div>
                            <div class="w-full h-full"></div>
                        </div>
                    </template>
                    
                    <div class="absolute inset-8 rounded-full border border-gray-100 bg-white shadow-inner"></div>
                </div>
                
                <!-- Rotating Wrapper -->
                <div class="absolute inset-0 transition-transform duration-300 ease-out flex justify-center items-center"
                     :style="'transform: rotate(' + (-deviceAlpha) + 'deg)'">
                     
                     <!-- Phone Direction indicator -->
                     <div class="absolute top-1 text-red-500 text-[10px] font-bold z-10 flex flex-col items-center">
                         <i class="fa-solid fa-caret-up text-lg -mb-1"></i>
                     </div>
                     
                     <!-- Qibla Arrow - Rotated relative to North -->
                     <div class="absolute inset-0 flex items-center justify-center transition-transform duration-700 ease-in-out"
                          :style="'transform: rotate(' + qiblaAngle + 'deg)'">
                          
                          <!-- Kaaba Icon -->
                          <div class="absolute top-[18px] w-12 h-12 bg-emerald-500 rounded-full flex justify-center items-center shadow-lg border-2 border-white transform -translate-y-1/2 z-20">
                              <i class="fa-solid fa-kaaba text-white text-xl"></i>
                          </div>
                          
                          <!-- Arrow line -->
                          <div class="w-1.5 h-[130px] bg-gradient-to-t from-transparent via-emerald-400 to-emerald-500 rounded-full transform -translate-y-[45px] z-10"></div>
                     </div>
                </div>
                
                <!-- Center point -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-5 h-5 bg-gray-800 rounded-full border-2 border-white shadow-md z-30 flex items-center justify-center">
                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                    </div>
                </div>
            </div>
            
            <!-- Angle Display -->
            <div x-show="!needsPermission && qiblaAngle !== null" class="mt-8 bg-white px-8 py-4 rounded-3xl shadow-sm border border-gray-100 inline-block w-full">
                <p class="text-[10px] text-gray-500 uppercase tracking-widest font-extrabold mb-1">Sudut Arah Kiblat</p>
                <div class="flex items-start justify-center gap-1">
                    <span class="text-5xl font-black text-emerald-600 font-mono tracking-tighter" x-text="Math.round(qiblaAngle)"></span>
                    <span class="text-2xl font-bold text-emerald-400 mt-1">°</span>
                </div>
                <p class="text-xs text-gray-400 mt-2 font-medium">Sejajarkan ikon Ka'bah dengan arah ponsel.</p>
            </div>
            
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('qiblaCompass', () => ({
        locationName: 'Mencari lokasi...',
        latitude: null,
        longitude: null,
        qiblaAngle: null,
        deviceAlpha: 0,
        
        isLoadingLocation: true,
        error: null,
        needsPermission: false,
        
        init() {
            this.checkLocation();
            
            // For iOS 13+ devices we need to explicitly request permission for device orientation
            if (typeof DeviceOrientationEvent !== 'undefined' && typeof DeviceOrientationEvent.requestPermission === 'function') {
                this.needsPermission = true;
            } else {
                this.setupOrientationListener();
            }
        },
        
        checkLocation() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.latitude = position.coords.latitude;
                        this.longitude = position.coords.longitude;
                        this.locationName = this.latitude.toFixed(4) + ', ' + this.longitude.toFixed(4);
                        this.fetchQiblaAngle();
                    },
                    (error) => {
                        this.isLoadingLocation = false;
                        this.error = "Akses lokasi ditolak atau tidak tersedia. Pastikan GPS aktif pada browser Anda.";
                        // Fallback to Jakarta
                        this.latitude = -6.2088;
                        this.longitude = 106.8456;
                        this.locationName = "Jakarta (Default)";
                        this.fetchQiblaAngle();
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                this.isLoadingLocation = false;
                this.error = "Browser tidak mendukung sensor Geolokasi.";
            }
        },
        
        fetchQiblaAngle() {
            fetch(`/api/qibla?lat=${this.latitude}&lng=${this.longitude}`)
                .then(res => res.json())
                .then(data => {
                    this.isLoadingLocation = false;
                    // Aladhan API returns direction directly in degrees from North
                    this.qiblaAngle = data.direction;
                })
                .catch(err => {
                    this.isLoadingLocation = false;
                    this.error = "Gagal mengambil kalkulasi arah kiblat dari server.";
                    console.error(err);
                });
        },
        
        requestOrientationPermission() {
            if (typeof DeviceOrientationEvent !== 'undefined' && typeof DeviceOrientationEvent.requestPermission === 'function') {
                DeviceOrientationEvent.requestPermission()
                    .then(permissionState => {
                        if (permissionState === 'granted') {
                            this.needsPermission = false;
                            this.setupOrientationListener();
                        } else {
                            this.error = "Izin akses sensor kompas ditolak oleh sistem IOS.";
                        }
                    })
                    .catch(console.error);
            }
        },
        
        setupOrientationListener() {
            window.addEventListener('deviceorientationabsolute', this.handleOrientation.bind(this), true);
            // Fallback for browsers that don't support absolute
            window.addEventListener('deviceorientation', this.handleOrientation.bind(this), true);
        },
        
        handleOrientation(event) {
            let alpha = event.alpha;
            let webkitCompassHeading = event.webkitCompassHeading;
            
            // iOS devices use webkitCompassHeading
            if (webkitCompassHeading) {
                alpha = webkitCompassHeading;
                this.deviceAlpha = alpha;
            } else if (alpha !== null) {
                // Absolute orientation or standard alpha
                this.deviceAlpha = 360 - alpha;
            }
        }
    }));
});
</script>
@endsection
