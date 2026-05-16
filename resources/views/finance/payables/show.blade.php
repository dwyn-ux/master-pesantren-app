@extends('layouts.app')
@section('title', 'Detail Hutang')
@section('page-title', 'Hutang ' . $payable->nomor)

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="glass-panel rounded-2xl p-6 mb-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase">Vendor</p>
                    <p class="font-bold text-lg">{{ $payable->vendor->nama }}</p>
                    <p class="text-xs text-gray-500">{{ $payable->vendor->telepon }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 font-bold uppercase">Status</p>
                    @switch($payable->status)
                        @case('belum_bayar')<span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">BELUM BAYAR</span>@break
                        @case('sebagian')<span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">SEBAGIAN</span>@break
                        @case('lunas')<span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">LUNAS</span>@break
                        @case('void')<span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-bold">VOID</span>@break
                    @endswitch
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase">Tanggal</p>
                    <p class="font-semibold">{{ $payable->tanggal->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase">Jatuh Tempo</p>
                    <p class="font-semibold {{ $payable->status != 'lunas' && $payable->jatuh_tempo->isPast() ? 'text-red-600' : '' }}">
                        {{ $payable->jatuh_tempo->format('d F Y') }}
                        @if($payable->status != 'lunas' && $payable->jatuh_tempo->isPast())
                            <span class="ml-1 text-xs">({{ $payable->jatuh_tempo->diffForHumans() }})</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($payable->keterangan)
            <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm">{{ $payable->keterangan }}</div>
            @endif
        </div>

        <div class="glass-panel rounded-2xl p-6">
            <h6 class="text-lg font-bold mb-4">Riwayat Pembayaran</h6>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Tanggal</th>
                        <th class="px-3 py-2 text-left">Kas/Bank</th>
                        <th class="px-3 py-2 text-right">Nominal</th>
                        <th class="px-3 py-2 text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($payable->payments as $pay)
                    <tr>
                        <td class="px-3 py-2">{{ $pay->tanggal->format('d/m/Y') }}</td>
                        <td class="px-3 py-2">{{ $pay->kasBank->nama }}</td>
                        <td class="px-3 py-2 text-right font-bold">Rp {{ number_format($pay->nominal) }}</td>
                        <td class="px-3 py-2 text-xs text-gray-500">{{ $pay->keterangan }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400">Belum ada pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="glass-panel rounded-2xl p-6 mb-4 text-center">
            <p class="text-xs text-gray-500 uppercase font-bold">Sisa Hutang</p>
            <p class="text-3xl font-extrabold text-amber-600">Rp {{ number_format($payable->sisa) }}</p>
            <div class="grid grid-cols-2 gap-2 mt-4 text-xs">
                <div><span class="text-gray-500">Total:</span> <strong>Rp {{ number_format($payable->nominal) }}</strong></div>
                <div><span class="text-gray-500">Bayar:</span> <strong class="text-green-600">Rp {{ number_format($payable->terbayar) }}</strong></div>
            </div>
        </div>

        @if(in_array($payable->status, ['belum_bayar', 'sebagian']))
        <form method="POST" action="{{ route('finance.payables.pay', $payable) }}" class="glass-panel rounded-2xl p-6">
            @csrf
            <h6 class="font-bold mb-3">Bayar Hutang</h6>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold mb-1">Tanggal *</label>
                    <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Nominal *</label>
                    <input type="number" step="0.01" min="0.01" max="{{ $payable->sisa }}" name="nominal" value="{{ $payable->sisa }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Bayar dari Kas/Bank *</label>
                    <select name="kas_bank_id" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">— Pilih —</option>
                        @foreach($kasBanks as $kb)
                            <option value="{{ $kb->id }}">{{ $kb->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Keterangan</label>
                    <input type="text" name="keterangan" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg font-bold">
                    <i class="fa-solid fa-money-bill-transfer mr-1"></i> Bayar
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
