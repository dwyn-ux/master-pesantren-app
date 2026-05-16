@extends('layouts.app')
@section('title', 'Dashboard Finance')
@section('page-title', 'Dashboard Finance')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="GET" class="mb-6 flex items-end gap-3 flex-wrap">
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Bulan</label>
        <select name="bulan" class="rounded-lg border-gray-300 text-sm">
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun</label>
        <select name="tahun" class="rounded-lg border-gray-300 text-sm">
            @foreach(range(now()->year-2, now()->year+1) as $y)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">
        <i class="fa-solid fa-filter mr-1"></i> Filter
    </button>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="glass-panel p-6 rounded-2xl border-b-4 border-indigo-500">
        <p class="text-xs font-bold text-gray-500 uppercase mb-2">Total Saldo Kas/Bank</p>
        <h3 class="text-xl font-extrabold text-indigo-600">Rp {{ number_format($totalKas) }}</h3>
        <p class="text-xs text-gray-400 mt-1">Akumulasi semua kas & bank</p>
    </div>
    <div class="glass-panel p-6 rounded-2xl border-b-4 border-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase mb-2">Pemasukan Bulan Ini</p>
        <h3 class="text-xl font-extrabold text-green-600">Rp {{ number_format($masuk) }}</h3>
    </div>
    <div class="glass-panel p-6 rounded-2xl border-b-4 border-red-500">
        <p class="text-xs font-bold text-gray-500 uppercase mb-2">Pengeluaran Bulan Ini</p>
        <h3 class="text-xl font-extrabold text-red-600">Rp {{ number_format($keluar) }}</h3>
    </div>
    <div class="glass-panel p-6 rounded-2xl border-b-4 {{ $masuk - $keluar >= 0 ? 'border-emerald-500' : 'border-orange-500' }}">
        <p class="text-xs font-bold text-gray-500 uppercase mb-2">Selisih Bersih</p>
        <h3 class="text-xl font-extrabold {{ $masuk - $keluar >= 0 ? 'text-emerald-600' : 'text-orange-600' }}">
            Rp {{ number_format($masuk - $keluar) }}
        </h3>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 glass-panel rounded-2xl p-6">
        <h6 class="text-lg font-bold mb-4">Trend 6 Bulan Terakhir</h6>
        <canvas id="chartTrend" height="100"></canvas>
    </div>
    <div class="glass-panel rounded-2xl p-6">
        <h6 class="text-lg font-bold mb-4">Saldo Kas/Bank</h6>
        <div class="space-y-3">
            @foreach($kasBanks as $kb)
            <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50">
                <div>
                    <div class="font-semibold text-sm">{{ $kb->nama }}</div>
                    <div class="text-xs text-gray-500">{{ strtoupper($kb->tipe) }} · {{ $kb->kode }}</div>
                </div>
                <div class="font-bold text-indigo-600">Rp {{ number_format($kb->saldo_berjalan) }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="glass-panel rounded-2xl p-6">
        <h6 class="text-lg font-bold mb-4">Top 5 Pemasukan</h6>
        @forelse($masukPerKategori as $item)
        <div class="flex justify-between items-center py-2 border-b last:border-b-0">
            <span class="text-sm">{{ $item->nama }}</span>
            <span class="font-semibold text-green-600">Rp {{ number_format($item->total) }}</span>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">Belum ada pemasukan.</p>
        @endforelse
    </div>
    <div class="glass-panel rounded-2xl p-6">
        <h6 class="text-lg font-bold mb-4">Top 5 Pengeluaran</h6>
        @forelse($keluarPerKategori as $item)
        <div class="flex justify-between items-center py-2 border-b last:border-b-0">
            <span class="text-sm">{{ $item->nama }}</span>
            <span class="font-semibold text-red-600">Rp {{ number_format($item->total) }}</span>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">Belum ada pengeluaran.</p>
        @endforelse
    </div>
</div>

<div class="glass-panel rounded-2xl p-6">
    <div class="flex justify-between items-center mb-4">
        <h6 class="text-lg font-bold">Transaksi Terbaru</h6>
        <a href="{{ route('finance.transaksi.index') }}" class="text-sm text-indigo-600 font-semibold">Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i></a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <th class="text-left px-3 py-2">Tanggal</th>
                    <th class="text-left px-3 py-2">Nomor</th>
                    <th class="text-left px-3 py-2">Tipe</th>
                    <th class="text-left px-3 py-2">Kategori</th>
                    <th class="text-left px-3 py-2">Kas/Bank</th>
                    <th class="text-right px-3 py-2">Nominal</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($recentTransaksi as $t)
                <tr class="hover:bg-indigo-50/30">
                    <td class="px-3 py-2">{{ $t->tanggal->format('d/m/Y') }}</td>
                    <td class="px-3 py-2 font-mono text-xs">{{ $t->nomor }}</td>
                    <td class="px-3 py-2">
                        @if($t->tipe == 'masuk')<span class="text-green-600 font-semibold">+ Masuk</span>
                        @elseif($t->tipe == 'keluar')<span class="text-red-600 font-semibold">- Keluar</span>
                        @else<span class="text-blue-600 font-semibold">↔ Transfer</span>
                        @endif
                    </td>
                    <td class="px-3 py-2">{{ $t->kategori->nama ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $t->kasBank->nama }}</td>
                    <td class="px-3 py-2 text-right font-bold">Rp {{ number_format($t->nominal) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chartTrend');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($trendBulanan->pluck('label')) !!},
        datasets: [
            { label: 'Pemasukan',  data: {!! json_encode($trendBulanan->pluck('masuk')) !!},  backgroundColor: '#10b981' },
            { label: 'Pengeluaran',data: {!! json_encode($trendBulanan->pluck('keluar')) !!}, backgroundColor: '#ef4444' },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});
</script>
@endpush
@endsection
