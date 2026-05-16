@extends('layouts.app')
@section('title', 'Periode Akuntansi')
@section('page-title', 'Tutup Buku / Periode Akuntansi')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="glass-panel rounded-2xl p-5 mb-6 bg-amber-50 border-l-4 border-amber-500">
    <div class="flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-amber-600 text-xl mt-1"></i>
        <div class="text-sm text-gray-700">
            <p class="font-bold mb-1">Tentang Tutup Buku</p>
            <p>Setelah periode ditutup, transaksi pada periode tersebut <strong>tidak bisa diubah/ditambah</strong>. Pastikan semua jurnal penyesuaian sudah dimasukkan sebelum menutup. Anda bisa membuka kembali kapan saja.</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
    @foreach($bulanList as $b)
    @php $p = $b['periode']; @endphp
    <div class="glass-panel rounded-2xl p-4 {{ $p && $p->status == 'closed' ? 'bg-gray-100' : '' }}">
        <p class="text-xs text-gray-500 uppercase font-bold">Periode</p>
        <p class="text-lg font-bold mb-3">{{ $b['label'] }}</p>

        @if($p && $p->status == 'closed')
            <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-xs font-bold">
                <i class="fa-solid fa-lock mr-1"></i> CLOSED
            </span>
            <p class="text-xs text-gray-500 mt-2">Ditutup: {{ $p->closed_at?->format('d/m/Y H:i') }}</p>
            <form method="POST" action="{{ route('finance.periode.reopen', $p) }}" class="mt-3" onsubmit="return confirm('Buka kembali periode {{ $b['label'] }}?')">
                @csrf @method('PATCH')
                <button class="w-full px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold">
                    <i class="fa-solid fa-lock-open mr-1"></i> Buka Kembali
                </button>
            </form>
        @else
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                <i class="fa-solid fa-lock-open mr-1"></i> OPEN
            </span>
            <form method="POST" action="{{ route('finance.periode.close') }}" class="mt-3" onsubmit="return confirm('Tutup periode {{ $b['label'] }}? Transaksi tidak bisa diubah lagi.')">
                @csrf
                <input type="hidden" name="tahun" value="{{ $b['tahun'] }}">
                <input type="hidden" name="bulan" value="{{ $b['bulan'] }}">
                <button class="w-full px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold">
                    <i class="fa-solid fa-lock mr-1"></i> Tutup Periode
                </button>
            </form>
        @endif
    </div>
    @endforeach
</div>
@endsection
