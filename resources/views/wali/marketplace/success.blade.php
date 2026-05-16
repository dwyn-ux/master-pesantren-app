@extends('layouts.app')
@section('title', 'Order Berhasil')
@section('page-title', 'Order Berhasil')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-3xl mx-auto">
    <div class="p-8 text-center bg-gradient-to-b from-green-50/50 to-transparent border-b border-gray-100">
        <div class="w-24 h-24 bg-green-100 text-green-500 rounded-full flex items-center justify-center text-5xl mx-auto mb-6 shadow-sm border-4 border-white">
            <i class="fa-solid fa-check"></i>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-800 mb-3">Order Berhasil!</h2>
        <p class="text-gray-500 max-w-lg mx-auto text-lg leading-relaxed">
            Terima kasih! Pesanan Anda telah diterima sistem dan akan segera diproses oleh petugas kantin/koperasi.
        </p>
    </div>

    <div class="p-6 sm:p-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <div class="p-5 rounded-xl border border-gray-200 bg-gray-50/50 flex flex-col justify-center items-center text-center">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">ID Transaksi</div>
                <div class="text-2xl font-mono font-bold text-indigo-700">#{{ $order->id }}</div>
            </div>
            
            <div class="p-5 rounded-xl border border-green-200 bg-green-50/50 flex flex-col justify-center items-center text-center">
                <div class="text-xs font-bold text-green-600 uppercase tracking-wider mb-2">Total Pembayaran</div>
                <div class="text-2xl font-extrabold text-green-700">Rp {{ number_format($order->total) }}</div>
            </div>
        </div>

        <div class="border border-gray-100 rounded-xl overflow-hidden mb-8">
            <div class="bg-gray-50/80 px-5 py-3 border-b border-gray-100 font-bold text-gray-700 text-sm flex items-center gap-2">
                <i class="fa-solid fa-list-check text-indigo-500"></i> Rincian Pesanan
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($order->items as $item)
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h6 class="font-bold text-gray-800 text-sm mb-1">{{ $item->nama_produk }}</h6>
                        <div class="text-xs text-gray-500">
                            Rp {{ number_format($item->harga) }} <i class="fa-solid fa-xmark mx-1 text-[10px]"></i> {{ $item->qty }} item
                        </div>
                    </div>
                    <div class="font-bold text-gray-800">
                        Rp {{ number_format($item->subtotal) }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-5 flex gap-4 text-blue-800 mb-8">
            <i class="fa-solid fa-circle-info text-blue-500 text-2xl mt-0.5"></i>
            <div>
                <h6 class="font-bold mb-1">Informasi Pengiriman</h6>
                <p class="text-sm leading-relaxed">
                    Pesanan akan dikonfirmasi oleh admin dan dikirimkan langsung kepada <span class="font-bold">{{ $order->tujuanSantri->nama }}</span>.
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('wali.marketplace.orders') }}" class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors text-center flex justify-center items-center gap-2">
                <i class="fa-solid fa-list"></i> Lihat Order Saya
            </a>
            <a href="{{ route('wali.marketplace.index') }}" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm text-center flex justify-center items-center gap-2">
                <i class="fa-solid fa-store"></i> Belanja Lagi
            </a>
        </div>
    </div>
</div>
@endsection