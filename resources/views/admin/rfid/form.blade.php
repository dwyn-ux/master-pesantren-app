@extends('layouts.app')
@section('title', 'Assign RFID - ' . $santri->nama)
@section('page-title', 'Assign RFID Santri')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex items-center justify-between">
            <h6 class="text-lg font-bold text-gray-800">Registrasi Kartu RFID</h6>
            <a href="{{ route('admin.rfid.index') }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="p-8">
            <div class="flex items-center gap-6 mb-8 p-4 bg-indigo-50 rounded-2xl">
                <div class="w-16 h-16 rounded-full bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                    {{ substr($santri->nama, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $santri->nama }}</h3>
                    <p class="text-indigo-600 font-medium">{{ $santri->nis }} • {{ $santri->kelas }}</p>
                </div>
            </div>

            <form action="{{ route('admin.rfid.update', $santri) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">RFID UID / Nomor Kartu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i class="fa-solid fa-id-card"></i>
                        </span>
                        <input type="text" name="rfid_uid" id="rfid_input" value="{{ old('rfid_uid', $santri->rfid_uid) }}" 
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-mono"
                               placeholder="Tap kartu pada reader..." required autofocus autocomplete="off">
                    </div>
                    @error('rfid_uid')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="mt-3 text-sm text-gray-500 italic">
                        <i class="fa-solid fa-circle-info mr-1"></i> Letakkan kursor pada input di atas, lalu tempelkan kartu RFID ke alat pembaca.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="flex-1 py-3 px-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-indigo-200">
                        Simpan RFID
                    </button>
                    <button type="button" onclick="document.getElementById('rfid_input').value = ''; document.getElementById('rfid_input').focus();" 
                            class="py-3 px-6 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition-all">
                        Clear
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Memastikan input tetap fokus agar bisa langsung di-tap
    document.addEventListener('click', function() {
        document.getElementById('rfid_input').focus();
    });
</script>
@endsection
