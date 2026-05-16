@extends('layouts.app')
@section('title', 'Order Laundry')
@section('page-title', 'Order Laundry')

@section('sidebar')
    @include('partials.sidebar-outlet')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden max-w-6xl mx-auto">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-basket-shopping text-indigo-500"></i> Daftar Order Laundry
            </h6>
            <div class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                <span class="inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
                Tarif aktif: <span class="font-bold text-gray-700">Rp {{ number_format($tarif->harga_per_kg) }}/kg</span>
            </div>
        </div>
        <a href="{{ route('outlet.laundry.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Terima Laundry Baru
        </a>
    </div>

    <div class="p-6 border-b border-gray-100 bg-gray-50/30">
        <form method="GET" class="flex flex-col sm:flex-row gap-4 w-full">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm" placeholder="Cari tiket / nama / NIS...">
            </div>
            
            <div class="w-full sm:w-48">
                <select name="status" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center flex-1 sm:flex-none">
                    Filter
                </button>
                <a href="{{ route('outlet.laundry.index') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800 rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center flex-1 sm:flex-none">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if($orders->isEmpty())
        <div class="p-16 flex flex-col items-center justify-center text-center">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                <i class="fa-solid fa-basket-shopping text-4xl text-gray-300"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-600 mb-1">Belum Ada Order</h4>
            <p class="text-gray-400 max-w-sm mb-6">Tidak ada order laundry yang ditemukan. Silakan tambahkan order baru.</p>
            <a href="{{ route('outlet.laundry.create') }}" class="px-6 py-2.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-xl font-medium transition-colors">
                Terima Laundry
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                        <th class="px-6 py-4 font-medium">No. Tiket</th>
                        <th class="px-6 py-4 font-medium">Santri</th>
                        <th class="px-6 py-4 font-medium">Berat & Total</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($orders as $order)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg inline-block mb-1 border border-indigo-100">{{ $order->nomor_tiket }}</div>
                                <div class="text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i> {{ $order->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 flex items-center gap-1.5"><i class="fa-solid fa-child text-indigo-400 text-xs"></i> {{ $order->santri->nama }}</div>
                                <div class="text-xs text-gray-500 font-mono mt-0.5 ml-4">{{ $order->santri->nis }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-700">{{ $order->berat_kg }} kg</div>
                                <div class="text-sm font-extrabold text-emerald-600 mt-0.5">Rp {{ number_format($order->total) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusData = match($order->status) {
                                        'diterima' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'fa-inbox'],
                                        'dicuci' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'fa-soap'],
                                        'selesai' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'fa-check-double'],
                                        'diambil' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'icon' => 'fa-hand-holding'],
                                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'icon' => 'fa-circle-info']
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $statusData['bg'] }} {{ $statusData['text'] }} border {{ $statusData['border'] }}">
                                    <i class="fa-solid {{ $statusData['icon'] }} mr-1.5"></i> {{ $statusOptions[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('outlet.laundry.show', $order) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-white border border-gray-200 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 rounded-lg text-sm font-semibold transition-colors shadow-sm gap-2">
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
