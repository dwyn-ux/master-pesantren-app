@extends('layouts.app')
@section('title', $produk ? 'Edit Produk' : 'Tambah Produk')
@section('page-title', $produk ? 'Edit Produk' : 'Tambah Produk')

@section('sidebar')
    @include('partials.sidebar-outlet')
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex justify-between items-center">
            <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid {{ $produk ? 'fa-pen-to-square text-amber-500' : 'fa-box-open text-indigo-500' }}"></i> 
                {{ $produk ? 'Edit Informasi Produk' : 'Tambah Produk Baru' }}
            </h6>
            <a href="{{ route('outlet.produk.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">
                Batal
            </a>
        </div>
        
        <div class="p-6">
            <form method="POST" action="{{ $produk ? route('outlet.produk.update', $produk) : route('outlet.produk.store') }}" enctype="multipart/form-data">
                @csrf
                @if($produk)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    
                    <!-- Left Column (Main Info) -->
                    <div class="md:col-span-8 space-y-5">
                        
                        <!-- Nama Produk -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" value="{{ old('nama', $produk->nama ?? '') }}" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm @error('nama') border-red-500 ring-red-500/20 @enderror" 
                                placeholder="Cth: Indomie Goreng" required>
                            @error('nama')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Barcode & Stok Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Barcode (Opsional)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-solid fa-barcode"></i>
                                    </div>
                                    <input type="text" name="barcode" value="{{ old('barcode', $produk->barcode ?? '') }}" 
                                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm font-mono @error('barcode') border-red-500 ring-red-500/20 @enderror" 
                                        placeholder="Scan barcode...">
                                </div>
                                @error('barcode')
                                    <p class="mt-1 text-sm text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Stok Awal <span class="text-red-500">*</span></label>
                                <input type="number" name="stok" value="{{ old('stok', $produk->stok ?? 0) }}" min="0"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm @error('stok') border-red-500 ring-red-500/20 @enderror" 
                                    required>
                                @error('stok')
                                    <p class="mt-1 text-sm text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Harga -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Harga Jual <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none font-bold text-gray-500">
                                    Rp
                                </div>
                                <input type="number" name="harga" value="{{ old('harga', $produk->harga ?? '') }}" min="0"
                                    class="w-full pl-12 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm font-bold @error('harga') border-red-500 ring-red-500/20 @enderror" 
                                    placeholder="0" required>
                            </div>
                            @error('harga')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Toggle -->
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl mt-6">
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" name="is_aktif" value="1" class="sr-only" @checked(old('is_aktif', $produk->is_aktif ?? true))>
                                    <div class="block bg-gray-300 w-12 h-7 rounded-full transition-colors duration-300"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-5 h-5 rounded-full transition-transform duration-300 shadow-sm"></div>
                                </div>
                                <div class="ml-4">
                                    <span class="block text-sm font-bold text-gray-800">Tampilkan di Marketplace Wali</span>
                                    <span class="block text-xs text-gray-500">Jika aktif, wali santri dapat melihat dan membeli produk ini.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Right Column (Image) -->
                    <div class="md:col-span-4 space-y-5" x-data="imagePreview()">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Foto Produk</label>
                            
                            <div class="relative border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer group overflow-hidden" 
                                 :class="{'border-indigo-400 bg-indigo-50/30': previewUrl}"
                                 @click="$refs.fileInput.click()">
                                
                                <!-- File Input (Hidden) -->
                                <input type="file" name="foto" x-ref="fileInput" @change="fileChosen" accept="image/*" class="hidden">
                                
                                <!-- No Image State -->
                                <div x-show="!previewUrl" class="flex flex-col items-center justify-center py-12 px-4 text-center">
                                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-300 mb-3 group-hover:text-indigo-400 transition-colors"></i>
                                    <p class="text-sm font-bold text-gray-600">Pilih Foto Produk</p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                                </div>
                                
                                <!-- Preview State -->
                                <div x-show="previewUrl" style="display: none;" class="relative w-full aspect-square">
                                    <img :src="previewUrl" class="w-full h-full object-cover" alt="Preview">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span class="text-white font-bold text-sm bg-black/50 px-3 py-1.5 rounded-lg"><i class="fa-solid fa-pen mr-1"></i> Ganti Foto</span>
                                    </div>
                                </div>
                            </div>
                            @error('foto')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200 hover:-translate-y-0.5 flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> {{ $produk ? 'Simpan Perubahan' : 'Tambah Produk' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Toggle Switch Styles */
input:checked ~ .block {
    background-color: #4f46e5; /* indigo-600 */
}
input:checked ~ .dot {
    transform: translateX(1.25rem); /* 20px */
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('imagePreview', () => ({
        previewUrl: '{{ $produk && $produk->foto ? Storage::url($produk->foto) : "" }}',
        
        fileChosen(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewUrl = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }));
});
</script>
@endsection
