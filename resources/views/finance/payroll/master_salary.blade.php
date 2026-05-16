@extends('layouts.app')
@section('title', 'Master Gaji Ustadz')
@section('page-title', 'Master Gaji Ustadz')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="GET" class="glass-panel p-4 rounded-2xl mb-4 flex gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama ustadz..." class="flex-1 rounded-lg border-gray-300 text-sm">
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">Cari</button>
</form>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-4 py-3 text-left">Nama Ustadz</th>
                <th class="px-4 py-3 text-left">NIK</th>
                <th class="px-4 py-3 text-right">Gaji Pokok</th>
                <th class="px-4 py-3 text-right">Total Tunjangan</th>
                <th class="px-4 py-3 text-right">Take Home</th>
                <th class="px-4 py-3 text-left">Berlaku Sejak</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $u)
            @php $s = $u->latestSalary; @endphp
            <tr class="hover:bg-indigo-50/30">
                <td class="px-4 py-3 font-semibold">{{ $u->nama }}</td>
                <td class="px-4 py-3 font-mono text-xs">{{ $u->nik ?: '-' }}</td>
                <td class="px-4 py-3 text-right">Rp {{ $s ? number_format($s->gaji_pokok) : '0' }}</td>
                <td class="px-4 py-3 text-right">Rp {{ $s ? number_format($s->tunjangan_jabatan + $s->tunjangan_transport + $s->tunjangan_makan + $s->tunjangan_lain) : '0' }}</td>
                <td class="px-4 py-3 text-right font-bold text-indigo-600">Rp {{ $s ? number_format($s->totalGross() - $s->potongan_tetap) : '0' }}</td>
                <td class="px-4 py-3 text-xs">{{ $s?->berlaku_sejak?->format('d/m/Y') ?: '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('finance.payroll.salary.edit', $u) }}" class="text-indigo-600 font-semibold text-xs">
                        {{ $s ? 'Edit' : 'Set Gaji' }} <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada ustadz.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
