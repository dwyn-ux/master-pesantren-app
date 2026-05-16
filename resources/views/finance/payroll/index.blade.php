@extends('layouts.app')
@section('title', 'Payroll')
@section('page-title', 'Payroll Ustadz')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="flex justify-between items-center mb-4 flex-wrap gap-3">
    <div class="flex gap-2">
        <a href="{{ route('finance.payroll.master-salary') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">
            <i class="fa-solid fa-users-gear mr-1"></i> Master Gaji Ustadz
        </a>
    </div>
    <form method="POST" action="{{ route('finance.payroll.generate') }}" class="flex items-end gap-2">
        @csrf
        <div>
            <label class="block text-xs font-semibold mb-1">Bulan</label>
            <select name="bulan" class="rounded-lg border-gray-300 text-sm" required>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1">Tahun</label>
            <input type="number" name="tahun" value="{{ now()->year }}" class="w-24 rounded-lg border-gray-300 text-sm" required>
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">
            <i class="fa-solid fa-circle-plus mr-1"></i> Generate Payroll
        </button>
    </form>
</div>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-4 py-3 text-left">Periode</th>
                <th class="px-4 py-3 text-left">Nomor</th>
                <th class="px-4 py-3 text-center">Jumlah Slip</th>
                <th class="px-4 py-3 text-right">Total Gross</th>
                <th class="px-4 py-3 text-right">Total Potongan</th>
                <th class="px-4 py-3 text-right">Total Net</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $r)
            <tr class="hover:bg-indigo-50/30">
                <td class="px-4 py-3 font-bold">{{ \Carbon\Carbon::create($r->tahun, $r->bulan)->translatedFormat('F Y') }}</td>
                <td class="px-4 py-3 font-mono text-xs">{{ $r->nomor }}</td>
                <td class="px-4 py-3 text-center">{{ $r->slips_count }}</td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($r->total_gross) }}</td>
                <td class="px-4 py-3 text-right text-red-600">- Rp {{ number_format($r->total_potongan) }}</td>
                <td class="px-4 py-3 text-right font-bold text-indigo-600">Rp {{ number_format($r->total_net) }}</td>
                <td class="px-4 py-3 text-center">
                    @if($r->status == 'draft')<span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">DRAFT</span>
                    @elseif($r->status == 'approved')<span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">APPROVED</span>
                    @elseif($r->status == 'paid')<span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">PAID</span>
                    @else<span class="px-2 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-bold line-through">VOID</span>@endif
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('finance.payroll.show', $r) }}" class="text-indigo-600"><i class="fa-solid fa-eye"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada payroll. Klik "Generate Payroll" di atas.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
