@extends('layouts.app')
@section('title', 'Detail Jurnal')
@section('page-title', 'Jurnal ' . $journal->nomor)

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 glass-panel rounded-2xl p-6">
        <div class="flex justify-between items-start mb-6 pb-4 border-b">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Nomor Jurnal</p>
                <h2 class="text-2xl font-bold font-mono">{{ $journal->nomor }}</h2>
            </div>
            <div class="text-right">
                @if($journal->status == 'posted')
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">POSTED</span>
                @elseif($journal->status == 'void')
                    <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-600 text-xs font-bold">VOID</span>
                @else
                    <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">DRAFT</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-6">
            <div><p class="text-xs text-gray-500 font-bold">Tanggal</p><p class="font-bold">{{ $journal->tanggal->format('d F Y') }}</p></div>
            <div><p class="text-xs text-gray-500 font-bold">Tipe</p><p class="font-bold capitalize">{{ str_replace('_',' ', $journal->tipe) }}</p></div>
            <div><p class="text-xs text-gray-500 font-bold">Referensi</p><p class="font-mono text-xs">{{ $journal->referensi ?: '-' }}</p></div>
            <div><p class="text-xs text-gray-500 font-bold">Diposting</p><p class="text-xs">{{ $journal->posted_at?->format('d/m/Y H:i') ?: '-' }}</p></div>
        </div>

        <p class="text-sm bg-gray-50 p-3 rounded-lg mb-4">{{ $journal->keterangan }}</p>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase">
                <tr>
                    <th class="px-3 py-2 text-left">Akun</th>
                    <th class="px-3 py-2 text-right">Debit</th>
                    <th class="px-3 py-2 text-right">Kredit</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($journal->lines as $l)
                <tr>
                    <td class="px-3 py-2">
                        <div class="font-mono text-xs text-gray-500">{{ $l->account->kode }}</div>
                        <div class="font-semibold">{{ $l->account->nama }}</div>
                        @if($l->keterangan)<div class="text-xs text-gray-500">{{ $l->keterangan }}</div>@endif
                    </td>
                    <td class="px-3 py-2 text-right font-bold">{{ $l->debit > 0 ? 'Rp ' . number_format($l->debit) : '-' }}</td>
                    <td class="px-3 py-2 text-right font-bold">{{ $l->kredit > 0 ? 'Rp ' . number_format($l->kredit) : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-indigo-50 font-bold">
                <tr>
                    <td class="px-3 py-2">TOTAL</td>
                    <td class="px-3 py-2 text-right">Rp {{ number_format($journal->total_debit) }}</td>
                    <td class="px-3 py-2 text-right">Rp {{ number_format($journal->total_kredit) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($journal->status == 'posted')
    <div class="space-y-4">
        <form method="POST" action="{{ route('finance.journal.reverse', $journal) }}" class="glass-panel rounded-2xl p-5 border-l-4 border-amber-500" onsubmit="return confirm('Buat jurnal pembalik?')">
            @csrf
            <h6 class="font-bold mb-3 text-amber-700"><i class="fa-solid fa-arrow-rotate-left mr-1"></i> Buat Jurnal Pembalik</h6>
            <p class="text-xs text-gray-600 mb-3">Membalik posisi debit/kredit ke jurnal baru. Saldo akun akan kembali seperti semula.</p>
            <div class="space-y-2">
                <input type="date" name="tanggal" value="{{ now()->toDateString() }}" class="w-full rounded-lg border-gray-300 text-sm">
                <input type="text" name="alasan" required placeholder="Alasan reverse (wajib)" class="w-full rounded-lg border-gray-300 text-sm">
                <button class="w-full px-4 py-2 bg-amber-500 text-white rounded-lg font-bold text-sm">Buat Pembalik</button>
            </div>
        </form>

        <form method="POST" action="{{ route('finance.journal.void', $journal) }}" class="glass-panel rounded-2xl p-5 border-l-4 border-red-500" onsubmit="return confirm('VOID jurnal? Status akan jadi VOID & saldo TIDAK akan diubah otomatis. Gunakan Reverse jika ingin balik saldo.')">
            @csrf
            <h6 class="font-bold mb-3 text-red-700"><i class="fa-solid fa-ban mr-1"></i> Void Jurnal</h6>
            <p class="text-xs text-gray-600 mb-3">Tandai jurnal sebagai void. Tidak menghapus data tapi tidak ikut dihitung di laporan.</p>
            <div class="space-y-2">
                <input type="text" name="alasan" required placeholder="Alasan void (wajib)" class="w-full rounded-lg border-gray-300 text-sm">
                <button class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-bold text-sm">Void</button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
