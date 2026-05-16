@extends('layouts.app')
@section('title', 'Keuangan ' . $santri->nama)
@section('page-title', 'Keuangan Santri: ' . $santri->nama)

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="glass-panel rounded-2xl p-6 mb-6 bg-gradient-to-br from-indigo-50 to-emerald-50">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <h2 class="text-xl font-bold">{{ $santri->nama }}</h2>
            <p class="text-sm text-gray-600">NIS: {{ $santri->nis }} · Kelas: {{ $santri->kelas ?? '-' }}</p>
            <p class="text-xs text-gray-500 mt-1">Wali: {{ $santri->wali->first()?->nama ?? '-' }}</p>
        </div>
        <div class="text-center">
            <p class="text-xs text-gray-500 uppercase font-bold">Saldo Wallet Saat Ini</p>
            <p class="text-3xl font-extrabold text-emerald-600">Rp {{ number_format($stats['saldo_sekarang']) }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-500 uppercase font-bold">Tagihan Belum Bayar</p>
            <p class="text-2xl font-bold text-amber-600">Rp {{ number_format($stats['tagihan_belum_bayar']) }}</p>
        </div>
    </div>
</div>

<form method="GET" class="mb-6 flex items-end gap-3 flex-wrap">
    <div>
        <label class="block text-xs font-semibold mb-1">Bulan</label>
        <select name="bulan" class="rounded-lg border-gray-300 text-sm">
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" @selected($bulan==$m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1">Tahun</label>
        <input type="number" name="tahun" value="{{ $tahun }}" class="w-24 rounded-lg border-gray-300 text-sm">
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Filter</button>
</form>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Top Up Bulan Ini</p>
        <p class="text-xl font-bold text-blue-600">Rp {{ number_format($stats['topup_bulan']) }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-red-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Belanja Kantin Bulan Ini</p>
        <p class="text-xl font-bold text-red-600">Rp {{ number_format($stats['belanja_kantin_bulan']) }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-green-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Pembayaran SPP Bulan Ini</p>
        <p class="text-xl font-bold text-green-600">Rp {{ number_format($stats['pembayaran_bulan']) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="glass-panel rounded-2xl p-5">
        <h6 class="font-bold mb-3"><i class="fa-solid fa-receipt mr-1"></i> Tagihan ({{ $tagihan->count() }})</h6>
        <div class="overflow-y-auto max-h-80 space-y-2">
            @forelse($tagihan as $t)
            <div class="flex justify-between p-2 rounded-lg {{ $t->status == 'lunas' ? 'bg-green-50' : 'bg-amber-50' }} text-sm">
                <div>
                    <p class="font-semibold">{{ $t->jenisTagihan->nama ?? 'Tagihan' }}</p>
                    <p class="text-xs text-gray-500">{{ $t->keterangan }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold">Rp {{ number_format($t->nominal) }}</p>
                    <p class="text-xs {{ $t->status == 'lunas' ? 'text-green-600' : 'text-amber-600' }}">{{ strtoupper($t->status) }}</p>
                </div>
            </div>
            @empty<p class="text-gray-400 text-sm text-center py-3">Tidak ada tagihan.</p>@endforelse
        </div>
    </div>

    <div class="glass-panel rounded-2xl p-5">
        <h6 class="font-bold mb-3"><i class="fa-solid fa-money-bill-transfer mr-1"></i> Pembayaran SPP</h6>
        <div class="overflow-y-auto max-h-80 space-y-2">
            @forelse($pembayaran as $p)
            <div class="flex justify-between p-2 rounded-lg bg-blue-50 text-sm">
                <div>
                    <p class="font-semibold">{{ $p->tagihan->jenisTagihan->nama ?? 'Pembayaran' }}</p>
                    <p class="text-xs text-gray-500">{{ $p->paid_at?->format('d/m/Y H:i') }} · {{ strtoupper($p->metode) }}</p>
                </div>
                <p class="font-bold text-blue-600">Rp {{ number_format($p->nominal) }}</p>
            </div>
            @empty<p class="text-gray-400 text-sm text-center py-3">Belum ada pembayaran.</p>@endforelse
        </div>
    </div>

    <div class="glass-panel rounded-2xl p-5">
        <h6 class="font-bold mb-3"><i class="fa-solid fa-arrow-down-to-line mr-1"></i> Top Up Wallet</h6>
        <div class="overflow-y-auto max-h-80 space-y-2">
            @forelse($topups as $t)
            <div class="flex justify-between p-2 rounded-lg bg-indigo-50 text-sm">
                <div>
                    <p class="font-semibold">+ Rp {{ number_format($t->nominal) }}</p>
                    <p class="text-xs text-gray-500">{{ $t->updated_at->format('d/m/Y H:i') }} · {{ strtoupper($t->metode ?? 'TOPUP') }}</p>
                </div>
                <span class="text-xs text-green-600 font-bold">{{ strtoupper($t->status) }}</span>
            </div>
            @empty<p class="text-gray-400 text-sm text-center py-3">Belum ada topup.</p>@endforelse
        </div>
    </div>

    <div class="glass-panel rounded-2xl p-5">
        <h6 class="font-bold mb-3"><i class="fa-solid fa-utensils mr-1"></i> Transaksi Kantin Bulan Ini ({{ $kantin->count() }})</h6>
        <div class="overflow-y-auto max-h-80 space-y-2">
            @forelse($kantin as $k)
            <div class="flex justify-between p-2 rounded-lg bg-red-50 text-sm">
                <div>
                    <p class="font-semibold">{{ $k->outlet->nama ?? 'Kantin' }}</p>
                    <p class="text-xs text-gray-500">{{ $k->created_at->format('d/m/Y H:i') }} · {{ $k->items->count() }} item</p>
                </div>
                <p class="font-bold text-red-600">- Rp {{ number_format($k->total) }}</p>
            </div>
            @empty<p class="text-gray-400 text-sm text-center py-3">Belum ada transaksi.</p>@endforelse
        </div>
    </div>
</div>

<div class="glass-panel rounded-2xl p-5 mt-6">
    <h6 class="font-bold mb-3"><i class="fa-solid fa-list mr-1"></i> Mutasi Wallet ({{ $walletTrx->count() }} transaksi bulan ini)</h6>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2 text-left">Tipe</th>
                <th class="px-3 py-2 text-right">Nominal</th>
                <th class="px-3 py-2 text-right">Saldo Sebelum</th>
                <th class="px-3 py-2 text-right">Saldo Sesudah</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($walletTrx as $w)
            <tr>
                <td class="px-3 py-2 text-xs">{{ $w->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-3 py-2"><span class="text-xs uppercase font-bold">{{ $w->tipe }}</span></td>
                <td class="px-3 py-2 text-right font-bold {{ $w->jenis == 'kredit' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $w->jenis == 'kredit' ? '+' : '-' }} Rp {{ number_format($w->nominal) }}
                </td>
                <td class="px-3 py-2 text-right text-gray-500 text-xs">Rp {{ number_format($w->saldo_sebelum) }}</td>
                <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($w->saldo_sesudah) }}</td>
            </tr>
            @empty<tr><td colspan="5" class="px-3 py-6 text-center text-gray-400">Belum ada mutasi wallet.</td></tr>@endforelse
        </tbody>
    </table>
</div>
@endsection
