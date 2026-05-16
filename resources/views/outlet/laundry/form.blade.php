@extends('layouts.app')
@section('title', 'Terima Laundry')
@section('page-title', 'Terima Laundry')

@section('sidebar')
    @include('partials.sidebar-outlet')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex justify-between items-center">
            <div>
                <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-scale-balanced text-indigo-500"></i> Form Terima Laundry
                </h6>
                <p class="text-sm text-gray-500 mt-1">Tarif aktif: <span class="font-bold text-indigo-600">Rp {{ number_format($tarif->harga_per_kg) }}/kg</span></p>
            </div>
            <a href="{{ route('outlet.laundry.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">
                Batal
            </a>
        </div>
        
        <div class="p-6">
            <form method="POST" action="{{ route('outlet.laundry.store') }}">
                @csrf
                
                <div class="space-y-6">
                    <!-- Fingerprint Input -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fingerprint ID Santri <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-fingerprint text-lg"></i>
                            </div>
                            <input type="text" name="fingerprint_id" value="{{ old('fingerprint_id') }}" 
                                class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm font-mono tracking-widest @error('fingerprint_id') border-red-500 ring-red-500/20 @enderror" 
                                placeholder="Scan fingerprint..." required autofocus>
                        </div>
                        @error('fingerprint_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Berat Laundry -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Berat Pakaian (kg) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" step="0.01" min="1" name="berat_kg" id="berat_kg" value="{{ old('berat_kg', 1) }}" 
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm font-bold text-lg @error('berat_kg') border-red-500 ring-red-500/20 @enderror" 
                                required>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 font-bold">
                                kg
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Minimal perhitungan adalah 1 kg.</p>
                        @error('berat_kg')
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estimasi Total -->
                    <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-5 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Estimasi Total</p>
                            <p class="text-xs text-gray-400">Total akan dibulatkan ke atas</p>
                        </div>
                        <div class="text-2xl font-extrabold text-indigo-600" id="totalPreview">
                            Rp {{ number_format($tarif->harga_per_kg) }}
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" rows="3" 
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm resize-none @error('catatan') border-red-500 ring-red-500/20 @enderror" 
                            placeholder="Contoh: Ada 2 selimut tebal, luntur harap pisahkan...">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex gap-3">
                    <button type="submit" class="flex-1 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200 hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <i class="fa-solid fa-check-circle"></i> Terima & Potong Saldo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hargaPerKg = {{ $tarif->harga_per_kg }};
    const beratInput = document.getElementById('berat_kg');
    const totalPreview = document.getElementById('totalPreview');
    
    function updateTotal() {
        // Minimal 1 kg
        const berat = Math.max(parseFloat(beratInput.value || 1), 1);
        const total = Math.ceil(berat * hargaPerKg);
        totalPreview.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
    
    beratInput.addEventListener('input', updateTotal);
    
    // Select all text on focus for easier typing
    beratInput.addEventListener('focus', function() {
        this.select();
    });
    
    updateTotal();
});
</script>
@endsection
