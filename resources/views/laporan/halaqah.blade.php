@extends('layouts.app')
@section('title', 'Laporan Halaqah')
@section('page-title', 'Laporan Setoran & Hafalan')

@section('sidebar')
    @if(auth()->user()->hasRole('admin'))
        @include('partials.sidebar-admin')
    @else
        @include('partials.sidebar-bendahara')
    @endif
@endsection

@section('content')

<div class="flex items-center justify-end mb-4">
    <a href="{{ route('laporan.pdf.tahfidz', ['dari' => $dari->format('Y-m-d'), 'sampai' => $sampai->format('Y-m-d')]) }}"
       class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium text-sm shadow-sm">
        <i class="fa-solid fa-file-pdf mr-1"></i> Download PDF Tahfidz + Analisis
    </a>
    <a href="{{ route('laporan.excel.halaqah', array_filter(['dari' => $dari->format('Y-m-d'), 'sampai' => $sampai->format('Y-m-d'), 'halaqah_id' => $halaqahId])) }}"
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
        <div class="min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Halaqah</label>
            <select name="halaqah_id" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white text-sm">
                <option value="">Semua Halaqah</option>
                @foreach($halaqahList as $h)
                <option value="{{ $h->id }}" @selected($halaqahId == $h->id)>
                    {{ $h->nama }} ({{ $h->ustadz?->nama ?? '-' }})
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('laporan.halaqah') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">Reset</a>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-indigo-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Setoran</p>
        <h3 class="text-2xl font-extrabold text-gray-800">{{ number_format($totalSetoran) }}</h3>
        <p class="text-xs text-gray-400 mt-1">pertemuan</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-emerald-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Halaman Ziyadah</p>
        <h3 class="text-2xl font-extrabold text-emerald-700">{{ number_format($totalHalamanZiyadah, 1) }}</h3>
        <p class="text-xs text-gray-400 mt-1">halaman maqbul</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-blue-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Halaman Murojaah</p>
        <h3 class="text-2xl font-extrabold text-blue-700">{{ number_format($totalHalamanMurojaah, 1) }}</h3>
        <p class="text-xs text-gray-400 mt-1">halaman</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-amber-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Santri Aktif</p>
        <h3 class="text-2xl font-extrabold text-amber-700">{{ number_format($santriUnik) }}</h3>
        <p class="text-xs text-gray-400 mt-1">santri setoran</p>
    </div>
</div>

{{-- Chart + Top Hafalan --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 glass-panel rounded-2xl shadow-sm p-6">
        <h6 class="text-base font-bold text-gray-800 mb-4">Frekuensi Setoran per Hari</h6>
        <div class="relative h-56">
            <canvas id="setoranChart"></canvas>
        </div>
    </div>
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-5 py-4">
            <h6 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-trophy text-amber-400"></i> Top Hafalan (Ziyadah Maqbul)
            </h6>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($topHafalan as $idx => $item)
            <div class="px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $idx === 0 ? 'bg-amber-100 text-amber-700' : ($idx === 1 ? 'bg-gray-200 text-gray-600' : 'bg-gray-100 text-gray-500') }}">
                        {{ $idx + 1 }}
                    </span>
                    <span class="text-sm font-medium text-gray-800">{{ $item->santri?->nama ?? '-' }}</span>
                </div>
                <span class="text-sm font-bold text-emerald-600">{{ number_format($item->total_halaman, 1) }} hlm</span>
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
        <h6 class="font-bold text-gray-800">Detail Setoran <span class="text-sm font-normal text-gray-400">({{ $setoran->total() }} data)</span></h6>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-5 py-3 font-medium">Tanggal</th>
                    <th class="px-5 py-3 font-medium">Santri</th>
                    <th class="px-5 py-3 font-medium">Penerima</th>
                    <th class="px-5 py-3 font-medium">Jenis</th>
                    <th class="px-5 py-3 font-medium">Halaman</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($setoran as $s)
                <tr class="hover:bg-indigo-50/20 transition-colors">
                    <td class="px-5 py-3 text-sm text-gray-600">{{ $s->tanggal->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 font-medium text-gray-800 text-sm">{{ $s->santri?->nama ?? '-' }}</td>
                    <td class="px-5 py-3 text-sm text-gray-600">{{ $s->penerima?->nama ?? '-' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-bold {{ $s->jenis === 'ziyadah' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ ucfirst($s->jenis) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm font-semibold text-gray-800">{{ $s->jumlah_halaman }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-bold {{ $s->status === 'maqbul' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($s->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada data setoran pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($setoran->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $setoran->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('setoranChart'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Jumlah Setoran',
            data: @json($setoranChart),
            backgroundColor: 'rgba(99,102,241,0.7)',
            borderRadius: 4,
        }],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } },
        },
    },
});
</script>
@endpush
