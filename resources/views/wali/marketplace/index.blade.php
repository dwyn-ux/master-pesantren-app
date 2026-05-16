@extends('layouts.app')
@section('title', 'Marketplace Santri')
@section('page-title', 'Marketplace Santri')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8 relative">

    {{-- Main Content (Products) --}}
    <div class="lg:col-span-8 space-y-6">
        
        {{-- Header & Search --}}
        <div class="glass-panel p-6 rounded-2xl shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Katalog Produk</h4>
                <p class="text-sm text-gray-500">Belanja keperluan santri tanpa harus ke pondok.</p>
            </div>
            <a href="{{ route('wali.marketplace.orders') }}" class="px-5 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Order
            </a>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-xl text-green-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-xl text-red-500"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        {{-- Products Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($produk as $item)
                <div class="glass-panel rounded-2xl shadow-sm overflow-hidden flex flex-col group hover:-translate-y-1 transition-all hover:shadow-md border border-transparent hover:border-indigo-100">
                    <div class="h-40 bg-gray-100 flex items-center justify-center text-gray-300 relative overflow-hidden">
                        @if($item->foto)
                            <img src="{{ Storage::url($item->foto) }}" class="w-full h-full object-cover" alt="{{ $item->nama }}">
                        @else
                            <i class="fa-solid fa-box-open text-5xl group-hover:scale-110 transition-transform duration-500"></i>
                        @endif
                        <div class="absolute top-3 right-3 px-2 py-1 bg-white/80 backdrop-blur-sm rounded-lg text-xs font-bold text-gray-700 shadow-sm">
                            Tersedia: {{ $item->stok }}
                        </div>
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <h5 class="text-lg font-bold text-gray-800 mb-1 leading-tight">{{ $item->nama }}</h5>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $item->deskripsi ?? 'Tanpa deskripsi' }}</p>
                        
                        <div class="mt-auto flex items-end justify-between">
                            <div>
                                <span class="text-xs text-gray-500 block mb-0.5">Harga:</span>
                                <div class="text-lg font-extrabold text-indigo-600">Rp {{ number_format($item->harga) }}</div>
                            </div>
                            <button type="button" class="w-10 h-10 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center shadow-md shadow-indigo-200 transition-transform active:scale-95 add-to-cart"
                                    data-produk-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama }}"
                                    data-harga="{{ $item->harga }}">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 sm:col-span-2 glass-panel p-10 rounded-2xl flex flex-col items-center justify-center text-gray-500">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                        <i class="fa-solid fa-store-slash text-3xl text-gray-300"></i>
                    </div>
                    <h5 class="text-lg font-bold text-gray-700 mb-1">Katalog Kosong</h5>
                    <p class="text-center">Saat ini belum ada produk yang dijual di Marketplace.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Sidebar (Cart & Saldo) --}}
    <div class="lg:col-span-4 space-y-6">
        
        {{-- Dompet Saldo --}}
        <div class="p-6 rounded-2xl shadow-md text-white relative overflow-hidden" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%)">
            <div class="absolute right-0 bottom-0 w-32 h-32 bg-white/10 rounded-full blur-2xl translate-x-10 translate-y-10"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-bold text-emerald-100 uppercase tracking-wider">Saldo Digital</span>
                    <i class="fa-solid fa-wallet text-xl text-emerald-200"></i>
                </div>
                <h3 class="text-3xl font-extrabold mb-1">Rp {{ number_format(Auth::user()->wali->saldo ?? 0) }}</h3>
                <p class="text-emerald-100 text-sm">Gunakan saldo untuk membayar order</p>
            </div>
        </div>

        {{-- Shopping Cart (Sticky) --}}
        <div class="glass-panel rounded-2xl shadow-sm sticky top-6 flex flex-col" style="max-height: calc(100vh - 3rem);">
            <div class="border-b border-gray-100 bg-white/50 px-5 py-4 flex items-center justify-between">
                <h6 class="font-bold text-gray-800"><i class="fa-solid fa-cart-shopping text-indigo-500 mr-2"></i> Keranjang</h6>
                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold" id="cartCount">0</span>
            </div>
            
            <form id="orderForm" method="POST" action="{{ route('wali.marketplace.create-order') }}" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="p-5 overflow-y-auto flex-1 border-b border-gray-100">
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kirim ke Santri <span class="text-red-500">*</span></label>
                        <select name="santri_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all bg-white appearance-none text-sm" required>
                            <option value="">-- Pilih Santri --</option>
                            @foreach($santriList as $santri)
                                <option value="{{ $santri->id }}">{{ $santri->nama }} ({{ $santri->nis }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="cartItems" class="space-y-3">
                        <div class="text-center py-8 text-gray-400 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                            <i class="fa-solid fa-basket-shopping text-2xl mb-2 text-gray-300"></i>
                            <p class="text-sm">Keranjang belanja masih kosong.</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-5 bg-gray-50/50 mt-auto">
                    <div class="flex justify-between items-end mb-4">
                        <span class="text-gray-500 font-medium text-sm">Total Bayar:</span>
                        <span class="text-2xl font-extrabold text-indigo-600" id="totalPrice">Rp 0</span>
                    </div>
                    
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" id="checkoutBtn" disabled>
                        Proses Pembayaran <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>
@endsection

@section('scripts')
<script>
let cart = {};

function updateCart() {
    const cartItems = document.getElementById('cartItems');
    const totalPrice = document.getElementById('totalPrice');
    const cartCount = document.getElementById('cartCount');
    const checkoutBtn = document.getElementById('checkoutBtn');

    let total = 0;
    let itemCount = 0;
    let html = '';

    for (const [produkId, item] of Object.entries(cart)) {
        const subtotal = item.harga * item.qty;
        total += subtotal;
        itemCount += item.qty;

        html += `
            <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-xl shadow-sm group hover:border-indigo-100 transition-colors">
                <div class="flex-1 pr-3">
                    <div class="font-bold text-gray-800 text-sm leading-tight mb-1">${item.nama}</div>
                    <div class="text-xs text-gray-500 font-medium">Rp ${item.harga.toLocaleString('id-ID')} <span class="mx-1">x</span> <span class="text-indigo-600 font-bold bg-indigo-50 px-1.5 rounded">${item.qty}</span></div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <div class="font-bold text-indigo-700 text-sm">Rp ${subtotal.toLocaleString('id-ID')}</div>
                    <button type="button" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-colors shadow-sm" onclick="removeFromCart(${produkId})" title="Hapus">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            </div>
        `;
    }

    if (html === '') {
        html = `
            <div class="text-center py-8 text-gray-400 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                <i class="fa-solid fa-basket-shopping text-2xl mb-2 text-gray-300"></i>
                <p class="text-sm">Keranjang belanja masih kosong.</p>
            </div>
        `;
        checkoutBtn.disabled = true;
    } else {
        checkoutBtn.disabled = false;
    }

    cartItems.innerHTML = html;
    totalPrice.textContent = 'Rp ' + total.toLocaleString('id-ID');
    cartCount.textContent = itemCount;
}

function addToCart(produkId, nama, harga) {
    if (cart[produkId]) {
        cart[produkId].qty += 1;
    } else {
        cart[produkId] = {
            nama: nama,
            harga: parseInt(harga),
            qty: 1
        };
    }

    updateFormInputs();
    updateCart();
    showToast('Berhasil!', `<b>${nama}</b> ditambahkan ke keranjang.`, 'success');
}

function removeFromCart(produkId) {
    const nama = cart[produkId].nama;
    delete cart[produkId];
    updateFormInputs();
    updateCart();
    showToast('Dihapus', `<b>${nama}</b> dihapus dari keranjang.`, 'warning');
}

function updateFormInputs() {
    const form = document.getElementById('orderForm');

    // Remove existing item inputs
    form.querySelectorAll('input[name^="items"]').forEach(input => input.remove());

    // Add new item inputs
    let index = 0;
    for (const [produkId, item] of Object.entries(cart)) {
        form.insertAdjacentHTML('beforeend', `
            <input type="hidden" name="items[${index}][produk_id]" value="${produkId}">
            <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
        `);
        index++;
    }
}

function showToast(title, message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    
    let iconClass, bgClass, iconColorClass;
    if (type === 'success') {
        iconClass = 'fa-circle-check';
        bgClass = 'bg-white border-green-200';
        iconColorClass = 'text-green-500';
    } else if (type === 'warning') {
        iconClass = 'fa-triangle-exclamation';
        bgClass = 'bg-white border-amber-200';
        iconColorClass = 'text-amber-500';
    }

    toast.className = `flex items-center p-4 border rounded-xl shadow-lg transform transition-all duration-300 translate-x-full ${bgClass}`;
    toast.innerHTML = `
        <div class="mr-3 ${iconColorClass} text-xl"><i class="fa-solid ${iconClass}"></i></div>
        <div>
            <div class="font-bold text-gray-800 text-sm">${title}</div>
            <div class="text-sm text-gray-600">${message}</div>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 10);

    // Animate out and remove
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const produkId = this.dataset.produkId;
            const nama = this.dataset.nama;
            const harga = this.dataset.harga;
            addToCart(produkId, nama, harga);
        });
    });
});
</script>
@endsection