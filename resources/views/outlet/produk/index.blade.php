@extends('layouts.app')
@section('title', 'Produk Kantin')
@section('page-title', 'Produk & Stok')

@section('sidebar')
    @include('partials.sidebar-outlet')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden max-w-6xl mx-auto">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-box-open text-indigo-500"></i> Katalog Produk
            </h6>
            <p class="text-sm text-gray-500 mt-1">{{ $outlet->nama }}</p>
        </div>
        <a href="{{ route('outlet.produk.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </a>
    </div>

    <div class="p-6 border-b border-gray-100 bg-gray-50/30">
        <form method="GET" class="flex flex-col sm:flex-row gap-4 w-full">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm" placeholder="Cari nama produk atau barcode...">
            </div>
            
            <div class="w-full sm:w-48">
                <select name="status" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center flex-1 sm:flex-none">
                    Filter
                </button>
                <a href="{{ route('outlet.produk.index') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800 rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center flex-1 sm:flex-none">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if($produk->isEmpty())
        <div class="p-16 flex flex-col items-center justify-center text-center">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                <i class="fa-solid fa-box text-4xl text-gray-300"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-600 mb-1">Katalog Masih Kosong</h4>
            <p class="text-gray-400 max-w-sm mb-6">Belum ada produk yang ditambahkan. Tambahkan produk pertama Anda untuk dijual di marketplace.</p>
            <a href="{{ route('outlet.produk.create') }}" class="px-6 py-2.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-xl font-medium transition-colors">
                Tambah Produk
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                        <th class="px-6 py-4 font-medium">Informasi Produk</th>
                        <th class="px-6 py-4 font-medium">Barcode</th>
                        <th class="px-6 py-4 font-medium">Harga</th>
                        <th class="px-6 py-4 font-medium">Stok</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($produk as $item)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    @if($item->foto)
                                        <img src="{{ Storage::url($item->foto) }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 bg-white" alt="{{ $item->nama }}">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center border border-gray-200 text-gray-400">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $item->nama }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-sm text-gray-600">
                                {{ $item->barcode ?? '-' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-800">
                                Rp {{ number_format($item->harga) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->stok <= 5)
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-amber-50 border border-amber-200 text-amber-600 text-xs font-bold gap-1">
                                        <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> {{ $item->stok }} Tersisa
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs font-bold">
                                        {{ $item->stok }} Tersedia
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($item->is_aktif)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('outlet.produk.edit', $item) }}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition-colors border border-indigo-100" title="Edit Produk">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>
                                    
                                    @if($item->is_aktif)
                                        <form method="POST" action="{{ route('outlet.produk.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan produk ini dari marketplace?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-colors border border-red-100" title="Nonaktifkan">
                                                <i class="fa-solid fa-eye-slash text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($produk->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $produk->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
