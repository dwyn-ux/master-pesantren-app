@extends('layouts.app')
@section('title', $asset->exists ? 'Edit Aset' : 'Tambah Aset')
@section('page-title', $asset->exists ? 'Edit Aset' : 'Tambah Aset')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ $asset->exists ? route('finance.assets.update', $asset) : route('finance.assets.store') }}" class="max-w-3xl">
    @csrf
    @if($asset->exists) @method('PUT') @endif

    <div class="glass-panel rounded-2xl p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Kode Aset *</label>
            <input type="text" name="kode" value="{{ old('kode', $asset->kode) }}" required class="w-full rounded-lg border-gray-300 font-mono">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Kategori</label>
            <input type="text" name="kategori" value="{{ old('kategori', $asset->kategori) }}" placeholder="Bangunan/Kendaraan/Komputer" class="w-full rounded-lg border-gray-300">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Nama Aset *</label>
            <input type="text" name="nama" value="{{ old('nama', $asset->nama) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Tanggal Perolehan *</label>
            <input type="date" name="tanggal_perolehan" value="{{ old('tanggal_perolehan', optional($asset->tanggal_perolehan)->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Status *</label>
            <select name="status" required class="w-full rounded-lg border-gray-300">
                @foreach(['aktif','rusak','dijual','dihapus'] as $s)
                    <option value="{{ $s }}" @selected(old('status', $asset->status ?? 'aktif')==$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Harga Perolehan (Rp) *</label>
            <input type="number" step="0.01" min="0" name="harga_perolehan" value="{{ old('harga_perolehan', $asset->harga_perolehan ?? 0) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Nilai Residu (Rp)</label>
            <input type="number" step="0.01" min="0" name="nilai_residu" value="{{ old('nilai_residu', $asset->nilai_residu ?? 0) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Umur Ekonomis (bulan) *</label>
            <input type="number" min="1" name="umur_ekonomis_bulan" value="{{ old('umur_ekonomis_bulan', $asset->umur_ekonomis_bulan ?? 60) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Metode Penyusutan *</label>
            <select name="metode_penyusutan" required class="w-full rounded-lg border-gray-300">
                <option value="garis_lurus" @selected(old('metode_penyusutan', $asset->metode_penyusutan ?? 'garis_lurus')=='garis_lurus')>Garis Lurus</option>
                <option value="saldo_menurun" @selected(old('metode_penyusutan', $asset->metode_penyusutan)=='saldo_menurun')>Saldo Menurun</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Akun COA</label>
            <select name="account_id" class="w-full rounded-lg border-gray-300">
                <option value="">— Pilih akun aktiva tetap —</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" @selected(old('account_id', $asset->account_id)==$acc->id)>{{ $acc->kode }} - {{ $acc->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Lokasi</label>
            <input type="text" name="lokasi" value="{{ old('lokasi', $asset->lokasi) }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Keterangan</label>
            <textarea name="keterangan" rows="2" class="w-full rounded-lg border-gray-300">{{ old('keterangan', $asset->keterangan) }}</textarea>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">Simpan</button>
        <a href="{{ route('finance.assets.index') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>
    @if($errors->any())
    <div class="mt-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded text-sm">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif
</form>
@endsection
