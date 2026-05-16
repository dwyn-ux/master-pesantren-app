@extends('layouts.app')
@section('title', 'Detail Kas Kecil')
@section('page-title', 'Kas Kecil: ' . $pettyCash->penanggung_jawab)

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="glass-panel rounded-2xl p-6 mb-6 text-center bg-gradient-to-br from-amber-50 to-orange-50">
            <p class="text-xs text-gray-500 uppercase font-bold">Saldo Berjalan</p>
            <h2 class="text-4xl font-extrabold text-amber-600">Rp {{ number_format($pettyCash->saldo_berjalan) }}</h2>
            <p class="text-xs text-gray-500 mt-2">PJ: {{ $pettyCash->penanggung_jawab }} · Saldo Awal: Rp {{ number_format($pettyCash->saldo_awal) }}</p>
        </div>

        <div class="glass-panel rounded-2xl p-6">
            <h6 class="text-lg font-bold mb-4">Riwayat Transaksi</h6>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Tgl</th>
                        <th class="px-3 py-2 text-left">Tipe</th>
                        <th class="px-3 py-2 text-left">Kategori</th>
                        <th class="px-3 py-2 text-left">Keterangan</th>
                        <th class="px-3 py-2 text-right">Nominal</th>
                        <th class="px-3 py-2 text-center">Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($pettyCash->transactions as $t)
                    <tr>
                        <td class="px-3 py-2 text-xs whitespace-nowrap">{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="px-3 py-2">
                            @if($t->tipe == 'pengisian')<span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">+ ISI</span>
                            @else<span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">- KELUAR</span>@endif
                        </td>
                        <td class="px-3 py-2 text-xs">{{ $t->kategori->nama ?? '-' }}</td>
                        <td class="px-3 py-2 text-xs">{{ $t->keterangan }}</td>
                        <td class="px-3 py-2 text-right font-bold {{ $t->tipe == 'pengisian' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $t->tipe == 'pengisian' ? '+' : '-' }} Rp {{ number_format($t->nominal) }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($t->bukti)<a href="{{ asset('storage/' . $t->bukti) }}" target="_blank" class="text-indigo-600"><i class="fa-solid fa-paperclip"></i></a>@endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-4">
        <form method="POST" action="{{ route('finance.petty-cash.top-up', $pettyCash) }}" class="glass-panel rounded-2xl p-5 border-t-4 border-green-500">
            @csrf
            <h6 class="font-bold mb-3 text-green-700"><i class="fa-solid fa-circle-plus mr-1"></i> Pengisian Kas Kecil</h6>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Dari Kas/Bank</label>
                    <select name="kas_bank_id" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">— Pilih —</option>
                        @foreach($kasBanks as $kb)
                            <option value="{{ $kb->id }}">{{ $kb->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Nominal</label>
                    <input type="number" step="0.01" min="0.01" name="nominal" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Keterangan</label>
                    <input type="text" name="keterangan" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg font-bold text-sm">
                    <i class="fa-solid fa-arrow-down mr-1"></i> Top Up
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('finance.petty-cash.spend', $pettyCash) }}" enctype="multipart/form-data" class="glass-panel rounded-2xl p-5 border-t-4 border-red-500">
            @csrf
            <h6 class="font-bold mb-3 text-red-700"><i class="fa-solid fa-circle-minus mr-1"></i> Pengeluaran Kas Kecil</h6>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Kategori</label>
                    <select name="kategori_id" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">— Pilih —</option>
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Nominal</label>
                    <input type="number" step="0.01" min="0.01" max="{{ $pettyCash->saldo_berjalan }}" name="nominal" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Keterangan</label>
                    <input type="text" name="keterangan" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Bukti (opsional)</label>
                    <input type="file" name="bukti" accept="image/*,.pdf" class="w-full text-xs">
                </div>
                <button class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-bold text-sm">
                    <i class="fa-solid fa-arrow-up mr-1"></i> Catat Pengeluaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
