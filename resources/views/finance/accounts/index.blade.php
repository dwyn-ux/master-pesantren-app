@extends('layouts.app')
@section('title', 'Chart of Accounts')
@section('page-title', 'Chart of Accounts (Daftar Akun)')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
@php
    $totalAcc      = \App\Models\Finance\Account::count();
    $totalAktif    = \App\Models\Finance\Account::where('is_active', true)->count();
    $totalKasBank  = \App\Models\Finance\Account::where('is_kas_bank', true)->count();
    $perTipe       = \App\Models\Finance\Account::selectRaw('tipe, COUNT(*) as total')->groupBy('tipe')->pluck('total', 'tipe');
@endphp

<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-indigo-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Total Akun</p>
        <p class="text-xl font-bold text-indigo-600">{{ $totalAcc }}</p>
        <p class="text-xs text-gray-400">{{ $totalAktif }} aktif</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Aktiva</p>
        <p class="text-xl font-bold text-blue-600">{{ $perTipe['asset'] ?? 0 }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-amber-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Liabilitas</p>
        <p class="text-xl font-bold text-amber-600">{{ $perTipe['liability'] ?? 0 }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-green-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Pendapatan</p>
        <p class="text-xl font-bold text-green-600">{{ $perTipe['revenue'] ?? 0 }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-red-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Beban</p>
        <p class="text-xl font-bold text-red-600">{{ $perTipe['expense'] ?? 0 }}</p>
    </div>
</div>

<form method="GET" class="glass-panel p-4 rounded-2xl mb-4 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-semibold mb-1">Cari</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Kode / nama akun" class="w-full rounded-lg border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1">Tipe</label>
        <select name="tipe" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua</option>
            @foreach(\App\Models\Finance\Account::TIPE as $k => $v)
                <option value="{{ $k }}" @selected(request('tipe')==$k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">Filter</button>
    <a href="{{ route('finance.accounts.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold ml-auto">
        <i class="fa-solid fa-plus mr-1"></i> Akun Baru
    </a>
</form>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr>
                <th class="text-left px-4 py-3">Kode</th>
                <th class="text-left px-4 py-3">Nama Akun</th>
                <th class="text-left px-4 py-3">Tipe</th>
                <th class="text-left px-4 py-3">Saldo Normal</th>
                <th class="text-left px-4 py-3">Parent</th>
                <th class="text-center px-4 py-3">Kas/Bank</th>
                <th class="text-center px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($accounts as $acc)
            @php
                $tipeBadge = match($acc->tipe) {
                    'asset'     => 'bg-blue-100 text-blue-700',
                    'liability' => 'bg-amber-100 text-amber-700',
                    'equity'    => 'bg-purple-100 text-purple-700',
                    'revenue'   => 'bg-green-100 text-green-700',
                    'expense'   => 'bg-red-100 text-red-700',
                    default     => 'bg-gray-100 text-gray-600',
                };
                $level = strlen($acc->kode);
            @endphp
            <tr class="hover:bg-indigo-50/30">
                <td class="px-4 py-3 font-mono text-xs font-bold">{{ $acc->kode }}</td>
                <td class="px-4 py-3">
                    <span style="padding-left: {{ ($level - 1) * 14 }}px;" class="{{ $level <= 1 ? 'font-bold uppercase' : '' }}">
                        {{ $acc->nama }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $tipeBadge }}">
                        {{ \App\Models\Finance\Account::TIPE[$acc->tipe] ?? $acc->tipe }}
                    </span>
                </td>
                <td class="px-4 py-3 text-xs uppercase font-semibold {{ $acc->saldo_normal == 'debit' ? 'text-blue-600' : 'text-amber-600' }}">
                    {{ $acc->saldo_normal }}
                </td>
                <td class="px-4 py-3 text-xs text-gray-500 font-mono">{{ $acc->parent?->kode ?: '—' }}</td>
                <td class="px-4 py-3 text-center">@if($acc->is_kas_bank)<i class="fa-solid fa-vault text-amber-500" title="Akun Kas/Bank"></i>@endif</td>
                <td class="px-4 py-3 text-center">
                    @if($acc->is_active)<span class="text-green-600 text-base">●</span>@else<span class="text-gray-300 text-base">●</span>@endif
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="{{ route('finance.accounts.edit', $acc) }}" class="text-indigo-600 hover:text-indigo-800 mr-2" title="Edit"><i class="fa-solid fa-pen"></i></a>
                    <form method="POST" action="{{ route('finance.accounts.destroy', $acc) }}" class="inline" onsubmit="return confirm('Hapus akun {{ $acc->kode }} - {{ $acc->nama }}?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $accounts->links() }}</div>
</div>
@endsection
