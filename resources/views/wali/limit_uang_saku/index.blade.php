@extends('layouts.app')
@section('title', 'Limit Uang Saku')
@section('page-title', 'Limit Uang Saku')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="space-y-6">
    <div class="glass-panel rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500">
        <h6 class="text-base font-bold text-gray-800 mb-1 flex items-center gap-2">
            <i class="fa-solid fa-wallet text-indigo-500"></i> Pengaturan Limit Uang Saku
        </h6>
        <p class="text-sm text-gray-500">Batasi pengeluaran harian atau mingguan anak Anda di kantin dan laundry pondok.</p>
    </div>

    @foreach($santri as $s)
    @php
        $terpakai_hari = $s->walletTransactions()
            ->where('jenis', 'debit')
            ->whereDate('created_at', today())
            ->sum('nominal');
        $terpakai_minggu = $s->walletTransactions()
            ->where('jenis', 'debit')
            ->where('created_at', '>=', now()->startOfWeek())
            ->sum('nominal');
    @endphp
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
            <div>
                <h6 class="font-bold text-gray-800">{{ $s->nama }}</h6>
                <span class="text-xs text-gray-500">NIS: {{ $s->nis }} &bull; Saldo: <span class="font-semibold text-indigo-600">Rp {{ number_format($s->saldo) }}</span></span>
            </div>
            @if($s->tipe_limit !== 'tidak_ada' && $s->nominal_limit)
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                Limit {{ ucfirst($s->tipe_limit) }}: Rp {{ number_format($s->nominal_limit) }}
            </span>
            @else
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Tidak Ada Limit</span>
            @endif
        </div>

        <div class="p-6">
            @if($s->tipe_limit !== 'tidak_ada' && $s->nominal_limit)
            <div class="mb-5 grid grid-cols-2 gap-3">
                <div class="p-3 bg-blue-50 rounded-xl text-center">
                    <p class="text-xs text-blue-600 font-medium">Terpakai Hari Ini</p>
                    <p class="text-lg font-bold text-blue-800 mt-0.5">Rp {{ number_format($terpakai_hari) }}</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-xl text-center">
                    <p class="text-xs text-purple-600 font-medium">Terpakai Minggu Ini</p>
                    <p class="text-lg font-bold text-purple-800 mt-0.5">Rp {{ number_format($terpakai_minggu) }}</p>
                </div>
            </div>
            @endif

            <form action="{{ route('wali.limit-uang-saku.update', $s) }}" method="POST" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Limit</label>
                    <div class="grid grid-cols-3 gap-3" x-data="{ tipe: '{{ old('tipe_limit_'.$s->id, $s->tipe_limit) }}' }">
                        @foreach(['tidak_ada' => 'Tidak Ada', 'harian' => 'Harian', 'mingguan' => 'Mingguan'] as $val => $label)
                        <label class="relative flex items-center justify-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-all
                            {{ old('tipe_limit_'.$s->id, $s->tipe_limit) === $val ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}">
                            <input type="radio" name="tipe_limit" value="{{ $val }}"
                                   @checked(old('tipe_limit_'.$s->id, $s->tipe_limit) === $val)
                                   onchange="this.closest('.grid').querySelectorAll('label').forEach(l=>l.classList.remove('border-indigo-500','bg-indigo-50')); this.closest('label').classList.add('border-indigo-500','bg-indigo-50'); document.getElementById('nominal-{{ $s->id }}').style.display = this.value === 'tidak_ada' ? 'none' : 'block';"
                                   class="sr-only">
                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div id="nominal-{{ $s->id }}" style="{{ old('tipe_limit_'.$s->id, $s->tipe_limit) === 'tidak_ada' ? 'display:none' : '' }}">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal Limit (Rp)</label>
                    <input type="number" name="nominal_limit" min="1000" step="1000"
                           value="{{ old('nominal_limit_'.$s->id, $s->nominal_limit) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"
                           placeholder="Cth: 50000">
                    @error('nominal_limit')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
