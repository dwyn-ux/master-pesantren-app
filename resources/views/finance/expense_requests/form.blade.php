@extends('layouts.app')
@section('title', 'Permintaan Pengeluaran Baru')
@section('page-title', 'Buat Permintaan Pengeluaran')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ route('finance.expense-requests.store') }}" enctype="multipart/form-data" class="max-w-3xl">
    @csrf

    <div class="glass-panel rounded-2xl p-5 mb-4 bg-blue-50 border-l-4 border-blue-500">
        <p class="text-sm text-gray-700">
            <i class="fa-solid fa-circle-info text-blue-600 mr-1"></i>
            <strong>Threshold Approval:</strong>
            Kepala Pondok ≥ <strong>Rp {{ number_format($thresholdK) }}</strong>,
            Yayasan ≥ <strong>Rp {{ number_format($thresholdY) }}</strong>.
            Di bawah itu otomatis approved.
        </p>
    </div>

    <div class="glass-panel rounded-2xl p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Tanggal *</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Nominal (Rp) *</label>
            <input type="number" step="0.01" min="0.01" name="nominal" value="{{ old('nominal') }}" required class="w-full rounded-lg border-gray-300 text-lg font-bold">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Judul *</label>
            <input type="text" name="judul" value="{{ old('judul') }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Deskripsi / Justifikasi *</label>
            <textarea name="deskripsi" rows="3" required class="w-full rounded-lg border-gray-300">{{ old('deskripsi') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Kategori Pengeluaran *</label>
            <select name="kategori_id" required class="w-full rounded-lg border-gray-300">
                <option value="">— Pilih —</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}" @selected(old('kategori_id')==$k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Dibayar Dari Kas/Bank *</label>
            <select name="kas_bank_id" required class="w-full rounded-lg border-gray-300">
                <option value="">— Pilih —</option>
                @foreach($kasBanks as $kb)
                    <option value="{{ $kb->id }}" @selected(old('kas_bank_id')==$kb->id)>{{ $kb->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Vendor (opsional)</label>
            <select name="vendor_id" class="w-full rounded-lg border-gray-300">
                <option value="">— Tidak ada —</option>
                @foreach($vendors as $v)
                    <option value="{{ $v->id }}">{{ $v->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Bukti / Quotation (opsional)</label>
            <input type="file" name="bukti" accept="image/*,.pdf" class="w-full text-sm">
        </div>
    </div>

    <div class="flex gap-3">
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">
            <i class="fa-solid fa-paper-plane mr-1"></i> Submit
        </button>
        <a href="{{ route('finance.expense-requests.index') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>
</form>
@endsection
