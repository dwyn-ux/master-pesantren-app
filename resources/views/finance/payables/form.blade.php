@extends('layouts.app')
@section('title', 'Hutang Baru')
@section('page-title', 'Catat Hutang Vendor')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ route('finance.payables.store') }}" class="max-w-3xl">
    @csrf
    <div class="glass-panel rounded-2xl p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Tanggal *</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Jatuh Tempo *</label>
            <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo', now()->addDays(30)->toDateString()) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Vendor *</label>
            <select name="vendor_id" required class="w-full rounded-lg border-gray-300">
                <option value="">— Pilih vendor —</option>
                @foreach($vendors as $v)
                    <option value="{{ $v->id }}" @selected(old('vendor_id')==$v->id)>{{ $v->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Nominal Hutang (Rp) *</label>
            <input type="number" step="0.01" min="0.01" name="nominal" value="{{ old('nominal') }}" required class="w-full rounded-lg border-gray-300 text-lg font-bold">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Akun Beban/Aset *</label>
            <select name="account_id" required class="w-full rounded-lg border-gray-300">
                <option value="">— Pilih akun —</option>
                @foreach($accounts as $a)
                    <option value="{{ $a->id }}" @selected(old('account_id')==$a->id)>[{{ $a->tipe == 'expense' ? 'BEBAN' : 'ASET' }}] {{ $a->kode }} - {{ $a->nama }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Pilih akun yang menerima nilai hutang ini (mis: Beban Maintenance, Persediaan).</p>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Keterangan</label>
            <textarea name="keterangan" rows="2" class="w-full rounded-lg border-gray-300">{{ old('keterangan') }}</textarea>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">Simpan</button>
        <a href="{{ route('finance.payables.index') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>
    @if($errors->any())<div class="mt-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded text-sm">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
</form>
@endsection
