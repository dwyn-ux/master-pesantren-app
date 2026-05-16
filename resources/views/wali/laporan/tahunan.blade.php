@extends('layouts.app')
@section('title', 'Laporan Tahunan ' . $santri->nama)
@section('page-title', 'Laporan Tahunan')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="rounded-2xl shadow-md p-6 mb-6 text-white" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%)">
    <p class="text-cyan-200 text-sm font-medium">Laporan Tahunan {{ $report['periode']['tahun'] }}</p>
    <h3 class="text-2xl font-extrabold">{{ $santri->nama }}</h3>
    <p class="text-cyan-100 text-sm mt-1">Periode: {{ $report['periode']['start'] }} — {{ $report['periode']['end'] }}</p>
</div>

<form method="GET" class="glass-panel rounded-2xl shadow-sm p-5 mb-6 flex flex-wrap items-end gap-3">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
        <input type="number" name="year" value="{{ $year }}" class="px-4 py-2 rounded-xl border border-gray-200 text-sm w-28">
    </div>
    <button class="px-5 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-sm font-medium">Tampilkan</button>
</form>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-cyan-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Saldo</p>
        <h3 class="text-lg font-extrabold text-cyan-700">Rp {{ number_format($report['saldo']) }}</h3>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-green-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Top Up Setahun</p>
        <h3 class="text-lg font-extrabold text-green-700">Rp {{ number_format($report['topup']) }}</h3>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-red-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Belanja Setahun</p>
        <h3 class="text-lg font-extrabold text-red-600">Rp {{ number_format($report['belanja']) }}</h3>
    </div>
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-emerald-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Hafalan Setahun</p>
        <h3 class="text-lg font-extrabold text-emerald-700">{{ $report['tahfidz']['halaman_baru'] }} hal</h3>
        <p class="text-xs text-gray-400 mt-1">Total: {{ $report['tahfidz']['juz'] }} juz {{ $report['tahfidz']['sisa_halaman'] }} hal</p>
    </div>
</div>

<div class="glass-panel rounded-2xl shadow-sm p-6 mb-6">
    <h4 class="font-bold text-gray-700 mb-3">Rangkuman Tahunan</h4>
    <p class="text-gray-600 text-sm leading-relaxed">
        Tahun {{ $report['periode']['tahun'] }}, santri menambah
        <strong>{{ $report['tahfidz']['halaman_baru'] }} halaman</strong> hafalan baru.
        Total akumulasi saat ini mencapai
        <strong>{{ $report['tahfidz']['juz'] }} juz {{ $report['tahfidz']['sisa_halaman'] }} halaman</strong>.
        Total top-up tahun ini: <strong>Rp {{ number_format($report['topup']) }}</strong>,
        dengan total belanja <strong>Rp {{ number_format($report['belanja']) }}</strong>.
    </p>
</div>

<div class="flex">
    <a href="{{ route('wali.laporan.semester', $santri) }}" class="text-sm text-gray-500 hover:text-indigo-600"><i class="fa-solid fa-arrow-left"></i> Laporan Semester</a>
</div>
@endsection
