@extends('layouts.app')
@section('title', 'Kesehatan Santri')
@section('page-title', 'Catatan Kesehatan Santri')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="rounded-2xl shadow-md p-6 mb-6 text-white" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%)">
    <p class="text-red-200 text-sm font-medium">Rekam Medis</p>
    <h3 class="text-2xl font-extrabold">Catatan Kesehatan Santri</h3>
    <p class="text-red-100 text-sm mt-1">Semua kunjungan klinik santri Anda.</p>
</div>

<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-50 text-gray-500 text-sm">
                <th class="px-5 py-3 font-medium">Tanggal</th>
                <th class="px-5 py-3 font-medium">Santri</th>
                <th class="px-5 py-3 font-medium">Keluhan</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Pemeriksa</th>
                <th class="px-5 py-3 font-medium"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 text-sm">
            @forelse($kunjungan as $k)
                <tr>
                    <td class="px-5 py-3 text-gray-500">{{ $k->tanggal_kunjungan?->format('d M Y H:i') }}</td>
                    <td class="px-5 py-3 font-semibold">{{ $k->patient?->nama }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ \Illuminate\Support\Str::limit($k->keluhan, 60) }}</td>
                    <td class="px-5 py-3">
                        @if($k->perlu_rujuk)
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700">Perlu Rujuk</span>
                        @elseif($k->perlu_dirawat_ortu)
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700">Rawat di Rumah</span>
                        @elseif($k->status_pengobatan === 'istirahat')
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-700">Istirahat</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700">Aktif Normal</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $k->pemeriksa?->nama ?? '-' }}</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('wali.kesehatan.show', $k) }}" class="text-indigo-600 hover:underline text-sm">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Belum ada catatan kunjungan klinik.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $kunjungan->links() }}</div>
@endsection
