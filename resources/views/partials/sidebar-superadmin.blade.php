<div class="px-4 py-2 mt-4">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Superadmin</p>
    <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('superadmin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-shield-halved w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    <a href="{{ route('superadmin.features.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('superadmin.features*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-sliders w-5 text-center"></i>
        <span class="font-medium text-sm">Manajemen Fitur</span>
    </a>
    <a href="{{ route('superadmin.staff-password.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('superadmin.staff-password*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-key w-5 text-center"></i>
        <span class="font-medium text-sm">Password Staff</span>
    </a>
</div>

<div class="px-4 py-2 mt-2">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Akses Cepat</p>
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all text-indigo-100 hover:bg-white/10">
        <i class="fa-solid fa-gauge w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard Admin</span>
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
