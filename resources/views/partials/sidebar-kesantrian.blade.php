{{-- Sidebar: Kesantrian --}}
<div class="px-4 py-3 mt-4">
    <p class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider mb-3 ml-1">Menu Utama</p>
    <div class="space-y-1">
        <a href="{{ route('kesantrian.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('kesantrian.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-gauge-high w-5 text-center"></i>
            <span class="font-medium text-sm">Dashboard</span>
        </a>
    </div>
</div>

@feature('perizinan')
<div class="px-4 py-2 mt-2 mb-2">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Perizinan</p>
    <div class="space-y-1">
        <a href="{{ route('kesantrian.perizinan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('kesantrian.perizinan*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-person-walking-arrow-right w-5 text-center"></i>
            <span class="font-medium text-sm">Approval Perizinan</span>
        </a>
    </div>
</div>
@endfeature

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
