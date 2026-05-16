@extends('layouts.app')
@section('title', 'Struk Transaksi #' . $transaksi->id)
@section('page-title', 'Struk Transaksi')

@section('sidebar')
    @include('partials.sidebar-outlet')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    
    <div class="flex justify-between items-center mb-4 px-2">
        <a href="{{ route('outlet.kasir.kantin.history') }}" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Riwayat
        </a>
        <div class="flex gap-2">
            <a href="{{ route('outlet.kasir.kantin.index') }}" class="px-4 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-sm font-semibold transition-colors">
                <i class="fa-solid fa-cash-register mr-1"></i> Kasir Baru
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg text-sm font-semibold transition-colors">
                <i class="fa-solid fa-print mr-1"></i> Cetak Struk
            </button>
        </div>
    </div>

    <!-- Print Area -->
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden bg-white print:shadow-none print:border-none print:w-[80mm] print:mx-auto print:text-black">
        
        <!-- Header Struk -->
        <div class="p-6 sm:p-8 text-center border-b border-gray-100 border-dashed print:p-4 print:border-b-2 print:border-black print:border-dashed">
            <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-4 print:hidden">
                <i class="fa-solid fa-shop"></i>
            </div>
            <h4 class="text-2xl font-extrabold text-gray-800 tracking-tight print:text-xl print:mb-1">{{ $outlet->nama }}</h4>
            <p class="text-sm text-gray-500 mb-1 print:text-xs">Digital Pesantren System</p>
            <p class="text-sm text-gray-500 print:text-xs">{{ $transaksi->created_at->format('d/m/Y H:i:s') }}</p>
            
            <div class="inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded font-mono font-bold mt-4 print:border print:border-black print:bg-white print:text-black print:text-sm">
                #{{ $transaksi->id }}
            </div>
        </div>

        <!-- Info Pembeli -->
        <div class="p-6 sm:px-8 sm:py-5 border-b border-gray-100 border-dashed print:p-4 print:border-b-2 print:border-black print:border-dashed">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg print:hidden">
                    <i class="fa-solid fa-child"></i>
                </div>
                <div>
                    <h6 class="font-bold text-gray-800 print:text-sm">{{ $transaksi->santri->nama }}</h6>
                    <div class="text-xs text-gray-500 flex flex-wrap gap-2 mt-0.5 print:text-[10px]">
                        <span>NIS: {{ $transaksi->santri->nis }}</span>
                        <span class="text-gray-300 print:hidden">|</span>
                        <span class="text-emerald-600 font-semibold print:text-black">Sisa Saldo: Rp {{ number_format($transaksi->santri->saldo) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Item -->
        <div class="p-6 sm:px-8 py-5 print:p-4">
            <h6 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 print:text-black print:text-[10px] print:mb-2">Item Pembelian</h6>
            
            <div class="space-y-4 print:space-y-2">
                @foreach($transaksi->items as $item)
                <div class="flex justify-between text-sm print:text-xs">
                    <div class="flex-1 pr-4">
                        <div class="font-bold text-gray-800 leading-tight print:font-semibold">{{ $item->nama_produk }}</div>
                        <div class="text-gray-500 mt-0.5">
                            {{ $item->qty }} x {{ number_format($item->harga_satuan) }}
                        </div>
                    </div>
                    <div class="font-bold text-gray-800 print:font-semibold">
                        {{ number_format($item->subtotal) }}
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Total -->
            <div class="mt-6 pt-4 border-t-2 border-gray-100 border-dashed print:mt-4 print:pt-2 print:border-black">
                <div class="flex justify-between items-end text-lg print:text-sm">
                    <span class="font-bold text-gray-600 uppercase tracking-wider text-xs print:text-[10px]">Total Pembayaran</span>
                    <span class="font-extrabold text-gray-900 text-2xl print:text-base">Rp {{ number_format($transaksi->total) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer / Status Verifikasi -->
        <div class="bg-gray-50/50 p-6 sm:p-8 text-center print:bg-white print:p-4 print:border-t-2 print:border-black print:border-dashed">
            @if($transaksi->fingerprint_verified)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-600 rounded-full text-xs font-bold border border-green-200 mb-6 print:border-none print:bg-white print:text-black print:mb-4 print:px-0">
                    <i class="fa-solid fa-fingerprint"></i> Biometrik Terverifikasi
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-600 rounded-full text-xs font-bold border border-amber-200 mb-6 print:border-none print:bg-white print:text-black print:mb-4 print:px-0">
                    <i class="fa-solid fa-user-pen"></i> Input Manual
                </div>
            @endif
            
            <p class="text-gray-400 text-sm italic print:text-[10px] print:text-black print:text-center print:font-serif">
                "Terima kasih atas kunjungannya."
            </p>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print\:visible, .print\:visible * {
        visibility: visible;
    }
    .glass-panel {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: none !important;
    }
}
</style>
@endsection
