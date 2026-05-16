@extends('layouts.app')
@section('title', $kategori->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('page-title', $kategori->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ $kategori->exists ? route('finance.kategoris.update', $kategori) : route('finance.kategoris.store') }}" class="max-w-2xl">
    @csrf
    @if($kategori->exists) @method('PUT') @endif

    <div class="glass-panel rounded-2xl p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Kode *</label>
            <input type="text" name="kode" value="{{ old('kode', $kategori->kode) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Tipe *</label>
            <select name="tipe" required class="w-full rounded-lg border-gray-300" onchange="filterAccounts(this.value)">
                <option value="pemasukan" @selected(old('tipe', $kategori->tipe) == 'pemasukan')>Pemasukan</option>
                <option value="pengeluaran" @selected(old('tipe', $kategori->tipe) == 'pengeluaran')>Pengeluaran</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Nama *</label>
            <input type="text" name="nama" value="{{ old('nama', $kategori->nama) }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Akun COA *</label>
            <select name="account_id" id="account_id" required class="w-full rounded-lg border-gray-300">
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" data-tipe="{{ $acc->tipe }}" @selected(old('account_id', $kategori->account_id) == $acc->id)>
                        [{{ $acc->tipe == 'revenue' ? 'PEND' : 'BEBAN' }}] {{ $acc->kode }} - {{ $acc->nama }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Pemasukan → akun Revenue. Pengeluaran → akun Expense.</p>
        </div>
        <div class="md:col-span-2 flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $kategori->is_active ?? true))>
            <label for="is_active" class="text-sm">Aktif</label>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Keterangan</label>
            <textarea name="keterangan" rows="2" class="w-full rounded-lg border-gray-300">{{ old('keterangan', $kategori->keterangan) }}</textarea>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">Simpan</button>
        <a href="{{ route('finance.kategoris.index') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>
    @if($errors->any())
    <div class="mt-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded text-sm">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif
</form>

<script>
function filterAccounts(tipe) {
    const sel = document.getElementById('account_id');
    const target = tipe == 'pemasukan' ? 'revenue' : 'expense';
    Array.from(sel.options).forEach(opt => {
        opt.hidden = opt.dataset.tipe && opt.dataset.tipe !== target;
    });
}
filterAccounts(document.querySelector('[name=tipe]').value);
</script>
@endsection
