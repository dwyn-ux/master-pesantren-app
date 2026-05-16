@extends('layouts.app')
@section('title', 'Master Kas & Bank')
@section('page-title', 'Master Kas & Bank')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-sm text-gray-600">Total saldo: <span class="font-bold text-indigo-600">Rp {{ number_format($items->sum('saldo_berjalan')) }}</span></p>
    <a href="{{ route('finance.kas-banks.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">
        <i class="fa-solid fa-plus mr-1"></i> Tambah Kas/Bank
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($items as $kb)
    <div class="glass-panel rounded-2xl p-6 border-l-4 {{ $kb->tipe == 'kas' ? 'border-amber-500' : 'border-blue-500' }}">
        <div class="flex justify-between items-start mb-3">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">{{ strtoupper($kb->tipe) }} · {{ $kb->kode }}</p>
                <h3 class="text-lg font-bold">{{ $kb->nama }}</h3>
                @if($kb->tipe == 'bank')
                    <p class="text-xs text-gray-500 mt-1">{{ $kb->nama_bank }} · {{ $kb->no_rekening }}</p>
                @endif
            </div>
            <i class="fa-solid {{ $kb->tipe == 'kas' ? 'fa-money-bill-wave text-amber-500' : 'fa-building-columns text-blue-500' }} text-2xl"></i>
        </div>
        <p class="text-xs text-gray-500 font-semibold">Saldo Berjalan</p>
        <p class="text-2xl font-extrabold text-indigo-600 mb-3">Rp {{ number_format($kb->saldo_berjalan) }}</p>
        <div class="flex gap-2 text-xs">
            <a href="{{ route('finance.kas-banks.edit', $kb) }}" class="flex-1 text-center px-3 py-1.5 bg-gray-100 rounded-lg font-semibold">Edit</a>
            @if(!$kb->is_active)<span class="px-2 py-1 bg-gray-200 rounded text-gray-500">Non-Aktif</span>@endif
        </div>
    </div>
    @empty
    <p class="col-span-full text-center text-gray-400 py-8">Belum ada kas/bank.</p>
    @endforelse
</div>
@endsection
