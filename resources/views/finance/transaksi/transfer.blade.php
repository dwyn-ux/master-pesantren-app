@extends('layouts.app')
@section('title', 'Transfer Antar Kas')
@section('page-title', 'Transfer Antar Kas/Bank')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ route('finance.transaksi.transfer.store') }}" enctype="multipart/form-data" class="max-w-3xl">
    @csrf

    <div class="glass-panel rounded-2xl p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Tanggal *</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $transaksi->tanggal) }}" required class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Nominal (Rp) *</label>
                <input type="number" step="0.01" min="0.01" name="nominal" value="{{ old('nominal') }}" required class="w-full rounded-lg border-gray-300 text-lg font-bold">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Dari Kas/Bank *</label>
                <select name="kas_bank_id" required class="w-full rounded-lg border-gray-300">
                    <option value="">— Pilih sumber —</option>
                    @foreach($kasBanks as $kb)
                        <option value="{{ $kb->id }}" @selected(old('kas_bank_id')==$kb->id)>{{ $kb->nama }} (Saldo: Rp {{ number_format($kb->saldo_berjalan) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Ke Kas/Bank *</label>
                <select name="kas_bank_tujuan_id" required class="w-full rounded-lg border-gray-300">
                    <option value="">— Pilih tujuan —</option>
                    @foreach($kasBanks as $kb)
                        <option value="{{ $kb->id }}" @selected(old('kas_bank_tujuan_id')==$kb->id)>{{ $kb->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full rounded-lg border-gray-300">{{ old('keterangan') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Bukti (foto/PDF, max 5MB)</label>
                <input type="file" name="bukti" accept="image/*,.pdf" class="w-full text-sm">
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold">
            <i class="fa-solid fa-right-left mr-1"></i> Proses Transfer
        </button>
        <a href="{{ route('finance.transaksi.index') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>

    @if($errors->any())
        <div class="mt-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif
</form>
@endsection
