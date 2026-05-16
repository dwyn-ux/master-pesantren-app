<div class="px-4 py-2 mt-4">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Outlet</p>
    <a href="{{ route('outlet.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('outlet.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
</div>

@if(auth()->user()->outlet?->tipe === 'kantin')
    @feature('kantin')
    <div class="px-4 py-2 mt-2">
        <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Kantin</p>
        <a href="{{ route('outlet.kasir.kantin.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('outlet.kasir.kantin.index') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-cash-register w-5 text-center"></i>
            <span class="font-medium text-sm">Kasir Kantin</span>
        </a>
        <a href="{{ route('outlet.kasir.kantin.history') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('outlet.kasir.kantin.history') || request()->routeIs('outlet.kasir.kantin.show') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i>
            <span class="font-medium text-sm">Riwayat Kasir</span>
        </a>
        <a href="{{ route('outlet.produk.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('outlet.produk*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-boxes-stacked w-5 text-center"></i>
            <span class="font-medium text-sm">Produk & Stok</span>
        </a>
    </div>
    @endfeature
    @feature('marketplace')
    <div class="px-4 py-2 mt-2">
        <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Marketplace</p>
        <a href="{{ route('outlet.marketplace.orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('outlet.marketplace.orders*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-bag-shopping w-5 text-center"></i>
            <span class="font-medium text-sm">Pesanan Marketplace</span>
        </a>
    </div>
    @endfeature
@elseif(auth()->user()->outlet?->tipe === 'laundry')
    @feature('laundry')
    <div class="px-4 py-2 mt-2">
        <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Laundry</p>
        <a href="{{ route('outlet.laundry.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('outlet.laundry.index') || request()->routeIs('outlet.laundry.show') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-basket-shopping w-5 text-center"></i>
            <span class="font-medium text-sm">Order Laundry</span>
        </a>
        <a href="{{ route('outlet.laundry.create') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('outlet.laundry.create') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-circle-plus w-5 text-center"></i>
            <span class="font-medium text-sm">Terima Laundry</span>
        </a>
    </div>
    @endfeature
@endif

<div class="px-4 py-2 mt-4">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Akun</p>
    <a href="{{ route('auth.change-password') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('auth.change-password') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-key w-5 text-center"></i>
        <span class="font-medium text-sm">Ganti Password</span>
    </a>
</div>
