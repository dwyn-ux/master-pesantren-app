@extends('layouts.app')
@section('title', 'Rekonsiliasi Bank')
@section('page-title', 'Rekonsiliasi Bank')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="GET" class="glass-panel p-4 rounded-2xl mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div>
        <label class="block text-xs font-semibold mb-1">Bank</label>
        <select name="kas_bank_id" required class="w-full rounded-lg border-gray-300 text-sm">
            <option value="">— Pilih Bank —</option>
            @foreach($kasBanks as $kb)
                <option value="{{ $kb->id }}" @selected($kasBank?->id == $kb->id)>{{ $kb->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1">Per Tanggal</label>
        <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full rounded-lg border-gray-300 text-sm">
    </div>
    <div class="flex items-end">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">Hitung Saldo Buku</button>
    </div>
</form>

@if($kasBank)
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="glass-panel rounded-2xl p-6">
        <h6 class="font-bold mb-4">{{ $kasBank->nama }}</h6>
        <p class="text-sm text-gray-500">Per {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p>
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <p class="text-xs text-gray-600 font-bold uppercase">Saldo Buku (Sistem)</p>
            <p class="text-2xl font-extrabold text-blue-700">Rp {{ number_format($saldoBuku) }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('finance.bank-recon.store') }}" class="glass-panel rounded-2xl p-6">
        @csrf
        <h6 class="font-bold mb-4">Input Saldo Rekening Koran</h6>
        <input type="hidden" name="kas_bank_id" value="{{ $kasBank->id }}">
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <input type="hidden" name="saldo_buku" value="{{ $saldoBuku }}">

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-semibold mb-1">Saldo Rekening Koran *</label>
                <input type="number" step="0.01" name="saldo_rekening_koran" required class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Catatan / Penyebab Selisih</label>
                <textarea name="catatan" rows="3" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Mis: cek belum cair, biaya admin bank, dll"></textarea>
            </div>
            <button class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg font-bold">
                <i class="fa-solid fa-save mr-1"></i> Simpan Rekonsiliasi
            </button>
        </div>
    </form>
</div>
@endif

<div class="glass-panel rounded-2xl p-6 mt-6">
    <h6 class="font-bold mb-4">Riwayat Rekonsiliasi</h6>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2 text-left">Bank</th>
                <th class="px-3 py-2 text-right">Saldo Buku</th>
                <th class="px-3 py-2 text-right">Saldo Bank</th>
                <th class="px-3 py-2 text-right">Selisih</th>
                <th class="px-3 py-2 text-left">Catatan</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($history as $r)
            <tr>
                <td class="px-3 py-2 text-xs">{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
                <td class="px-3 py-2 font-semibold">{{ $r->kas_bank_nama }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($r->saldo_buku) }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($r->saldo_rekening_koran) }}</td>
                <td class="px-3 py-2 text-right font-bold {{ abs($r->selisih) > 0.01 ? 'text-red-600' : 'text-green-600' }}">
                    {{ abs($r->selisih) <= 0.01 ? '✓ Match' : 'Rp ' . number_format($r->selisih) }}
                </td>
                <td class="px-3 py-2 text-xs">{{ $r->catatan }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Belum ada rekonsiliasi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
