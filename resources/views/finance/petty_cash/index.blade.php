@extends('layouts.app')
@section('title', 'Petty Cash')
@section('page-title', 'Kas Kecil (Petty Cash)')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="flex justify-end mb-4">
    <a href="{{ route('finance.petty-cash.create') }}" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-bold shadow">
        <i class="fa-solid fa-plus mr-1"></i> Buka Kas Kecil Baru
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($items as $p)
    <div class="glass-panel rounded-2xl p-6 border-l-4 {{ $p->is_active ? 'border-amber-500' : 'border-gray-400 opacity-60' }}">
        <div class="flex justify-between items-start mb-3">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">{{ $p->nomor }}</p>
                <h3 class="text-lg font-bold">{{ $p->nama ?: $p->penanggung_jawab }}</h3>
                <p class="text-xs text-gray-500">PJ: {{ $p->penanggung_jawab }}</p>
            </div>
            <i class="fa-solid fa-piggy-bank text-3xl text-amber-400"></i>
        </div>
        <p class="text-xs text-gray-500 font-semibold uppercase">Saldo Berjalan</p>
        <p class="text-2xl font-extrabold text-amber-600 mb-3">Rp {{ number_format($p->saldo_berjalan) }}</p>
        <div class="flex gap-2 text-xs">
            <a href="{{ route('finance.petty-cash.show', $p) }}" class="flex-1 text-center px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold">
                <i class="fa-solid fa-eye mr-1"></i> Detail & Transaksi
            </a>
            <a href="{{ route('finance.petty-cash.edit', $p) }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg"><i class="fa-solid fa-pen text-gray-600"></i></a>
        </div>
    </div>
    @empty
    <div class="col-span-full glass-panel rounded-2xl p-12 text-center">
        <i class="fa-solid fa-piggy-bank text-5xl text-gray-300 mb-3"></i>
        <h3 class="text-lg font-bold text-gray-600">Belum Ada Kas Kecil</h3>
        <p class="text-sm text-gray-500 mb-4">Buat kas kecil per penanggung jawab untuk mengelola pengeluaran harian kecil.</p>
        <a href="{{ route('finance.petty-cash.create') }}" class="inline-block px-5 py-2.5 bg-green-600 text-white rounded-lg font-bold">
            <i class="fa-solid fa-plus mr-1"></i> Buka Kas Kecil
        </a>
    </div>
    @endforelse
</div>
@endsection
