@extends('layouts.app')
@section('title', 'Neraca Saldo')
@section('page-title', 'Neraca Saldo (Trial Balance)')

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
</form>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama Akun</th>
                <th class="px-4 py-3 text-right">Total Debit</th>
                <th class="px-4 py-3 text-right">Total Kredit</th>
                <th class="px-4 py-3 text-right">Saldo</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($data['rows'] as $row)
            <tr class="hover:bg-indigo-50/30">
                <td class="px-4 py-2 font-mono text-xs">{{ $row['account']->kode }}</td>
                <td class="px-4 py-2">{{ $row['account']->nama }}</td>
                <td class="px-4 py-2 text-right">Rp {{ number_format($row['debit']) }}</td>
                <td class="px-4 py-2 text-right">Rp {{ number_format($row['kredit']) }}</td>
                <td class="px-4 py-2 text-right font-bold {{ $row['saldo'] < 0 ? 'text-red-600' : '' }}">
                    Rp {{ number_format($row['saldo']) }}
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
        <tfoot class="bg-indigo-50 font-bold">
            <tr>
                <td colspan="2" class="px-4 py-3">TOTAL</td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($data['total_debit']) }}</td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($data['total_kredit']) }}</td>
                <td class="px-4 py-3 text-right">
                    @if(abs($data['total_debit'] - $data['total_kredit']) < 0.01)
                        <span class="text-green-600">✓ Balance</span>
                    @else
                        <span class="text-red-600">✗ Tidak Balance</span>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
