@extends('layouts.app')
@section('title', 'Pembayaran')
@section('page-title', 'Kelola Pembayaran')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <h6 class="text-lg font-bold text-gray-800">Riwayat & Daftar Pembayaran</h6>
    </div>

    {{-- Filter --}}
    <div class="p-6 border-b border-gray-100">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" 
                       placeholder="Cari wali / santri...">
            </div>
            <div class="w-full md:w-48">
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all bg-white appearance-none">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-48">
                <select name="metode" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all bg-white appearance-none">
                    <option value="">Semua Metode</option>
                    @foreach($metodeOptions as $key => $label)
                        <option value="{{ $key }}" @selected(request('metode') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm">
                    Filter
                </button>
                <a href="{{ route('admin.pembayaran.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Tanggal</th>
                    <th class="px-6 py-4 font-medium">Wali</th>
                    <th class="px-6 py-4 font-medium">Santri</th>
                    <th class="px-6 py-4 font-medium">Nominal</th>
                    <th class="px-6 py-4 font-medium">Metode</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pembayaran as $item)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="px-6 py-4 text-gray-600">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $item->wali->nama }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->tagihan->santri->nama ?? '-' }}</td>
                    <td class="px-6 py-4 font-bold text-gray-800">Rp {{ number_format($item->nominal) }}</td>
                    <td class="px-6 py-4">
                        @php
                            $metodeLabel = [
                                'va_bca' => 'VA BCA', 'va_mandiri' => 'VA Mandiri',
                                'qris' => 'QRIS', 'gopay' => 'Gopay', 'ovo' => 'OVO', 'manual' => 'Manual Kasir'
                            ][$item->metode] ?? $item->metode;
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $metodeLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $badgeClass = match($item->status) {
                                'paid' => 'bg-green-100 text-green-800 border-green-200',
                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'failed' => 'bg-red-100 text-red-800 border-red-200',
                                'expired' => 'bg-gray-100 text-gray-600 border-gray-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badgeClass }}">
                            {{ strtoupper($item->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.pembayaran.show', $item) }}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition-colors" title="Lihat Detail">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                            @if($item->status === 'pending' && $item->tripay_ref)
                            <form method="POST" action="{{ route('admin.pembayaran.check-status', $item) }}" class="inline-block">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-colors" title="Cek status pembayaran">
                                    <i class="fa-solid fa-rotate-right text-sm"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.pembayaran.cancel', $item) }}" class="inline-block" onsubmit="return confirm('Batalkan transaksi ini? Tagihan terkait akan bisa dipilih kembali.')">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Batalkan Transaksi">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-receipt text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada riwayat pembayaran.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pembayaran->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $pembayaran->links() }}
    </div>
    @endif
</div>
@endsection
