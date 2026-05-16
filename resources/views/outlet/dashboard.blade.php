@extends('layouts.app')
@section('title', 'Dashboard Outlet')
@section('page-title', 'Dashboard Outlet')

@section('sidebar')
    @include('partials.sidebar-outlet')
@endsection

@section('content')
<div class="glass-panel p-6 rounded-2xl shadow-sm border-l-4 border-indigo-500 flex items-center justify-between mb-8 overflow-hidden relative group">
    <div class="absolute right-0 top-1/2 -translate-y-1/2 opacity-5 -translate-x-4 group-hover:-translate-x-2 transition-transform duration-500 pointer-events-none">
        <i class="fa-solid fa-shop text-9xl"></i>
    </div>
    <div class="relative z-10 flex items-center gap-5">
        <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl shadow-sm group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-shop"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Outlet Aktif</div>
            <h4 class="text-2xl font-bold text-gray-800 leading-tight mb-1">{{ $outlet->nama }}</h4>
            <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg text-xs font-bold uppercase tracking-wider">
                {{ $outlet->tipe }}
            </span>
        </div>
    </div>
</div>

@if($outlet->tipe === 'kantin')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-indigo-500">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Produk Aktif</div>
            <div class="text-3xl font-extrabold text-gray-800">{{ $stats['produk_aktif'] }}</div>
        </div>
        
        <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-amber-500">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Stok Menipis</div>
            <div class="text-3xl font-extrabold text-amber-600">{{ $stats['stok_menipis'] }}</div>
        </div>
        
        <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-amber-500">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Order Pending</div>
            <div class="text-3xl font-extrabold text-amber-600">{{ $stats['order_pending'] }}</div>
        </div>
        
        <div class="glass-panel p-6 rounded-2xl shadow-sm border-b-4 border-blue-500">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Order Diproses</div>
            <div class="text-3xl font-extrabold text-blue-600">{{ $stats['order_diproses'] }}</div>
        </div>
    </div>

    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center gap-2">
            <i class="fa-solid fa-bolt text-amber-500"></i>
            <h6 class="font-bold text-gray-800">Aksi Cepat Kantin</h6>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('outlet.kasir.kantin.index') }}" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors shadow-sm shadow-emerald-200 flex items-center justify-center gap-2 flex-1 min-w-[200px]">
                    <i class="fa-solid fa-cash-register"></i> Buka Kasir
                </a>
                <a href="{{ route('outlet.produk.index') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors shadow-sm shadow-indigo-200 flex items-center justify-center gap-2 flex-1 min-w-[200px]">
                    <i class="fa-solid fa-box-open"></i> Kelola Produk
                </a>
                <a href="{{ route('outlet.produk.create') }}" class="px-6 py-3 border-2 border-indigo-200 text-indigo-700 hover:bg-indigo-50 rounded-xl font-semibold transition-colors flex items-center justify-center gap-2 flex-1 min-w-[200px]">
                    <i class="fa-solid fa-plus"></i> Tambah Produk
                </a>
                <a href="{{ route('outlet.marketplace.orders.index') }}" class="px-6 py-3 border-2 border-emerald-200 text-emerald-700 hover:bg-emerald-50 rounded-xl font-semibold transition-colors flex items-center justify-center gap-2 flex-1 min-w-[200px]">
                    <i class="fa-solid fa-clipboard-check"></i> Proses Pesanan
                </a>
            </div>
        </div>
    </div>
@else
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-2xl mx-auto mt-10">
        <div class="p-10 text-center flex flex-col items-center">
            <div class="w-32 h-32 bg-indigo-50 rounded-full flex items-center justify-center mb-6 border border-indigo-100 shadow-sm relative">
                <i class="fa-solid fa-shirt text-5xl text-indigo-400"></i>
                <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-white rounded-full flex items-center justify-center border border-gray-100 shadow-sm">
                    <i class="fa-solid fa-fingerprint text-xl text-emerald-500"></i>
                </div>
            </div>
            
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Dashboard Laundry</h3>
            <p class="text-gray-500 leading-relaxed mb-8 max-w-md mx-auto">
                Kelola order laundry santri. Timbang pakaian, tentukan harga, dan konfirmasi pembayaran menggunakan verifikasi fingerprint.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
                <a href="{{ route('outlet.laundry.create') }}" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-plus"></i> Terima Laundry Baru
                </a>
                <a href="{{ route('outlet.laundry.index') }}" class="px-8 py-3 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-basket-shopping"></i> Daftar Order
                </a>
            </div>
        </div>
    </div>
@endif
@endsection
