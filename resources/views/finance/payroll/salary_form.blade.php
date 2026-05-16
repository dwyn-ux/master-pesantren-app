@extends('layouts.app')
@section('title', 'Set Gaji Ustadz')
@section('page-title', 'Set Gaji ' . $ustadz->nama)

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ route('finance.payroll.salary.update', $ustadz) }}" class="max-w-3xl">
    @csrf @method('PUT')

    <div class="glass-panel rounded-2xl p-6 mb-6">
        <div class="mb-6 pb-4 border-b">
            <h3 class="text-lg font-bold">{{ $ustadz->nama }}</h3>
            <p class="text-sm text-gray-500">NIK: {{ $ustadz->nik ?: '-' }}</p>
        </div>

        <h4 class="font-bold text-sm mb-3 text-green-700"><i class="fa-solid fa-plus-circle mr-1"></i> Komponen Gaji</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-semibold mb-1">Gaji Pokok *</label>
                <input type="number" step="0.01" min="0" name="gaji_pokok" value="{{ old('gaji_pokok', $salary->gaji_pokok ?? 0) }}" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tunjangan Jabatan</label>
                <input type="number" step="0.01" min="0" name="tunjangan_jabatan" value="{{ old('tunjangan_jabatan', $salary->tunjangan_jabatan ?? 0) }}" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tunjangan Transport</label>
                <input type="number" step="0.01" min="0" name="tunjangan_transport" value="{{ old('tunjangan_transport', $salary->tunjangan_transport ?? 0) }}" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tunjangan Makan</label>
                <input type="number" step="0.01" min="0" name="tunjangan_makan" value="{{ old('tunjangan_makan', $salary->tunjangan_makan ?? 0) }}" required class="w-full rounded-lg border-gray-300">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Tunjangan Lain</label>
                <input type="number" step="0.01" min="0" name="tunjangan_lain" value="{{ old('tunjangan_lain', $salary->tunjangan_lain ?? 0) }}" required class="w-full rounded-lg border-gray-300">
            </div>
        </div>

        <h4 class="font-bold text-sm mb-3 text-red-700"><i class="fa-solid fa-minus-circle mr-1"></i> Potongan Tetap Bulanan</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-semibold mb-1">Potongan Tetap</label>
                <input type="number" step="0.01" min="0" name="potongan_tetap" value="{{ old('potongan_tetap', $salary->potongan_tetap ?? 0) }}" required class="w-full rounded-lg border-gray-300">
                <p class="text-xs text-gray-500 mt-1">BPJS, koperasi, dll. Potongan variabel (PPh21, absensi) di-set per slip.</p>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Berlaku Sejak *</label>
                <input type="date" name="berlaku_sejak" value="{{ old('berlaku_sejak', optional($salary->berlaku_sejak)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300">
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">Simpan</button>
        <a href="{{ route('finance.payroll.master-salary') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>
    @if($errors->any())
    <div class="mt-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded text-sm">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif
</form>
@endsection
