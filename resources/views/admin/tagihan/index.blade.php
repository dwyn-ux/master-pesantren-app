@extends('layouts.app')
@section('title', 'Tagihan')
@section('page-title', 'Data Tagihan')

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
        <h6 class="text-lg font-bold text-gray-800">Daftar Tagihan Santri</h6>
        <a href="{{ route('admin.tagihan.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-file-invoice-dollar"></i> Buat Tagihan
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Santri</th>
                    <th class="px-6 py-4 font-medium">Jenis Tagihan</th>
                    <th class="px-6 py-4 font-medium">Nominal</th>
                    <th class="px-6 py-4 font-medium">Periode</th>
                    <th class="px-6 py-4 font-medium">Jatuh Tempo</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($tagihan as $item)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <span class="block font-semibold text-gray-800">{{ $item->santri->nama }}</span>
                        <span class="block text-xs font-mono text-gray-500 mt-0.5">{{ $item->santri->nis }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="block text-gray-700">{{ $item->jenisTagihan->nama }}</span>
                        @if($item->jenisTagihan?->kelompok)
                        @php
                            $kColor = match($item->jenisTagihan->kelompok) {
                                'bulanan'    => 'bg-blue-100 text-blue-700',
                                'semesteran' => 'bg-purple-100 text-purple-700',
                                'tahunan'    => 'bg-amber-100 text-amber-700',
                                'kegiatan'   => 'bg-green-100 text-green-700',
                                default      => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $kColor }}">
                            {{ ucfirst($item->jenisTagihan->kelompok) }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-800">Rp {{ number_format($item->nominal) }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->periode }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->due_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @if($item->status === 'lunas')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                <i class="fa-solid fa-check mr-1.5"></i> Lunas
                            </span>
                        @elseif($item->status === 'sebagian')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                <i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Sebagian
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                <i class="fa-solid fa-clock mr-1.5"></i> Belum Bayar
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($item->status !== 'lunas')
                                <a href="{{ route('admin.pembayaran.create', ['tagihan_id' => $item->id]) }}" class="px-3 py-1.5 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 flex items-center gap-1.5 transition-colors text-sm font-medium" title="Proses pembayaran kasir">
                                    <i class="fa-solid fa-money-bill-wave"></i> Bayar
                                </a>
                            @endif
                            <a href="{{ route('admin.tagihan.edit', $item) }}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition-colors">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.tagihan.destroy', $item) }}" class="inline-block" onsubmit="return confirm('Hapus tagihan santri ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Hapus">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-file-invoice text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada tagihan dibuat.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(method_exists($tagihan, 'hasPages') && $tagihan->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $tagihan->links() }}
    </div>
    @endif
</div>
@endsection
