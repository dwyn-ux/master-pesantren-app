@extends('layouts.app')
@section('title', 'Pengaturan Laporan Otomatis')
@section('page-title', 'Pengaturan Laporan Otomatis')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
@endif

<div class="rounded-2xl shadow-md p-6 mb-6 text-white" style="background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%)">
    <h3 class="text-2xl font-extrabold">Pengaturan Laporan Otomatis</h3>
    <p class="text-teal-100 text-sm mt-1">Atur kapan laporan mingguan & bulanan dikirim ke wali santri.</p>
</div>

<form method="POST" action="{{ route('admin.report-settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    @php
        $days = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
    @endphp

    <div class="glass-panel rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-gray-700 text-lg">
                <i class="fa-solid fa-calendar-week mr-2 text-teal-600"></i> Laporan Mingguan
            </h4>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="weekly_enabled" value="0">
                <input type="checkbox" name="weekly_enabled" value="1" @checked($weeklyReport['enabled'] ?? true) class="w-5 h-5 rounded text-teal-600">
                <span class="text-sm font-medium">Aktif</span>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Hari Kirim</label>
                <select name="weekly_day" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm">
                    @foreach($days as $idx => $label)
                        <option value="{{ $idx }}" @selected(($weeklyReport['day_of_week'] ?? 0) == $idx)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Jam</label>
                <input type="number" name="weekly_hour" min="0" max="23" value="{{ $weeklyReport['hour'] ?? 8 }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Menit</label>
                <input type="number" name="weekly_minute" min="0" max="59" value="{{ $weeklyReport['minute'] ?? 0 }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm">
            </div>
        </div>

        <p class="text-xs text-gray-400 mt-3"><i class="fa-solid fa-info-circle mr-1"></i> Scheduler dijalankan tiap jam; command akan cek hari & jam sesuai setting ini.</p>
    </div>

    <div class="glass-panel rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-gray-700 text-lg">
                <i class="fa-solid fa-calendar-days mr-2 text-teal-600"></i> Laporan Bulanan
            </h4>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="monthly_enabled" value="0">
                <input type="checkbox" name="monthly_enabled" value="1" @checked($monthlyReport['enabled'] ?? true) class="w-5 h-5 rounded text-teal-600">
                <span class="text-sm font-medium">Aktif</span>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Kirim</label>
                <input type="number" name="monthly_day" min="1" max="28" value="{{ $monthlyReport['day_of_month'] ?? 1 }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm">
                <p class="text-xs text-gray-400 mt-1">Max 28 agar valid tiap bulan.</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Jam</label>
                <input type="number" name="monthly_hour" min="0" max="23" value="{{ $monthlyReport['hour'] ?? 8 }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Menit</label>
                <input type="number" name="monthly_minute" min="0" max="59" value="{{ $monthlyReport['minute'] ?? 0 }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm">
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-medium text-sm shadow-sm">
            <i class="fa-solid fa-save mr-1"></i> Simpan Pengaturan
        </button>
    </div>
</form>
@endsection
