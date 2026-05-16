@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi ' . $transaksi->nomor)

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="glass-panel rounded-2xl p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Nomor</p>
                    <h2 class="text-2xl font-bold font-mono">{{ $transaksi->nomor }}</h2>
                </div>
                <div class="text-right">
                    @if($transaksi->status == 'posted')
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">POSTED</span>
                    @elseif($transaksi->status == 'void')
                        <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-xs font-bold">VOID</span>
                    @else
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">DRAFT</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Tanggal</p>
                    <p class="font-bold">{{ $transaksi->tanggal->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Tipe</p>
                    <p class="font-bold capitalize">{{ $transaksi->tipe }}</p>
                </div>
                @if($transaksi->kategori)
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Kategori</p>
                    <p class="font-bold">{{ $transaksi->kategori->nama }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Kas/Bank {{ $transaksi->tipe == 'transfer' ? 'Sumber' : '' }}</p>
                    <p class="font-bold">{{ $transaksi->kasBank->nama }}</p>
                </div>
                @if($transaksi->kasBankTujuan)
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Kas/Bank Tujuan</p>
                    <p class="font-bold">{{ $transaksi->kasBankTujuan->nama }}</p>
                </div>
                @endif
                @if($transaksi->pihak)
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Pihak</p>
                    <p class="font-bold">{{ $transaksi->pihak }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Dicatat Oleh</p>
                    <p class="font-bold">{{ $transaksi->creator?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Dibuat</p>
                    <p class="font-bold">{{ $transaksi->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            @if($transaksi->keterangan)
            <div class="mb-6">
                <p class="text-xs text-gray-500 font-semibold mb-1">Keterangan</p>
                <p class="text-sm bg-gray-50 p-3 rounded-lg">{{ $transaksi->keterangan }}</p>
            </div>
            @endif

            @if($transaksi->bukti)
            <div class="mb-6">
                <p class="text-xs text-gray-500 font-semibold mb-2">Bukti</p>
                <a href="{{ asset('storage/' . $transaksi->bukti) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-semibold">
                    <i class="fa-solid fa-paperclip"></i> Lihat Bukti
                </a>
            </div>
            @endif
        </div>

        @if($transaksi->journal)
        <div class="glass-panel rounded-2xl p-6">
            <h6 class="text-lg font-bold mb-4">Jurnal Otomatis ({{ $transaksi->journal->nomor }})</h6>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="text-left px-3 py-2">Akun</th>
                        <th class="text-right px-3 py-2">Debit</th>
                        <th class="text-right px-3 py-2">Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($transaksi->journal->lines as $line)
                    <tr>
                        <td class="px-3 py-2">
                            <div class="font-mono text-xs text-gray-500">{{ $line->account->kode }}</div>
                            <div class="font-semibold">{{ $line->account->nama }}</div>
                            @if($line->keterangan)<div class="text-xs text-gray-500">{{ $line->keterangan }}</div>@endif
                        </td>
                        <td class="px-3 py-2 text-right font-bold">{{ $line->debit > 0 ? 'Rp ' . number_format($line->debit) : '-' }}</td>
                        <td class="px-3 py-2 text-right font-bold">{{ $line->kredit > 0 ? 'Rp ' . number_format($line->kredit) : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td class="px-3 py-2">TOTAL</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($transaksi->journal->total_debit) }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($transaksi->journal->total_kredit) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    <div>
        <div class="glass-panel rounded-2xl p-6 mb-6 text-center">
            <p class="text-xs text-gray-500 uppercase font-bold">Nominal</p>
            <h2 class="text-3xl font-extrabold {{ $transaksi->tipe == 'masuk' ? 'text-green-600' : ($transaksi->tipe == 'keluar' ? 'text-red-600' : 'text-blue-600') }}">
                Rp {{ number_format($transaksi->nominal) }}
            </h2>
        </div>

        @if($transaksi->status == 'posted')
        <div class="glass-panel rounded-2xl p-6">
            <h6 class="text-sm font-bold mb-3 text-red-600">Aksi Bahaya</h6>
            <form method="POST" action="{{ route('finance.transaksi.void', $transaksi) }}" onsubmit="return confirm('Yakin void transaksi ini? Akan dibuat jurnal pembalik.')">
                @csrf
                <input type="text" name="alasan" required placeholder="Alasan void (wajib)" class="w-full rounded-lg border-gray-300 text-sm mb-2">
                <button class="w-full px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700">
                    <i class="fa-solid fa-ban mr-1"></i> Void Transaksi
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('finance.transaksi.index') }}" class="text-indigo-600 text-sm font-semibold"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke daftar</a>
</div>
@endsection
