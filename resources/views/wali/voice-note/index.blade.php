@extends('layouts.app')
@section('title', 'Voice Note')
@section('page-title', 'Pesan Suara (Voice Note)')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-indigo-500 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-indigo-500/10 transition-transform group-hover:scale-110">
            <i class="fa-solid fa-wallet text-8xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Saldo Anda</p>
            <h3 class="text-2xl font-extrabold text-indigo-600">Rp {{ number_format(Auth::user()->wali->saldo ?? 0) }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-amber-500 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-amber-500/10 transition-transform group-hover:scale-110">
            <i class="fa-solid fa-microphone-lines text-8xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Biaya Voice Note</p>
            <h3 class="text-xl font-bold text-amber-600">Rp 100 <span class="text-sm font-normal text-gray-500">per detik</span></h3>
            <p class="text-xs text-gray-500 mt-1">Maksimal durasi 5 menit (300 detik)</p>
        </div>
    </div>
</div>

<div class="glass-panel rounded-2xl shadow-sm overflow-hidden mb-8" x-data="{ tab: 'sent' }">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex gap-2">
            <button @click="tab = 'sent'" :class="tab === 'sent' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-5 py-2.5 rounded-xl font-medium transition-all flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i> Terkirim ({{ $sentVoiceNotes->count() }})
            </button>
            <button @click="tab = 'received'" :class="tab === 'received' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-5 py-2.5 rounded-xl font-medium transition-all flex items-center gap-2">
                <i class="fa-solid fa-inbox"></i> Diterima ({{ $receivedVoiceNotes->count() }})
            </button>
        </div>
        <a href="{{ route('wali.voice-note.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-microphone"></i> Kirim Baru
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mx-6 mt-6 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="p-4 mx-6 mt-6 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="p-6">
        {{-- Sent Tab --}}
        <div x-show="tab === 'sent'" x-transition>
            @if($sentVoiceNotes->isEmpty())
                <div class="py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                        <i class="fa-solid fa-paper-plane text-3xl text-gray-300"></i>
                    </div>
                    <h5 class="text-lg font-bold text-gray-700 mb-2">Belum ada voice note terkirim</h5>
                    <p class="text-gray-500 mb-6 max-w-sm">Mulai kirim pesan suara ke santri atau wali lain.</p>
                    <a href="{{ route('wali.voice-note.create') }}" class="px-6 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl font-medium shadow-sm transition-colors">
                        Kirim Voice Note Pertama
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($sentVoiceNotes as $voiceNote)
                        <div class="p-5 rounded-2xl border border-gray-200 bg-gray-50/50 hover:bg-white transition-colors group">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                        <i class="fa-solid fa-microphone"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-bold text-gray-800 text-sm">Ke: {{ $voiceNote->penerimaSantri ? $voiceNote->penerimaSantri->nama : 'Wali' }}</h6>
                                        <span class="text-xs text-gray-500">{{ $voiceNote->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase tracking-wider">
                                    <i class="fa-solid fa-check mr-1"></i> Terkirim
                                </span>
                            </div>
                            
                            <audio controls class="w-full h-10 mb-3 outline-none rounded-full shadow-sm">
                                <source src="{{ $voiceNote->audio_url }}" type="audio/mpeg">
                            </audio>
                            
                            <div class="flex items-center justify-between text-xs text-gray-600 bg-white px-3 py-2 rounded-lg border border-gray-100">
                                <span class="font-medium"><i class="fa-regular fa-clock mr-1"></i> {{ $voiceNote->durasi_detik }} detik</span>
                                <span class="font-bold text-indigo-600">Rp {{ number_format($voiceNote->biaya) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Received Tab --}}
        <div x-show="tab === 'received'" x-transition style="display: none;">
            @if($receivedVoiceNotes->isEmpty())
                <div class="py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                        <i class="fa-solid fa-inbox text-3xl text-gray-300"></i>
                    </div>
                    <h5 class="text-lg font-bold text-gray-700 mb-2">Kotak Masuk Kosong</h5>
                    <p class="text-gray-500">Belum ada voice note yang Anda terima.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($receivedVoiceNotes as $voiceNote)
                        <div class="p-5 rounded-2xl border {{ !$voiceNote->is_read ? 'border-indigo-300 bg-indigo-50/30' : 'border-gray-200 bg-gray-50/50' }} hover:bg-white transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                        <i class="fa-solid fa-microphone-lines"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-bold text-gray-800 text-sm">Dari: {{ $voiceNote->pengirimSantri ? $voiceNote->pengirimSantri->nama : 'Wali' }}</h6>
                                        <span class="text-xs text-gray-500">{{ $voiceNote->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                @if(!$voiceNote->is_read)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 uppercase tracking-wider">
                                        Baru
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-wider">
                                        Dibaca
                                    </span>
                                @endif
                            </div>
                            
                            <audio controls class="w-full h-10 mb-3 outline-none rounded-full shadow-sm">
                                <source src="{{ $voiceNote->audio_url }}" type="audio/mpeg">
                            </audio>
                            
                            <div class="text-xs text-gray-600 bg-white px-3 py-2 rounded-lg border border-gray-100">
                                <span class="font-medium"><i class="fa-regular fa-clock mr-1"></i> Durasi: {{ $voiceNote->durasi_detik }} detik</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection