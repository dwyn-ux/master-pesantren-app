@extends('layouts.app')
@section('title', 'Status Pembayaran')
@section('page-title', 'Status Top Up Saldo')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="max-w-lg mx-auto">

@php
    $isPaid    = $topUp->status === 'paid';
    $isExpired = $topUp->status === 'expired';
    $isFailed  = $topUp->status === 'failed';
    $isUnpaid  = $topUp->status === 'unpaid';
@endphp

<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    {{-- Status banner --}}
    <div class="px-6 py-8 text-center
        {{ $isPaid ? 'bg-emerald-50' : ($isExpired || $isFailed ? 'bg-red-50' : 'bg-amber-50') }}">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4
            {{ $isPaid ? 'bg-emerald-100 text-emerald-600' : ($isExpired || $isFailed ? 'bg-red-100 text-red-500' : 'bg-amber-100 text-amber-600') }}">
            <i class="text-3xl fa-solid
                {{ $isPaid ? 'fa-circle-check' : ($isExpired || $isFailed ? 'fa-circle-xmark' : 'fa-clock') }}"></i>
        </div>
        <h3 class="text-xl font-extrabold
            {{ $isPaid ? 'text-emerald-700' : ($isExpired || $isFailed ? 'text-red-600' : 'text-amber-700') }}">
            @if($isPaid) Pembayaran Berhasil!
            @elseif($isExpired) Waktu Pembayaran Habis
            @elseif($isFailed) Pembayaran Gagal
            @else Menunggu Pembayaran
            @endif
        </h3>
        <p class="text-sm mt-1 text-gray-500">
            @if($isPaid) Saldo santri sudah dikreditkan otomatis.
            @elseif($isExpired) Link pembayaran sudah kadaluarsa. Buat permintaan baru.
            @elseif($isFailed) Pembayaran tidak berhasil. Silakan coba lagi.
            @else Selesaikan pembayaran di halaman Tripay yang sudah dibuka.
            @endif
        </p>
    </div>

    {{-- Detail --}}
    <div class="px-6 py-5 space-y-3 border-t border-gray-100">
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Santri</span>
            <span class="font-semibold text-gray-800">{{ $topUp->santri->nama }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">NIS</span>
            <span class="font-mono text-gray-700">{{ $topUp->santri->nis }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Nominal</span>
            <span class="font-bold text-emerald-700 text-base">Rp {{ number_format($topUp->nominal) }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Metode</span>
            <span class="font-medium text-gray-700 uppercase">{{ $topUp->tripay_channel ?? '-' }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Tanggal</span>
            <span class="text-gray-600">{{ $topUp->created_at->format('d/m/Y H:i') }}</span>
        </div>
        @if($topUp->paid_at)
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Dibayar</span>
            <span class="text-gray-600">{{ $topUp->paid_at->format('d/m/Y H:i') }}</span>
        </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="px-6 py-4 border-t border-gray-100 flex gap-3">
        @if($isUnpaid && $topUp->payment_url)
        <a href="{{ $topUp->payment_url }}" target="_blank"
           class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm text-center transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Lanjut Bayar
        </a>
        @endif

        @if($isExpired || $isFailed)
        <a href="{{ route('wali.topup.create') }}"
           class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm text-center transition-colors shadow-sm">
            <i class="fa-solid fa-rotate-right mr-1"></i> Coba Lagi
        </a>
        @endif

        <a href="{{ route('wali.topup.index') }}"
           class="{{ $isUnpaid && $topUp->payment_url || $isExpired || $isFailed ? 'flex-none px-5' : 'flex-1' }} py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium text-sm text-center transition-colors">
            Riwayat
        </a>
    </div>
</div>

</div>
@endsection
