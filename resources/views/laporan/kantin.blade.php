@extends('layouts.app')
@section('title', 'Laporan Kantin')
@section('page-title', 'Laporan Transaksi Kantin')

@section('sidebar')
    @if(auth()->user()->hasRole('admin'))
        @include('partials.sidebar-admin')
    @else
        @include('partials.sidebar-bendahara')
    @endif
@endsection

@section('content')

<div class="flex items-center justify-end mb-4">
    <a href="{{ route('laporan.pdf.kantin', ['dari' => $dari->format('Y-m-d'), 'sampai' => $sampai->format('Y-m-d'), 'outlet_id' => $outletId]) }}"
       class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium text-sm shadow-sm">
        <i class="fa-solid fa-file-pdf mr-1"></i> Download PDF + Analisis
    </a>
</div>

{{-- Filter --}}
<div class="glass-panel rounded-2xl shadow-sm p-5 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari->format('Y-m-d') }}"
                   class="px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai->format('Y-m-d') }}"
                   class="px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">
        </div>
        @if($outletList->count() > 1)
        <div class="min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Outlet</label>
            <select name="outlet_id" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white text-sm">
                <option value="">Semua Kantin</option>
                @foreach($outletList as $o)
                <option value="{{ $o->id }}" @selected($outletId == $o->id)>{{ $o->nama }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="flex gap-2 items-end">
            <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('laporan.kantin') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">Reset</a>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-amber-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Transaksi</p>
        <h3 class="text-2xl font-extrabold text-gray-800">{{ number_format($totalTransaksi) }}</h3>
        <p class="text-xs text-gray-400 mt-1">transaksi</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Revenue</p>
        <h3 class="text-xl font-extrabold text-green-700">Rp {{ number_format($totalRevenue) }}</h3>
        <p class="text-xs text-gray-400 mt-1">pemasukan</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-indigo-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Santri Unik</p>
        <h3 class="text-2xl font-extrabold text-indigo-700">{{ number_format($santriUnik) }}</h3>
        <p class="text-xs text-gray-400 mt-1">pembeli berbeda</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-blue-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Rata-rata/Transaksi</p>
        <h3 class="text-xl font-extrabold text-blue-700">Rp {{ number_format($avgTransaksi) }}</h3>
        <p class="text-xs text-gray-400 mt-1">per transaksi</p>
    </div>
</div>

{{-- Chart + Top Pembeli --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 glass-panel rounded-2xl shadow-sm p-6">
        <h6 class="text-base font-bold text-gray-800 mb-4">Revenue Kantin per Hari</h6>
        <div class="relative h-56">
            <canvas id="kantinChart"></canvas>
        </div>
    </div>
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-5 py-4">
            <h6 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-star text-amber-400"></i> Top 5 Pembeli Terbanyak
            </h6>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($topPembeli as $idx => $item)
            <div class="px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $idx === 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $idx + 1 }}
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $item->santri?->nama ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $item->jumlah }}x transaksi</p>
                    </div>
                </div>
                <span class="text-sm font-bold text-amber-600">Rp {{ number_format($item->total_belanja) }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada data.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Table --}}
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
        <h6 class="font-bold text-gray-800">Detail Transaksi <span class="text-sm font-normal text-gray-400">({{ $transaksi->total() }} data)</span></h6>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-5 py-3 font-medium">Tanggal</th>
                    <th class="px-5 py-3 font-medium">Santri</th>
                    <th class="px-5 py-3 font-medium">NIS</th>
                    <th class="px-5 py-3 font-medium text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($transaksi as $t)
                <tr class="hover:bg-amber-50/20 transition-colors">
                    <td class="px-5 py-3 text-sm text-gray-600">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3 font-medium text-gray-800 text-sm">{{ $t->santri?->nama ?? '-' }}</td>
                    <td class="px-5 py-3 text-sm text-gray-500">{{ $t->santri?->nis ?? '-' }}</td>
                    <td class="px-5 py-3 text-sm font-bold text-amber-700 text-right">Rp {{ number_format($t->total) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada data transaksi pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transaksi->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $transaksi->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('kantinChart'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Revenue',
            data: @json($revenueChart),
            backgroundColor: 'rgba(245,158,11,0.75)',
            borderRadius: 4,
        }],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + Intl.NumberFormat('id').format(v) }, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } },
        },
    },
});
</script>
@endpush
