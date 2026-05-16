@extends('layouts.app')
@section('title', 'Kasir Kantin')
@section('page-title', 'Sistem Kasir')

@section('sidebar')
    @include('partials.sidebar-outlet')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="kasirApp()">
    
    <!-- Bagian Produk -->
    <div class="lg:col-span-2 flex flex-col h-[calc(100vh-10rem)]">
        <div class="glass-panel rounded-2xl overflow-hidden shadow-sm flex flex-col flex-1">
            <div class="px-6 py-4 border-b border-gray-100 bg-white/50 flex justify-between items-center z-10">
                <div>
                    <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-store text-indigo-500"></i> {{ $outlet->nama }}
                    </h6>
                    <p class="text-sm text-gray-500 mt-0.5">Pilih produk untuk ditambahkan ke keranjang</p>
                </div>
                <a href="{{ route('outlet.kasir.kantin.history') }}" class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center gap-2 bg-white">
                    <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i> Riwayat
                </a>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/30">
                @if($produk->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center text-center py-12">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                            <i class="fa-solid fa-box-open text-4xl text-gray-300"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-600 mb-1">Katalog Kosong</h4>
                        <p class="text-gray-400">Belum ada produk aktif atau stok tersedia.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($produk as $item)
                            <button type="button" @click="addItem({{ $item->id }}, '{{ addslashes($item->nama) }}', {{ $item->harga }}, {{ $item->stok }})" 
                                class="flex flex-col items-start p-4 bg-white border border-gray-100 rounded-2xl hover:border-indigo-400 hover:shadow-md hover:-translate-y-0.5 transition-all text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 group relative overflow-hidden">
                                
                                <div class="w-full aspect-square bg-gray-50 rounded-xl mb-3 flex items-center justify-center text-gray-300 relative overflow-hidden">
                                    <i class="fa-solid fa-box text-3xl group-hover:scale-110 transition-transform"></i>
                                    <div class="absolute top-2 right-2 px-2 py-0.5 bg-white/90 backdrop-blur text-[10px] font-bold text-gray-600 rounded shadow-sm border border-gray-100">
                                        Stok: {{ $item->stok }}
                                    </div>
                                </div>
                                
                                <span class="font-bold text-gray-800 text-sm line-clamp-2 leading-tight mb-2 flex-1">{{ $item->nama }}</span>
                                <span class="text-indigo-600 font-extrabold w-full">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bagian Keranjang & Checkout -->
    <div class="lg:col-span-1 flex flex-col h-[calc(100vh-10rem)]">
        <div class="glass-panel rounded-2xl overflow-hidden shadow-sm flex flex-col flex-1 relative">
            <div class="px-6 py-4 border-b border-gray-100 bg-white/50 z-10 flex justify-between items-center">
                <h6 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-cart-shopping mr-2 text-indigo-500"></i> Keranjang</h6>
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full" x-text="cartCount"></span>
            </div>
            
            <form method="POST" action="{{ route('outlet.kasir.kantin.store') }}" id="kasirForm" @submit.prevent="submitForm" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                
                <div class="flex-1 overflow-y-auto bg-white/40">
                    
                    <!-- Pilihan Metode Auth -->
                    <div class="px-5 pt-5 pb-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Metode Validasi</label>
                        <div class="flex bg-gray-100/80 p-1.5 rounded-xl border border-gray-200/50">
                            <button type="button" @click="authMethod = 'fingerprint'; focusInput()" 
                                :class="authMethod === 'fingerprint' ? 'bg-white shadow-sm border-gray-200 text-indigo-600' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'" 
                                class="flex-1 py-2 text-xs font-bold rounded-lg transition-all border border-transparent">
                                <i class="fa-solid fa-fingerprint mb-1 block text-lg"></i> Sidik Jari
                            </button>
                            <button type="button" @click="authMethod = 'rfid'; focusInput()"
                                :class="authMethod === 'rfid' ? 'bg-white shadow-sm border-gray-200 text-indigo-600' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'" 
                                class="flex-1 py-2 text-xs font-bold rounded-lg transition-all border border-transparent">
                                <i class="fa-solid fa-id-card mb-1 block text-lg"></i> RFID
                            </button>
                            <button type="button" @click="authMethod = 'manual'"
                                :class="authMethod === 'manual' ? 'bg-white shadow-sm border-gray-200 text-indigo-600' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'" 
                                class="flex-1 py-2 text-xs font-bold rounded-lg transition-all border border-transparent">
                                <i class="fa-solid fa-magnifying-glass mb-1 block text-lg"></i> Manual
                            </button>
                        </div>
                        <input type="hidden" name="auth_method" :value="authMethod">
                    </div>

                    <!-- Input Auth Value -->
                    <div class="px-5 pb-5 border-b border-gray-100">
                        <div x-show="authMethod === 'fingerprint' || authMethod === 'rfid'" x-transition>
                            <div class="relative">
                                <input type="text" x-model="authValue" x-ref="authInput" 
                                    class="w-full bg-white border-2 border-indigo-100 text-indigo-900 rounded-xl focus:ring-0 focus:border-indigo-500 block p-3 shadow-sm transition-all text-center tracking-widest font-mono text-lg outline-none placeholder-indigo-300" 
                                    :placeholder="authMethod === 'fingerprint' ? 'Scan Jari...' : 'Tap Kartu...'" 
                                    @keydown.enter.prevent>
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-indigo-300">
                                    <i class="fa-solid fa-keyboard" x-show="authMethod === 'rfid'"></i>
                                    <i class="fa-solid fa-hand-pointer" x-show="authMethod === 'fingerprint'"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div x-show="authMethod === 'manual'" x-cloak x-transition>
                            <select x-model="authValue" class="w-full bg-white border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block p-3 shadow-sm transition-all outline-none font-medium">
                                <option value="">-- Pilih Santri --</option>
                                @foreach($santriList as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nis }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <input type="hidden" name="auth_value" :value="authValue">
                    </div>

                    <!-- Cart Items -->
                    <div class="p-5 space-y-3">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2" x-show="cartCount > 0">Daftar Belanja</div>
                        
                        <template x-if="Object.keys(cart).length === 0">
                            <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300 shadow-sm border border-gray-100">
                                    <i class="fa-solid fa-basket-shopping text-2xl"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-500">Keranjang masih kosong</p>
                            </div>
                        </template>
                        
                        <template x-for="(item, id) in cart" :key="id">
                            <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-3 flex flex-col gap-2 relative group hover:border-indigo-200 transition-colors">
                                <div class="pr-8">
                                    <h6 class="font-bold text-gray-800 text-sm leading-tight" x-text="item.nama"></h6>
                                    <p class="text-xs text-gray-500 mt-0.5">Rp <span x-text="formatRupiah(item.harga)"></span> / item</p>
                                </div>
                                
                                <button type="button" @click="removeItem(id)" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors" title="Hapus">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                                
                                <div class="flex justify-between items-end mt-1 pt-2 border-t border-gray-50">
                                    <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200/60 p-0.5">
                                        <button type="button" @click="decreaseQty(id)" class="w-7 h-7 flex items-center justify-center rounded-md bg-white border border-gray-200 text-gray-600 hover:text-indigo-600 hover:border-indigo-300 focus:outline-none transition-colors shadow-sm"><i class="fa-solid fa-minus text-[10px]"></i></button>
                                        <span class="text-sm font-bold w-8 text-center text-indigo-700" x-text="item.qty"></span>
                                        <button type="button" @click="increaseQty(id)" class="w-7 h-7 flex items-center justify-center rounded-md bg-white border border-gray-200 text-gray-600 hover:text-indigo-600 hover:border-indigo-300 focus:outline-none transition-colors shadow-sm"><i class="fa-solid fa-plus text-[10px]"></i></button>
                                    </div>
                                    <span class="font-extrabold text-gray-800">Rp <span x-text="formatRupiah(item.harga * item.qty)"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Hidden Inputs -->
                <template x-for="(item, id) in cart" :key="id">
                    <div>
                        <input type="hidden" :name="'items['+id+'][produk_id]'" :value="id">
                        <input type="hidden" :name="'items['+id+'][qty]'" :value="item.qty">
                    </div>
                </template>

                <div class="bg-gray-50/80 p-5 mt-auto border-t border-gray-200">
                    <div class="flex justify-between items-end mb-4">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Pembayaran</span>
                        <span class="text-2xl font-extrabold text-indigo-600">Rp <span x-text="formatRupiah(cartTotal)"></span></span>
                    </div>

                    <button type="submit" 
                        class="w-full py-3.5 rounded-xl font-bold shadow-md transition-all flex justify-center items-center gap-2 text-lg"
                        :class="canSubmit ? 'bg-indigo-600 hover:bg-indigo-700 text-white hover:-translate-y-0.5 shadow-indigo-200' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                        :disabled="!canSubmit">
                        <i class="fa-solid fa-cash-register"></i> Bayar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kasirApp', () => ({
        cart: {},
        authMethod: 'fingerprint',
        authValue: '',
        
        init() {
            this.$nextTick(() => {
                this.focusInput();
            });
        },
        
        focusInput() {
            if(this.authMethod !== 'manual') {
                this.authValue = '';
                if(this.$refs.authInput) {
                    setTimeout(() => {
                        this.$refs.authInput.focus();
                    }, 50);
                }
            }
        },

        get cartTotal() {
            return Object.values(this.cart).reduce((total, item) => total + (item.harga * item.qty), 0);
        },
        
        get cartCount() {
            return Object.keys(this.cart).length;
        },

        get canSubmit() {
            return this.cartCount > 0 && this.authValue.trim() !== '';
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        },

        addItem(id, nama, harga, stok) {
            if (!this.cart[id]) {
                this.cart[id] = { id: id, nama: nama, harga: parseInt(harga), stok: parseInt(stok), qty: 0 };
            }
            this.increaseQty(id);
        },

        increaseQty(id) {
            if (this.cart[id].qty < this.cart[id].stok) {
                this.cart[id].qty++;
            }
        },

        decreaseQty(id) {
            if (this.cart[id].qty > 1) {
                this.cart[id].qty--;
            } else {
                this.removeItem(id);
            }
        },

        removeItem(id) {
            delete this.cart[id];
        },
        
        submitForm(e) {
            if(this.canSubmit) {
                e.target.submit();
            }
        }
    }));
});
</script>
@endsection
