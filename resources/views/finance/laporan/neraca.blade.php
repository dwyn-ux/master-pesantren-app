@extends('layouts.app')
@section('title', 'Neraca')
@section('page-title', 'Laporan Neraca (Posisi Keuangan)')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="GET" class="glass-panel p-4 rounded-2xl mb-6 flex items-end gap-3">
    <div>
        <label class="block text-xs font-semibold mb-1">Per Tanggal</label>
        <input type="date" name="sampai" value="{{ $sampai }}" class="rounded-lg border-gray-300 text-sm">
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">Tampilkan</button>
    <button onclick="window.print()" type="button" class="px-4 py-2 bg-gray-200 rounded-lg text-sm font-semibold ml-auto">
        <i class="fa-solid fa-print mr-1"></i> Print
    </button>
</form>

<div class="glass-panel rounded-2xl p-8">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold">LAPORAN POSISI KEUANGAN (NERACA)</h2>
        <p class="text-sm text-gray-500">Per {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="font-bold text-lg bg-blue-50 px-4 py-2 mb-2 text-blue-700">AKTIVA</h3>
            <table class="w-full text-sm">
                @foreach($data['aset'] as $row)
                <tr class="border-b">
                    <td class="px-3 py-2 font-mono text-xs">{{ $row->kode }}</td>
                    <td class="px-3 py-2">{{ $row->nama }}</td>
                    <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($row->saldo) }}</td>
                </tr>
                @endforeach
                <tr class="bg-blue-100 font-bold">
                    <td colspan="2" class="px-3 py-2">TOTAL AKTIVA</td>
                    <td class="px-3 py-2 text-right text-blue-700">Rp {{ number_format($data['total_aset']) }}</td>
                </tr>
            </table>
        </div>

        <div>
            <h3 class="font-bold text-lg bg-amber-50 px-4 py-2 mb-2 text-amber-700">PASIVA</h3>
            <table class="w-full text-sm">
                <tr class="bg-amber-50/50"><td colspan="3" class="px-3 py-1 font-semibold text-xs uppercase">Liabilitas</td></tr>
                @forelse($data['liabilitas'] as $row)
                <tr class="border-b">
                    <td class="px-3 py-2 font-mono text-xs">{{ $row->kode }}</td>
                    <td class="px-3 py-2">{{ $row->nama }}</td>
                    <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($row->saldo) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-3 py-2 text-gray-400 text-xs italic">— kosong —</td></tr>
                @endforelse

                <tr class="bg-amber-50/50"><td colspan="3" class="px-3 py-1 font-semibold text-xs uppercase">Ekuitas</td></tr>
                @forelse($data['ekuitas'] as $row)
                <tr class="border-b">
                    <td class="px-3 py-2 font-mono text-xs">{{ $row->kode }}</td>
                    <td class="px-3 py-2">{{ $row->nama }}</td>
                    <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($row->saldo) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-3 py-2 text-gray-400 text-xs italic">— kosong —</td></tr>
                @endforelse
                <tr class="border-b">
                    <td class="px-3 py-2 font-mono text-xs">—</td>
                    <td class="px-3 py-2 italic">Laba Tahun Berjalan</td>
                    <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($data['laba_berjalan']) }}</td>
                </tr>

                <tr class="bg-amber-100 font-bold">
                    <td colspan="2" class="px-3 py-2">TOTAL PASIVA</td>
                    <td class="px-3 py-2 text-right text-amber-700">Rp {{ number_format($data['total_liabilitas'] + $data['total_ekuitas'] + $data['laba_berjalan']) }}</td>
                </tr>
            </table>
        </div>
    </div>

    @php
        $selisih = $data['total_aset'] - ($data['total_liabilitas'] + $data['total_ekuitas'] + $data['laba_berjalan']);
    @endphp
    @if(abs($selisih) > 0.01)
    <div class="mt-6 bg-red-50 border border-red-200 text-red-700 p-3 rounded text-sm">
        ⚠ Neraca tidak balance! Selisih: Rp {{ number_format($selisih) }}
    </div>
    @else
    <div class="mt-6 text-center text-green-600 font-bold">✓ Neraca Balance</div>
    @endif
</div>
@endsection
