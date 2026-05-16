@extends('layouts.app')
@section('title', 'Buku Besar')
@section('page-title', 'Buku Besar')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="GET" class="glass-panel p-4 rounded-2xl mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold mb-1">Akun</label>
        <select name="account_id" required class="w-full rounded-lg border-gray-300 text-sm">
            <option value="">— Pilih Akun —</option>
            @foreach($accounts as $a)
                <option value="{{ $a->id }}" @selected($accountId == $a->id)>{{ $a->kode }} - {{ $a->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1">Dari</label>
        <input type="date" name="dari" value="{{ $dari }}" class="w-full rounded-lg border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1">Sampai</label>
        <input type="date" name="sampai" value="{{ $sampai }}" class="w-full rounded-lg border-gray-300 text-sm">
    </div>
    <div class="md:col-span-4">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">Tampilkan</button>
    </div>
</form>

@if($data)
<div class="glass-panel rounded-2xl p-6">
    <div class="mb-4">
        <h2 class="text-xl font-bold">{{ $data['account']->kode }} - {{ $data['account']->nama }}</h2>
        <p class="text-sm text-gray-500">Periode {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2 text-left">Nomor Jurnal</th>
                <th class="px-3 py-2 text-left">Keterangan</th>
                <th class="px-3 py-2 text-right">Debit</th>
                <th class="px-3 py-2 text-right">Kredit</th>
                <th class="px-3 py-2 text-right">Saldo</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <tr class="bg-gray-50">
                <td colspan="5" class="px-3 py-2 font-semibold italic text-gray-600">Saldo Awal</td>
                <td class="px-3 py-2 text-right font-bold">Rp {{ number_format($data['saldo_awal']) }}</td>
            </tr>
            @forelse($data['items'] as $row)
            <tr>
                <td class="px-3 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                <td class="px-3 py-2 font-mono text-xs">{{ $row['nomor'] }}</td>
                <td class="px-3 py-2">{{ $row['keterangan'] }}</td>
                <td class="px-3 py-2 text-right">{{ $row['debit'] > 0 ? 'Rp ' . number_format($row['debit']) : '-' }}</td>
                <td class="px-3 py-2 text-right">{{ $row['kredit'] > 0 ? 'Rp ' . number_format($row['kredit']) : '-' }}</td>
                <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($row['saldo']) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-3 py-4 text-center text-gray-400 italic">Tidak ada mutasi.</td></tr>
            @endforelse
            <tr class="bg-indigo-50 font-bold">
                <td colspan="5" class="px-3 py-2">Saldo Akhir</td>
                <td class="px-3 py-2 text-right text-indigo-700">Rp {{ number_format($data['saldo_akhir']) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@else
<div class="glass-panel rounded-2xl p-12 text-center text-gray-400">
    <i class="fa-solid fa-book-open text-4xl mb-3"></i>
    <p>Pilih akun untuk melihat buku besar.</p>
</div>
@endif
@endsection
