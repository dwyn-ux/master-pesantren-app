@extends('layouts.app')
@section('title', 'Detail Aset')
@section('page-title', $asset->nama)

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 glass-panel rounded-2xl p-6">
        <p class="text-xs text-gray-500 font-bold uppercase">Kode</p>
        <p class="text-lg font-mono font-bold mb-3">{{ $asset->kode }}</p>

        <p class="text-xs text-gray-500 font-bold uppercase">Kategori</p>
        <p class="font-semibold mb-3">{{ $asset->kategori ?: '-' }}</p>

        <p class="text-xs text-gray-500 font-bold uppercase">Tanggal Perolehan</p>
        <p class="font-semibold mb-3">{{ $asset->tanggal_perolehan->format('d F Y') }}</p>

        <p class="text-xs text-gray-500 font-bold uppercase">Lokasi</p>
        <p class="font-semibold mb-3">{{ $asset->lokasi ?: '-' }}</p>

        <p class="text-xs text-gray-500 font-bold uppercase">Status</p>
        <p class="font-semibold mb-3 capitalize">{{ $asset->status }}</p>

        <hr class="my-4">

        <p class="text-xs text-gray-500 font-bold uppercase">Harga Perolehan</p>
        <p class="text-lg font-bold mb-2">Rp {{ number_format($asset->harga_perolehan) }}</p>

        <p class="text-xs text-gray-500 font-bold uppercase">Akumulasi Penyusutan</p>
        <p class="text-lg font-bold text-amber-600 mb-2">Rp {{ number_format($asset->akumulasi_penyusutan) }}</p>

        <p class="text-xs text-gray-500 font-bold uppercase">Nilai Buku</p>
        <p class="text-2xl font-extrabold text-indigo-600">Rp {{ number_format($asset->nilai_buku) }}</p>

        <hr class="my-4">

        <p class="text-xs text-gray-500 font-bold uppercase">Umur Ekonomis</p>
        <p class="font-semibold mb-2">{{ $asset->umur_ekonomis_bulan }} bulan</p>

        <p class="text-xs text-gray-500 font-bold uppercase">Metode Penyusutan</p>
        <p class="font-semibold capitalize">{{ str_replace('_', ' ', $asset->metode_penyusutan) }}</p>

        <p class="text-xs text-gray-500 font-bold uppercase mt-2">Penyusutan / Bulan</p>
        <p class="font-semibold text-amber-600">Rp {{ number_format($asset->penyusutanBulanan()) }}</p>

        @if($asset->keterangan)
        <hr class="my-4">
        <p class="text-xs text-gray-500 font-bold uppercase">Keterangan</p>
        <p class="text-sm">{{ $asset->keterangan }}</p>
        @endif
    </div>

    <div class="lg:col-span-2 glass-panel rounded-2xl p-6">
        <h6 class="text-lg font-bold mb-4">Riwayat Penyusutan</h6>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase">
                <tr>
                    <th class="px-3 py-2 text-left">Periode</th>
                    <th class="px-3 py-2 text-right">Penyusutan</th>
                    <th class="px-3 py-2 text-right">Akumulasi</th>
                    <th class="px-3 py-2 text-right">Nilai Buku</th>
                    <th class="px-3 py-2 text-left">Jurnal</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($asset->depreciations as $d)
                <tr>
                    <td class="px-3 py-2 font-semibold">{{ \Carbon\Carbon::create($d->tahun, $d->bulan)->translatedFormat('M Y') }}</td>
                    <td class="px-3 py-2 text-right text-amber-600">Rp {{ number_format($d->nominal) }}</td>
                    <td class="px-3 py-2 text-right">Rp {{ number_format($d->akumulasi) }}</td>
                    <td class="px-3 py-2 text-right font-bold">Rp {{ number_format($d->nilai_buku) }}</td>
                    <td class="px-3 py-2 font-mono text-xs">{{ $d->journal?->nomor }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-3 py-6 text-center text-gray-400">Belum ada penyusutan diposting.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <a href="{{ route('finance.assets.edit', $asset) }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg font-semibold"><i class="fa-solid fa-pen mr-1"></i> Edit</a>
    <a href="{{ route('finance.assets.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg font-semibold">Kembali</a>
</div>
@endsection
