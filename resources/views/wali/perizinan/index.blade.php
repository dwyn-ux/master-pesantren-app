@extends('layouts.app')
@section('title', 'Perizinan Santri')
@section('page-title', 'Perizinan')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Form Pengajuan Izin -->
    <div class="glass-panel rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500">
        <h6 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-file-signature text-indigo-500"></i> Buat Pengajuan Izin
        </h6>

        <form action="{{ route('wali.perizinan.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Santri <span class="text-red-500">*</span></label>
                <select name="santri_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white">
                    <option value="">-- Pilih Anak Anda --</option>
                    @foreach($santri as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Keluar <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tanggal_mulai" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Kembali (Maksimal) <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tanggal_selesai" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Izin <span class="text-red-500">*</span></label>
                <textarea name="alasan" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none" placeholder="Jelaskan alasan secara lengkap. Misalnya: Acara keluarga pernikahan kakak kandung."></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Ajukan Izin
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat -->
    <div class="glass-panel rounded-2xl shadow-sm p-6 border-t-4 border-gray-200">
        <h6 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-gray-500"></i> Riwayat Perizinan
        </h6>

        <div class="space-y-4">
            @forelse($perizinan as $p)
                <div class="p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="font-bold text-gray-800">{{ $p->santri->nama }}</span>
                            <span class="text-xs text-gray-500 block mt-0.5">
                                <i class="fa-regular fa-calendar"></i> {{ $p->tanggal_mulai->format('d M Y H:i') }} - {{ $p->tanggal_selesai->format('d M Y H:i') }}
                            </span>
                        </div>
                        
                        @php
                            $color = match($p->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'disetujui_ustadz' => 'bg-blue-100 text-blue-800',
                                'disetujui_kesantrian' => 'bg-emerald-100 text-emerald-800',
                                'ditolak' => 'bg-red-100 text-red-800',
                                'sedang_keluar' => 'bg-indigo-100 text-indigo-800',
                                'selesai' => 'bg-gray-100 text-gray-800',
                                'terlambat' => 'bg-red-200 text-red-900',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            $label = str_replace('_', ' ', strtoupper($p->status));
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold tracking-wider {{ $color }}">
                            {{ $label }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-700 bg-white p-3 rounded-lg border border-gray-50 mt-3 shadow-sm">
                        <span class="font-semibold block mb-1">Alasan:</span>
                        {{ $p->alasan }}
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 text-sm">
                    Belum ada riwayat perizinan yang diajukan.
                </div>
            @endforelse
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100">
            {{ $perizinan->links() }}
        </div>
    </div>
</div>
@endsection
