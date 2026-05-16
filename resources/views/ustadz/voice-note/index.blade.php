@extends('layouts.app')
@section('title', 'Voice Note ke Wali')
@section('page-title', 'Voice Note ke Wali Santri')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h3 class="text-xl font-bold text-gray-800">Voice Note yang Anda Kirim</h3>
        <p class="text-gray-500 text-sm">Pesan suara ke wali santri — <span class="font-semibold text-emerald-600">gratis</span>.</p>
    </div>
    <a href="{{ route('ustadz.voice-note.create') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-sm">
        <i class="fa-solid fa-plus mr-1"></i> Kirim Voice Note
    </a>
</div>

<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                <th class="px-5 py-3 font-medium">Tanggal</th>
                <th class="px-5 py-3 font-medium">Santri</th>
                <th class="px-5 py-3 font-medium">Wali Penerima</th>
                <th class="px-5 py-3 font-medium">Konteks</th>
                <th class="px-5 py-3 font-medium">Durasi</th>
                <th class="px-5 py-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 text-sm">
            @forelse($sent as $vn)
                <tr>
                    <td class="px-5 py-3 text-gray-500">{{ $vn->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3 font-semibold">{{ $vn->penerimaSantri?->nama ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $vn->penerimaWali?->nama ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600">{{ $vn->konteks ?? 'umum' }}</span>
                    </td>
                    <td class="px-5 py-3">{{ $vn->durasi_detik }}s</td>
                    <td class="px-5 py-3">
                        @if($vn->is_read)
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700">Dibaca</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-600">Belum Dibaca</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Belum ada voice note yang dikirim.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $sent->links() }}</div>
@endsection
