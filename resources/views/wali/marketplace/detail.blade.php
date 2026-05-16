@extends('layouts.app')
@section('title', 'Detail Order #' . $order->id)
@section('page-title', 'Detail Order')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-4xl mx-auto">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
        <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-indigo-500"></i> Transaksi #{{ $order->id }}
        </h6>
        <a href="{{ route('wali.marketplace.orders') }}" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl text-sm font-medium transition-colors">
            Kembali
        </a>
    </div>

    <div class="p-6">
        
        {{-- Status Banner --}}
        @php
            $statusData = match($order->status) {
                'pending' => ['bg' => 'bg-amber-100 border-amber-200 text-amber-800', 'icon' => 'fa-clock', 'label' => 'Menunggu Diproses', 'desc' => 'Order sedang mengantre untuk dikonfirmasi.'],
                'diproses' => ['bg' => 'bg-blue-100 border-blue-200 text-blue-800', 'icon' => 'fa-spinner fa-spin', 'label' => 'Sedang Diproses', 'desc' => 'Order sedang disiapkan oleh petugas.'],
                'selesai' => ['bg' => 'bg-green-100 border-green-200 text-green-800', 'icon' => 'fa-check-double', 'label' => 'Selesai', 'desc' => 'Barang telah diterima santri dengan baik.'],
                'dibatalkan' => ['bg' => 'bg-red-100 border-red-200 text-red-800', 'icon' => 'fa-ban', 'label' => 'Dibatalkan', 'desc' => 'Pesanan dibatalkan. Saldo telah dikembalikan.'],
                default => ['bg' => 'bg-gray-100 border-gray-200 text-gray-800', 'icon' => 'fa-circle-question', 'label' => 'Tidak Diketahui', 'desc' => '']
            };
        @endphp

        <div class="mb-8 p-5 rounded-2xl border {{ $statusData['bg'] }} flex flex-col sm:flex-row items-center sm:items-start gap-4 text-center sm:text-left">
            <div class="w-12 h-12 rounded-full bg-white/50 flex items-center justify-center text-2xl flex-shrink-0 shadow-sm">
                <i class="fa-solid {{ $statusData['icon'] }}"></i>
            </div>
            <div>
                <h4 class="text-lg font-bold mb-1">{{ $statusData['label'] }}</h4>
                <p class="text-sm opacity-90 leading-relaxed">{{ $statusData['desc'] }}</p>
                <div class="mt-2 text-xs font-medium opacity-75">
                    <i class="fa-regular fa-calendar mr-1"></i> Dibuat pada {{ $order->created_at->format('d M Y, H:i') }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- Info Penerima --}}
            <div class="p-5 rounded-xl border border-gray-100 bg-gray-50/50">
                <div class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-child"></i> Penerima (Santri)
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg shadow-sm">
                        {{ substr($order->tujuanSantri->nama, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-gray-800 text-lg">{{ $order->tujuanSantri->nama }}</div>
                        <div class="text-sm text-gray-600 mt-1"><span class="text-gray-400 w-16 inline-block">NIS</span> : {{ $order->tujuanSantri->nis }}</div>
                        <div class="text-sm text-gray-600 mt-2">
                            <span class="inline-flex px-2 py-0.5 rounded bg-white border border-gray-200 text-xs font-medium">Opsi: {{ ucfirst(str_replace('_', ' ', $order->opsi_terima)) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Info Order --}}
            <div class="p-5 rounded-xl border border-gray-100 bg-gray-50/50">
                <div class="text-xs font-bold text-emerald-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Rincian Pembayaran
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">ID Transaksi</span>
                        <span class="font-mono font-medium bg-white px-2 py-0.5 rounded border border-gray-200">#{{ $order->id }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Sumber Dana</span>
                        <span class="font-medium text-gray-800 flex items-center gap-1"><i class="fa-solid fa-wallet text-emerald-500"></i> Saldo Wali</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                        <span class="font-bold text-gray-700">Total Dibayar</span>
                        <span class="font-extrabold text-emerald-600 text-lg">Rp {{ number_format($order->total) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Barang --}}
        <div class="border border-gray-100 rounded-xl overflow-hidden">
            <div class="bg-gray-50/80 px-5 py-3 border-b border-gray-100 font-bold text-gray-700 text-sm">
                Produk yang Dibeli
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($order->items as $item)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50/30 transition-colors">
                    <div class="flex-1 pr-4">
                        <h6 class="font-bold text-gray-800 text-sm mb-1">{{ $item->nama_produk }}</h6>
                        @if($item->produk)
                            <p class="text-xs text-gray-500 line-clamp-1">{{ $item->produk->deskripsi }}</p>
                        @endif
                        <div class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                            Rp {{ number_format($item->harga) }} 
                            <i class="fa-solid fa-xmark text-[10px] text-gray-400"></i>
                            <span class="font-bold text-indigo-600 bg-indigo-50 px-1.5 rounded">{{ $item->qty }}</span>
                        </div>
                    </div>
                    <div class="font-bold text-gray-800">
                        Rp {{ number_format($item->subtotal) }}
                    </div>
                </div>
                @endforeach
                
                <div class="p-5 bg-gray-50 flex justify-between items-center border-t-2 border-gray-100">
                    <span class="font-bold text-gray-600 uppercase text-xs tracking-wider">Grand Total</span>
                    <span class="text-xl font-extrabold text-indigo-600">Rp {{ number_format($order->total) }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
