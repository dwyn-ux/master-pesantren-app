@extends('layouts.app')
@section('title', 'Detail Laundry ' . $laundry->nomor_tiket)
@section('page-title', 'Detail Order Laundry')

@section('sidebar')
    @include('partials.sidebar-outlet')
@endsection

@section('content')
<div class="flex justify-between items-center mb-6 max-w-5xl mx-auto px-2 print:hidden">
    <a href="{{ route('outlet.laundry.index') }}" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar Order
    </a>
    <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl text-sm font-semibold transition-colors shadow-sm">
        <i class="fa-solid fa-print mr-1"></i> Cetak Tiket
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
    <!-- Tiket / Struk Information -->
    <div class="lg:col-span-2">
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden bg-white print:shadow-none print:border-none print:w-full">
            
            <div class="p-6 sm:p-8 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center sm:items-start gap-4 text-center sm:text-left print:flex-row print:border-b-2 print:border-black print:pb-4">
                <div>
                    <h6 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1 print:text-black">No. Tiket Laundry</h6>
                    <h4 class="text-3xl font-mono font-extrabold text-indigo-700 tracking-tight print:text-black">{{ $laundry->nomor_tiket }}</h4>
                </div>
                
                @php
                    $statusData = match($laundry->status) {
                        'diterima' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'fa-inbox'],
                        'dicuci' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-soap'],
                        'selesai' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'fa-check-double'],
                        'diambil' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-hand-holding'],
                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-circle-info']
                    };
                @endphp
                <div class="print:hidden">
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold {{ $statusData['bg'] }} {{ $statusData['text'] }}">
                        <i class="fa-solid {{ $statusData['icon'] }} mr-2"></i> {{ $statusOptions[$laundry->status] ?? $laundry->status }}
                    </span>
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-gray-50/50 print:bg-white print:p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:grid-cols-2 print:gap-4">
                    
                    <!-- Customer Info -->
                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm print:border-black print:shadow-none">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 print:text-black">Informasi Santri</div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl print:hidden">
                                <i class="fa-solid fa-child"></i>
                            </div>
                            <div>
                                <h6 class="font-bold text-gray-800 text-lg print:text-sm print:mb-0">{{ $laundry->santri->nama }}</h6>
                                <p class="text-sm text-gray-500 font-mono print:text-xs">NIS: {{ $laundry->santri->nis }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm print:border-black print:shadow-none">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 print:text-black">Rincian Pembayaran</div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-500 print:text-xs">Berat Cucian</span>
                            <span class="font-bold text-gray-800 print:text-xs">{{ $laundry->berat_kg }} kg</span>
                        </div>
                        <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-50 print:border-black">
                            <span class="text-sm text-gray-500 print:text-xs">Tarif / kg</span>
                            <span class="text-sm text-gray-800 print:text-xs">Rp {{ number_format($laundry->harga_per_kg) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-700 print:text-sm">Total Lunas</span>
                            <span class="font-extrabold text-emerald-600 text-lg print:text-black print:text-sm">Rp {{ number_format($laundry->total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Timeline / Dates -->
                <div class="mt-6 bg-white p-5 rounded-xl border border-gray-100 shadow-sm print:border-black print:shadow-none">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 print:text-black">Timeline Order</div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1 print:text-black"><i class="fa-solid fa-inbox text-amber-500 mr-1 print:hidden"></i> Tanggal Antar</p>
                            <p class="font-semibold text-sm text-gray-800 print:text-xs">{{ $laundry->tanggal_antar ? $laundry->tanggal_antar->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1 print:text-black"><i class="fa-solid fa-check-double text-emerald-500 mr-1 print:hidden"></i> Selesai Cuci</p>
                            <p class="font-semibold text-sm text-gray-800 print:text-xs">{{ $laundry->tanggal_selesai ? $laundry->tanggal_selesai->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1 print:text-black"><i class="fa-solid fa-hand-holding text-blue-500 mr-1 print:hidden"></i> Tanggal Diambil</p>
                            <p class="font-semibold text-sm text-gray-800 print:text-xs">{{ $laundry->tanggal_diambil ? $laundry->tanggal_diambil->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                @if($laundry->catatan)
                <div class="mt-6 bg-amber-50/50 p-5 rounded-xl border border-amber-100 print:border-black print:shadow-none">
                    <div class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2 flex items-center gap-1.5 print:text-black">
                        <i class="fa-regular fa-note-sticky"></i> Catatan Khusus
                    </div>
                    <p class="text-sm text-amber-900 leading-relaxed italic print:text-black">"{{ $laundry->catatan }}"</p>
                </div>
                @endif
                
                <div class="mt-6 text-center print:block hidden">
                    <p class="text-xs font-serif italic mb-1">Harap bawa tiket ini saat mengambil laundry.</p>
                    <p class="text-[10px] text-gray-500">Digital Pesantren System - Layanan Laundry Outlet</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Widget (Hidden in Print) -->
    <div class="lg:col-span-1 print:hidden">
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden sticky top-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
                <h6 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-indigo-500"></i> Update Status</h6>
            </div>
            
            <div class="p-6">
                <!-- Status Timeline Visualization -->
                <div class="mb-6 relative">
                    <div class="absolute left-3.5 top-2 bottom-2 w-0.5 bg-gray-200"></div>
                    
                    @php
                        $statuses = ['diterima', 'dicuci', 'selesai', 'diambil'];
                        $currentIndex = array_search($laundry->status, $statuses);
                    @endphp
                    
                    <ul class="space-y-4">
                        @foreach($statuses as $index => $statusKey)
                            @php
                                $isCompleted = $index <= $currentIndex;
                                $isCurrent = $index === $currentIndex;
                                
                                $dotClass = $isCompleted ? 'bg-indigo-600 border-indigo-200 text-white' : 'bg-white border-gray-300 text-transparent';
                                if($isCurrent) $dotClass = 'bg-indigo-600 border-indigo-200 text-white ring-4 ring-indigo-100';
                                
                                $textClass = $isCompleted ? 'text-gray-800 font-bold' : 'text-gray-400 font-medium';
                                if($isCurrent) $textClass = 'text-indigo-700 font-extrabold';
                            @endphp
                            <li class="relative pl-10">
                                <div class="absolute left-0 top-1 w-7 h-7 rounded-full border-2 flex items-center justify-center text-xs z-10 transition-all {{ $dotClass }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div class="{{ $textClass }} text-sm uppercase tracking-wider">{{ $statusOptions[$statusKey] }}</div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <form method="POST" action="{{ route('outlet.laundry.update-status', $laundry) }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Ubah Status Menjadi:</label>
                        <select name="status" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm font-medium" required>
                            @foreach($statusOptions as $key => $label)
                                <option value="{{ $key }}" @selected($laundry->status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200 flex justify-center items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .print\:visible, .print\:visible * { visibility: visible; }
    .glass-panel {
        position: absolute; left: 0; top: 0;
        width: 100%; box-shadow: none !important; border: none !important; background: transparent !important;
    }
}
</style>
@endsection
