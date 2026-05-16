@extends('layouts.app')
@section('title', 'Keuangan Santri')
@section('page-title', 'Keuangan Santri')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-emerald-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Total Saldo Santri</p>
        <p class="text-xl font-bold text-emerald-600">Rp {{ number_format($summary['total_saldo']) }}</p>
        <p class="text-xs text-gray-400">Akumulasi wallet semua santri</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-amber-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Tagihan Belum Bayar</p>
        <p class="text-xl font-bold text-amber-600">Rp {{ number_format($summary['total_tagihan']) }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Pembayaran Bulan Ini</p>
        <p class="text-xl font-bold text-blue-600">Rp {{ number_format($summary['total_pembayaran_bulan']) }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-indigo-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Top Up Bulan Ini</p>
        <p class="text-xl font-bold text-indigo-600">Rp {{ number_format($summary['total_topup_bulan']) }}</p>
    </div>
</div>

<form method="GET" class="glass-panel p-4 rounded-2xl mb-4">
    <div class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS santri..." class="flex-1 rounded-lg border-gray-300 text-sm">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Cari</button>
    </div>
</form>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-4 py-3 text-left">NIS</th>
                <th class="px-4 py-3 text-left">Nama Santri</th>
                <th class="px-4 py-3 text-left">Kelas</th>
                <th class="px-4 py-3 text-right">Saldo Wallet</th>
                <th class="px-4 py-3 text-center">Limit</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $s)
            <tr class="hover:bg-indigo-50/30">
                <td class="px-4 py-3 font-mono text-xs">{{ $s->nis }}</td>
                <td class="px-4 py-3 font-semibold">{{ $s->nama }}</td>
                <td class="px-4 py-3 text-xs">{{ $s->kelas ?? '-' }}</td>
                <td class="px-4 py-3 text-right font-bold {{ $s->saldo < 50000 ? 'text-amber-600' : 'text-emerald-600' }}">Rp {{ number_format($s->saldo) }}</td>
                <td class="px-4 py-3 text-center text-xs">
                    @if($s->tipe_limit)
                        <span class="px-2 py-1 rounded bg-gray-100">{{ ucfirst($s->tipe_limit) }} Rp {{ number_format($s->nominal_limit ?? 0) }}</span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('finance.santri.show', $s) }}" class="text-indigo-600 font-semibold text-xs">Detail <i class="fa-solid fa-arrow-right ml-1"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada santri.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
