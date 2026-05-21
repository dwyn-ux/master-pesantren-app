@extends('layouts.app')
@section('title', 'Detail Voice Note')
@section('page-title', 'Pesan Suara')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="max-w-xl mx-auto">

    {{-- Header --}}
    <div class="rounded-2xl p-6 mb-6 text-white shadow-md"
         style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%)">
        <p class="text-indigo-200 text-sm font-medium mb-1">Pesan Suara</p>
        @if($voiceNote->pengirim_ustadz_id)
            <h3 class="text-xl font-extrabold">Dari Ustadz</h3>
            <p class="text-indigo-100 text-sm mt-1">{{ $voiceNote->pengirimUstadz?->nama ?? '-' }}</p>
        @elseif($voiceNote->pengirim_wali_id)
            <h3 class="text-xl font-extrabold">Dari Wali</h3>
            <p class="text-indigo-100 text-sm mt-1">{{ $voiceNote->pengirimWali?->nama ?? '-' }}</p>
        @else
            <h3 class="text-xl font-extrabold">Pesan Suara</h3>
        @endif
        <p class="text-indigo-200 text-xs mt-2">{{ $voiceNote->created_at->translatedFormat('l, d F Y H:i') }}</p>
    </div>

    {{-- Audio Player --}}
    <div class="glass-panel rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl shadow-sm">
                <i class="fa-solid fa-microphone-lines"></i>
            </div>
            <div>
                <p class="font-bold text-gray-800">Putar Pesan Suara</p>
                <p class="text-sm text-gray-500">Durasi: {{ $voiceNote->durasi_detik }} detik</p>
            </div>
        </div>
        <audio controls class="w-full rounded-xl" autoplay>
            <source src="{{ $voiceNote->audio_url }}" type="audio/mpeg">
            <source src="{{ $voiceNote->audio_url }}" type="audio/ogg">
            <source src="{{ $voiceNote->audio_url }}" type="audio/wav">
            Browser Anda tidak mendukung pemutar audio.
        </audio>
    </div>

    {{-- Info --}}
    <div class="glass-panel rounded-2xl shadow-sm p-6 mb-6">
        <h4 class="font-bold text-gray-700 mb-4">Informasi</h4>
        <dl class="space-y-3 text-sm">
            @if($voiceNote->penerimaSantri)
            <div class="flex justify-between">
                <dt class="text-gray-500">Untuk Santri</dt>
                <dd class="font-semibold text-gray-800">{{ $voiceNote->penerimaSantri->nama }}</dd>
            </div>
            @endif
            @if($voiceNote->konteks)
            <div class="flex justify-between">
                <dt class="text-gray-500">Konteks</dt>
                <dd class="font-semibold text-gray-800 capitalize">{{ $voiceNote->konteks }}</dd>
            </div>
            @endif
            <div class="flex justify-between">
                <dt class="text-gray-500">Dikirim</dt>
                <dd class="font-semibold text-gray-800">{{ $voiceNote->created_at->diffForHumans() }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Status</dt>
                <dd>
                    @if($voiceNote->is_read)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-bold">Sudah Dibaca</span>
                    @else
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">Belum Dibaca</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    <a href="{{ route('wali.voice-note.index') }}"
       class="flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar pesan suara
    </a>
</div>
@endsection
