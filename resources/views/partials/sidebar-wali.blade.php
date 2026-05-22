<div class="px-4 py-2 mt-4">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Menu</p>
    <a href="{{ route('wali.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('wali.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @feature('marketplace')
    <a href="{{ route('wali.marketplace.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('wali.marketplace.*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-store w-5 text-center"></i>
        <span class="font-medium text-sm">Marketplace</span>
    </a>
    @endfeature
    @feature('voice_note')
    <a href="{{ route('wali.voice-note.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('wali.voice-note.*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-microphone-lines w-5 text-center"></i>
        <span class="font-medium text-sm">Voice Note</span>
    </a>
    @endfeature
    @feature('perizinan')
    <a href="{{ route('wali.perizinan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('wali.perizinan.*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-person-walking-arrow-right w-5 text-center"></i>
        <span class="font-medium text-sm">Perizinan Anak</span>
    </a>
    @endfeature
    @feature('wallet')
    <a href="{{ route('wali.topup.create') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('wali.topup.*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-plus-circle w-5 text-center"></i>
        <span class="font-medium text-sm">Top Up Saldo</span>
    </a>
    @endfeature
    @feature('tagihan')
    @php
        $tunggakanCount = 0;
        if (auth()->user()?->wali) {
            $santriIds = auth()->user()->wali->santri->pluck('id');
            $tunggakanCount = \App\Models\Tagihan::whereIn('santri_id', $santriIds)->where('status', 'belum_bayar')->count();
        }
    @endphp
    <a href="{{ route('wali.tagihan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('wali.tagihan.*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
        <span class="font-medium text-sm flex-1">Tagihan</span>
        @if($tunggakanCount > 0)
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white text-xs font-bold leading-none">{{ $tunggakanCount }}</span>
        @endif
    </a>
    @endfeature
    @feature('wallet')
    <a href="{{ route('wali.limit-uang-saku.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('wali.limit-uang-saku.*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge w-5 text-center"></i>
        <span class="font-medium text-sm">Limit Uang Saku</span>
    </a>
    @endfeature
    @php $firstSantri = auth()->user()->wali?->santri->first(); @endphp
    @if($firstSantri)
    <a href="{{ route('wali.laporan.show', $firstSantri) }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('wali.laporan.*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-chart-line w-5 text-center"></i>
        <span class="font-medium text-sm">Laporan Santri</span>
    </a>
    @endif
    @feature('klinik')
    <a href="{{ route('wali.kesehatan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('wali.kesehatan.*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-heart-pulse w-5 text-center"></i>
        <span class="font-medium text-sm">Kesehatan Anak</span>
    </a>
    @endfeature
    <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('notifications.*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-bell w-5 text-center"></i>
        <span class="font-medium text-sm">Notifikasi</span>
    </a>
</div>

<div class="px-4 py-2 mt-4">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Akun</p>
    <a href="{{ route('auth.change-password') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('auth.change-password') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-key w-5 text-center"></i>
        <span class="font-medium text-sm">Ganti Password</span>
    </a>
</div>

@featureany('quran','prayer')
<div class="px-4 py-2 mt-2 mb-6">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Spiritual</p>
    @feature('quran')
    <a href="{{ route('quran.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('quran*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-book-open-reader w-5 text-center"></i>
        <span class="font-medium text-sm">Al-Quran</span>
    </a>
    @endfeature
    @feature('prayer')
    <a href="{{ route('prayer.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('prayer.index') || request()->routeIs('prayer.calendar') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-clock w-5 text-center"></i>
        <span class="font-medium text-sm">Jadwal Sholat</span>
    </a>
    <a href="{{ route('prayer.qibla') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('prayer.qibla') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-compass w-5 text-center"></i>
        <span class="font-medium text-sm">Arah Kiblat</span>
    </a>
    @endfeature
</div>
@endfeatureany
