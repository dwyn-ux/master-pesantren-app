@extends('layouts.app')
@section('title', $tipe == 'masuk' ? 'Pemasukan Baru' : 'Pengeluaran Baru')
@section('page-title', $tipe == 'masuk' ? 'Catat Pemasukan' : 'Catat Pengeluaran')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ $tipe == 'masuk' ? route('finance.transaksi.masuk.store') : route('finance.transaksi.keluar.store') }}" enctype="multipart/form-data" class="max-w-3xl">
    @csrf

    <div class="glass-panel rounded-2xl p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Tanggal *</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $transaksi->tanggal) }}" required class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Kas / Bank *</label>
                <select name="kas_bank_id" required class="w-full rounded-lg border-gray-300">
                    <option value="">— Pilih —</option>
                    @foreach($kasBanks as $kb)
                        <option value="{{ $kb->id }}" @selected(old('kas_bank_id')==$kb->id)>
                            {{ $kb->nama }} ({{ strtoupper($kb->tipe) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Kategori *</label>
                <select name="kategori_id" required class="w-full rounded-lg border-gray-300">
                    <option value="">— Pilih kategori —</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" @selected(old('kategori_id')==$k->id)>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Nominal (Rp) *</label>
                <input type="number" step="0.01" min="0.01" name="nominal" value="{{ old('nominal') }}" required class="w-full rounded-lg border-gray-300 text-lg font-bold">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Pihak (opsional)</label>
                <input type="text" name="pihak" value="{{ old('pihak') }}" placeholder="Nama vendor / donatur" class="w-full rounded-lg border-gray-300">
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
        <button type="submit" class="px-6 py-3 {{ $tipe == 'masuk' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white rounded-lg font-bold">
            <i class="fa-solid fa-check mr-1"></i> Simpan & Posting
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
