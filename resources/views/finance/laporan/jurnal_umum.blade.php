@extends('layouts.app')
@section('title', 'Jurnal Umum')
@section('page-title', 'Jurnal Umum')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="GET" class="glass-panel p-4 rounded-2xl mb-6 flex items-end gap-3">
    <div>
        <label class="block text-xs font-semibold mb-1">Dari</label>
        <input type="date" name="dari" value="{{ $dari }}" class="rounded-lg border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1">Sampai</label>
        <input type="date" name="sampai" value="{{ $sampai }}" class="rounded-lg border-gray-300 text-sm">
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">Tampilkan</button>
</form>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2 text-left">Nomor</th>
                <th class="px-3 py-2 text-left">Akun</th>
                <th class="px-3 py-2 text-right">Debit</th>
                <th class="px-3 py-2 text-right">Kredit</th>
                <th class="px-3 py-2 text-left">Keterangan</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $j)
                @foreach($j->lines as $i => $l)
                <tr class="{{ $i == 0 ? 'border-t-2 border-gray-200' : '' }} {{ $j->status == 'void' ? 'opacity-50 line-through' : '' }}">
                    <td class="px-3 py-2 whitespace-nowrap">{{ $i == 0 ? $j->tanggal->format('d/m/Y') : '' }}</td>
                    <td class="px-3 py-2 font-mono text-xs">{{ $i == 0 ? $j->nomor : '' }}</td>
                    <td class="px-3 py-2">
                        <span class="font-mono text-xs text-gray-500">{{ $l->account->kode }}</span>
                        <span class="ml-1">{{ $l->account->nama }}</span>
                    </td>
                    <td class="px-3 py-2 text-right">{{ $l->debit > 0 ? 'Rp ' . number_format($l->debit) : '-' }}</td>
                    <td class="px-3 py-2 text-right">{{ $l->kredit > 0 ? 'Rp ' . number_format($l->kredit) : '-' }}</td>
                    <td class="px-3 py-2 text-xs text-gray-600">{{ $i == 0 ? $j->keterangan : $l->keterangan }}</td>
                </tr>
                @endforeach
            @empty
            <tr><td colspan="6" class="px-3 py-8 text-center text-gray-400">Belum ada jurnal pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
