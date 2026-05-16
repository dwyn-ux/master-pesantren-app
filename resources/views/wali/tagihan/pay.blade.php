@extends('layouts.app')
@section('title', 'Bayar Tagihan')
@section('page-title', 'Pembayaran Tagihan')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Tagihan Info --}}
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
            <h5 class="font-bold text-gray-800"><i class="fa-solid fa-file-invoice-dollar text-indigo-500 mr-2"></i>Detail Tagihan</h5>
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
            @if($tagihan->due_date)
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Jatuh Tempo</span>
                <span class="font-semibold {{ \Carbon\Carbon::parse($tagihan->due_date)->isPast() ? 'text-red-600' : 'text-gray-800' }}">
                    {{ \Carbon\Carbon::parse($tagihan->due_date)->translatedFormat('d F Y') }}
                </span>
            </div>
            @endif
            <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">Total Pembayaran</span>
                <span class="text-2xl font-extrabold text-indigo-700">Rp {{ number_format($tagihan->nominal) }}</span>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-3">
        <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Payment Method --}}
    <form action="{{ route('wali.tagihan.pay.process', $tagihan) }}" method="POST">
        @csrf
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
                <h5 class="font-bold text-gray-800"><i class="fa-solid fa-credit-card text-indigo-500 mr-2"></i>Pilih Metode Pembayaran</h5>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($metodeOptions as $value => $label)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="metode" value="{{ $value }}" class="peer sr-only" {{ old('metode') === $value ? 'checked' : ($loop->first ? 'checked' : '') }}>
                        <div class="flex items-center gap-3 p-4 border-2 rounded-xl transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 border-gray-200 hover:border-indigo-300">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                {{ in_array($value, ['va_bca', 'va_mandiri']) ? 'bg-blue-100 text-blue-600' :
                                   ($value === 'qris' ? 'bg-gray-100 text-gray-700' :
                                   ($value === 'gopay' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600')) }}">
                                @if(in_array($value, ['va_bca', 'va_mandiri']))
                                    <i class="fa-solid fa-building-columns"></i>
                                @elseif($value === 'qris')
                                    <i class="fa-solid fa-qrcode"></i>
                                @else
                                    <i class="fa-solid fa-mobile-screen-button"></i>
                                @endif
                            </div>
                            <span class="text-sm font-medium text-gray-700 peer-checked:text-indigo-700">{{ $label }}</span>
                            <div class="ml-auto w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-500 flex items-center justify-center transition-all">
                                <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                <div class="mt-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-700 flex items-start gap-3">
                    <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0"></i>
                    <span>Anda akan diarahkan ke halaman pembayaran Tripay. Setelah pembayaran berhasil, status tagihan akan diperbarui secara otomatis.</span>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-4">
                <a href="{{ route('wali.tagihan.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
                    <i class="fa-solid fa-lock text-xs"></i>
                    Lanjutkan Pembayaran &rarr;
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
