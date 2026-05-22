    <a href="{{ route('bendahara.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('bendahara.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>

    @feature('finance')
    <a href="{{ route('finance.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-2 rounded-xl transition-all bg-gradient-to-r from-emerald-600/40 to-indigo-600/40 text-white hover:from-emerald-600/60 hover:to-indigo-600/60 ring-1 ring-emerald-400/30">
        <i class="fa-solid fa-chart-pie w-5 text-center text-emerald-300"></i>
        <span class="font-bold text-sm">Aplikasi Finance</span>
        <span class="ml-auto text-[10px] bg-emerald-400 text-emerald-900 px-1.5 py-0.5 rounded font-bold">NEW</span>
    </a>
    @endfeature

    @feature('tagihan')
    <div class="px-4 py-2 mt-6 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Laporan Keuangan</div>

    <a href="{{ route('bendahara.pembayaran') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('bendahara.pembayaran*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-receipt w-5 text-center"></i>
        <span class="font-medium text-sm">Daftar Pembayaran</span>
    </a>

    <a href="{{ route('bendahara.tagihan') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('bendahara.tagihan*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
        <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
        <span class="font-medium text-sm">Daftar Tagihan</span>
    </a>
    @endfeature

    <div class="px-4 py-2 mt-6 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Administrasi</div>

    @feature('perizinan')
    <a href="{{ route('bendahara.kepulangan.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('bendahara.kepulangan*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-file-signature w-5 text-center"></i>
        <span class="font-medium text-sm">ACC Surat Kesanggupan</span>
    </a>
    @endfeature
    @feature('wallet')
    <a href="{{ route('bendahara.limit-uang-saku.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('bendahara.limit-uang-saku*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
        <i class="fa-solid fa-gauge w-5 text-center"></i>
        <span class="font-medium text-sm">Limit Uang Saku</span>
    </a>
    <a href="{{ route('bendahara.topup.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('bendahara.topup*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
        <i class="fa-solid fa-money-bill-transfer w-5 text-center"></i>
        <span class="font-medium text-sm">Top Up Saldo</span>
        @php $pending = \App\Models\TopUpRequest::pending()->count(); @endphp
        @if($pending > 0)
        <span class="ml-auto px-1.5 py-0.5 bg-red-500 text-white text-xs rounded-full font-bold">{{ $pending }}</span>
        @endif
    </a>
    @endfeature

    <div class="px-4 py-2 mt-6 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Laporan</div>

    @feature('halaqah')
    <a href="{{ route('laporan.halaqah') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('laporan.halaqah') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-book-quran w-5 text-center"></i>
        <span class="font-medium text-sm">Laporan Halaqah</span>
    </a>
    @endfeature
    @feature('kantin')
    <a href="{{ route('laporan.kantin') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('laporan.kantin') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
        <i class="fa-solid fa-utensils w-5 text-center"></i>
        <span class="font-medium text-sm">Laporan Kantin</span>
    </a>
    @endfeature
    @feature('laundry')
    <a href="{{ route('laporan.laundry') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('laporan.laundry') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
        <i class="fa-solid fa-shirt w-5 text-center"></i>
        <span class="font-medium text-sm">Laporan Laundry</span>
    </a>
    @endfeature

@featureany('quran','prayer')
<div class="px-4 py-2 mt-2 mb-6">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Spiritual</p>
    @feature('quran')
    <a href="{{ route('quran.index') }}" class="flex items-center gap-3 px-4 py-2 mx-0 rounded-xl transition-all {{ request()->routeIs('quran*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-book-open-reader w-5 text-center"></i>
        <span class="font-medium text-sm">Al-Quran</span>
    </a>
    @endfeature
    @feature('prayer')
    <a href="{{ route('prayer.index') }}" class="flex items-center gap-3 px-4 py-2 mx-0 rounded-xl transition-all {{ request()->routeIs('prayer.index') || request()->routeIs('prayer.calendar') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-clock w-5 text-center"></i>
        <span class="font-medium text-sm">Jadwal Sholat</span>
    </a>
    <a href="{{ route('prayer.qibla') }}" class="flex items-center gap-3 px-4 py-2 mx-0 rounded-xl transition-all {{ request()->routeIs('prayer.qibla') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-compass w-5 text-center"></i>
        <span class="font-medium text-sm">Arah Kiblat</span>
    </a>
    @endfeature
</div>
@endfeatureany
