@extends('layouts.app')
@section('title', 'Laporan Bulanan ' . $santri->nama)
@section('page-title', 'Laporan Bulanan')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="rounded-2xl shadow-md p-6 mb-6 text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%)">
    <p class="text-indigo-200 text-sm font-medium">Laporan Bulanan</p>
    <h3 class="text-2xl font-extrabold">{{ $santri->nama }}</h3>
    <p class="text-indigo-100 text-sm mt-1">Periode: {{ $report['periode']['bulan'] }}</p>
</div>

<form method="GET" class="glass-panel rounded-2xl shadow-sm p-5 mb-6 flex flex-wrap items-end gap-3">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Bulan</label>
        <select name="month" class="px-4 py-2 rounded-xl border border-gray-200 text-sm">
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" @selected($m === $month)>{{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
        <input type="number" name="year" value="{{ $year }}" class="px-4 py-2 rounded-xl border border-gray-200 text-sm w-28">
    </div>
    <button class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium">Tampilkan</button>
</form>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-indigo-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Saldo Saat Ini</p>
        <h3 class="text-lg font-extrabold text-indigo-700">Rp {{ number_format($report['saldo']) }}</h3>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Top Up</p>
        <h3 class="text-lg font-extrabold text-green-700">Rp {{ number_format($report['topup']) }}</h3>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-red-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Belanja</p>
        <h3 class="text-lg font-extrabold text-red-600">Rp {{ number_format($report['belanja']) }}</h3>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-emerald-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Hafalan Bulan Ini</p>
        <h3 class="text-lg font-extrabold text-emerald-700">{{ $report['tahfidz']['halaman_baru'] }} hal</h3>
        <p class="text-xs text-gray-400 mt-1">Total: {{ $report['tahfidz']['juz'] }} juz {{ $report['tahfidz']['sisa_halaman'] }} hal</p>
    </div>
</div>

@if($report['tahfidz']['posisi_terakhir'])
<div class="glass-panel rounded-2xl shadow-sm p-5 mb-6">
    <p class="text-sm font-bold text-gray-600 mb-1">Posisi Hafalan Terakhir</p>
    <p class="text-gray-700">
        {{ $report['tahfidz']['posisi_terakhir']['surah'] }} : ayat {{ $report['tahfidz']['posisi_terakhir']['ayat'] }}
    </p>
</div>
@endif

<div class="glass-panel rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-3 border-b border-gray-100 bg-amber-50/40 font-semibold text-amber-700 text-sm">
        Detail Belanja ({{ $report['belanja_detail']->count() }} transaksi)
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500">
                    <th class="px-5 py-3 font-medium">Tanggal</th>
                    <th class="px-5 py-3 font-medium">Outlet</th>
                    <th class="px-5 py-3 font-medium text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($report['belanja_detail'] as $t)
                    <tr>
                        <td class="px-5 py-2 text-gray-500">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-2">{{ $t->outlet?->nama ?? '-' }}</td>
                        <td class="px-5 py-2 text-right font-semibold text-amber-700">Rp {{ number_format($t->total) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">Tidak ada transaksi kantin bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="flex gap-3">
    <a href="{{ route('wali.laporan.show', $santri) }}" class="text-sm text-gray-500 hover:text-indigo-600"><i class="fa-solid fa-arrow-left"></i> Laporan Harian</a>
    <a href="{{ route('wali.laporan.semester', ['santri' => $santri, 'semester' => $month <= 6 ? 1 : 2, 'year' => $year]) }}" class="text-sm text-gray-500 hover:text-indigo-600 ml-auto">Laporan Semester <i class="fa-solid fa-arrow-right"></i></a>
</div>
@endsection
