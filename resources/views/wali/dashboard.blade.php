@extends('layouts.app')
@section('title', 'Dashboard Wali')
@section('page-title', 'Dashboard Wali Santri')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
    
    {{-- Main Column --}}
    <div class="lg:col-span-8 space-y-6">
        
        {{-- Welcome Banner --}}
        <div class="p-8 rounded-2xl shadow-md text-white relative overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%)">
            <div class="absolute -right-6 -top-6 text-white/10">
                <i class="fa-solid fa-hands-holding-child text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-xs font-medium text-indigo-50 mb-4">
                    Role: Wali Santri
                </div>
                <h3 class="text-2xl sm:text-3xl font-bold mb-2">Ahlan wa Sahlan,</h3>
                <h4 class="text-xl sm:text-2xl font-light opacity-90 mb-6">{{ Auth::user()->name }}</h4>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('wali.marketplace.index') }}" class="px-6 py-3 bg-white text-indigo-700 hover:bg-indigo-50 rounded-xl font-semibold transition-colors shadow-sm flex items-center justify-center gap-2 group">
                        <i class="fa-solid fa-store group-hover:scale-110 transition-transform"></i>
                        Kunjungi Marketplace
                    </a>
                    <a href="{{ route('wali.voice-note.index') }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl font-medium transition-colors backdrop-blur-md flex items-center justify-center gap-2">
                        <i class="fa-solid fa-microphone"></i>
                        Pesan Voice Note
                    </a>
                    <form action="{{ route('wali.dashboard.test-notification') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-indigo-900 rounded-xl font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-bell"></i>
                            Tes Notif HP
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar Santri --}}
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
                <h6 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-users mr-2 text-indigo-500"></i> Daftar Santri Anda</h6>
            </div>
            
            <div class="p-0">
                @if(Auth::user()->wali->santri->isEmpty())
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                            <i class="fa-solid fa-user-slash text-3xl text-gray-300"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-700 mb-2">Belum ada santri terdaftar</h4>
                        <p class="text-gray-500">Silakan hubungi administrator pesantren untuk menautkan data santri ke akun Anda.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-0">
                        @foreach(Auth::user()->wali->santri as $index => $santri)
                            <div class="p-6 {{ $index > 0 ? 'border-t sm:border-t-0 sm:border-l' : '' }} border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <div class="flex items-start gap-4">
                                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 border border-indigo-200 flex items-center justify-center flex-shrink-0 text-indigo-600 font-bold text-xl shadow-sm">
                                        {{ substr($santri->nama, 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="text-lg font-bold text-gray-800 mb-1">{{ $santri->nama }}</h5>
                                        <div class="flex flex-col gap-1 text-sm text-gray-600 mb-3">
                                            <div class="flex items-center gap-2"><i class="fa-solid fa-id-card w-4 text-center text-gray-400"></i> NIS: <span class="font-mono">{{ $santri->nis }}</span></div>
                                            <div class="flex items-center gap-2"><i class="fa-solid fa-wallet w-4 text-center text-gray-400"></i> Saldo: <span class="font-semibold text-indigo-700">Rp {{ number_format($santri->saldo) }}</span></div>
                                        </div>
                                        <a href="{{ route('wali.laporan.show', $santri) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition-colors shadow-sm">
                                            <i class="fa-solid fa-chart-line"></i> Lihat Laporan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
    </div>
    
    {{-- Sidebar Column --}}
    <div class="lg:col-span-4 space-y-6">
        
        {{-- Dompet Saldo --}}
        <div class="p-6 rounded-2xl shadow-md text-white relative overflow-hidden" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%)">
            <div class="absolute right-0 bottom-0 w-32 h-32 bg-white/10 rounded-full blur-2xl translate-x-10 translate-y-10"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-bold text-emerald-100 uppercase tracking-wider">Saldo Digital</span>
                    <i class="fa-solid fa-wallet text-xl text-emerald-200"></i>
                </div>
                <h3 class="text-3xl font-extrabold mb-1">Rp {{ number_format(Auth::user()->wali->saldo ?? 0) }}</h3>
                <p class="text-emerald-100 text-sm mb-6">Tersedia untuk pembelanjaan santri di Kantin & Koperasi</p>
                
                <a href="{{ route('wali.topup.create') }}" class="block w-full py-2.5 bg-white/20 hover:bg-white/30 border border-white/30 rounded-xl text-center font-medium transition-colors text-sm backdrop-blur-sm">
                    <i class="fa-solid fa-plus mr-1"></i> Top Up Saldo
                </a>
            </div>
        </div>

        {{-- Tagihan Belum Bayar --}}
        @php
            $waliObj = Auth::user()->wali;
            $santriIds = $waliObj?->santri->pluck('id') ?? collect();
            $tagihanBelumBayar = $santriIds->isNotEmpty()
                ? \App\Models\Tagihan::whereIn('santri_id', $santriIds)->where('status', 'belum_bayar')->get()
                : collect();
            $totalTunggakan = $tagihanBelumBayar->sum('nominal');
        @endphp
        @if($tagihanBelumBayar->isNotEmpty())
        <div class="p-6 rounded-2xl shadow-md text-white relative overflow-hidden" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)">
            <div class="absolute -right-4 -top-4 text-white/10">
                <i class="fa-solid fa-file-invoice-dollar text-8xl"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-bold text-red-100 uppercase tracking-wider">Tagihan Belum Bayar</span>
                    <i class="fa-solid fa-triangle-exclamation text-xl text-red-200"></i>
                </div>
                <h3 class="text-2xl font-extrabold mb-1">Rp {{ number_format($totalTunggakan) }}</h3>
                <p class="text-red-200 text-xs mb-4">{{ $tagihanBelumBayar->count() }} tagihan belum dibayar</p>
                <a href="{{ route('wali.tagihan.index') }}" class="block w-full py-2.5 bg-white/20 hover:bg-white/30 border border-white/30 rounded-xl text-center font-medium transition-colors text-sm backdrop-blur-sm">
                    <i class="fa-solid fa-credit-card mr-1"></i> Bayar Sekarang
                </a>
            </div>
        </div>
        @endif

        {{-- Aksi Cepat --}}
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 bg-white/50 px-5 py-3">
                <h6 class="font-bold text-gray-800">Aksi Cepat</h6>
            </div>
            <div class="p-2">
                <a href="{{ route('wali.marketplace.index') }}" class="flex items-center p-3 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold">Marketplace</div>
                        <div class="text-xs text-gray-500">Belanja keperluan santri</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-indigo-400"></i>
                </a>
                
                <a href="{{ route('wali.voice-note.index') }}" class="flex items-center p-3 rounded-xl hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-microphone"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold">Pesan Suara</div>
                        <div class="text-xs text-gray-500">Kirim pesan ke santri</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-emerald-400"></i>
                </a>
                
                <a href="{{ route('auth.change-password') }}" class="flex items-center p-3 rounded-xl hover:bg-amber-50 text-gray-700 hover:text-amber-700 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold">Ganti Password</div>
                        <div class="text-xs text-gray-500">Amankan akun Anda</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-amber-400"></i>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

{{-- Modals Push ditaruh di luar @section content --}}
@push('modals')
@if($rekap['tagihan']->isNotEmpty() || $rekap['tahfidz']->isNotEmpty() || $rekap['kesehatan']->isNotEmpty())
<div x-data="rekapModal()" 
     x-show="showRekap" 
     class="fixed inset-0 flex items-center justify-center p-4" 
     style="display: none; z-index: 999999;"
     x-init="init()"
     x-effect="if(!showRekap) document.body.classList.remove('overflow-hidden')">
    {{-- Overlay Backdrop --}}
    <div x-show="showRekap" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-slate-950/90 backdrop-blur-xl"
         style="z-index: 1000000;"></div>

    {{-- Modal Container --}}
    <div x-show="showRekap"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="bg-white rounded-3xl shadow-[0_0_100px_rgba(0,0,0,0.5)] w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col relative" 
         style="z-index: 1000001;"
         @click.away="showRekap = false">
        {{-- Header --}}
        <div class="p-6 bg-white border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Ringkasan Informasi Santri</h3>
                <p class="text-gray-500 text-sm">Update terbaru untuk Ananda</p>
            </div>
            <button @click="close()" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 transition-colors">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        {{-- Content --}}
        <div class="p-6 overflow-y-auto space-y-6">
            {{-- Tagihan Section --}}
            @if($rekap['tagihan']->isNotEmpty())
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-red-600 font-bold uppercase text-xs tracking-wider">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Tagihan Belum Terbayar
                </div>
                <div class="space-y-2">
                    @foreach($rekap['tagihan'] as $tag)
                    <div class="p-4 rounded-2xl bg-red-50 border border-red-100 flex justify-between items-center">
                        <div>
                            <div class="font-bold text-red-900">{{ $tag->jenisTagihan->nama }}</div>
                            <div class="text-xs text-red-700">{{ $tag->santri->nama }} - Periode {{ $tag->periode }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-black text-red-600 text-lg">Rp {{ number_format($tag->nominal) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tahfidz Section --}}
            @if($rekap['tahfidz']->isNotEmpty())
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-emerald-600 font-bold uppercase text-xs tracking-wider">
                    <i class="fa-solid fa-quran"></i> Update Hafalan Terakhir
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($rekap['tahfidz'] as $haf)
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                        <div class="text-xs font-bold text-emerald-800 mb-1">{{ $haf->santri->nama }}</div>
                        <div class="text-sm font-medium text-emerald-700">
                            {{ $haf->surahAwal->nama }} s/d {{ $haf->surahAkhir->nama }}
                        </div>
                        <div class="text-[10px] text-emerald-600 mt-1 italic">{{ $haf->tanggal->format('d M Y') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Kesehatan Section --}}
            @if($rekap['kesehatan']->isNotEmpty())
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-red-600 font-bold uppercase text-xs tracking-wider">
                    <i class="fa-solid fa-heart-pulse"></i> Perhatian — Kondisi Kesehatan Santri
                </div>
                <div class="space-y-3">
                    @foreach($rekap['kesehatan'] as $kes)
                    <div class="p-4 rounded-2xl bg-red-50 border border-red-200">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-bold text-red-900 text-sm">{{ $kes->patient?->nama }}</span>
                                <span class="ml-2 text-[10px] bg-red-200 text-red-800 px-2 py-0.5 rounded-full uppercase font-bold">
                                    {{ $kes->perlu_rujuk ? 'Perlu Dirujuk' : 'Dirawat di Rumah' }}
                                </span>
                            </div>
                            <span class="text-[10px] text-red-500">{{ $kes->tanggal_kunjungan?->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-red-700 mb-1"><strong>Diagnosa:</strong> {{ $kes->diagnosa }}</p>
                        @if($kes->catatan_ortu)
                        <p class="text-xs text-red-600 mb-3"><strong>Pesan ustadz:</strong> {{ $kes->catatan_ortu }}</p>
                        @endif
                        {{-- Tombol konfirmasi langsung dari popup --}}
                        <div class="flex gap-2 mt-2">
                            <form method="POST" action="{{ route('wali.kesehatan.konfirmasi', $kes) }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="status" value="otw">
                                <input type="hidden" name="pesan" value="Siap ustadz, saya sedang dalam perjalanan.">
                                <button type="submit"
                                        class="w-full py-2 px-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-car-side"></i> Siap, Saya OTW
                                </button>
                            </form>
                            <form method="POST" action="{{ route('wali.kesehatan.konfirmasi', $kes) }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="status" value="besok">
                                <input type="hidden" name="pesan" value="Insyaallah besok saya jemput.">
                                <button type="submit"
                                        class="w-full py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-calendar-day"></i> Insyaallah Besok
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="p-6 border-t border-gray-100 bg-gray-50 flex gap-3">
            <button @click="close()" class="flex-1 py-3 px-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-indigo-200">
                Paham, Terima Kasih
            </button>
        </div>
    </div>
</div>
@endif
@endpush

@push('scripts')
<script>
    // Popup rekap — hanya muncul sekali per sesi (bukan setiap buka dashboard)
    function rekapModal() {
        return {
            showRekap: false,
            init() {
                // Key unik berdasarkan konten rekap supaya muncul lagi kalau ada data baru
                const key = 'rekap_shown_{{ md5(json_encode([$rekap["tagihan"]->pluck("id"), $rekap["tahfidz"]->pluck("id"), $rekap["kesehatan"]->pluck("id")])) }}';
                if (!sessionStorage.getItem(key)) {
                    this.showRekap = true;
                    document.body.classList.add('overflow-hidden');
                    sessionStorage.setItem(key, '1');
                }
            },
            close() {
                this.showRekap = false;
            }
        }
    }

    // Reload halaman jika ada pembayaran pending untuk update otomatis
    @if(Auth::user()->wali && \App\Models\Pembayaran::where('wali_id', Auth::user()->wali->id)->where('status', 'pending')->exists())
        setTimeout(function() {
            window.location.reload();
        }, 15000);
    @endif
</script>
@endpush
