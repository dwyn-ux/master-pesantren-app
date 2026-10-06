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

    {{-- Manual bank transfer --}}
    <form action="{{ route('wali.tagihan.pay.process', $tagihan) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="metode" value="manual_transfer">
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
                <h5 class="font-bold text-gray-800"><i class="fa-solid fa-building-columns text-indigo-500 mr-2"></i>Konfirmasi Transfer Manual</h5>
            </div>
            <div class="p-6 space-y-5">
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-sm text-blue-800 flex items-start gap-3">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <span>Upload foto atau PDF bukti transfer. Tagihan akan lunas setelah admin memeriksa dan mengonfirmasi bukti tersebut. Pembayaran online otomatis tetap tersedia dari halaman daftar tagihan.</span>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bukti Transfer</label>
                    <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required
                           class="block w-full text-sm text-gray-600 border border-gray-200 rounded-xl bg-white file:mr-4 file:py-3 file:px-4 file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold">
                    <p class="text-xs text-gray-500 mt-1">Format JPG, PNG, atau PDF. Maksimal 5 MB.</p>
                    @error('proof')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan untuk Admin (opsional)</label>
                    <textarea name="manual_note" rows="3" maxlength="500" placeholder="Contoh: transfer dari rekening atas nama ..."
                              class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">{{ old('manual_note') }}</textarea>
                    @error('manual_note')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-4">
                <a href="{{ route('wali.tagihan.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
                    <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                    Kirim Bukti Transfer
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
