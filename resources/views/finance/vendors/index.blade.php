@extends('layouts.app')
@section('title', 'Vendor / Supplier')
@section('page-title', 'Master Vendor / Supplier')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="flex justify-between items-center mb-4 flex-wrap gap-3">
    <form method="GET" class="flex gap-2 flex-1">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama/kode vendor..." class="flex-1 max-w-md rounded-lg border-gray-300 text-sm">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Cari</button>
    </form>
    <a href="{{ route('finance.vendors.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold">
        <i class="fa-solid fa-plus mr-1"></i> Vendor Baru
    </a>
</div>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama Vendor</th>
                <th class="px-4 py-3 text-left">Kontak</th>
                <th class="px-4 py-3 text-left">Telepon</th>
                <th class="px-4 py-3 text-left">Bank</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $v)
            <tr class="hover:bg-indigo-50/30">
                <td class="px-4 py-3 font-mono text-xs">{{ $v->kode }}</td>
                <td class="px-4 py-3 font-semibold">{{ $v->nama }}</td>
                <td class="px-4 py-3 text-xs">{{ $v->kontak_person ?: '-' }}</td>
                <td class="px-4 py-3 text-xs">{{ $v->telepon ?: '-' }}</td>
                <td class="px-4 py-3 text-xs">{{ $v->bank_nama ? $v->bank_nama . ' / ' . $v->bank_rekening : '-' }}</td>
                <td class="px-4 py-3 text-center">@if($v->is_active)<span class="text-green-600">●</span>@else<span class="text-gray-400">●</span>@endif</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('finance.vendors.edit', $v) }}" class="text-indigo-600 mr-2"><i class="fa-solid fa-pen"></i></a>
                    <form method="POST" action="{{ route('finance.vendors.destroy', $v) }}" class="inline" onsubmit="return confirm('Hapus vendor?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada vendor.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
