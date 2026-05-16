@extends('layouts.app')
@section('title', 'Riwayat Top Up')
@section('page-title', 'Top Up Saldo Santri')

@section('sidebar')
    @include('partials.sidebar-bendahara')
@endsection

@section('content')

<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h6 class="text-lg font-bold text-gray-800">Riwayat Top Up Saldo</h6>
            @if($pendingCount > 0)
            <p class="text-sm text-amber-600 font-semibold mt-0.5">
                <i class="fa-solid fa-clock mr-1"></i> {{ $pendingCount }} pembayaran sedang menunggu konfirmasi Tripay
            </p>
            @else
            <p class="text-sm text-gray-400 mt-0.5">Semua top-up sudah diproses</p>
            @endif
        </div>
    </div>

    {{-- Filter --}}
    <div class="p-5 border-b border-gray-100 bg-gray-50/30">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama santri, wali, atau NIS..."
                       class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">
            </div>
            <select name="status" class="px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white text-sm">
                <option value="">Semua Status</option>
                <option value="unpaid"  @selected(request('status') === 'unpaid')>Menunggu Bayar</option>
                <option value="paid"    @selected(request('status') === 'paid')>Lunas</option>
                <option value="expired" @selected(request('status') === 'expired')>Kadaluarsa</option>
                <option value="failed"  @selected(request('status') === 'failed')>Gagal</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">Filter</button>
            <a href="{{ route('bendahara.topup.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">Reset</a>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-5 py-3 font-medium">Tanggal</th>
                    <th class="px-5 py-3 font-medium">Santri</th>
                    <th class="px-5 py-3 font-medium">Wali</th>
                    <th class="px-5 py-3 font-medium">Metode</th>
                    <th class="px-5 py-3 font-medium text-right">Nominal</th>
                    <th class="px-5 py-3 font-medium text-center">Status</th>
                    <th class="px-5 py-3 font-medium text-center">Dibayar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($requests as $r)
                @php
                    $badge = match($r->status) {
                        'unpaid'  => 'bg-yellow-100 text-yellow-700',
                        'paid'    => 'bg-green-100 text-green-700',
                        'expired' => 'bg-gray-100 text-gray-500',
                        'failed'  => 'bg-red-100 text-red-600',
                        default   => 'bg-gray-100 text-gray-600',
                    };
                    $label = [
                        'unpaid'  => 'Menunggu Bayar',
                        'paid'    => 'Lunas',
                        'expired' => 'Kadaluarsa',
                        'failed'  => 'Gagal',
                    ];
                @endphp
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3">
                        <div class="font-semibold text-gray-800 text-sm">{{ $r->santri->nama }}</div>
                        <div class="text-xs text-gray-400 font-mono">{{ $r->santri->nis }}</div>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-600">{{ $r->wali->nama }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500 uppercase font-mono">{{ $r->tripay_channel ?? '-' }}</td>
                    <td class="px-5 py-3 text-sm font-bold text-emerald-700 text-right whitespace-nowrap">Rp {{ number_format($r->nominal) }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $badge }}">
                            {{ $label[$r->status] ?? $r->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center text-xs text-gray-500">
                        {{ $r->paid_at?->format('d/m/Y H:i') ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">Tidak ada data top-up.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
