{{-- Sidebar: Ustadz --}}
<div class="px-4 py-3 mt-4">
    <p class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider mb-3 ml-1">Menu Utama</p>
    <div class="space-y-1">
        <a href="{{ route('ustadz.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-gauge-high w-5 text-center"></i>
            <span class="font-medium text-sm">Dashboard</span>
        </a>
    </div>
</div>

@feature('halaqah')
<div class="px-4 py-2 mt-2">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Akademik</p>
    <div class="space-y-1">
        <a href="{{ route('ustadz.halaqah.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.halaqah*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-book-quran w-5 text-center"></i>
            <span class="font-medium text-sm">Halaqah Saya</span>
        </a>
        <a href="{{ route('ustadz.santri.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.santri*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-users w-5 text-center"></i>
            <span class="font-medium text-sm">Daftar Santri</span>
        </a>
        <a href="{{ route('ustadz.setoran.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.setoran.index') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-scroll w-5 text-center"></i>
            <span class="font-medium text-sm">Setoran & Hafalan</span>
        </a>
        <a href="{{ route('ustadz.setoran-umum.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.setoran-umum*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-users-rectangle w-5 text-center"></i>
            <span class="font-medium text-sm">Setoran Umum</span>
        </a>
        @feature('perizinan')
        <a href="{{ route('ustadz.perizinan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.perizinan*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-person-walking-arrow-right w-5 text-center"></i>
            <span class="font-medium text-sm">Perizinan Halaqah</span>
        </a>
        @endfeature
    </div>
</div>
@endfeature

@featureany('akademik_master','akademik_absensi','akademik_penilaian','halaqah_diniyah')
<div class="px-4 py-2 mt-2">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Akademik Diniyah</p>
    <div class="space-y-1">
        @feature('akademik_absensi')
        <a href="{{ route('ustadz.akademik.absensi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.akademik.absensi*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-clipboard-check w-5 text-center"></i>
            <span class="font-medium text-sm">Absensi & Jurnal</span>
        </a>
        @endfeature
        @feature('akademik_penilaian')
        <a href="{{ route('ustadz.akademik.nilai.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.akademik.nilai*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-pen-to-square w-5 text-center"></i>
            <span class="font-medium text-sm">Input Nilai</span>
        </a>
        @endfeature
        @feature('halaqah_diniyah')
        <a href="{{ route('ustadz.akademik.halaqah-diniyah.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.akademik.halaqah-diniyah*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-book-quran w-5 text-center"></i>
            <span class="font-medium text-sm">Halaqah Diniyah</span>
        </a>
        @endfeature
        @feature('akademik_raport')
        <a href="{{ route('ustadz.akademik.wali-kelas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.akademik.wali-kelas*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-user-tie w-5 text-center"></i>
            <span class="font-medium text-sm">Wali Kelas (Sikap)</span>
        </a>
        @endfeature
    </div>
</div>
@endfeatureany

@featureany('klinik','voice_note')
<div class="px-4 py-2 mt-2 mb-6">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Kesehatan</p>
    <div class="space-y-1">
        @feature('klinik')
        <a href="{{ route('ustadz.klinik.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.klinik*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-stethoscope w-5 text-center"></i>
            <span class="font-medium text-sm">Klinik (Rekam Medis)</span>
        </a>
        @endfeature
        @feature('voice_note')
        <a href="{{ route('ustadz.voice-note.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('ustadz.voice-note*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-microphone w-5 text-center"></i>
            <span class="font-medium text-sm">Voice Note ke Wali</span>
        </a>
        @endfeature
    </div>
</div>
@endfeatureany

@featureany('quran','prayer')
<div class="px-4 py-2 mt-2 mb-6">
    <p class="text-xs font-bold text-indigo-200/70 uppercase tracking-wider mb-2">Spiritual</p>
    <div class="space-y-1">
        @feature('quran')
        <a href="{{ route('quran.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('quran*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-book-open-reader w-5 text-center"></i>
            <span class="font-medium text-sm">Al-Qur'an</span>
        </a>
        @endfeature
        @feature('prayer')
        <a href="{{ route('prayer.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('prayer*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
            <i class="fa-solid fa-mosque w-5 text-center"></i>
            <span class="font-medium text-sm">Jadwal Sholat</span>
        </a>
        @endfeature
    </div>
</div>
@endfeatureany
