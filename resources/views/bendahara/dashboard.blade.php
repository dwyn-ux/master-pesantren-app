@extends('layouts.app')
@section('title', 'Dashboard Bendahara')
@section('page-title', 'Dashboard Bendahara')

@section('sidebar')
    @include('partials.sidebar-bendahara')
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-indigo-500 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-indigo-500/10 transition-transform group-hover:scale-110">
            <i class="fa-solid fa-file-invoice text-8xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Total Tagihan Beredar</p>
            <h3 class="text-xl font-extrabold text-indigo-600">Rp {{ number_format($summary['total_tagihan']) }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-amber-500 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-amber-500/10 transition-transform group-hover:scale-110">
            <i class="fa-solid fa-clock text-8xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tagihan Belum Bayar</p>
            <h3 class="text-xl font-extrabold text-amber-600">Rp {{ number_format($summary['tagihan_belum_bayar']) }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-green-500 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-green-500/10 transition-transform group-hover:scale-110">
            <i class="fa-solid fa-sack-dollar text-8xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Total Pembayaran Masuk</p>
            <h3 class="text-xl font-extrabold text-green-600">Rp {{ number_format($summary['total_pembayaran']) }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-blue-500 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-blue-500/10 transition-transform group-hover:scale-110">
            <i class="fa-solid fa-hourglass-half text-8xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pembayaran Pending</p>
            <h3 class="text-xl font-extrabold text-blue-600">Rp {{ number_format($summary['pending_pembayaran']) }}</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-1">
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden h-full">
            <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
                <h6 class="text-lg font-bold text-gray-800">Status Tagihan</h6>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 rounded-xl bg-amber-50 border border-amber-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <span class="font-semibold text-amber-800">Belum Bayar</span>
                    </div>
                    <span class="text-xl font-bold text-amber-600">{{ $tagihanByStatus['belum_bayar'] }}</span>
                </div>
                
                <div class="flex items-center justify-between p-4 rounded-xl bg-green-50 border border-green-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                        <span class="font-semibold text-green-800">Lunas</span>
                    </div>
                    <span class="text-xl font-bold text-green-600">{{ $tagihanByStatus['lunas'] }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-2">
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden h-full">
            <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
                <h6 class="text-lg font-bold text-gray-800">10 Pembayaran Terbaru</h6>
                <a href="{{ route('bendahara.pembayaran') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                            <th class="px-6 py-4 font-medium">Waktu Transaksi</th>
                            <th class="px-6 py-4 font-medium">Santri / Wali</th>
                            <th class="px-6 py-4 font-medium">Nominal</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentPayments as $payment)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ $payment->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $payment->tagihan->santri->nama ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-user-tie mr-1"></i> {{ $payment->wali->nama }}</div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-800">
                                Rp {{ number_format($payment->nominal) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeClass = match($payment->status) {
                                        'paid' => 'bg-green-100 text-green-800 border border-green-200',
                                        'pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        default => 'bg-gray-100 text-gray-600 border border-gray-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ strtoupper($payment->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-receipt text-3xl mb-2 text-gray-300"></i>
                                    <p class="text-sm">Belum ada pembayaran masuk.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
