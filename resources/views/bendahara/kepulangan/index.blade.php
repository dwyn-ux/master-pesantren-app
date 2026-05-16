@extends('layouts.app')
@section('title', 'ACC Kepulangan Santri')
@section('page-title', 'ACC Surat Kesanggupan')

@section('sidebar')
    @include('partials.sidebar-bendahara')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden" x-data="{ showModal: false, selectedSantri: null, actionUrl: '' }">
    @if(!$sesiAktif)
    <div class="p-12 text-center">
        <i class="fa-solid fa-folder-open text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-xl font-bold text-gray-800">Tidak Ada Sesi Kepulangan Aktif</h3>
        <p class="text-gray-500 mt-2">Belum ada sesi kepulangan yang dibuka oleh admin/kepala pondok saat ini.</p>
    </div>
    @else
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
        <h6 class="text-lg font-bold text-gray-800">Sesi Aktif: {{ $sesiAktif->nama_sesi }}</h6>
        <p class="text-sm text-gray-500">Daftar santri dan status administrasinya.</p>
    </div>

    {{-- Filter --}}
    <div class="p-6 border-b border-gray-100">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" 
                       placeholder="Cari nama santri atau NIS...">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm">Filter</button>
                <a href="{{ route('bendahara.kepulangan.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Santri</th>
                    <th class="px-6 py-4 font-medium">Status Administrasi</th>
                    <th class="px-6 py-4 font-medium">Janji Bayar</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kepulangan as $k)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $k->santri->nama }}</div>
                        <div class="text-xs text-gray-500">NIS: {{ $k->santri->nis }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($k->status_administrasi === 'lunas')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Lunas / ACC</span>
                        @elseif($k->status_administrasi === 'acc_bendahara')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">ACC Surat Kesanggupan</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Belum Lunas</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($k->tanggal_janji_bayar)
                            <div class="font-bold text-gray-800">{{ $k->tanggal_janji_bayar->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-gray-500 mt-0.5 truncate max-w-[150px]" title="{{ $k->keterangan_bendahara }}">{{ $k->keterangan_bendahara }}</div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($k->status_administrasi === 'belum_lunas')
                        <button type="button" @click="showModal = true; selectedSantri = '{{ $k->santri->nama }}'; actionUrl = '{{ route('bendahara.kepulangan.acc', $k) }}'" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-sm font-medium transition-colors">
                            <i class="fa-solid fa-file-signature"></i> ACC Surat
                        </button>
                        @else
                        <span class="text-xs text-gray-400 italic">Sudah di-ACC</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kepulangan->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $kepulangan->links() }}
    </div>
    @endif
    @endif

    <!-- Modal ACC Surat Kesanggupan -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showModal" x-transition.scale class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="actionUrl" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">ACC Surat Kesanggupan</h3>
                        <div class="mt-2 text-sm text-gray-500 mb-4">
                            Santri: <span class="font-bold text-gray-800" x-text="selectedSantri"></span>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Janji Bayar <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_janji_bayar" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan / Catatan</label>
                                <textarea name="keterangan_bendahara" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none" placeholder="Catatan opsional dari bendahara"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan ACC
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
