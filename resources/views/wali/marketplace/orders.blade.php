@extends('layouts.app')
@section('title', 'Riwayat Order')
@section('page-title', 'Riwayat Order Marketplace')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden max-w-5xl mx-auto">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i> Riwayat Belanja Santri
        </h6>
        <a href="{{ route('wali.marketplace.index') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-store"></i> Mulai Belanja
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="p-16 flex flex-col items-center justify-center text-gray-500 text-center">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-5 border border-gray-100">
                <i class="fa-solid fa-basket-shopping text-4xl text-gray-300"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-700 mb-2">Belum ada riwayat belanja</h4>
            <p class="max-w-md mx-auto mb-6 leading-relaxed">Anda belum pernah melakukan pemesanan di marketplace untuk santri. Gunakan saldo digital Anda untuk berbelanja sekarang.</p>
            <a href="{{ route('wali.marketplace.index') }}" class="px-8 py-3 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl font-semibold shadow-md shadow-indigo-200 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <i class="fa-solid fa-arrow-right"></i> Lihat Katalog Produk
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                        <th class="px-6 py-4 font-medium">Order ID</th>
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                        <th class="px-6 py-4 font-medium">Penerima (Santri)</th>
                        <th class="px-6 py-4 font-medium">Total Harga</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($orders as $order)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-1 rounded">#{{ $order->id }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                <div class="font-medium text-gray-800">{{ $order->created_at->format('d M Y') }}</div>
                                <div class="text-xs mt-0.5"><i class="fa-regular fa-clock"></i> {{ $order->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 flex items-center gap-1.5"><i class="fa-solid fa-child text-indigo-400 text-xs"></i> {{ $order->tujuanSantri->nama }}</div>
                                <div class="text-xs text-gray-500 font-mono mt-1 ml-4">{{ $order->tujuanSantri->nis }}</div>
                            </td>
                            <td class="px-6 py-4 font-extrabold text-gray-800">
                                Rp {{ number_format($order->total) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusData = match($order->status) {
                                        'pending' => ['class' => 'bg-amber-100 text-amber-800 border-amber-200', 'icon' => 'fa-clock'],
                                        'diproses' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200', 'icon' => 'fa-spinner fa-spin'],
                                        'selesai' => ['class' => 'bg-green-100 text-green-800 border-green-200', 'icon' => 'fa-check'],
                                        'dibatalkan' => ['class' => 'bg-red-100 text-red-800 border-red-200', 'icon' => 'fa-ban'],
                                        default => ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'fa-circle-info']
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusData['class'] }}">
                                    <i class="fa-solid {{ $statusData['icon'] }} mr-1.5"></i> {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('wali.marketplace.detail', $order) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-white border border-gray-200 text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 rounded-lg text-sm font-semibold transition-colors gap-2">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $orders->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
