@extends('layouts.app')
@section('title', $vendor->exists ? 'Edit Vendor' : 'Tambah Vendor')
@section('page-title', $vendor->exists ? 'Edit Vendor' : 'Tambah Vendor')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ $vendor->exists ? route('finance.vendors.update', $vendor) : route('finance.vendors.store') }}" class="max-w-3xl">
    @csrf
    @if($vendor->exists) @method('PUT') @endif

    <div class="glass-panel rounded-2xl p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Kode *</label>
            <input type="text" name="kode" value="{{ old('kode', $vendor->kode) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Nama *</label>
            <input type="text" name="nama" value="{{ old('nama', $vendor->nama) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Kontak Person</label>
            <input type="text" name="kontak_person" value="{{ old('kontak_person', $vendor->kontak_person) }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Telepon</label>
            <input type="text" name="telepon" value="{{ old('telepon', $vendor->telepon) }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $vendor->email) }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">NPWP</label>
            <input type="text" name="npwp" value="{{ old('npwp', $vendor->npwp) }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Alamat</label>
            <textarea name="alamat" rows="2" class="w-full rounded-lg border-gray-300">{{ old('alamat', $vendor->alamat) }}</textarea>
        </div>
        <div class="md:col-span-2 mt-2">
            <h4 class="text-sm font-bold text-indigo-700 mb-2">Info Bank (untuk transfer)</h4>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Nama Bank</label>
            <input type="text" name="bank_nama" value="{{ old('bank_nama', $vendor->bank_nama) }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">No Rekening</label>
            <input type="text" name="bank_rekening" value="{{ old('bank_rekening', $vendor->bank_rekening) }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Atas Nama</label>
            <input type="text" name="bank_atas_nama" value="{{ old('bank_atas_nama', $vendor->bank_atas_nama) }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div class="md:col-span-2 flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $vendor->is_active ?? true))>
            <label for="is_active" class="text-sm">Aktif</label>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">Simpan</button>
        <a href="{{ route('finance.vendors.index') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>
</form>
@endsection
