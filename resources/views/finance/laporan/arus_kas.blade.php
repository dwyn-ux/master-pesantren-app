@extends('layouts.app')
@section('title', 'Arus Kas')
@section('page-title', 'Laporan Arus Kas')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="GET" class="glass-panel p-4 rounded-2xl mb-6 flex items-end gap-3">
    <div>
        <label class="block text-xs font-semibold mb-1">Dari</label>
        <input type="date" name="dari" value="{{ $dari }}" class="rounded-lg border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1">Sampai</label>
        <input type="date" name="sampai" value="{{ $sampai }}" class="rounded-lg border-gray-300 text-sm">
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">Tampilkan</button>
</form>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="glass-panel rounded-2xl p-4 border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 font-semibold uppercase">Saldo Awal</p>
        <p class="text-xl font-bold text-blue-600">Rp {{ number_format($data['saldo_awal_total']) }}</p>
    </div>
    <div class="glass-panel rounded-2xl p-4 border-l-4 border-{{ $data['kas_bersih'] >= 0 ? 'green' : 'red' }}-500">
        <p class="text-xs text-gray-500 font-semibold uppercase">Mutasi Bersih</p>
        <p class="text-xl font-bold text-{{ $data['kas_bersih'] >= 0 ? 'green' : 'red' }}-600">Rp {{ number_format($data['kas_bersih']) }}</p>
    </div>
    <div class="glass-panel rounded-2xl p-4 border-l-4 border-indigo-500">
        <p class="text-xs text-gray-500 font-semibold uppercase">Saldo Akhir</p>
        <p class="text-xl font-bold text-indigo-600">Rp {{ number_format($data['saldo_akhir_total']) }}</p>
    </div>
</div>

<div class="glass-panel rounded-2xl p-6 mb-6">
    <h6 class="text-lg font-bold mb-4">Rekap Per Kas/Bank</h6>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Kas/Bank</th>
                <th class="px-3 py-2 text-right">Saldo Awal</th>
                <th class="px-3 py-2 text-right">Mutasi</th>
                <th class="px-3 py-2 text-right">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($data['per_kas'] as $row)
            <tr>
                <td class="px-3 py-2 font-semibold">{{ $row['kas_bank']->nama }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($row['saldo_awal']) }}</td>
                <td class="px-3 py-2 text-right {{ $row['mutasi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($row['mutasi']) }}</td>
                <td class="px-3 py-2 text-right font-bold">Rp {{ number_format($row['saldo_akhir']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="glass-panel rounded-2xl p-6">
        <h6 class="text-lg font-bold mb-4 text-green-700">Pemasukan Per Kategori</h6>
        @forelse($data['masuk_per_kategori'] as $row)
        <div class="flex justify-between border-b py-2">
            <span>{{ $row->nama }}</span>
            <span class="font-semibold text-green-600">Rp {{ number_format($row->total) }}</span>
        </div>
        @empty<p class="text-gray-400 text-sm">Tidak ada.</p>@endforelse
        <div class="flex justify-between mt-3 pt-2 border-t-2 font-bold">
            <span>TOTAL</span>
            <span class="text-green-700">Rp {{ number_format($data['total_masuk']) }}</span>
        </div>
    </div>

    <div class="glass-panel rounded-2xl p-6">
        <h6 class="text-lg font-bold mb-4 text-red-700">Pengeluaran Per Kategori</h6>
        @forelse($data['keluar_per_kategori'] as $row)
        <div class="flex justify-between border-b py-2">
            <span>{{ $row->nama }}</span>
            <span class="font-semibold text-red-600">Rp {{ number_format($row->total) }}</span>
        </div>
        @empty<p class="text-gray-400 text-sm">Tidak ada.</p>@endforelse
        <div class="flex justify-between mt-3 pt-2 border-t-2 font-bold">
            <span>TOTAL</span>
            <span class="text-red-700">Rp {{ number_format($data['total_keluar']) }}</span>
        </div>
    </div>
</div>
@endsection
