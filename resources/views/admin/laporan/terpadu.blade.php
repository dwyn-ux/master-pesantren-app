@extends('layouts.app')
@section('title', 'Laporan Terpadu')
@section('page-title', 'Laporan Terpadu')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')

{{-- Filter Bulan/Tahun --}}
<div class="glass-panel rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <span class="text-sm font-semibold text-gray-600 mr-1">Periode:</span>
        <select name="bulan" class="px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white text-sm">
            @foreach($bulanList as $num => $nama)
                <option value="{{ $num }}" @selected($bulan == $num)>{{ $nama }}</option>
            @endforeach
        </select>
        <select name="tahun" class="px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white text-sm">
            @foreach($tahunList as $y)
                <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
            <i class="fa-solid fa-filter mr-1"></i> Tampilkan
        </button>
        <span class="text-xs text-gray-400 ml-2">
            Data: {{ $awal->translatedFormat('d F Y') }} &mdash; {{ $awal->copy()->endOfMonth()->translatedFormat('d F Y') }}
        </span>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-indigo-500 relative overflow-hidden group">
        <div class="absolute -right-3 -bottom-3 text-indigo-500/10 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-scroll text-7xl"></i>
        </div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Setoran</p>
        <h3 class="text-2xl font-extrabold text-gray-800">{{ number_format($totalSetoran) }}</h3>
        <p class="text-xs text-gray-400 mt-1">pertemuan bulan ini</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-emerald-500 relative overflow-hidden group">
        <div class="absolute -right-3 -bottom-3 text-emerald-500/10 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-book-open text-7xl"></i>
        </div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Halaman Ziyadah</p>
        <h3 class="text-2xl font-extrabold text-gray-800">{{ number_format($totalHalamanZiyadah, 1) }}</h3>
        <p class="text-xs text-gray-400 mt-1">halaman maqbul</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-amber-500 relative overflow-hidden group">
        <div class="absolute -right-3 -bottom-3 text-amber-500/10 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-utensils text-7xl"></i>
        </div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Revenue Kantin</p>
        <h3 class="text-xl font-extrabold text-gray-800">Rp {{ number_format($revenueKantin) }}</h3>
        <p class="text-xs text-gray-400 mt-1">total transaksi</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-blue-500 relative overflow-hidden group">
        <div class="absolute -right-3 -bottom-3 text-blue-500/10 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-shirt text-7xl"></i>
        </div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Revenue Laundry</p>
        <h3 class="text-xl font-extrabold text-gray-800">Rp {{ number_format($revenueLaundry) }}</h3>
        <p class="text-xs text-gray-400 mt-1">total order</p>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Revenue Chart --}}
    <div class="lg:col-span-2 glass-panel rounded-2xl shadow-sm p-6">
        <h6 class="text-base font-bold text-gray-800 mb-4">Revenue Outlet per Hari</h6>
        <div class="relative h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Setoran Donut --}}
    <div class="glass-panel rounded-2xl shadow-sm p-6 flex flex-col">
        <h6 class="text-base font-bold text-gray-800 mb-4">Komposisi Setoran</h6>
        <div class="flex-1 flex items-center justify-center">
            <div class="relative h-52 w-52">
                <canvas id="setoranChart"></canvas>
            </div>
        </div>
        <div class="mt-4 space-y-1">
            @foreach(['ziyadah' => ['label'=>'Ziyadah','color'=>'bg-emerald-500'], 'murojaah' => ['label'=>'Murojaah','color'=>'bg-blue-500']] as $key => $cfg)
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $cfg['color'] }} inline-block"></span>
                    <span class="text-gray-600">{{ $cfg['label'] }}</span>
                </span>
                <span class="font-bold text-gray-800">{{ $setoranByJenis[$key] ?? 0 }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Tables Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Top Hafalan --}}
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-5 py-4">
            <h6 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-trophy text-amber-400"></i> Top Hafalan Bulan Ini
            </h6>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($topHafalan as $idx => $item)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $idx === 0 ? 'bg-amber-100 text-amber-700' : ($idx === 1 ? 'bg-gray-100 text-gray-600' : ($idx === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-500')) }}">
                        {{ $idx + 1 }}
                    </span>
                    <span class="text-sm font-medium text-gray-800">{{ $item->santri?->nama ?? '-' }}</span>
                </div>
                <span class="text-sm font-bold text-emerald-600">{{ number_format($item->total_halaman, 1) }} hlm</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada data setoran.</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Kantin --}}
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-5 py-4">
            <h6 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-utensils text-amber-500"></i> Transaksi Kantin Terbaru
            </h6>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentKantin as $t)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $t->santri?->nama ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $t->created_at->format('d/m H:i') }}</p>
                </div>
                <span class="text-sm font-bold text-amber-600">Rp {{ number_format($t->total) }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada transaksi.</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Laundry --}}
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-5 py-4">
            <h6 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-shirt text-blue-500"></i> Order Laundry Terbaru
            </h6>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentLaundry as $o)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $o->santri?->nama ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $o->created_at->format('d/m H:i') }} &bull; {{ $o->berat_kg }} kg</p>
                </div>
                <span class="text-sm font-bold text-blue-600">Rp {{ number_format($o->total) }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada order.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels  = @json($chartLabels);
const kantin  = @json($kantinData);
const laundry = @json($laundryData);

// Revenue bar chart
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Kantin',
                data: kantin,
                backgroundColor: 'rgba(245,158,11,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Laundry',
                data: laundry,
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderRadius: 4,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: {
                ticks: {
                    callback: v => 'Rp ' + Intl.NumberFormat('id').format(v),
                },
                grid: { color: 'rgba(0,0,0,0.05)' },
            },
            x: { grid: { display: false } },
        },
    },
});

// Setoran donut
const setoranData = @json($setoranByJenis);
new Chart(document.getElementById('setoranChart'), {
    type: 'doughnut',
    data: {
        labels: ['Ziyadah', 'Murojaah', 'Lainnya'],
        datasets: [{
            data: [
                setoranData['ziyadah'] ?? 0,
                setoranData['murojaah'] ?? 0,
                setoranData['lainnya'] ?? 0,
            ],
            backgroundColor: ['#10b981','#3b82f6','#f59e0b'],
            borderWidth: 2,
            borderColor: '#fff',
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { display: false },
        },
    },
});
</script>
@endpush
