{{-- Sidebar: Kepala Pondok --}}
<div class="px-4 py-3 mt-4">
    <p class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider mb-3 ml-1">Menu Kepala Pondok</p>
    <div class="space-y-1">
        <a href="{{ route('kepala-pondok.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('kepala-pondok.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-gauge-high w-5 text-center"></i>
            <span class="font-medium text-sm">Dashboard</span>
        </a>
    </div>
</div>

<div class="px-4 py-2 mt-2">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Monitoring</p>
    <div class="space-y-1">
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all text-indigo-100 hover:bg-white/10">
            <i class="fa-solid fa-chart-column w-5 text-center"></i>
            <span class="font-medium text-sm">Laporan</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all text-indigo-100 hover:bg-white/10">
            <i class="fa-solid fa-user-graduate w-5 text-center"></i>
            <span class="font-medium text-sm">Data Santri</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all text-indigo-100 hover:bg-white/10">
            <i class="fa-solid fa-book-quran w-5 text-center"></i>
            <span class="font-medium text-sm">Halaqah</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all text-indigo-100 hover:bg-white/10">
            <i class="fa-solid fa-money-bill-trend-up w-5 text-center"></i>
            <span class="font-medium text-sm">Keuangan</span>
        </a>
    </div>
</div>

<div class="px-4 py-2 mt-2 mb-6">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Spiritual</p>
    <div class="space-y-1">
        <a href="{{ route('quran.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('quran*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-book-open-reader w-5 text-center"></i>
            <span class="font-medium text-sm">Al-Qur'an</span>
        </a>
        <a href="{{ route('prayer.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('prayer*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-mosque w-5 text-center"></i>
            <span class="font-medium text-sm">Jadwal Sholat</span>
        </a>
    </div>
</div>
