@extends('layouts.app')
@section('title', 'Laporan ' . $santri->nama)
@section('page-title', 'Laporan Santri')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')

{{-- Header --}}
<div class="rounded-2xl shadow-md p-6 mb-6 text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%)">
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-full bg-white/20 border-2 border-white/30 flex items-center justify-center text-2xl font-extrabold">
            {{ substr($santri->nama, 0, 1) }}
        </div>
        <div>
            <p class="text-indigo-200 text-sm font-medium">Laporan Santri</p>
            <h3 class="text-2xl font-extrabold">{{ $santri->nama }}</h3>
            <p class="text-indigo-200 text-sm">NIS: {{ $santri->nis }}</p>
        </div>
        <div class="ml-auto text-right">
            <p class="text-indigo-200 text-xs uppercase font-bold">Saldo Saat Ini</p>
            <h3 class="text-2xl font-extrabold">Rp {{ number_format($santri->saldo) }}</h3>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="glass-panel rounded-2xl shadow-sm p-5 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari->toDateString() }}"
                   class="px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai->toDateString() }}"
                   class="px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">
        </div>
        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>
        <a href="{{ route('wali.laporan.show', $santri) }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">Reset</a>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-red-400">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Belanja</p>
        <h3 class="text-lg font-extrabold text-red-600">Rp {{ number_format($totalBelanja) }}</h3>
        <p class="text-xs text-gray-400 mt-1">periode ini</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Top Up</p>
        <h3 class="text-lg font-extrabold text-green-700">Rp {{ number_format($totalTopUp) }}</h3>
        <p class="text-xs text-gray-400 mt-1">periode ini</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-emerald-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Hafalan (Ziyadah)</p>
        <h3 class="text-2xl font-extrabold text-emerald-700">{{ number_format($totalHafalan, 1) }}</h3>
        <p class="text-xs text-gray-400 mt-1">halaman maqbul</p>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-blue-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Laundry</p>
        <h3 class="text-lg font-extrabold text-blue-700">Rp {{ number_format($totalLaundry) }}</h3>
        <p class="text-xs text-gray-400 mt-1">{{ $laundry->count() }} order</p>
    </div>
</div>

{{-- Tabs --}}
<div x-data="{ tab: 'mutasi' }" class="space-y-4">

    {{-- Tab nav --}}
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-100 overflow-x-auto">
            <button @click="tab = 'mutasi'" :class="tab === 'mutasi' ? 'border-b-2 border-indigo-600 text-indigo-600 bg-indigo-50/50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap transition-colors flex items-center gap-2">
                <i class="fa-solid fa-right-left"></i> Mutasi Keuangan
                <span class="px-1.5 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $mutasi->count() }}</span>
            </button>
            <button @click="tab = 'tahfidz'" :class="tab === 'tahfidz' ? 'border-b-2 border-emerald-600 text-emerald-600 bg-emerald-50/50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap transition-colors flex items-center gap-2">
                <i class="fa-solid fa-book-quran"></i> Tahfidz
                <span class="px-1.5 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $setoran->count() }}</span>
            </button>
            <button @click="tab = 'laundry'" :class="tab === 'laundry' ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50/50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap transition-colors flex items-center gap-2">
                <i class="fa-solid fa-shirt"></i> Laundry
                <span class="px-1.5 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $laundry->count() }}</span>
            </button>
            <button @click="tab = 'kantin'" :class="tab === 'kantin' ? 'border-b-2 border-amber-600 text-amber-600 bg-amber-50/50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap transition-colors flex items-center gap-2">
                <i class="fa-solid fa-utensils"></i> Kantin
                <span class="px-1.5 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $kantin->count() }}</span>
            </button>
        </div>

        {{-- Mutasi Keuangan --}}
        <div x-show="tab === 'mutasi'" class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                        <th class="px-5 py-3 font-medium">Tanggal</th>
                        <th class="px-5 py-3 font-medium">Keterangan</th>
                        <th class="px-5 py-3 font-medium text-center">Jenis</th>
                        <th class="px-5 py-3 font-medium text-right">Nominal</th>
                        <th class="px-5 py-3 font-medium text-right">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($mutasi as $m)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ $m->keterangan }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($m->jenis === 'kredit')
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700">+ Kredit</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-600">- Debit</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm font-semibold text-right {{ $m->jenis === 'kredit' ? 'text-green-700' : 'text-red-600' }}">
                            {{ $m->jenis === 'kredit' ? '+' : '-' }} Rp {{ number_format($m->nominal) }}
                        </td>
                        <td class="px-5 py-3 text-sm font-semibold text-indigo-700 text-right">Rp {{ number_format($m->saldo_sesudah) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada mutasi keuangan pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tahfidz --}}
        <div x-show="tab === 'tahfidz'" style="display:none" class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                        <th class="px-5 py-3 font-medium">Tanggal</th>
                        <th class="px-5 py-3 font-medium">Penerima</th>
                        <th class="px-5 py-3 font-medium">Jenis</th>
                        <th class="px-5 py-3 font-medium">Dari</th>
                        <th class="px-5 py-3 font-medium">Sampai</th>
                        <th class="px-5 py-3 font-medium text-center">Hlm</th>
                        <th class="px-5 py-3 font-medium text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($setoran as $s)
                    <tr class="hover:bg-emerald-50/20 transition-colors">
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $s->tanggal->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ $s->penerima?->nama ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $s->jenis === 'ziyadah' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($s->jenis) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-700">
                            @if($s->surahAwal)
                                <span class="font-medium">{{ $s->surahAwal->nama_latin }}</span>
                                @if($s->ayat_awal)<span class="text-gray-400">: {{ $s->ayat_awal }}</span>@endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-700">
                            @if($s->surahAkhir)
                                <span class="font-medium">{{ $s->surahAkhir->nama_latin }}</span>
                                @if($s->ayat_akhir)<span class="text-gray-400">: {{ $s->ayat_akhir }}</span>@endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm font-semibold text-gray-800 text-center">{{ $s->jumlah_halaman }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $s->status === 'maqbul' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ ucfirst($s->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada data setoran pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Laundry --}}
        <div x-show="tab === 'laundry'" style="display:none" class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                        <th class="px-5 py-3 font-medium">Tanggal</th>
                        <th class="px-5 py-3 font-medium">No. Tiket</th>
                        <th class="px-5 py-3 font-medium text-center">Berat</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($laundry as $l)
                    @php
                        $lsc = match($l->status) {
                            'diterima'  => 'bg-yellow-100 text-yellow-700',
                            'dicuci'    => 'bg-blue-100 text-blue-700',
                            'selesai'   => 'bg-green-100 text-green-700',
                            'diambil'   => 'bg-gray-100 text-gray-600',
                            default     => 'bg-gray-100 text-gray-600',
                        };
                        $lLabel = ['diterima' => 'Diterima', 'dicuci' => 'Dicuci', 'selesai' => 'Selesai', 'diambil' => 'Diambil'];
                    @endphp
                    <tr class="hover:bg-blue-50/20 transition-colors">
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3 text-sm font-mono text-gray-800">{{ $l->nomor_tiket }}</td>
                        <td class="px-5 py-3 text-sm text-center text-gray-600">{{ $l->berat_kg }} kg</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $lsc }}">{{ $lLabel[$l->status] ?? $l->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm font-bold text-blue-700 text-right">Rp {{ number_format($l->total) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada order laundry pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Kantin --}}
        <div x-show="tab === 'kantin'" style="display:none" class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                        <th class="px-5 py-3 font-medium">Tanggal</th>
                        <th class="px-5 py-3 font-medium">Kantin</th>
                        <th class="px-5 py-3 font-medium text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($kantin as $k)
                    <tr class="hover:bg-amber-50/20 transition-colors">
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $k->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ $k->outlet?->nama ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm font-bold text-amber-700 text-right">Rp {{ number_format($k->total) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada transaksi kantin pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- Back button --}}
<div class="mt-6">
    <a href="{{ route('wali.dashboard') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

@endsection
