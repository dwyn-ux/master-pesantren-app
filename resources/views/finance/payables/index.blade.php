@extends('layouts.app')
@section('title', 'Hutang Vendor')
@section('page-title', 'Hutang Vendor (Account Payable)')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-amber-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Total Hutang</p>
        <p class="text-xl font-bold text-amber-600">Rp {{ number_format($summary['total_hutang']) }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-orange-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Jatuh Tempo &le; 7 hari</p>
        <p class="text-xl font-bold text-orange-600">Rp {{ number_format($summary['jatuh_tempo_7']) }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-red-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Overdue</p>
        <p class="text-xl font-bold text-red-600">Rp {{ number_format($summary['overdue']) }}</p>
    </div>
</div>

<div class="flex justify-end mb-4">
    <a href="{{ route('finance.payables.create') }}" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-bold shadow">
        <i class="fa-solid fa-plus mr-1"></i> Hutang Baru
    </a>
</div>

<div class="flex flex-wrap gap-2 mb-4">
    <form method="GET" class="flex gap-2 flex-wrap">
        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua Status</option>
            <option value="belum_bayar" @selected(request('status')=='belum_bayar')>Belum Bayar</option>
            <option value="sebagian" @selected(request('status')=='sebagian')>Sebagian</option>
            <option value="lunas" @selected(request('status')=='lunas')>Lunas</option>
        </select>
        <select name="vendor_id" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua Vendor</option>
            @foreach($vendors as $v)
                <option value="{{ $v->id }}" @selected(request('vendor_id')==$v->id)>{{ $v->nama }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Filter</button>
    </form>
</div>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2 text-left">Nomor</th>
                <th class="px-3 py-2 text-left">Vendor</th>
                <th class="px-3 py-2 text-left">Jatuh Tempo</th>
                <th class="px-3 py-2 text-right">Nominal</th>
                <th class="px-3 py-2 text-right">Terbayar</th>
                <th class="px-3 py-2 text-right">Sisa</th>
                <th class="px-3 py-2 text-center">Status</th>
                <th class="px-3 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $p)
            @php $overdue = $p->status != 'lunas' && $p->jatuh_tempo->isPast(); @endphp
            <tr class="hover:bg-indigo-50/30 {{ $overdue ? 'bg-red-50/40' : '' }}">
                <td class="px-3 py-2 text-xs">{{ $p->tanggal->format('d/m/Y') }}</td>
                <td class="px-3 py-2 font-mono text-xs">{{ $p->nomor }}</td>
                <td class="px-3 py-2 font-semibold">{{ $p->vendor->nama }}</td>
                <td class="px-3 py-2 text-xs {{ $overdue ? 'text-red-600 font-bold' : '' }}">{{ $p->jatuh_tempo->format('d/m/Y') }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($p->nominal) }}</td>
                <td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($p->terbayar) }}</td>
                <td class="px-3 py-2 text-right font-bold text-amber-600">Rp {{ number_format($p->sisa) }}</td>
                <td class="px-3 py-2 text-center">
                    @switch($p->status)
                        @case('belum_bayar')<span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-xs">Belum</span>@break
                        @case('sebagian')<span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs">Sebagian</span>@break
                        @case('lunas')<span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">Lunas</span>@break
                        @case('void')<span class="px-2 py-1 rounded-full bg-gray-100 text-gray-500 text-xs">Void</span>@break
                    @endswitch
                </td>
                <td class="px-3 py-2 text-right">
                    <a href="{{ route('finance.payables.show', $p) }}" class="text-indigo-600"><i class="fa-solid fa-eye"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="px-3 py-8 text-center text-gray-400">Belum ada hutang.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
