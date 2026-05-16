@extends('layouts.app')
@section('title', 'Aset Tetap')
@section('page-title', 'Aset Tetap')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Total Aset</p>
        <p class="text-lg font-bold text-blue-600">{{ $summary['jumlah_aset_aktif'] }} aktif</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-indigo-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Harga Perolehan</p>
        <p class="text-lg font-bold text-indigo-600">Rp {{ number_format($summary['total_aset']) }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-amber-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Akum. Penyusutan</p>
        <p class="text-lg font-bold text-amber-600">Rp {{ number_format($summary['total_akumulasi']) }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-emerald-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Nilai Buku</p>
        <p class="text-lg font-bold text-emerald-600">Rp {{ number_format($summary['nilai_buku']) }}</p>
    </div>
</div>

<div class="flex justify-between items-center mb-4 flex-wrap gap-3">
    <form method="GET" class="flex gap-2 items-end flex-1">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari aset..." class="flex-1 max-w-md rounded-lg border-gray-300 text-sm">
        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua Status</option>
            @foreach(['aktif','rusak','dijual','dihapus'] as $s)
                <option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Filter</button>
    </form>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('finance.assets.depreciate') }}" class="flex gap-2 items-end" onsubmit="return confirm('Posting penyusutan untuk periode terpilih?')">
            @csrf
            <select name="bulan" class="rounded-lg border-gray-300 text-sm">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('M') }}</option>
                @endforeach
            </select>
            <input type="number" name="tahun" value="{{ now()->year }}" class="w-24 rounded-lg border-gray-300 text-sm">
            <button class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-semibold"><i class="fa-solid fa-calculator mr-1"></i> Post Penyusutan</button>
        </form>
        <a href="{{ route('finance.assets.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold">
            <i class="fa-solid fa-plus mr-1"></i> Aset Baru
        </a>
    </div>
</div>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Kode</th>
                <th class="px-3 py-2 text-left">Nama Aset</th>
                <th class="px-3 py-2 text-left">Kategori</th>
                <th class="px-3 py-2 text-left">Diperoleh</th>
                <th class="px-3 py-2 text-right">Harga</th>
                <th class="px-3 py-2 text-right">Akum</th>
                <th class="px-3 py-2 text-right">Nilai Buku</th>
                <th class="px-3 py-2 text-center">Status</th>
                <th class="px-3 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $a)
            <tr class="hover:bg-indigo-50/30">
                <td class="px-3 py-2 font-mono text-xs">{{ $a->kode }}</td>
                <td class="px-3 py-2 font-semibold">{{ $a->nama }}</td>
                <td class="px-3 py-2 text-xs">{{ $a->kategori }}</td>
                <td class="px-3 py-2 text-xs">{{ $a->tanggal_perolehan->format('d/m/Y') }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($a->harga_perolehan) }}</td>
                <td class="px-3 py-2 text-right text-amber-600">Rp {{ number_format($a->akumulasi_penyusutan) }}</td>
                <td class="px-3 py-2 text-right font-bold text-indigo-600">Rp {{ number_format($a->nilai_buku) }}</td>
                <td class="px-3 py-2 text-center">
                    @if($a->status=='aktif')<span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">Aktif</span>
                    @elseif($a->status=='rusak')<span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-xs">Rusak</span>
                    @else<span class="px-2 py-1 rounded-full bg-gray-100 text-gray-500 text-xs">{{ ucfirst($a->status) }}</span>@endif
                </td>
                <td class="px-3 py-2 text-right whitespace-nowrap">
                    <a href="{{ route('finance.assets.show', $a) }}" class="text-indigo-600 mr-2"><i class="fa-solid fa-eye"></i></a>
                    <a href="{{ route('finance.assets.edit', $a) }}" class="text-amber-600 mr-2"><i class="fa-solid fa-pen"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="px-3 py-8 text-center text-gray-400">Belum ada aset.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
