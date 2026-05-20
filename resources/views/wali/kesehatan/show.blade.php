@extends('layouts.app')
@section('title', 'Detail Kesehatan ' . $kunjungan->patient?->nama)
@section('page-title', 'Detail Kunjungan Klinik')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="rounded-2xl shadow-md p-6 mb-6 text-white"
    style="background: linear-gradient(135deg, {{ $kunjungan->perlu_rujuk ? '#dc2626 0%, #991b1b' : ($kunjungan->perlu_dirawat_ortu ? '#d97706 0%, #92400e' : '#0f766e 0%, #115e59') }} 100%)">
    <p class="text-white/70 text-sm font-medium">Kunjungan Klinik</p>
    <h3 class="text-2xl font-extrabold">{{ $kunjungan->patient?->nama }}</h3>
    <p class="text-white/80 text-sm mt-1">{{ $kunjungan->tanggal_kunjungan?->translatedFormat('l, d F Y H:i') }}</p>
</div>

@if($kunjungan->perlu_rujuk || $kunjungan->perlu_dirawat_ortu)
<div class="rounded-2xl p-5 mb-6 {{ $kunjungan->perlu_rujuk ? 'bg-red-50 border border-red-200' : 'bg-amber-50 border border-amber-200' }}">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-full {{ $kunjungan->perlu_rujuk ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold {{ $kunjungan->perlu_rujuk ? 'text-red-700' : 'text-amber-700' }}">
                {{ $kunjungan->perlu_rujuk ? 'Perlu Dirujuk ke Faskes' : 'Disarankan Dirawat di Rumah' }}
            </h4>
            @if($kunjungan->catatan_ortu)
                <p class="text-sm text-gray-700 mt-1">{{ $kunjungan->catatan_ortu }}</p>
            @endif

            {{-- Status konfirmasi --}}
            @if($kunjungan->wali_konfirmasi)
                <div class="mt-3 flex items-center gap-2 text-sm font-semibold {{ $kunjungan->wali_konfirmasi === 'otw' ? 'text-amber-700' : 'text-blue-700' }}">
                    <i class="fa-solid {{ $kunjungan->wali_konfirmasi === 'otw' ? 'fa-car-side' : 'fa-calendar-day' }}"></i>
                    {{ $kunjungan->wali_konfirmasi === 'otw' ? 'Anda sudah konfirmasi: Sedang dalam perjalanan' : 'Anda sudah konfirmasi: Insyaallah besok' }}
                    <span class="text-xs font-normal text-gray-500">({{ $kunjungan->wali_konfirmasi_at?->diffForHumans() }})</span>
                </div>
                @if($kunjungan->wali_konfirmasi_pesan)
                    <p class="text-xs text-gray-500 mt-1 italic">"{{ $kunjungan->wali_konfirmasi_pesan }}"</p>
                @endif
            @else
                {{-- Tombol konfirmasi --}}
                <div class="mt-4 flex gap-2">
                    <form method="POST" action="{{ route('wali.kesehatan.konfirmasi', $kunjungan) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="status" value="otw">
                        <input type="hidden" name="pesan" value="Siap ustadz, saya sedang dalam perjalanan.">
                        <button type="submit"
                                class="w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-car-side"></i> Siap, Saya OTW
                        </button>
                    </form>
                    <form method="POST" action="{{ route('wali.kesehatan.konfirmasi', $kunjungan) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="status" value="besok">
                        <input type="hidden" name="pesan" value="Insyaallah besok saya jemput.">
                        <button type="submit"
                                class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-calendar-day"></i> Insyaallah, Besok
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="glass-panel rounded-2xl shadow-sm p-6">
        <h4 class="font-bold text-gray-700 mb-4">Informasi Pemeriksaan</h4>
        <dl class="space-y-3 text-sm">
            <div><dt class="text-gray-500 text-xs uppercase">Keluhan</dt><dd class="text-gray-800 mt-1">{{ $kunjungan->keluhan }}</dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">Diagnosa</dt><dd class="text-gray-800 mt-1">{{ $kunjungan->diagnosa }}</dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">Tindakan / Obat</dt><dd class="text-gray-800 mt-1">{{ $kunjungan->tindakan_obat }}</dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">Status Pengobatan</dt><dd class="text-gray-800 mt-1">{{ str_replace('_', ' ', $kunjungan->status_pengobatan) }}</dd></div>
            @if($kunjungan->lama_istirahat_hari)
                <div><dt class="text-gray-500 text-xs uppercase">Lama Istirahat</dt><dd class="text-gray-800 mt-1">{{ $kunjungan->lama_istirahat_hari }} hari</dd></div>
            @endif
            <div><dt class="text-gray-500 text-xs uppercase">Diperiksa Oleh</dt><dd class="text-gray-800 mt-1">{{ $kunjungan->pemeriksa?->nama ?? '-' }}</dd></div>
        </dl>
    </div>

    <div class="glass-panel rounded-2xl shadow-sm p-6">
        <h4 class="font-bold text-gray-700 mb-4">Pesan Suara Terkait</h4>
        @forelse($kunjungan->voiceNotes as $vn)
            <div class="border border-gray-100 rounded-xl p-4 mb-3">
                <div class="flex items-center justify-between mb-2">
                    <p class="font-semibold text-sm text-gray-700">{{ $vn->pengirimUstadz?->nama ?? 'Ustadz' }}</p>
                    <p class="text-xs text-gray-400">{{ $vn->created_at->diffForHumans() }}</p>
                </div>
                <audio controls class="w-full"><source src="{{ $vn->audio_url }}"></audio>
                <p class="text-xs text-gray-400 mt-2">{{ $vn->durasi_detik }}s · konteks: {{ $vn->konteks ?? 'umum' }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-6">Belum ada pesan suara dari ustadz.</p>
        @endforelse
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('wali.kesehatan.index') }}" class="text-sm text-gray-500 hover:text-indigo-600"><i class="fa-solid fa-arrow-left"></i> Kembali ke daftar kesehatan</a>
</div>
@endsection
