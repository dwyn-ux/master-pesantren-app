@extends('layouts.app')
@section('title', 'Laporan Laundry')
@section('page-title', 'Laporan Order Laundry')

@section('sidebar')
    @if(auth()->user()->hasRole('admin'))
        @include('partials.sidebar-admin')
    @else
        @include('partials.sidebar-bendahara')
    @endif
@endsection

@section('content')

<div class="flex items-center justify-end mb-4">
    <a href="{{ route('laporan.pdf.laundry', array_filter(['dari' => $dari->format('Y-m-d'), 'sampai' => $sampai->format('Y-m-d')])) }}"
       class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium text-sm shadow-sm">
        <i class="fa-solid fa-file-pdf mr-1"></i> Download PDF + Analisis
    </a>
    <a href="{{ route('laporan.excel.laundry', array_filter(['dari' => $dari->format('Y-m-d'), 'sampai' => $sampai->format('Y-m-d'), 'status' => $filterStatus])) }}"
       class="ml-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium text-sm shadow-sm">
        <i class="fa-solid fa-file-excel mr-1"></i> Download Excel
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
        <div class="min-w-[160px]">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white text-sm">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $val => $label)
                <option value="{{ $val }}" @selected($filterStatus === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('laporan.laundry') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">Reset</a>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-blue-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Order</p>
        <h3 class="text-2xl font-extrabold text-gray-800">{{ number_format($totalOrder) }}</h3>
        <p class="text-xs text-gray-400 mt-1">order laundry</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Revenue</p>
        <h3 class="text-xl font-extrabold text-green-700">Rp {{ number_format($totalRevenue) }}</h3>
        <p class="text-xs text-gray-400 mt-1">pemasukan</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-indigo-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Berat</p>
        <h3 class="text-2xl font-extrabold text-indigo-700">{{ number_format($totalKg, 1) }} kg</h3>
        <p class="text-xs text-gray-400 mt-1">total cucian</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-amber-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Rata-rata/Order</p>
        <h3 class="text-2xl font-extrabold text-amber-700">{{ $avgKg }} kg</h3>
        <p class="text-xs text-gray-400 mt-1">per order</p>
    </div>
</div>

{{-- Chart --}}
<div class="glass-panel rounded-2xl shadow-sm p-6 mb-6">
    <h6 class="text-base font-bold text-gray-800 mb-4">Revenue Laundry per Hari</h6>
    <div class="relative h-56">
        <canvas id="laundryChart"></canvas>
    </div>
</div>

{{-- Table --}}
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
        <h6 class="font-bold text-gray-800">Detail Order <span class="text-sm font-normal text-gray-400">({{ $orders->total() }} data)</span></h6>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-5 py-3 font-medium">Tanggal</th>
                    <th class="px-5 py-3 font-medium">No. Tiket</th>
                    <th class="px-5 py-3 font-medium">Santri</th>
                    <th class="px-5 py-3 font-medium text-center">Berat</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $o)
                <tr class="hover:bg-blue-50/20 transition-colors">
                    <td class="px-5 py-3 text-sm text-gray-600">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3 text-sm font-mono text-gray-800">{{ $o->nomor_tiket }}</td>
                    <td class="px-5 py-3 font-medium text-gray-800 text-sm">{{ $o->santri?->nama ?? '-' }}</td>
                    <td class="px-5 py-3 text-sm text-center text-gray-600">{{ $o->berat_kg }} kg</td>
                    <td class="px-5 py-3">
                        @php
                            $sc = match($o->status) {
                                'diterima' => 'bg-yellow-100 text-yellow-700',
                                'dicuci' => 'bg-blue-100 text-blue-700',
                                'selesai' => 'bg-green-100 text-green-700',
                                'diambil' => 'bg-gray-100 text-gray-600',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded text-xs font-bold {{ $sc }}">
                            {{ $statusOptions[$o->status] ?? $o->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm font-bold text-blue-700 text-right">Rp {{ number_format($o->total) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada data order pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('laundryChart'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Revenue',
            data: @json($revenueChart),
            backgroundColor: 'rgba(59,130,246,0.75)',
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
