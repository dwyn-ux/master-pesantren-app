@extends('layouts.app')
@section('title', 'Status Tagihan')
@section('page-title', 'Status Pembayaran')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Payment Status Banner --}}
    @if($pembayaran)
        @if($pembayaran->status === 'paid' || $tagihan->status === 'lunas')
        <div class="p-6 rounded-2xl shadow-md text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%)">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-3xl flex-shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Pembayaran Berhasil!</h3>
                    <p class="text-emerald-100 text-sm">Tagihan telah lunas. Terima kasih atas pembayaran Anda.</p>
                    @if($pembayaran->paid_at)
                        <p class="text-emerald-200 text-xs mt-1">Dibayar pada {{ \Carbon\Carbon::parse($pembayaran->paid_at)->translatedFormat('d F Y, H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>

        @elseif(in_array($pembayaran->status, ['expired', 'failed']))
        <div class="p-6 rounded-2xl shadow-md text-white" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-3xl flex-shrink-0">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">{{ $pembayaran->status === 'expired' ? 'Pembayaran Kadaluarsa' : 'Pembayaran Gagal' }}</h3>
                    <p class="text-red-100 text-sm">Silakan coba lagi dengan membuat pembayaran baru.</p>
                </div>
            </div>
        </div>

        @else
        <div class="p-6 rounded-2xl shadow-md text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%)">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-3xl flex-shrink-0">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Menunggu Pembayaran</h3>
                    <p class="text-amber-100 text-sm">Selesaikan pembayaran sebelum batas waktu habis.</p>
                </div>
            </div>
        </div>
        @endif
    @else
        <div class="p-6 rounded-2xl shadow-md" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)">
            <div class="flex items-center gap-4 text-white">
                <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-3xl flex-shrink-0">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Detail Tagihan</h3>
                    <p class="text-indigo-100 text-sm">Belum ada data pembayaran untuk tagihan ini.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Tagihan Detail --}}
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
            <h5 class="font-bold text-gray-800"><i class="fa-solid fa-receipt text-indigo-500 mr-2"></i>Rincian Tagihan</h5>
        </div>
        <div class="p-6 space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Santri</span>
                <span class="font-semibold text-gray-800">{{ $tagihan->santri->nama }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Jenis Tagihan</span>
                <span class="font-semibold text-gray-800">{{ $tagihan->jenisTagihan->nama ?? '-' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Periode</span>
                <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($tagihan->periode . '-01')->translatedFormat('F Y') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Status Tagihan</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ $tagihan->status === 'lunas' ? 'bg-emerald-100 text-emerald-700' :
                       ($tagihan->status === 'sebagian' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                    {{ $tagihan->status === 'lunas' ? 'Lunas' : ($tagihan->status === 'sebagian' ? 'Sebagian' : 'Belum Bayar') }}
                </span>
            </div>
            <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">Nominal</span>
                <span class="text-xl font-extrabold text-indigo-700">Rp {{ number_format($tagihan->nominal) }}</span>
            </div>
        </div>
    </div>

    {{-- Payment Detail --}}
    @if($pembayaran)
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
            <h5 class="font-bold text-gray-800"><i class="fa-solid fa-credit-card text-indigo-500 mr-2"></i>Data Pembayaran</h5>
        </div>
        <div class="p-6 space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Referensi</span>
                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">{{ $pembayaran->tripay_ref }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Metode</span>
                <span class="font-semibold text-gray-800">{{ $pembayaran->tripay_channel ?? $pembayaran->metode ?? '-' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Status</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ $pembayaran->status === 'paid' ? 'bg-emerald-100 text-emerald-700' :
                       ($pembayaran->status === 'pending' ? 'bg-amber-100 text-amber-700' :
                       'bg-red-100 text-red-700') }}">
                    {{ match($pembayaran->status) {
                        'paid'    => 'Lunas',
                        'pending' => 'Menunggu',
                        'expired' => 'Kadaluarsa',
                        'failed'  => 'Gagal',
                        default   => ucfirst($pembayaran->status),
                    } }}
                </span>
            </div>
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row items-center gap-3">
        @if($pembayaran && $pembayaran->status === 'pending' && $pembayaran->payment_url ?? null)
        <a href="{{ $pembayaran->payment_url }}" target="_blank"
           class="w-full sm:w-auto text-center inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors shadow-sm">
            <i class="fa-solid fa-external-link"></i> Lanjut ke Halaman Pembayaran
        </a>
        @elseif($pembayaran && in_array($pembayaran->status, ['expired', 'failed']))
        <a href="{{ route('wali.tagihan.pay', $tagihan) }}"
           class="w-full sm:w-auto text-center inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors shadow-sm">
            <i class="fa-solid fa-rotate-right"></i> Coba Bayar Lagi
        </a>
        @endif

        <a href="{{ route('wali.tagihan.index') }}"
           class="w-full sm:w-auto text-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium text-sm transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Tagihan
        </a>
    </div>

</div>
@endsection
