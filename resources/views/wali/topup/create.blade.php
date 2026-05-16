@extends('layouts.app')
@section('title', 'Top Up Saldo')
@section('page-title', 'Top Up Saldo Santri')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="max-w-xl mx-auto" x-data="topupForm()">

    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
            <h6 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-money-bill-transfer mr-2 text-indigo-500"></i> Top Up Saldo</h6>
            <p class="text-sm text-gray-500 mt-0.5">Saldo otomatis masuk setelah pembayaran dikonfirmasi.</p>
        </div>

        <div class="p-6 space-y-5">

            {{-- Pilih Santri --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Santri <span class="text-red-500">*</span></label>
                @if($santriList->isEmpty())
                    <p class="text-sm text-red-500">Belum ada santri terdaftar pada akun ini.</p>
                @elseif($santriList->count() === 1)
                    <input type="hidden" x-model="santriId" value="{{ $santriList->first()->id }}">
                    <div class="flex items-center gap-3 px-4 py-3 bg-indigo-50 rounded-xl border border-indigo-100">
                        <div class="w-10 h-10 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-base shrink-0">
                            {{ substr($santriList->first()->nama, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800 text-sm">{{ $santriList->first()->nama }}</div>
                            <div class="text-xs text-gray-400 font-mono">NIS: {{ $santriList->first()->nis }}</div>
                            <div class="text-xs text-emerald-700 font-semibold mt-0.5">Saldo: Rp {{ number_format($santriList->first()->saldo) }}</div>
                        </div>
                    </div>
                @else
                    <select x-model="santriId" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white text-sm">
                        <option value="">-- Pilih Santri --</option>
                        @foreach($santriList as $santri)
                        <option value="{{ $santri->id }}">{{ $santri->nama }} (NIS: {{ $santri->nis }}) — Saldo: Rp {{ number_format($santri->saldo) }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            {{-- Nominal --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nominal Top Up <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium text-sm">Rp</span>
                    <input type="number" x-model="nominal" min="10000" max="10000000" step="1000"
                           placeholder="100000" required
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm">
                </div>
                <p class="text-xs text-gray-400 mt-1">Minimum Rp 10.000 — maksimum Rp 10.000.000</p>
                <div class="flex flex-wrap gap-2 mt-2.5">
                    @foreach([50000, 100000, 200000, 500000] as $amount)
                    <button type="button" @click="nominal = {{ $amount }}"
                            class="px-3 py-1.5 text-xs font-semibold bg-gray-100 hover:bg-indigo-100 hover:text-indigo-700 text-gray-600 rounded-lg transition-colors">
                        Rp {{ number_format($amount, 0, ',', '.') }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Error --}}
            <div x-show="errorMsg" x-transition
                 class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                <span x-text="errorMsg"></span>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="button" @click="submit()" :disabled="loading || !santriId || !nominal"
                        :class="loading || !santriId || !nominal ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                        class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm flex items-center justify-center gap-2">
                    <span x-show="!loading"><i class="fa-solid fa-credit-card"></i> Bayar Sekarang</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
                <a href="{{ route('wali.topup.index') }}" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium text-sm transition-colors">
                    Riwayat
                </a>
            </div>
        </div>
    </div>

    <div class="mt-4 px-4 py-3 bg-blue-50 rounded-xl border border-blue-100 text-xs text-blue-700 flex items-start gap-2">
        <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
        <span>Anda akan diarahkan ke halaman pembayaran yang aman. Saldo santri akan otomatis diisi setelah pembayaran berhasil.</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
function topupForm() {
    return {
        santriId: '{{ $santriList->count() === 1 ? $santriList->first()->id : "" }}',
        nominal: '',
        loading: false,
        errorMsg: '',

        async submit() {
            if (!this.santriId || !this.nominal) return;

            this.errorMsg = '';
            this.loading  = true;

            try {
                const res = await fetch('{{ route("wali.topup.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ santri_id: parseInt(this.santriId), nominal: parseInt(this.nominal) }),
                });

                const data = await res.json();

                if (!res.ok) {
                    this.errorMsg = data.message || 'Terjadi kesalahan.';
                    return;
                }

                window.handlePaymentResponse(data);
            } catch (err) {
                this.errorMsg = 'Koneksi bermasalah. Periksa internet Anda.';
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
