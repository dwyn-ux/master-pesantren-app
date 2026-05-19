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
