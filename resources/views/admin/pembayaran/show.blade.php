@extends('layouts.app')
@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Pembayaran')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-3xl mx-auto">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
        <h6 class="text-lg font-bold text-gray-800">Transaksi #{{ $pembayaran->id }}</h6>
        <a href="{{ route('admin.pembayaran.index') }}" class="px-4 py-1.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium transition-colors">
            Kembali
        </a>
    </div>
    
    <div class="p-6">
        @php
            $badgeClass = match($pembayaran->status) {
                'paid' => 'bg-green-100 text-green-800 border-green-200',
                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                'failed' => 'bg-red-100 text-red-800 border-red-200',
                'expired' => 'bg-gray-100 text-gray-600 border-gray-200',
                default => 'bg-gray-100 text-gray-800 border-gray-200'
            };
            
            $iconClass = match($pembayaran->status) {
                'paid' => 'fa-check',
                'pending' => 'fa-clock',
                'failed' => 'fa-triangle-exclamation',
                'expired' => 'fa-calendar-xmark',
                default => 'fa-circle-question'
            };

            $metodeLabel = [
                'va_bca' => 'Virtual Account BCA', 'va_mandiri' => 'Virtual Account Mandiri',
                'qris' => 'QRIS Payment', 'gopay' => 'Gopay', 'ovo' => 'OVO', 'manual' => 'Pembayaran Manual Kasir'
            ][$pembayaran->metode] ?? $pembayaran->metode;
        @endphp

        <div class="flex flex-col items-center justify-center p-6 bg-gray-50/50 rounded-2xl border border-gray-100 mb-8">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold border mb-4 {{ $badgeClass }}">
                <i class="fa-solid {{ $iconClass }} mr-2"></i> {{ strtoupper($pembayaran->status) }}
            </span>
            <div class="text-sm font-medium text-gray-500 mb-1">Total Pembayaran</div>
            <div class="text-4xl font-extrabold text-gray-900">Rp {{ number_format($pembayaran->nominal) }}</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
            <div class="space-y-1 border-b border-gray-100 pb-4 md:border-0 md:pb-0">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Wali Pembayar</div>
                <div class="font-semibold text-gray-800">{{ $pembayaran->wali->nama }}</div>
                <div class="text-sm text-gray-500 font-mono">{{ $pembayaran->wali->no_hp }}</div>
            </div>
            
            <div class="space-y-1 border-b border-gray-100 pb-4 md:border-0 md:pb-0">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Metode Transaksi</div>
                <div class="font-medium text-gray-800">{{ $metodeLabel }}</div>
                <div class="text-xs text-gray-500 font-mono">{{ $pembayaran->tripay_ref }}</div>
            </div>
        </div>

        <div class="mt-8">
            <h6 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Rincian Item Pembayaran</h6>
            <div class="space-y-3">
                @if($pembayaran->tagihan)
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div>
                            <div class="font-bold text-gray-800">{{ $pembayaran->tagihan->jenisTagihan->nama }}</div>
                            <div class="text-xs text-indigo-600 font-medium">Santri: {{ $pembayaran->tagihan->santri->nama }} ({{ $pembayaran->tagihan->santri->nis }})</div>
                            <div class="text-xs text-gray-500">Periode: {{ $pembayaran->tagihan->periode }}</div>
                        </div>
                        <div class="font-bold text-gray-900 text-right">
                            Rp {{ number_format($pembayaran->tagihan->nominal) }}
                        </div>
                    </div>
                @endif

                @if($pembayaran->tagihan_ids)
                    @foreach(\App\Models\Tagihan::with(['santri', 'jenisTagihan'])->whereIn('id', $pembayaran->tagihan_ids)->get() as $t)
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <div>
                                <div class="font-bold text-gray-800">{{ $t->jenisTagihan->nama }}</div>
                                <div class="text-xs text-indigo-600 font-medium">Santri: {{ $t->santri->nama }} ({{ $t->santri->nis }})</div>
                                <div class="text-xs text-gray-500">Periode: {{ $t->periode }}</div>
                            </div>
                            <div class="font-bold text-gray-900 text-right">
                                Rp {{ number_format($t->nominal) }}
                            </div>
                        </div>
                    @endforeach
                @endif

                @if($pembayaran->topup_items)
                    @foreach($pembayaran->topup_items as $topup)
                        @php $s = \App\Models\Santri::find($topup['santri_id']); @endphp
                        <div class="flex justify-between items-center p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div>
                                <div class="font-bold text-emerald-800">Top Up Saldo Santri</div>
                                <div class="text-xs text-emerald-600 font-medium">Santri: {{ $s?->nama ?? 'N/A' }} ({{ $s?->nis ?? '-' }})</div>
                            </div>
                            <div class="font-bold text-emerald-900 text-right">
                                Rp {{ number_format($topup['nominal']) }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50/50 rounded-xl border border-gray-100">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Waktu Transaksi</div>
                <div class="text-sm text-gray-800"><span class="font-medium">Dibuat:</span> {{ $pembayaran->created_at->format('d/m/Y H:i:s') }}</div>
                <div class="text-sm text-gray-800"><span class="font-medium">Selesai:</span> {{ $pembayaran->paid_at?->format('d/m/Y H:i:s') ?? '-' }}</div>
            </div>
        </div>

        @if($pembayaran->status === 'pending' && $pembayaran->tripay_ref)
            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-center">
                <form method="POST" action="{{ route('admin.pembayaran.check-status', $pembayaran) }}">
                    @csrf
                    <button class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rotate-right"></i> Sinkronisasi Status Gateway
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
