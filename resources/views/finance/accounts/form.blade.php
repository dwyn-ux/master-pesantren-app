@extends('layouts.app')
@section('title', $account->exists ? 'Edit Akun' : 'Tambah Akun')
@section('page-title', $account->exists ? 'Edit Akun COA' : 'Tambah Akun COA')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ $account->exists ? route('finance.accounts.update', $account) : route('finance.accounts.store') }}" class="max-w-2xl">
    @csrf
    @if($account->exists) @method('PUT') @endif

    <div class="glass-panel rounded-2xl p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Kode *</label>
            <input type="text" name="kode" value="{{ old('kode', $account->kode) }}" required class="w-full rounded-lg border-gray-300 font-mono">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Tipe *</label>
            <select name="tipe" required class="w-full rounded-lg border-gray-300">
                @foreach(\App\Models\Finance\Account::TIPE as $k => $v)
                    <option value="{{ $k }}" @selected(old('tipe', $account->tipe) == $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Nama Akun *</label>
            <input type="text" name="nama" value="{{ old('nama', $account->nama) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Saldo Normal *</label>
            <select name="saldo_normal" required class="w-full rounded-lg border-gray-300">
                <option value="debit" @selected(old('saldo_normal', $account->saldo_normal) == 'debit')>Debit</option>
                <option value="kredit" @selected(old('saldo_normal', $account->saldo_normal) == 'kredit')>Kredit</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Parent</label>
            <select name="parent_id" class="w-full rounded-lg border-gray-300">
                <option value="">— Tidak ada —</option>
                @foreach($parents as $p)
                    <option value="{{ $p->id }}" @selected(old('parent_id', $account->parent_id) == $p->id)>{{ $p->kode }} - {{ $p->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_kas_bank" value="1" id="is_kas_bank" @checked(old('is_kas_bank', $account->is_kas_bank))>
            <label for="is_kas_bank" class="text-sm">Akun Kas/Bank</label>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $account->is_active ?? true))>
            <label for="is_active" class="text-sm">Aktif</label>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Keterangan</label>
            <textarea name="keterangan" rows="2" class="w-full rounded-lg border-gray-300">{{ old('keterangan', $account->keterangan) }}</textarea>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">Simpan</button>
        <a href="{{ route('finance.accounts.index') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>
    @if($errors->any())
    <div class="mt-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded text-sm">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif
</form>
@endsection
