@extends('layouts.app')
@section('title', 'Buat Pembayaran')
@section('page-title', 'Buat Pembayaran Kasir')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-3xl mx-auto">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
        <h6 class="text-lg font-bold text-gray-800">Konfirmasi Pembayaran Tagihan</h6>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/50 flex flex-col justify-center">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Data Santri</div>
                <div class="font-semibold text-gray-800">{{ $tagihan->santri->nama }}</div>
                <div class="text-sm text-gray-500 font-mono">{{ $tagihan->santri->nis }}</div>
            </div>
            
            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/50 flex flex-col justify-center">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Wali Pembayar</div>
                <div class="font-semibold text-gray-800">{{ $wali->nama }}</div>
                <div class="text-sm text-gray-500 font-mono">{{ $wali->no_hp }}</div>
            </div>
            
            <div class="p-4 rounded-xl border border-indigo-100 bg-indigo-50/30 flex flex-col justify-center">
                <div class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-1">Rincian Tagihan</div>
                <div class="font-semibold text-indigo-900">{{ $tagihan->jenisTagihan->nama }}</div>
                <div class="text-sm text-indigo-700">Periode {{ $tagihan->periode }}</div>
            </div>
            
            <div class="p-4 rounded-xl border border-green-200 bg-green-50/50 flex flex-col justify-center items-end">
                <div class="text-xs font-bold text-green-600 uppercase tracking-wider mb-1">Total Nominal</div>
                <div class="text-2xl font-bold text-green-700">Rp {{ number_format($tagihan->nominal) }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pembayaran.store') }}">
            @csrf
            <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Metode Pembayaran</label>
                <div class="relative">
                    <select name="metode" class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('metode') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all bg-white appearance-none" required>
                        @foreach($metodeOptions as $key => $label)
                            <option value="{{ $key }}" @selected(old('metode') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-money-bill-transfer text-gray-400"></i>
                    </div>
                </div>
                @error('metode')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                
                <div class="mt-3 bg-blue-50/50 border border-blue-100 rounded-xl p-4 flex gap-3 text-blue-800">
                    <i class="fa-solid fa-circle-info text-blue-500 text-lg mt-0.5"></i>
                    <p class="text-sm leading-relaxed">
                        Jika memilih <strong>Manual Kasir</strong>, tagihan akan langsung ditandai Lunas. Jika memilih metode online (VA/QRIS/E-Wallet), pembayaran butuh diproses via sistem Tripay oleh wali.
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-100 justify-end">
                <a href="{{ route('admin.tagihan.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors text-center">Batal</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2 text-lg">
                    <i class="fa-solid fa-receipt"></i> Proses Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
