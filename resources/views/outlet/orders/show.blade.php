@extends('layouts.app')
@section('title', 'Detail Pesanan #' . $order->id)
@section('page-title', 'Detail Pesanan Masuk')

@section('sidebar')
    @include('partials.sidebar-outlet')
@endsection

@section('content')
<div class="flex justify-between items-center mb-6 max-w-5xl mx-auto px-2">
    <a href="{{ route('outlet.marketplace.orders.index') }}" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar Pesanan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
    <!-- Detail Produk -->
    <div class="lg:col-span-2 space-y-6">
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden bg-white">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-box-open text-indigo-500"></i> Item Pesanan
                </h6>
                <div class="font-mono font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100">
                    Order #{{ $order->id }}
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-100 text-gray-500 text-sm">
                            <th class="px-6 py-4 font-medium">Produk</th>
                            <th class="px-6 py-4 font-medium text-center">Qty</th>
                            <th class="px-6 py-4 font-medium text-right">Harga</th>
                            <th class="px-6 py-4 font-medium text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($order->items as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $item->nama_produk }}</div>
                                    @if($item->produk)
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-cubes text-[10px]"></i> Sisa stok: {{ $item->produk->stok }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-md font-bold text-sm">
                                        {{ $item->qty }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-600">
                                    Rp {{ number_format($item->harga) }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-800">
                                    Rp {{ number_format($item->subtotal) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50/50 border-t-2 border-gray-100">
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-500 uppercase tracking-wider text-xs">Total Pembayaran</td>
                            <td class="px-6 py-4 text-right font-extrabold text-emerald-600 text-lg">Rp {{ number_format($order->total) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Informasi & Update Status -->
    <div class="lg:col-span-1 space-y-6">
        
        <!-- Info Pesanan -->
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
                <h6 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-indigo-500"></i> Informasi Pesanan
                </h6>
            </div>
            <div class="p-6 space-y-4">
                
                @php
                    $badgeClass = match($order->status) {
                        'pending' => 'bg-amber-100 text-amber-700 border border-amber-200',
                        'diproses' => 'bg-blue-100 text-blue-700 border border-blue-200',
                        'selesai' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                        'dibatalkan' => 'bg-red-100 text-red-700 border border-red-200',
                        default => 'bg-gray-100 text-gray-700 border border-gray-200'
                    };
                @endphp
                <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                    <span class="text-sm font-bold text-gray-400 uppercase">Status</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">
                        {{ $statusOptions[$order->status] ?? $order->status }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                    <span class="text-sm font-bold text-gray-400 uppercase">Tanggal</span>
                    <span class="text-sm font-semibold text-gray-700">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="border-b border-gray-50 pb-3">
                    <span class="text-sm font-bold text-gray-400 uppercase block mb-1">Pembeli (Wali)</span>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-800">{{ $order->wali->nama }}</div>
                            <div class="text-xs text-gray-500">{{ $order->wali->no_hp }}</div>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-sm font-bold text-gray-400 uppercase block mb-1">Penerima (Santri)</span>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-child"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-800">{{ $order->tujuanSantri?->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $order->tujuanSantri?->nis ?? '-' }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Proses Pesanan -->
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden sticky top-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
                <h6 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-check text-indigo-500"></i> Proses Pesanan
                </h6>
            </div>
            <div class="p-6">
                @if(in_array($order->status, ['selesai', 'dibatalkan']))
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center">
                        <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-2 {{ $order->status === 'selesai' ? 'bg-emerald-100 text-emerald-500' : 'bg-red-100 text-red-500' }}">
                            <i class="fa-solid {{ $order->status === 'selesai' ? 'fa-check' : 'fa-xmark' }} text-xl"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800">Order telah {{ $statusOptions[$order->status] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Status pesanan tidak dapat diubah lagi.</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('outlet.marketplace.orders.update-status', $order) }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ubah Status Menjadi:</label>
                            <select name="status" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm font-medium" required>
                                @foreach($statusOptions as $key => $label)
                                    <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-amber-600 flex items-start gap-1">
                                <i class="fa-solid fa-circle-info mt-0.5"></i>
                                Jika dibatalkan, uang saku (saldo wali) akan dikembalikan dan stok bertambah kembali secara otomatis.
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
