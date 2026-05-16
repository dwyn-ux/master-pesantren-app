@extends('layouts.app')
@section('title', 'Kategori Keuangan')
@section('page-title', 'Master Kategori Pemasukan & Pengeluaran')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="flex justify-end mb-4">
    <a href="{{ route('finance.kategoris.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">
        <i class="fa-solid fa-plus mr-1"></i> Tambah Kategori
    </a>
</div>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr>
                <th class="text-left px-4 py-3">Kode</th>
                <th class="text-left px-4 py-3">Nama</th>
                <th class="text-left px-4 py-3">Tipe</th>
                <th class="text-left px-4 py-3">Akun COA</th>
                <th class="text-center px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($items as $k)
            <tr class="hover:bg-indigo-50/30">
                <td class="px-4 py-3 font-mono text-xs">{{ $k->kode }}</td>
                <td class="px-4 py-3 font-semibold">{{ $k->nama }}</td>
                <td class="px-4 py-3">
                    @if($k->tipe == 'pemasukan')
                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Pemasukan</span>
                    @else
                        <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Pengeluaran</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-xs text-gray-600">{{ $k->account?->kode }} - {{ $k->account?->nama }}</td>
                <td class="px-4 py-3 text-center">
                    @if($k->is_active)<span class="text-green-600 text-xs">●</span>@else<span class="text-gray-400 text-xs">●</span>@endif
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('finance.kategoris.edit', $k) }}" class="text-indigo-600 mr-2"><i class="fa-solid fa-pen"></i></a>
                    <form method="POST" action="{{ route('finance.kategoris.destroy', $k) }}" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
