@extends('layouts.app')
@section('title', 'Transaksi Kas')
@section('page-title', 'Transaksi Kas')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="flex justify-between items-center mb-4 flex-wrap gap-3">
    <div class="flex gap-2">
        <a href="{{ route('finance.transaksi.masuk') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700">
            <i class="fa-solid fa-circle-plus mr-1"></i> Pemasukan
        </a>
        <a href="{{ route('finance.transaksi.keluar') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700">
            <i class="fa-solid fa-circle-minus mr-1"></i> Pengeluaran
        </a>
        <a href="{{ route('finance.transaksi.transfer') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
            <i class="fa-solid fa-right-left mr-1"></i> Transfer
        </a>
    </div>
</div>

<form method="GET" class="glass-panel p-4 rounded-2xl mb-6 grid grid-cols-1 md:grid-cols-5 gap-3">
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nomor / pihak / keterangan" class="w-full rounded-lg border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Tipe</label>
        <select name="tipe" class="w-full rounded-lg border-gray-300 text-sm">
            <option value="">Semua</option>
            <option value="masuk" @selected(request('tipe')=='masuk')>Masuk</option>
            <option value="keluar" @selected(request('tipe')=='keluar')>Keluar</option>
            <option value="transfer" @selected(request('tipe')=='transfer')>Transfer</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Kas/Bank</label>
        <select name="kas_bank_id" class="w-full rounded-lg border-gray-300 text-sm">
            <option value="">Semua</option>
            @foreach($kasBanks as $kb)
                <option value="{{ $kb->id }}" @selected(request('kas_bank_id')==$kb->id)>{{ $kb->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Dari</label>
        <input type="date" name="dari" value="{{ request('dari') }}" class="w-full rounded-lg border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai</label>
        <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full rounded-lg border-gray-300 text-sm">
    </div>
    <div class="md:col-span-5 flex gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold"><i class="fa-solid fa-filter mr-1"></i> Filter</button>
        <a href="{{ route('finance.transaksi.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg text-sm">Reset</a>
    </div>
</form>

<div class="glass-panel rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-500 text-xs uppercase">
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Nomor</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3">Kategori / Tujuan</th>
                    <th class="px-4 py-3">Kas/Bank</th>
                    <th class="px-4 py-3">Pihak</th>
                    <th class="px-4 py-3 text-right">Nominal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($items as $t)
                <tr class="hover:bg-indigo-50/30">
                    <td class="px-4 py-3 whitespace-nowrap">{{ $t->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $t->nomor }}</td>
                    <td class="px-4 py-3">
                        @if($t->tipe == 'masuk')<span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">+ Masuk</span>
                        @elseif($t->tipe == 'keluar')<span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">- Keluar</span>
                        @else<span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">↔ Transfer</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($t->tipe == 'transfer')
                            <span class="text-xs">→ {{ $t->kasBankTujuan->nama ?? '-' }}</span>
                        @else
                            {{ $t->kategori->nama ?? '-' }}
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $t->kasBank->nama }}</td>
                    <td class="px-4 py-3 text-xs">{{ $t->pihak ?: '-' }}</td>
                    <td class="px-4 py-3 text-right font-bold {{ $t->tipe == 'masuk' ? 'text-green-600' : ($t->tipe == 'keluar' ? 'text-red-600' : 'text-blue-600') }}">
                        Rp {{ number_format($t->nominal) }}
                    </td>
                    <td class="px-4 py-3">
                        @if($t->status == 'posted')<span class="text-green-600 text-xs font-semibold">Posted</span>
                        @elseif($t->status == 'void')<span class="text-gray-400 text-xs font-semibold line-through">Void</span>
                        @else<span class="text-amber-600 text-xs font-semibold">Draft</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('finance.transaksi.show', $t) }}" class="text-indigo-600 hover:text-indigo-800"><i class="fa-solid fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
