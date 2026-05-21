<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a1a2e">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    {{-- Tailwind via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .sidebar-glass {
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden" @click="sidebarOpen = false"></div>

    {{-- Sidebar --}}
    <nav :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="sidebar-glass fixed inset-y-0 left-0 z-30 w-64 transform overflow-y-auto transition duration-300 lg:translate-x-0 lg:static lg:inset-0 flex flex-col">
        <div class="flex items-center justify-center py-6 border-b border-indigo-900/50">
            <a href="/" class="flex items-center gap-3">
                <img src="/logo-nobg.png" alt="Logo" class="w-8 h-8">
                <span class="text-lg font-bold text-yellow-400 tracking-wider">{{ config('app.name') }}</span>
            </a>
        </div>
        
        <div class="mt-4 flex-1">
            @yield('sidebar')
        </div>
        
        <div class="p-4 border-t border-indigo-900/50">
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-indigo-200 hover:bg-indigo-800 hover:text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </button>
            </form>
        </div>
    </nav>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
        {{-- Topbar --}}
        <header class="glass-panel sticky top-0 z-10 flex items-center justify-between px-6 py-4">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden mr-4">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-800">@yield('page-title')</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 bg-white/50 px-3 py-1.5 rounded-full border border-white">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-full shadow-sm object-cover" alt="Avatar">
                    @else
                        <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center shadow-sm">
                            <span class="text-white font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-6 pt-6">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative flex items-center justify-between shadow-sm" role="alert">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-700 hover:text-green-900">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative flex items-center justify-between shadow-sm" role="alert">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-700 hover:text-red-900">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
        </div>

        {{-- Main View --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
            @yield('content')
        </main>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- Midtrans Snap.js (loaded only when Midtrans is the active gateway) --}}
    @php
        $paymentSetting = \App\Models\PaymentSetting::first();
        $midtransActive = $paymentSetting?->active_gateway === 'midtrans';
        $midtransClientKey = $paymentSetting?->midtrans_client_key ?? '';
        $midtransSandbox   = !($paymentSetting?->midtrans_is_production ?? false);
    @endphp
    @if($midtransActive && $midtransClientKey)
    <script src="{{ $midtransSandbox ? 'https://app.sandbox.midtrans.com/snap/snap.js' : 'https://app.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ $midtransClientKey }}"></script>
    @endif

    <script>
    window.handlePaymentResponse = function(data) {
        if (data.type === 'snap' && typeof snap !== 'undefined') {
            snap.pay(data.token, {
                onSuccess:  function() { window.location.href = data.return_url || window.location.href; },
                onPending:  function() { window.location.href = data.return_url || window.location.href; },
                onError:    function(result) { alert('Pembayaran gagal: ' + (result.status_message || 'Terjadi kesalahan.')); },
                onClose:    function() { /* user closed without paying */ },
            });
        } else if (data.type === 'redirect') {
            window.location.href = data.url;
        }
    };
    </script>

    @yield('scripts')
    @stack('scripts')
    
    {{-- Capacitor Push Notifications --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const capacitor = window.Capacitor || (window.parent && window.parent.Capacitor);
                
                if (capacitor && capacitor.isPluginAvailable('PushNotifications')) {
                    const PushNotifications = capacitor.Plugins.PushNotifications;

                    PushNotifications.requestPermissions().then(result => {
                        if (result.receive === 'granted') {
                            PushNotifications.register();
                        }
                    });

                    // Simpan FCM token
                    PushNotifications.addListener('registration', (token) => {
                        console.log('FCM Token:', token.value);
                        fetch('{{ route("wali.update-fcm-token") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ fcm_token: token.value })
                        })
                        .then(r => r.json())
                        .then(data => { if(data.success) console.log('Token Synced'); })
                        .catch(err => console.error('FCM Sync Error:', err));
                    });

                    // Notifikasi diterima saat app foreground — putar suara
                    PushNotifications.addListener('pushNotificationReceived', (notification) => {
                        console.log('Push received (foreground):', notification);
                        // Putar suara notifikasi
                        try {
                            const audio = new Audio('{{ asset("sounds/notification.wav") }}');
                            audio.volume = 0.8;
                            audio.play().catch(() => {});
                        } catch(e) {}
                    });

                    // Notifikasi diklik — deep link ke halaman yang sesuai
                    PushNotifications.addListener('pushNotificationActionPerformed', (action) => {
                        console.log('Push action performed:', action);
                        const data = action.notification.data || {};
                        const baseUrl = '{{ url("/") }}';

                        let targetUrl = null;

                        // Tentukan URL tujuan berdasarkan tipe notifikasi
                        if (data.action_url) {
                            // Kalau server sudah set action_url, pakai itu
                            targetUrl = data.action_url;
                        } else if (data.type === 'voice_note_ustadz' || data.type === 'voice_note_wali') {
                            targetUrl = data.voice_note_id
                                ? baseUrl + '/wali/voice-note/' + data.voice_note_id
                                : baseUrl + '/wali/voice-note';
                        } else if (data.type === 'tagihan_baru') {
                            targetUrl = baseUrl + '/wali/tagihan';
                        } else if (data.type === 'kesehatan' || data.type === 'konfirmasi_kesehatan') {
                            targetUrl = data.kunjungan_id
                                ? baseUrl + '/wali/kesehatan/' + data.kunjungan_id
                                : baseUrl + '/wali/kesehatan';
                        } else if (data.type === 'quran_reminder') {
                            targetUrl = baseUrl + '/quran';
                        } else if (data.type === 'weekly_report' || data.type === 'monthly_report') {
                            targetUrl = data.santri_id
                                ? baseUrl + '/wali/laporan/' + data.santri_id
                                : baseUrl + '/wali/dashboard';
                        } else {
                            targetUrl = baseUrl + '/notifications';
                        }

                        if (targetUrl && window.location.href !== targetUrl) {
                            window.location.href = targetUrl;
                        }
                    });
                }
            }, 3000);
        });
    </script>
    @stack('modals')
</body>
</html>
