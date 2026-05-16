@extends('layouts.app')
@section('title', $petty->exists ? 'Edit Kas Kecil' : 'Buka Kas Kecil')
@section('page-title', $petty->exists ? 'Edit Kas Kecil' : 'Buka Kas Kecil Baru')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ $petty->exists ? route('finance.petty-cash.update', $petty) : route('finance.petty-cash.store') }}" class="max-w-2xl">
    @csrf
    @if($petty->exists) @method('PUT') @endif

    <div class="glass-panel rounded-2xl p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Penanggung Jawab *</label>
            <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $petty->penanggung_jawab) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', optional($petty->tanggal)->format('Y-m-d') ?? now()->toDateString()) }}" class="w-full rounded-lg border-gray-300">
        </div>
        @if(!$petty->exists)
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Saldo Awal (Rp) *</label>
            <input type="number" step="0.01" min="0" name="saldo_awal" value="{{ old('saldo_awal', 0) }}" required class="w-full rounded-lg border-gray-300 text-lg font-bold">
            <p class="text-xs text-gray-500 mt-1">Top up bisa dilakukan dari kas/bank besar setelah dibuat.</p>
        </div>
        @endif
        <div class="md:col-span-2 flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $petty->is_active ?? true))>
            <label for="is_active" class="text-sm">Aktif</label>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Keterangan</label>
            <textarea name="keterangan" rows="2" class="w-full rounded-lg border-gray-300">{{ old('keterangan', $petty->keterangan) }}</textarea>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">Simpan</button>
        <a href="{{ route('finance.petty-cash.index') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>
</form>
@endsection
