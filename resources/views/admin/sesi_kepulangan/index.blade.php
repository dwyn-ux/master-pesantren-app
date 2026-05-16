@extends('layouts.app')
@section('title', 'Sesi Kepulangan Santri')
@section('page-title', 'Sesi Kepulangan')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-4">
        <div class="glass-panel rounded-2xl shadow-sm p-6 border-t-4 border-indigo-500">
            <h6 class="text-lg font-bold text-gray-800 mb-4">Buat Sesi Baru</h6>
            <form action="{{ route('admin.sesi-kepulangan.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Sesi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sesi" required placeholder="Misal: Libur Semester Ganjil 2026" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai Pulang <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pulang" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Wajib Kembali <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_kembali" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm">
                    Buat Sesi Kepulangan
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-8">
        <div class="glass-panel rounded-2xl shadow-sm p-6">
            <h6 class="text-lg font-bold text-gray-800 mb-4">Daftar Sesi Kepulangan</h6>
            <div class="space-y-4">
                @forelse($sesi as $s)
                <div class="p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">{{ $s->nama_sesi }}</h4>
                        <div class="text-sm text-gray-500 mt-1">
                            <i class="fa-solid fa-calendar mr-1"></i> Pulang: {{ $s->tanggal_pulang->format('d M Y') }} &mdash; Kembali: {{ $s->tanggal_kembali->format('d M Y') }}
                        </div>
                        <div class="text-xs font-semibold text-indigo-600 mt-2 bg-indigo-50 inline-block px-2 py-1 rounded">
                            Total Santri Terdaftar: {{ $s->kepulangan_santri_count }}
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 min-w-[120px]">
                        @if($s->is_active)
                            <span class="text-center px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold">Aktif</span>
                        @else
                            <span class="text-center px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs font-bold">Ditutup</span>
                        @endif
                        <a href="{{ route('admin.sesi-kepulangan.show', $s) }}" class="text-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 text-sm">
                    Belum ada sesi kepulangan yang dibuat.
                </div>
                @endforelse
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                {{ $sesi->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
