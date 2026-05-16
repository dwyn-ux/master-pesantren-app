@extends('layouts.app')
@section('title', 'Laporan Semester ' . $santri->nama)
@section('page-title', 'Laporan Semester')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="rounded-2xl shadow-md p-6 mb-6 text-white" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%)">
    <p class="text-purple-200 text-sm font-medium">Laporan Semester {{ $report['periode']['semester'] }}</p>
    <h3 class="text-2xl font-extrabold">{{ $santri->nama }}</h3>
    <p class="text-purple-100 text-sm mt-1">Periode: {{ $report['periode']['start'] }} — {{ $report['periode']['end'] }}</p>
</div>

<form method="GET" class="glass-panel rounded-2xl shadow-sm p-5 mb-6 flex flex-wrap items-end gap-3">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Semester</label>
        <select name="semester" class="px-4 py-2 rounded-xl border border-gray-200 text-sm">
            <option value="1" @selected($semester === 1)>Semester 1 (Jan-Jun)</option>
            <option value="2" @selected($semester === 2)>Semester 2 (Jul-Des)</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
        <input type="number" name="year" value="{{ $year }}" class="px-4 py-2 rounded-xl border border-gray-200 text-sm w-28">
    </div>
    <button class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-medium">Tampilkan</button>
</form>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-5 rounded-2xl shadow-sm border-b-4 border-purple-500">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Saldo</p>
        <h3 class="text-lg font-extrabold text-purple-700">Rp {{ number_format($report['saldo']) }}</h3>
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
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Hafalan Semester</p>
        <h3 class="text-lg font-extrabold text-emerald-700">{{ $report['tahfidz']['halaman_baru'] }} hal</h3>
        <p class="text-xs text-gray-400 mt-1">Total: {{ $report['tahfidz']['juz'] }} juz {{ $report['tahfidz']['sisa_halaman'] }} hal</p>
    </div>
</div>

<div class="glass-panel rounded-2xl shadow-sm p-6 mb-6">
    <h4 class="font-bold text-gray-700 mb-3">Catatan Semester</h4>
    <p class="text-gray-600 text-sm leading-relaxed">
        Sepanjang semester ini, santri melakukan top-up sebesar
        <strong>Rp {{ number_format($report['topup']) }}</strong> dan belanja
        <strong>Rp {{ number_format($report['belanja']) }}</strong>.
        Capaian hafalan baru: <strong>{{ $report['tahfidz']['halaman_baru'] }} halaman</strong>
        dengan total akumulasi <strong>{{ $report['tahfidz']['juz'] }} juz {{ $report['tahfidz']['sisa_halaman'] }} halaman</strong>.
    </p>
</div>

<div class="flex gap-3">
    <a href="{{ route('wali.laporan.bulanan', $santri) }}" class="text-sm text-gray-500 hover:text-indigo-600"><i class="fa-solid fa-arrow-left"></i> Laporan Bulanan</a>
    <a href="{{ route('wali.laporan.tahunan', ['santri' => $santri, 'year' => $year]) }}" class="text-sm text-gray-500 hover:text-indigo-600 ml-auto">Laporan Tahunan <i class="fa-solid fa-arrow-right"></i></a>
</div>
@endsection
