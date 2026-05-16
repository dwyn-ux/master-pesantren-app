@extends('layouts.app')
@section('title', 'Detail Voice Note')
@section('page-title', 'Detail Voice Note')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm p-6 max-w-2xl">
    <h4 class="font-bold text-gray-700 mb-4">Voice Note ke {{ $voiceNote->penerimaWali?->nama }}</h4>

    <dl class="space-y-3 text-sm mb-5">
        <div><dt class="text-gray-500 text-xs uppercase">Santri</dt><dd class="text-gray-800">{{ $voiceNote->penerimaSantri?->nama }}</dd></div>
        <div><dt class="text-gray-500 text-xs uppercase">Tanggal</dt><dd class="text-gray-800">{{ $voiceNote->created_at->translatedFormat('l, d F Y H:i') }}</dd></div>
        <div><dt class="text-gray-500 text-xs uppercase">Durasi</dt><dd class="text-gray-800">{{ $voiceNote->durasi_detik }} detik</dd></div>
        <div><dt class="text-gray-500 text-xs uppercase">Konteks</dt><dd class="text-gray-800">{{ $voiceNote->konteks ?? 'umum' }}</dd></div>
        <div><dt class="text-gray-500 text-xs uppercase">Status Baca</dt><dd>
            @if($voiceNote->is_read)
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700">Sudah Dibaca</span>
            @else
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-600">Belum Dibaca</span>
            @endif
        </dd></div>
    </dl>

    <audio src="{{ $voiceNote->audio_url }}" controls class="w-full"></audio>

    <a href="{{ route('ustadz.voice-note.index') }}" class="text-sm text-gray-500 hover:text-teal-600 inline-block mt-4">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
    </a>
</div>
@endsection
