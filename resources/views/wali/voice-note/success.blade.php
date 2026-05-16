@extends('layouts.app')
@section('title', 'Voice Note Terkirim')
@section('page-title', 'Terkirim')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-3xl mx-auto">
    <div class="p-8 text-center bg-gradient-to-b from-emerald-50/50 to-transparent border-b border-gray-100">
        <div class="w-24 h-24 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center text-5xl mx-auto mb-6 shadow-sm border-4 border-white">
            <i class="fa-solid fa-microphone-lines text-4xl"></i>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-800 mb-3">Pesan Suara Terkirim!</h2>
        <p class="text-gray-500 max-w-lg mx-auto text-lg leading-relaxed">
            Voice note Anda telah berhasil dikirim dan akan segera disampaikan kepada penerima.
        </p>
    </div>

    <div class="p-6 sm:p-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <div class="p-5 rounded-xl border border-gray-200 bg-gray-50/50 flex flex-col justify-center items-center text-center">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Durasi Rekaman</div>
                <div class="text-2xl font-bold text-indigo-700">{{ $voiceNote->durasi_detik }} <span class="text-sm font-medium text-gray-500">detik</span></div>
            </div>
            
            <div class="p-5 rounded-xl border border-amber-200 bg-amber-50/50 flex flex-col justify-center items-center text-center">
                <div class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2">Biaya Pengiriman</div>
                <div class="text-2xl font-extrabold text-amber-600">Rp {{ number_format($voiceNote->biaya) }}</div>
            </div>
        </div>

        <div class="p-6 rounded-2xl border border-gray-100 bg-white shadow-sm mb-8 text-center sm:text-left">
            <h6 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center justify-center sm:justify-start gap-2">
                <i class="fa-solid fa-paper-plane"></i> Tujuan Pengiriman
            </h6>
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600 flex items-center justify-center font-bold text-2xl shadow-sm border border-indigo-200">
                    @if($voiceNote->penerimaSantri)
                        {{ substr($voiceNote->penerimaSantri->nama, 0, 1) }}
                    @else
                        W
                    @endif
                </div>
                <div>
                    @if($voiceNote->penerimaSantri)
                        <h4 class="text-xl font-bold text-gray-800">{{ $voiceNote->penerimaSantri->nama }}</h4>
                        <div class="text-sm text-gray-500 font-mono mt-1 border border-gray-200 px-2 py-0.5 rounded inline-block bg-gray-50">{{ $voiceNote->penerimaSantri->nis }}</div>
                    @else
                        <h4 class="text-xl font-bold text-gray-800">Wali Santri</h4>
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-8">
            <h6 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Preview Audio</h6>
            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200">
                <audio controls class="w-full h-12 outline-none rounded-full bg-white shadow-sm">
                    <source src="{{ $voiceNote->audio_url }}" type="audio/mpeg">
                </audio>
            </div>
        </div>

        <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-5 flex gap-4 text-blue-800 mb-8">
            <i class="fa-solid fa-circle-info text-blue-500 text-2xl mt-0.5"></i>
            <div>
                <p class="text-sm leading-relaxed">
                    Voice note akan disimpan dengan aman di sistem pesantren dan dapat diakses selama <strong>30 hari ke depan</strong>.
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('wali.voice-note.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors text-center flex justify-center items-center gap-2">
                <i class="fa-solid fa-list"></i> Riwayat Voice Note
            </a>
            <a href="{{ route('wali.voice-note.create') }}" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm text-center flex justify-center items-center gap-2">
                <i class="fa-solid fa-microphone"></i> Kirim Pesan Baru
            </a>
        </div>
    </div>
</div>
@endsection