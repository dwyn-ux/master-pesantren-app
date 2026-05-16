@extends('layouts.app')
@section('title', 'Tagihan')
@section('page-title', 'Tagihan Santri')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="space-y-6" x-data="tagihanCheckout()">

    {{-- Header tunggakan --}}
    @if($totalTunggakan > 0)
    <div class="p-6 rounded-2xl shadow-md text-white relative overflow-hidden" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)">
        <div class="absolute -right-4 -top-4 text-white/10">
            <i class="fa-solid fa-file-invoice-dollar text-9xl"></i>
        </div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-red-100 mb-1">Total Tunggakan</p>
                <h3 class="text-3xl font-extrabold">Rp {{ number_format($totalTunggakan) }}</h3>
                <p class="text-red-200 text-sm mt-1">{{ $belumBayar->count() }} tagihan belum dibayar</p>
            </div>
            <div class="hidden sm:flex flex-col items-end gap-2">
                <i class="fa-solid fa-triangle-exclamation text-4xl text-red-200"></i>
                <span class="text-xs text-red-200">Segera selesaikan pembayaran</span>
            </div>
        </div>
    </div>
    @else
    <div class="p-6 rounded-2xl shadow-md text-white relative overflow-hidden" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%)">
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-3xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold">Semua tagihan lunas!</h3>
                <p class="text-emerald-100 text-sm">Tidak ada tunggakan untuk santri Anda.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabs --}}
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-200/50 bg-white/50 px-6 pt-4">
            <div class="flex gap-1">
                <button type="button" @click="tab = 'belum_bayar'"
                    :class="tab === 'belum_bayar' ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2.5 text-sm transition-colors relative">
                    Belum Bayar
                    @if($belumBayar->count() > 0)
                    <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-700 text-xs font-bold">{{ $belumBayar->count() }}</span>
                    @endif
                </button>
                <button type="button" @click="tab = 'riwayat'"
                    :class="tab === 'riwayat' ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2.5 text-sm transition-colors">
                    Riwayat Pembayaran
                </button>
            </div>
        </div>

        {{-- Tab: Belum Bayar --}}
        <div x-show="tab === 'belum_bayar'" x-transition>
            @if($belumBayar->isEmpty())
                <div class="py-16 text-center">
                    <i class="fa-solid fa-check-circle text-5xl text-emerald-400 mb-3"></i>
                    <p class="text-gray-600 font-medium">Tidak ada tagihan yang belum dibayar</p>
                </div>
            @else
                {{-- Pilih Semua --}}
                <div class="px-5 py-3 bg-gray-50/70 border-b border-gray-100 flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 font-medium select-none">
                        <input type="checkbox" @change="toggleAll($event)" :checked="allChecked"
                               class="w-4 h-4 accent-indigo-600 rounded">
                        Pilih semua tagihan
                    </label>
                    <span class="text-xs text-gray-400" x-text="checkedCount + ' dipilih'"></span>
                </div>

                {{-- Daftar tagihan --}}
                <div class="divide-y divide-gray-100">
                    @foreach($belumBayar as $tagihan)
                    <label class="flex items-start gap-4 p-5 {{ $tagihan->payment_status === 'pending' ? 'bg-amber-50/30' : 'hover:bg-indigo-50/30 cursor-pointer' }} transition-colors"
                           :class="isChecked({{ $tagihan->id }}) ? 'bg-indigo-50/50' : ''">
                        <input type="checkbox"
                               x-model="checked"
                               value="{{ $tagihan->id }}"
                               @if($tagihan->payment_status === 'pending') disabled @endif
                               @change="updateNominalTagihan({{ $tagihan->id }}, {{ $tagihan->nominal }}, $event.target.checked)"
                               class="mt-1 w-4 h-4 {{ $tagihan->payment_status === 'pending' ? 'accent-gray-400 opacity-50' : 'accent-indigo-600' }} rounded shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-800 truncate">{{ $tagihan->jenisTagihan->nama ?? '-' }}</span>
                                @if($tagihan->payment_status === 'pending')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 animate-pulse uppercase">
                                        <i class="fa-solid fa-spinner fa-spin mr-1"></i> Sedang Diproses
                                    </span>
                                @endif
                                @if($tagihan->jenisTagihan?->kelompok)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $tagihan->jenisTagihan->kelompok === 'bulanan' ? 'bg-blue-100 text-blue-700' :
                                       ($tagihan->jenisTagihan->kelompok === 'semesteran' ? 'bg-purple-100 text-purple-700' :
                                       ($tagihan->jenisTagihan->kelompok === 'tahunan' ? 'bg-amber-100 text-amber-700' :
                                       'bg-gray-100 text-gray-600')) }}">
                                    {{ ucfirst($tagihan->jenisTagihan->kelompok) }}
                                </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500">
                                <span class="font-medium text-gray-700">{{ $tagihan->santri->nama }}</span>
                                &bull; Periode: {{ \Carbon\Carbon::parse($tagihan->periode . '-01')->translatedFormat('F Y') }}
                            </p>
                            @if($tagihan->due_date)
                            <p class="text-xs mt-1 {{ \Carbon\Carbon::parse($tagihan->due_date)->isPast() ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                <i class="fa-solid fa-calendar-xmark mr-1"></i>
                                Jatuh tempo: {{ \Carbon\Carbon::parse($tagihan->due_date)->translatedFormat('d F Y') }}
                                @if(\Carbon\Carbon::parse($tagihan->due_date)->isPast())
                                    <span class="ml-1 text-red-500">(Terlambat)</span>
                                @endif
                            </p>
                            @endif

                            @if($tagihan->payment_status === 'pending' && $tagihan->payment_url)
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <a href="{{ $tagihan->payment_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-[10px] font-bold transition-all shadow-sm">
                                        <i class="fa-solid fa-external-link text-[8px]"></i> Lanjutkan Pembayaran
                                    </a>
                                    @php
                                        $pId = \App\Models\Pembayaran::where('status', 'pending')
                                            ->where(function($q) use ($tagihan) {
                                                $q->where('tagihan_id', $tagihan->id)
                                                  ->orWhereJsonContains('tagihan_ids', $tagihan->id);
                                            })->value('id');
                                    @endphp
                                    @if($pId)
                                    <form action="{{ route('admin.pembayaran.cancel', $pId) }}" method="POST" onsubmit="return confirm('Batalkan transaksi ini dan pilih ulang tagihan?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-[10px] font-bold transition-all">
                                            <i class="fa-solid fa-trash-can text-[8px]"></i> Batalkan & Pilih Ulang
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-base font-bold text-gray-800">Rp {{ number_format($tagihan->nominal) }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- Top Up per Santri --}}
                @if($wali->santri->isNotEmpty())
                <div class="px-5 py-4 border-t border-dashed border-indigo-200 bg-indigo-50/30">
                    <p class="text-sm font-semibold text-indigo-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-wallet"></i> Top Up Saldo (opsional)
                    </p>
                    <div class="space-y-3">
                        @foreach($wali->santri as $santri)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-xs shrink-0">
                                {{ substr($santri->nama, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-gray-700 block truncate">{{ $santri->nama }}</span>
                                <span class="text-xs text-gray-400">Saldo: Rp {{ number_format($santri->saldo) }}</span>
                            </div>
                            <div class="relative w-36 shrink-0">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">Rp</span>
                                <input type="number" min="0" step="1000" placeholder="0"
                                       @input="updateTopUp({{ $santri->id }}, $event.target.value)"
                                       class="w-full pl-8 pr-3 py-2 text-sm rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Total + Checkout --}}
                <div class="px-5 py-4 border-t border-gray-200 bg-white/80">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Total Pembayaran</p>
                            <p class="text-2xl font-extrabold text-gray-900" x-text="'Rp ' + formatNumber(totalNominal)"></p>
                            <p class="text-xs text-gray-400 mt-0.5"
                               x-text="checkedCount + ' tagihan' + (topUpTotal > 0 ? ' + top up Rp ' + formatNumber(topUpTotal) : '')"></p>
                        </div>
                        <button type="button"
                                @click="doCheckout()"
                                :disabled="checkedCount === 0 || loading"
                                :class="checkedCount === 0 || loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm flex items-center gap-2">
                            <span x-show="!loading"><i class="fa-solid fa-credit-card"></i> Checkout & Bayar</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>

                    {{-- Error message --}}
                    <div x-show="errorMsg" x-transition
                         class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                        <span x-text="errorMsg"></span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Tab: Riwayat --}}
        <div x-show="tab === 'riwayat'" x-transition style="display:none">
            @if($riwayat->isEmpty())
                <div class="py-16 text-center">
                    <i class="fa-solid fa-clock-rotate-left text-5xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada riwayat pembayaran</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($riwayat as $tagihan)
                    <div class="p-5 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-800 truncate">{{ $tagihan->jenisTagihan->nama ?? '-' }}</span>
                                    @if($tagihan->status === 'lunas')
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Lunas</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Sebagian</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500">
                                    <span class="font-medium text-gray-700">{{ $tagihan->santri->nama }}</span>
                                    &bull; Periode: {{ \Carbon\Carbon::parse($tagihan->periode . '-01')->translatedFormat('F Y') }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-base font-bold text-gray-800">Rp {{ number_format($tagihan->nominal) }}</p>
                                <a href="{{ route('wali.tagihan.show', $tagihan) }}" class="mt-1 text-xs text-indigo-600 hover:underline">Detail</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $riwayat->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function tagihanCheckout() {
    return {
        tab: 'belum_bayar',
        checked: [],
        nominalMap: {},
        topupMap: {},
        loading: false,
        errorMsg: '',

        get checkedCount() { return this.checked.length; },

        get allChecked() {
            const all = @json($belumBayar->pluck('id'));
            return all.length > 0 && all.every(id => this.checked.includes(String(id)));
        },

        get tagihanTotal() {
            return this.checked.reduce((sum, id) => sum + (this.nominalMap[id] || 0), 0);
        },

        get topUpTotal() {
            return Object.values(this.topupMap).reduce((sum, v) => sum + v, 0);
        },

        get totalNominal() {
            return this.tagihanTotal + this.topUpTotal;
        },

        isChecked(id) {
            return this.checked.includes(String(id));
        },

        toggleAll(e) {
            if (e.target.checked) {
                const all = @json($belumBayar->pluck('id'));
                this.checked = all.map(String);
                @foreach($belumBayar as $t)
                this.nominalMap['{{ $t->id }}'] = {{ $t->nominal }};
                @endforeach
            } else {
                this.checked = [];
                this.nominalMap = {};
            }
        },

        updateNominalTagihan(id, nominal, checked) {
            if (checked) {
                this.nominalMap[String(id)] = nominal;
            } else {
                delete this.nominalMap[String(id)];
            }
        },

        updateTopUp(santriId, value) {
            const v = parseInt(value) || 0;
            if (v > 0) {
                this.topupMap[santriId] = v;
            } else {
                delete this.topupMap[santriId];
            }
        },

        formatNumber(n) {
            return new Intl.NumberFormat('id-ID').format(n);
        },

        async doCheckout() {
            if (this.checkedCount === 0) {
                this.errorMsg = 'Pilih minimal 1 tagihan untuk dibayar.';
                return;
            }

            this.errorMsg = '';
            this.loading = true;

            const topupItems = Object.entries(this.topupMap)
                .filter(([, v]) => v > 0)
                .map(([santri_id, nominal]) => ({ santri_id: parseInt(santri_id), nominal }));

            try {
                const res = await fetch('{{ route("wali.tagihan.checkout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        tagihan_ids: this.checked.map(Number),
                        topup_items: topupItems,
                    }),
                });

                const data = await res.json();

                if (!res.ok) {
                    this.errorMsg = data.message || 'Terjadi kesalahan. Coba lagi.';
                    return;
                }

                if (data.type === 'snap') {
                    window.handlePaymentResponse(data);
                } else if (data.type === 'redirect') {
                    window.location.href = data.url;
                } else {
                    this.errorMsg = 'Respons tidak dikenal dari server.';
                }
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
