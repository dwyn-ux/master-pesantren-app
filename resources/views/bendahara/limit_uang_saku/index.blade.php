@extends('layouts.app')
@section('title', 'Limit Uang Saku Santri')
@section('page-title', 'Limit Uang Saku')

@section('sidebar')
    @include('partials.sidebar-bendahara')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            <h6 class="text-lg font-bold text-gray-800">Kelola Limit Uang Saku Santri</h6>
            <p class="text-sm text-gray-400 mt-0.5">Atur batas pengeluaran harian atau mingguan per santri.</p>
        </div>
    </div>

    {{-- Search --}}
    <div class="p-6 border-b border-gray-100">
        <form method="GET" class="flex gap-3">
            <div class="flex-1 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"
                       placeholder="Cari nama santri atau NIS...">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm">Cari</button>
            <a href="{{ route('bendahara.limit-uang-saku.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">Reset</a>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Santri</th>
                    <th class="px-6 py-4 font-medium">Saldo</th>
                    <th class="px-6 py-4 font-medium">Tipe Limit</th>
                    <th class="px-6 py-4 font-medium">Nominal Limit</th>
                    <th class="px-6 py-4 font-medium text-right">Atur</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($santri as $s)
                <tr class="hover:bg-indigo-50/20 transition-colors" x-data="{ open: false }">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $s->nama }}</div>
                        <div class="text-xs text-gray-400">NIS: {{ $s->nis }}</div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-indigo-700">Rp {{ number_format($s->saldo) }}</td>
                    <td class="px-6 py-4">
                        @if($s->tipe_limit === 'tidak_ada')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Tidak Ada</span>
                        @elseif($s->tipe_limit === 'harian')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">Harian</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">Mingguan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                        {{ $s->nominal_limit ? 'Rp ' . number_format($s->nominal_limit) : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button @click="open = !open" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-sm font-medium transition-colors">
                            <i class="fa-solid fa-pen-to-square"></i> Atur
                        </button>
                    </td>
                </tr>
                <tr x-show="open" style="display:none" x-data="{ tipe: '{{ $s->tipe_limit }}' }" class="bg-indigo-50/30">
                    <td colspan="5" class="px-6 py-4">
                        <form action="{{ route('bendahara.limit-uang-saku.update', $s) }}" method="POST" class="flex flex-wrap items-end gap-4">
                            @csrf @method('PATCH')
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Tipe Limit</label>
                                <select name="tipe_limit" x-model="tipe"
                                        class="px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white text-sm">
                                    <option value="tidak_ada">Tidak Ada</option>
                                    <option value="harian">Harian</option>
                                    <option value="mingguan">Mingguan</option>
                                </select>
                            </div>
                            <div x-show="tipe !== 'tidak_ada'">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nominal (Rp)</label>
                                <input type="number" name="nominal_limit" min="1000" step="1000"
                                       value="{{ $s->nominal_limit }}"
                                       class="px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm w-40"
                                       placeholder="Cth: 50000">
                            </div>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
                                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">Tidak ada santri ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($santri->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $santri->links() }}</div>
    @endif
</div>
@endsection
