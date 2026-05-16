@extends('layouts.app')
@section('title', 'Laporan Laba Rugi')
@section('page-title', 'Laporan Laba Rugi')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="GET" class="glass-panel p-4 rounded-2xl mb-6 flex flex-wrap items-end gap-3">
    <div>
        <label class="block text-xs font-semibold mb-1">Dari</label>
        <input type="date" name="dari" value="{{ $dari }}" class="rounded-lg border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1">Sampai</label>
        <input type="date" name="sampai" value="{{ $sampai }}" class="rounded-lg border-gray-300 text-sm">
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">Tampilkan</button>
    <button onclick="window.print()" type="button" class="px-4 py-2 bg-gray-200 rounded-lg text-sm font-semibold ml-auto">
        <i class="fa-solid fa-print mr-1"></i> Print
    </button>
</form>

<div class="glass-panel rounded-2xl p-8">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold">LAPORAN LABA RUGI</h2>
        <p class="text-sm text-gray-500">Periode {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}</p>
    </div>

    <table class="w-full text-sm">
        <tbody>
            <tr class="bg-green-50">
                <td colspan="3" class="px-4 py-2 font-bold uppercase text-green-700">Pendapatan</td>
            </tr>
            @forelse($data['pendapatan'] as $row)
            <tr class="border-b">
                <td class="px-4 py-2 pl-8 font-mono text-xs text-gray-500">{{ $row->kode }}</td>
                <td class="px-4 py-2">{{ $row->nama }}</td>
                <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($row->saldo) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-3 text-center text-gray-400">Tidak ada pendapatan.</td></tr>
            @endforelse
            <tr class="bg-green-100 font-bold">
                <td colspan="2" class="px-4 py-2">TOTAL PENDAPATAN</td>
                <td class="px-4 py-2 text-right text-green-700">Rp {{ number_format($data['total_pendapatan']) }}</td>
            </tr>

            <tr><td colspan="3" class="py-2"></td></tr>

            <tr class="bg-red-50">
                <td colspan="3" class="px-4 py-2 font-bold uppercase text-red-700">Beban</td>
            </tr>
            @forelse($data['beban'] as $row)
            <tr class="border-b">
                <td class="px-4 py-2 pl-8 font-mono text-xs text-gray-500">{{ $row->kode }}</td>
                <td class="px-4 py-2">{{ $row->nama }}</td>
                <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($row->saldo) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-3 text-center text-gray-400">Tidak ada beban.</td></tr>
            @endforelse
            <tr class="bg-red-100 font-bold">
                <td colspan="2" class="px-4 py-2">TOTAL BEBAN</td>
                <td class="px-4 py-2 text-right text-red-700">Rp {{ number_format($data['total_beban']) }}</td>
            </tr>

            <tr><td colspan="3" class="py-2"></td></tr>

            <tr class="bg-indigo-100 font-extrabold text-lg">
                <td colspan="2" class="px-4 py-3">{{ $data['laba_bersih'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</td>
                <td class="px-4 py-3 text-right {{ $data['laba_bersih'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    Rp {{ number_format(abs($data['laba_bersih'])) }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
