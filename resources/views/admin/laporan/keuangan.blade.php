@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan Pesantren')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Ringkasan tagihan & pembayaran pondok.</p>
    <a href="{{ route('admin.laporan.pdf.keuangan') }}" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium text-sm shadow-sm">
        <i class="fa-solid fa-file-pdf mr-1"></i> Download PDF
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-indigo-500 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-indigo-500/10 transition-transform group-hover:scale-110">
            <i class="fa-solid fa-file-invoice text-8xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Total Tagihan Beredar</p>
            <h3 class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($summary['total_tagihan']) }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-amber-500 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-amber-500/10 transition-transform group-hover:scale-110">
            <i class="fa-solid fa-clock text-8xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Tagihan Belum Lunas</p>
            <h3 class="text-2xl font-extrabold text-amber-600">Rp {{ number_format($summary['tagihan_belum_bayar']) }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-green-500 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-green-500/10 transition-transform group-hover:scale-110">
            <i class="fa-solid fa-sack-dollar text-8xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Total Pemasukan</p>
            <h3 class="text-2xl font-extrabold text-green-600">Rp {{ number_format($summary['total_pembayaran']) }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-blue-500 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-blue-500/10 transition-transform group-hover:scale-110">
            <i class="fa-solid fa-money-bill-transfer text-8xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Jumlah Transaksi Lunas</p>
            <h3 class="text-2xl font-extrabold text-blue-600">{{ $summary['jumlah_pembayaran'] }} <span class="text-base font-normal text-gray-500">trx</span></h3>
        </div>
    </div>
</div>

<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
        <h6 class="text-lg font-bold text-gray-800">50 Pembayaran Terbaru</h6>
        <a href="{{ route('admin.pembayaran.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i></a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Tanggal Masuk</th>
                    <th class="px-6 py-4 font-medium">Wali / Santri</th>
                    <th class="px-6 py-4 font-medium">Rincian Tagihan</th>
                    <th class="px-6 py-4 font-medium">Nominal</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentPayments as $payment)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ optional($payment->paid_at)->format('d/m/Y H:i') ?? $payment->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $payment->wali->nama }}</div>
                        <div class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-child mr-1"></i> {{ $payment->tagihan->santri->nama ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-700">
                        {{ $payment->tagihan->jenisTagihan->nama ?? '-' }}
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-800">
                        Rp {{ number_format($payment->nominal) }}
                    </td>
                    <td class="px-6 py-4">
                        @if($payment->status === 'paid')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                <i class="fa-solid fa-check mr-1.5"></i> Lunas
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                {{ strtoupper($payment->status) }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-receipt text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada riwayat pembayaran yang masuk.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
