<div class="px-4 py-2 mt-4">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Master Data</p>
    <a href="{{ route('admin.santri.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.santri*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-users w-5 text-center"></i>
        <span class="font-medium text-sm">Santri</span>
    </a>
    <a href="{{ route('admin.wali.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.wali*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-user-group w-5 text-center"></i>
        <span class="font-medium text-sm">Wali</span>
    </a>
    <a href="{{ route('admin.ustadz.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.ustadz*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-chalkboard-user w-5 text-center"></i>
        <span class="font-medium text-sm">Ustadz</span>
    </a>
    @feature('halaqah')
    <a href="{{ route('admin.halaqah.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.halaqah*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-book-quran w-5 text-center"></i>
        <span class="font-medium text-sm">Halaqah</span>
    </a>
    @endfeature
    @feature('perizinan')
    <a href="{{ route('admin.perizinan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.perizinan*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-person-walking-arrow-right w-5 text-center"></i>
        <span class="font-medium text-sm">Perizinan Insidental</span>
    </a>
    <a href="{{ route('admin.sesi-kepulangan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.sesi-kepulangan*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-bus w-5 text-center"></i>
        <span class="font-medium text-sm">Sesi Kepulangan</span>
    </a>
    @endfeature
</div>

@feature('akademik_master')
<div class="px-4 py-2 mt-2">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Akademik Diniyah</p>
    <a href="{{ route('admin.akademik.tahun-ajaran.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.akademik.tahun-ajaran*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-calendar-days w-5 text-center"></i>
        <span class="font-medium text-sm">Tahun Ajaran</span>
    </a>
    <a href="{{ route('admin.akademik.tingkat.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.akademik.tingkat*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-layer-group w-5 text-center"></i>
        <span class="font-medium text-sm">Tingkat / Marhalah</span>
    </a>
    <a href="{{ route('admin.akademik.kelas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.akademik.kelas*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-school w-5 text-center"></i>
        <span class="font-medium text-sm">Kelas / Rombel</span>
    </a>
    <a href="{{ route('admin.akademik.mata-pelajaran.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.akademik.mata-pelajaran*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-book-bookmark w-5 text-center"></i>
        <span class="font-medium text-sm">Mata Pelajaran</span>
    </a>
    <a href="{{ route('admin.akademik.jadwal-pelajaran.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.akademik.jadwal-pelajaran*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-calendar w-5 text-center"></i>
        <span class="font-medium text-sm">Jadwal Pelajaran</span>
    </a>
    @feature('akademik_penilaian')
    <a href="{{ route('admin.akademik.komponen-nilai.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.akademik.komponen-nilai*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-percent w-5 text-center"></i>
        <span class="font-medium text-sm">Komponen Nilai</span>
    </a>
    <a href="{{ route('admin.akademik.kkm.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.akademik.kkm*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">KKM</span>
    </a>
    @endfeature
    @feature('akademik_raport')
    <a href="{{ route('admin.akademik.raport.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.akademik.raport*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-file-pdf w-5 text-center"></i>
        <span class="font-medium text-sm">Raport</span>
    </a>
    @endfeature
    @feature('akademik_kenaikan')
    <a href="{{ route('admin.akademik.kenaikan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.akademik.kenaikan*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-arrow-up-from-bracket w-5 text-center"></i>
        <span class="font-medium text-sm">Kenaikan Kelas</span>
    </a>
    @endfeature
</div>
@endfeature

@feature('tagihan')
<div class="px-4 py-2 mt-2">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Keuangan</p>
    <a href="{{ route('admin.jenis-tagihan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.jenis-tagihan*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-tags w-5 text-center"></i>
        <span class="font-medium text-sm">Jenis Tagihan</span>
    </a>
    <a href="{{ route('admin.tagihan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.tagihan*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-receipt w-5 text-center"></i>
        <span class="font-medium text-sm">Tagihan</span>
    </a>
    <a href="{{ route('admin.pembayaran.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.pembayaran*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-wallet w-5 text-center"></i>
        <span class="font-medium text-sm">Pembayaran</span>
    </a>
</div>
@endfeature

@feature('marketplace')
<div class="px-4 py-2 mt-2">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Marketplace</p>
    <a href="{{ route('admin.marketplace.orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.marketplace.orders*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-bag-shopping w-5 text-center"></i>
        <span class="font-medium text-sm">Pesanan</span>
    </a>
</div>
@endfeature

<div class="px-4 py-2 mt-2">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Utilitas</p>
    @feature('fingerprint')
    <a href="{{ route('admin.fingerprint.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.fingerprint*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-fingerprint w-5 text-center"></i>
        <span class="font-medium text-sm">Fingerprint</span>
    </a>
    @endfeature
    @feature('rfid')
    <a href="{{ route('admin.rfid.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.rfid*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-id-card w-5 text-center"></i>
        <span class="font-medium text-sm">Kartu RFID</span>
    </a>
    @endfeature
    @feature('tagihan')
    <a href="{{ route('admin.payment-settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.payment-settings*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-money-check-dollar w-5 text-center"></i>
        <span class="font-medium text-sm">Payment Gateway</span>
    </a>
    @endfeature
    @feature('import_excel')
    <a href="{{ route('admin.import.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.import*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-file-excel w-5 text-center"></i>
        <span class="font-medium text-sm">Import Excel</span>
    </a>
    @endfeature
    @feature('laporan_otomatis')
    <a href="{{ route('admin.report-settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.report-settings*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-calendar-check w-5 text-center"></i>
        <span class="font-medium text-sm">Laporan Otomatis</span>
    </a>
    @endfeature
    @feature('tagihan')
    <a href="{{ route('admin.laporan.keuangan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.laporan.keuangan') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-sack-dollar w-5 text-center"></i>
        <span class="font-medium text-sm">Laporan Keuangan</span>
    </a>
    @endfeature
    @feature('halaqah')
    <a href="{{ route('laporan.halaqah') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('laporan.halaqah') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-book-quran w-5 text-center"></i>
        <span class="font-medium text-sm">Laporan Halaqah</span>
    </a>
    @endfeature
    @feature('kantin')
    <a href="{{ route('laporan.kantin') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('laporan.kantin') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-utensils w-5 text-center"></i>
        <span class="font-medium text-sm">Laporan Kantin</span>
    </a>
    @endfeature
    @feature('laundry')
    <a href="{{ route('laporan.laundry') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('laporan.laundry') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-shirt w-5 text-center"></i>
        <span class="font-medium text-sm">Laporan Laundry</span>
    </a>
    @endfeature
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
    <a href="{{ route('prayer.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('prayer*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-compass w-5 text-center"></i>
        <span class="font-medium text-sm">Jadwal Sholat</span>
    </a>
    @endfeature
</div>
@endfeatureany
