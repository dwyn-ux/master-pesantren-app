@extends('layouts.app')
@section('title', 'Budgeting')
@section('page-title', 'Anggaran (Budgeting)')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="GET" class="glass-panel p-4 rounded-2xl mb-4 flex items-end gap-3">
    <div>
        <label class="block text-xs font-semibold mb-1">Tahun</label>
        <input type="number" name="tahun" value="{{ $tahun }}" class="w-32 rounded-lg border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1">Bulan (opsional)</label>
        <select name="bulan" class="rounded-lg border-gray-300 text-sm">
            <option value="">Tahunan</option>
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" @selected($bulan == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endforeach
        </select>
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">Tampilkan</button>
</form>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        @if($items->count() > 0)
        @php
            $totalAnggaran = $items->sum('nominal_anggaran');
            $totalRealisasi = $items->sum('nominal_realisasi');
            $persenTotal = $totalAnggaran > 0 ? ($totalRealisasi / $totalAnggaran * 100) : 0;
        @endphp
        <div class="glass-panel rounded-2xl p-6 mb-4">
            <div class="flex justify-between mb-2">
                <span class="font-bold">Total Anggaran</span>
                <span class="font-bold text-lg">Rp {{ number_format($totalAnggaran) }}</span>
            </div>
            <div class="flex justify-between mb-2 text-sm">
                <span class="text-gray-600">Realisasi</span>
                <span class="font-semibold {{ $persenTotal > 100 ? 'text-red-600' : 'text-indigo-600' }}">
                    Rp {{ number_format($totalRealisasi) }} ({{ number_format($persenTotal, 1) }}%)
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="h-3 transition-all {{ $persenTotal > 100 ? 'bg-red-500' : ($persenTotal > $alertPercent ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $persenTotal) }}%"></div>
            </div>
        </div>
        @endif

        <div class="space-y-3">
            @forelse($items as $b)
            @php
                $persen = $b->nominal_anggaran > 0 ? ((float)$b->nominal_realisasi / (float)$b->nominal_anggaran * 100) : 0;
                $sisa = (float)$b->nominal_anggaran - (float)$b->nominal_realisasi;
                $color = $persen > 100 ? 'red' : ($persen > $alertPercent ? 'amber' : 'emerald');
            @endphp
            <div class="glass-panel rounded-2xl p-5">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-bold">{{ $b->kategori->nama }}
                            <span class="text-xs ml-2 px-2 py-0.5 rounded-full {{ $b->kategori->tipe == 'pemasukan' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $b->kategori->tipe == 'pemasukan' ? 'Target' : 'Plafond' }}
                            </span>
                        </p>
                        <p class="text-xs text-gray-500">{{ $b->bulan ? \Carbon\Carbon::create()->month($b->bulan)->translatedFormat('F') . ' ' . $b->tahun : 'Tahun ' . $b->tahun }}</p>
                    </div>
                    <form method="POST" action="{{ route('finance.budgets.destroy', $b) }}" onsubmit="return confirm('Hapus anggaran ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 text-xs"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </div>
                <div class="flex justify-between text-sm mb-2">
                    <span>Anggaran: <strong>Rp {{ number_format($b->nominal_anggaran) }}</strong></span>
                    <span>Realisasi: <strong class="text-{{ $color }}-600">Rp {{ number_format($b->nominal_realisasi) }}</strong></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 bg-{{ $color }}-500 transition-all" style="width: {{ min(100, $persen) }}%"></div>
                </div>
                <div class="flex justify-between mt-1 text-xs">
                    <span class="text-{{ $color }}-600 font-bold">{{ number_format($persen, 1) }}% terpakai</span>
                    <span class="{{ $sisa < 0 ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                        {{ $sisa < 0 ? 'Over: ' : 'Sisa: ' }}Rp {{ number_format(abs($sisa)) }}
                    </span>
                </div>
                @if($persen > 100)
                <p class="mt-2 text-xs text-red-600"><i class="fa-solid fa-triangle-exclamation"></i> Anggaran terlampaui!</p>
                @elseif($persen > $alertPercent)
                <p class="mt-2 text-xs text-amber-600"><i class="fa-solid fa-circle-exclamation"></i> Mendekati batas anggaran.</p>
                @endif
            </div>
            @empty
            <div class="glass-panel rounded-2xl p-12 text-center">
                <i class="fa-solid fa-chart-pie text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Belum ada anggaran untuk periode ini. Tambahkan di kanan →</p>
            </div>
            @endforelse
        </div>
    </div>

    <div>
        <form method="POST" action="{{ route('finance.budgets.store') }}" class="glass-panel rounded-2xl p-5 sticky top-20">
            @csrf
            <h6 class="font-bold mb-3"><i class="fa-solid fa-plus mr-1"></i> Tambah Anggaran</h6>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold mb-1">Tahun</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Bulan (kosongkan = tahunan)</label>
                    <select name="bulan" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">— Tahunan —</option>
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" @selected($bulan == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Kategori</label>
                    <select name="kategori_id" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">— Pilih —</option>
                        @foreach($kategoris->groupBy('tipe') as $tipe => $list)
                        <optgroup label="{{ ucfirst($tipe) }}">
                            @foreach($list as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Nominal Anggaran</label>
                    <input type="number" step="0.01" min="0" name="nominal_anggaran" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Catatan</label>
                    <textarea name="catatan" rows="2" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                </div>
                <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm">Simpan</button>
            </div>
            <p class="text-xs text-gray-500 mt-3 text-center">Realisasi dihitung otomatis dari transaksi tercatat.</p>
        </form>
    </div>
</div>
@endsection
