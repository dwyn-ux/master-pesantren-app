@extends('layouts.app')
@section('title', 'Riwayat Kasir Kantin')
@section('page-title', 'Riwayat Transaksi')

@section('sidebar')
    @include('partials.sidebar-outlet')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden max-w-5xl mx-auto">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-indigo-500"></i> Riwayat Penjualan Kantin
        </h6>
        <a href="{{ route('outlet.kasir.kantin.index') }}" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-cash-register"></i> Transaksi Baru
        </a>
    </div>

    <div class="p-6 border-b border-gray-100 bg-gray-50/30">
        <form method="GET" class="flex flex-col sm:flex-row gap-4 w-full">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm" placeholder="Cari nama / NIS santri...">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center flex-1 sm:flex-none">
                    Filter
                </button>
                <a href="{{ route('outlet.kasir.kantin.history') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800 rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center flex-1 sm:flex-none">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if($transaksi->isEmpty())
        <div class="p-16 flex flex-col items-center justify-center text-center">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                <i class="fa-solid fa-receipt text-4xl text-gray-300"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-600 mb-1">Belum Ada Transaksi</h4>
            <p class="text-gray-400 max-w-sm">Riwayat penjualan kasir kantin akan muncul di sini.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                        <th class="px-6 py-4 font-medium">Transaksi</th>
                        <th class="px-6 py-4 font-medium">Santri</th>
                        <th class="px-6 py-4 font-medium">Item</th>
                        <th class="px-6 py-4 font-medium">Total Harga</th>
                        <th class="px-6 py-4 font-medium">Verifikasi</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($transaksi as $item)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-1 rounded inline-block mb-1">#{{ $item->id }}</div>
                                <div class="text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i> {{ $item->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 flex items-center gap-1.5"><i class="fa-solid fa-child text-indigo-400 text-xs"></i> {{ $item->santri->nama }}</div>
                                <div class="text-xs text-gray-500 font-mono mt-0.5 ml-4">{{ $item->santri->nis }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-bold">{{ $item->items->sum('qty') }} pcs</span>
                            </td>
                            <td class="px-6 py-4 font-extrabold text-gray-800">
                                Rp {{ number_format($item->total) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->fingerprint_verified)
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-green-50 text-green-600 border border-green-200 text-xs font-bold gap-1">
                                        <i class="fa-solid fa-fingerprint"></i> Biometrik
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-amber-50 text-amber-600 border border-amber-200 text-xs font-bold gap-1">
                                        <i class="fa-solid fa-user-pen"></i> Manual
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('outlet.kasir.kantin.show', $item) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors border border-indigo-100 shadow-sm" title="Lihat Detail">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($transaksi->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $transaksi->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
