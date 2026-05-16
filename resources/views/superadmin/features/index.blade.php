@extends('layouts.app')

@section('title', 'Manajemen Fitur')
@section('page-title', 'Manajemen Fitur')

@section('sidebar')
    @include('partials.sidebar-superadmin')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Paket Preset --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Paket Preset</h2>
                <p class="text-sm text-gray-500">Pilih paket untuk langsung mengatur kombinasi fitur sesuai harga jual.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($packages as $key => $pkg)
                <div class="border border-gray-200 rounded-xl p-5 hover:border-indigo-300 hover:shadow-md transition flex flex-col">
                    <h3 class="font-bold text-gray-800 text-base">{{ $pkg['name'] }}</h3>
                    
                    <div class="mt-2 space-y-1">
                        <div>
                            <p class="text-xs text-gray-500">Setup (one-time)</p>
                            <p class="text-xl font-bold text-indigo-600">
                                Rp {{ number_format($pkg['setup_price'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Bulanan</p>
                            <p class="text-lg font-semibold text-green-600">
                                Rp {{ number_format($pkg['monthly_price'], 0, ',', '.') }}/bln
                            </p>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-xs text-gray-400">Total tahun 1</p>
                            <p class="text-sm font-bold text-gray-700">
                                Rp {{ number_format($pkg['setup_price'] + ($pkg['monthly_price'] * 12), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-3 flex-1">{{ $pkg['description'] }}</p>
                    
                    @if(isset($pkg['target_size']))
                    <p class="text-[10px] text-indigo-600 font-semibold mt-2">{{ $pkg['target_size'] }}</p>
                    @endif

                    <div class="flex flex-wrap gap-1 mt-3">
                        @foreach($pkg['features'] as $f)
                            <span class="text-[9px] uppercase tracking-wider bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded">{{ $f }}</span>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('superadmin.features.apply-package', $key) }}" class="mt-4"
                          onsubmit="return confirm('Terapkan {{ $pkg['name'] }}? Semua fitur akan diatur ulang sesuai paket ini.')">
                        @csrf
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-lg transition text-sm">
                            <i class="fa-solid fa-bolt mr-1"></i> Terapkan
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Toggle Manual --}}
    <form method="POST" action="{{ route('superadmin.features.update') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        @csrf
        @method('PUT')

        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Toggle Fitur Manual</h2>
                <p class="text-sm text-gray-500">Aktifkan / nonaktifkan tiap fitur sesuai kebutuhan pesantren.</p>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg transition">
                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Semua
            </button>
        </div>

        @foreach($grouped as $group => $items)
            <div class="mt-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 border-b pb-2">{{ $group }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($items as $key => $row)
                        @php
                            $meta = $row['meta'];
                            $enabled = $row['enabled'];
                            $isCore  = !empty($meta['core']);
                        @endphp
                        <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 transition {{ $enabled ? 'bg-indigo-50/40 border-indigo-200' : '' }}">
                            <input
                                type="checkbox"
                                name="features[{{ $key }}]"
                                value="1"
                                @checked($enabled)
                                @disabled($isCore)
                                class="mt-1 w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50"
                            >
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-800">{{ $meta['label'] }}</span>
                                    @if($isCore)
                                        <span class="text-[10px] uppercase bg-gray-200 text-gray-600 px-2 py-0.5 rounded">Core</span>
                                    @elseif($enabled)
                                        <span class="text-[10px] uppercase bg-green-100 text-green-700 px-2 py-0.5 rounded">Aktif</span>
                                    @else
                                        <span class="text-[10px] uppercase bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Nonaktif</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $meta['description'] ?? '' }}</p>
                                <p class="text-[10px] text-gray-400 mt-1 font-mono">{{ $key }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="mt-6 pt-4 border-t flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg transition">
                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Semua
            </button>
        </div>
    </form>
</div>
@endsection
