@extends('layouts.app')
@section('title', 'Pesanan Marketplace')
@section('page-title', 'Pesanan Marketplace')
@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection
@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
        <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-bag-shopping text-indigo-500"></i> Daftar Pesanan Marketplace
        </h6>
    </div>
    <div class="p-6 border-b border-gray-100 bg-gray-50/30">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></div>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm" placeholder="Cari ID order, wali, santri, NIS...">
            </div>
            <select name="status" class="w-full sm:w-48 px-4 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm appearance-none cursor-pointer">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-gray-800 text-white rounded-xl font-medium shadow-sm">Filter</button>
                <a href="{{ route('admin.marketplace.orders.index') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-medium shadow-sm text-center">Reset</a>
            </div>
        </form>
    </div>
    @if($orders->isEmpty())
        <div class="p-16 text-center"><i class="fa-solid fa-bag-shopping text-4xl text-gray-300 mb-4 block"></i><p class="text-gray-500">Belum ada pesanan marketplace.</p></div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead><tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Order</th>
                    <th class="px-6 py-4 font-medium">Wali</th>
                    <th class="px-6 py-4 font-medium">Santri Tujuan</th>
                    <th class="px-6 py-4 font-medium">Item & Total</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($orders as $order)
                    <tr class="hover:bg-indigo-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded inline-block border border-indigo-100">#{{ $order->id }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $order->wali->nama }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $order->tujuanSantri?->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $order->tujuanSantri?->nis ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-700">{{ $order->items->sum('qty') }} item</div>
                            <div class="text-sm font-extrabold text-emerald-600">Rp {{ number_format($order->total) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $s = match($order->status) {
                                    'pending'    => ['bg-amber-100',   'text-amber-700',   'fa-clock'],
                                    'diproses'   => ['bg-blue-100',    'text-blue-700',    'fa-spinner'],
                                    'selesai'    => ['bg-emerald-100', 'text-emerald-700', 'fa-check-double'],
                                    'dibatalkan' => ['bg-red-100',     'text-red-700',     'fa-ban'],
                                    default      => ['bg-gray-100',    'text-gray-700',    'fa-circle']
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $s[0] }} {{ $s[1] }}">
                                <i class="fa-solid {{ $s[2] }} mr-1.5"></i> {{ $statusOptions[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.marketplace.orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 text-indigo-600 hover:bg-indigo-50 rounded-lg text-sm font-semibold transition-colors shadow-sm gap-1.5">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
        @endif
    @endif
</div>
@endsection
