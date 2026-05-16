@extends('layouts.app')
@section('title', $kasBank->exists ? 'Edit Kas/Bank' : 'Tambah Kas/Bank')
@section('page-title', $kasBank->exists ? 'Edit Kas/Bank' : 'Tambah Kas/Bank')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ $kasBank->exists ? route('finance.kas-banks.update', $kasBank) : route('finance.kas-banks.store') }}" class="max-w-2xl">
    @csrf
    @if($kasBank->exists) @method('PUT') @endif

    <div class="glass-panel rounded-2xl p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Kode *</label>
                <input type="text" name="kode" value="{{ old('kode', $kasBank->kode) }}" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tipe *</label>
                <select name="tipe" required class="w-full rounded-lg border-gray-300">
                    <option value="kas" @selected(old('tipe', $kasBank->tipe) == 'kas')>Kas Tunai</option>
                    <option value="bank" @selected(old('tipe', $kasBank->tipe) == 'bank')>Bank</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Nama *</label>
                <input type="text" name="nama" value="{{ old('nama', $kasBank->nama) }}" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Nama Bank</label>
                <input type="text" name="nama_bank" value="{{ old('nama_bank', $kasBank->nama_bank) }}" class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">No. Rekening</label>
                <input type="text" name="no_rekening" value="{{ old('no_rekening', $kasBank->no_rekening) }}" class="w-full rounded-lg border-gray-300">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Atas Nama</label>
                <input type="text" name="atas_nama" value="{{ old('atas_nama', $kasBank->atas_nama) }}" class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Akun COA *</label>
                <select name="account_id" required class="w-full rounded-lg border-gray-300">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" @selected(old('account_id', $kasBank->account_id) == $acc->id)>{{ $acc->kode }} - {{ $acc->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Saldo Awal</label>
                <input type="number" step="0.01" min="0" name="saldo_awal" value="{{ old('saldo_awal', $kasBank->saldo_awal ?? 0) }}" required class="w-full rounded-lg border-gray-300">
            </div>
            <div class="md:col-span-2 flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $kasBank->is_active ?? true))>
                <label for="is_active" class="text-sm">Aktif</label>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full rounded-lg border-gray-300">{{ old('keterangan', $kasBank->keterangan) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">Simpan</button>
        <a href="{{ route('finance.kas-banks.index') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>
    @if($errors->any())
    <div class="mt-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif
</form>
@endsection
