@extends('layouts.app')
@section('title', 'Detail Pesanan #' . $order->id)
@section('page-title', 'Detail Pesanan')
@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all text-indigo-100 hover:bg-white/10">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection
@section('content')
<div class="flex items-center justify-between mb-6 max-w-5xl mx-auto px-1">
    <a href="{{ route('admin.marketplace.orders.index') }}" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar Pesanan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
    <!-- Item Pesanan -->
    <div class="lg:col-span-2">
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-box-open text-indigo-500"></i> Item Pesanan
                </h6>
                <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100">Order #{{ $order->id }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead><tr class="bg-white border-b border-gray-100 text-gray-500 text-sm">
                        <th class="px-6 py-4 font-medium">Produk</th>
                        <th class="px-6 py-4 font-medium text-center">Qty</th>
                        <th class="px-6 py-4 font-medium text-right">Harga</th>
                        <th class="px-6 py-4 font-medium text-right">Subtotal</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($order->items as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $item->nama_produk }}</div>
                                @if($item->produk)
                                    <div class="text-xs text-gray-500 mt-1">Sisa stok: {{ $item->produk->stok }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center"><span class="inline-block px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-md font-bold text-sm">{{ $item->qty }}</span></td>
                            <td class="px-6 py-4 text-right text-gray-600">Rp {{ number_format($item->harga) }}</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-800">Rp {{ number_format($item->subtotal) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr class="bg-gray-50/50 border-t-2 border-gray-100">
                        <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-500 uppercase text-xs tracking-wider">Total Pembayaran</td>
                        <td class="px-6 py-4 text-right font-extrabold text-emerald-600 text-lg">Rp {{ number_format($order->total) }}</td>
                    </tr></tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Kanan -->
    <div class="space-y-6">
        <!-- Info Pesanan -->
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
                <h6 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-indigo-500"></i> Informasi Pesanan</h6>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $s = match($order->status) {
                        'pending'    => 'bg-amber-100 text-amber-700 border border-amber-200',
                        'diproses'   => 'bg-blue-100 text-blue-700 border border-blue-200',
                        'selesai'    => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                        'dibatalkan' => 'bg-red-100 text-red-700 border border-red-200',
                        default      => 'bg-gray-100 text-gray-700 border border-gray-200'
                    };
                @endphp
                <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                    <span class="text-xs font-bold text-gray-400 uppercase">Status</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $s }}">{{ $statusOptions[$order->status] ?? $order->status }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                    <span class="text-xs font-bold text-gray-400 uppercase">Tanggal</span>
                    <span class="text-sm font-semibold text-gray-700">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="border-b border-gray-50 pb-3">
                    <span class="text-xs font-bold text-gray-400 uppercase block mb-2">Pembeli (Wali)</span>
                    <div class="text-sm font-bold text-gray-800">{{ $order->wali->nama }}</div>
                    <div class="text-xs text-gray-500">{{ $order->wali->no_hp }}</div>
                </div>
                <div class="border-b border-gray-50 pb-3">
                    <span class="text-xs font-bold text-gray-400 uppercase block mb-2">Penerima (Santri)</span>
                    <div class="text-sm font-bold text-gray-800">{{ $order->tujuanSantri?->nama ?? '-' }}</div>
                    <div class="text-xs text-gray-500 font-mono">{{ $order->tujuanSantri?->nis ?? '-' }}</div>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase block mb-1">Opsi Terima</span>
                    <span class="text-sm font-bold text-gray-800">{{ ucwords(str_replace('_', ' ', $order->opsi_terima)) }}</span>
                </div>
            </div>
        </div>

        <!-- Ubah Status -->
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
                <h6 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-clipboard-check text-indigo-500"></i> Ubah Status</h6>
            </div>
            <div class="p-6">
                @if(in_array($order->status, ['selesai', 'dibatalkan']))
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center">
                        <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-2 {{ $order->status === 'selesai' ? 'bg-emerald-100 text-emerald-500' : 'bg-red-100 text-red-500' }}">
                            <i class="fa-solid {{ $order->status === 'selesai' ? 'fa-check' : 'fa-xmark' }} text-xl"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800">Order sudah {{ $statusOptions[$order->status] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Status tidak bisa diubah lagi.</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.marketplace.orders.update-status', $order) }}">
                        @csrf @method('PATCH')
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Status Baru:</label>
                            <select name="status" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm font-medium" required>
                                @foreach($statusOptions as $key => $label)
                                    <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-amber-600 flex items-start gap-1">
                                <i class="fa-solid fa-circle-info mt-0.5"></i> Jika dibatalkan, stok akan dikembalikan otomatis.
                            </p>
                        </div>
                        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200 flex justify-center items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Status
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
