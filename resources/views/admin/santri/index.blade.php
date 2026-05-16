@extends('layouts.app')
@section('title', 'Data Santri')
@section('page-title', 'Data Santri')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <h6 class="text-lg font-bold text-gray-800">Daftar Santri</h6>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.santri.naik-kelas') }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-dots"></i> Naik Kelas
            </a>
            <a href="{{ route('admin.santri.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Santri
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="p-6 border-b border-gray-100">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" 
                       placeholder="Cari nama / NIS / NIK...">
            </div>
            <div class="w-full md:w-48">
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all bg-white appearance-none">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Non-aktif</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm">
                    Filter
                </button>
                <a href="{{ route('admin.santri.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">NIS/NIK</th>
                    <th class="px-6 py-4 font-medium">Nama</th>
                    <th class="px-6 py-4 font-medium">Lahir</th>
                    <th class="px-6 py-4 font-medium">Saldo</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($santri as $s)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="px-6 py-4 font-semibold text-gray-800">
                        {{ $s->nis }}<br>
                        <span class="text-xs text-gray-500">{{ $s->nik ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $s->nama }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $s->tanggal_lahir ? $s->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">Rp {{ number_format($s->saldo) }}</td>
                    <td class="px-6 py-4">
                        @if($s->is_aktif)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">Non-aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($s->is_aktif)
                                <a href="{{ route('admin.santri.edit', $s) }}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition-colors" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.santri.toggle-status', $s) }}" class="inline-block" onsubmit="return confirm('Nonaktifkan santri {{ $s->nama }}?')">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 flex items-center justify-center transition-colors" title="Nonaktifkan">
                                        <i class="fa-solid fa-user-minus text-sm"></i>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.santri.toggle-status', $s) }}" class="inline-block" onsubmit="return confirm('Aktifkan kembali santri {{ $s->nama }}?')">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 flex items-center justify-center transition-colors" title="Aktifkan">
                                        <i class="fa-solid fa-user-check text-sm"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.santri.force-destroy', $s) }}" class="inline-block" onsubmit="return confirm('Hapus PERMANEN santri {{ $s->nama }}? Semua data (setoran, tagihan, saldo, transaksi) akan ikut terhapus dan tidak bisa dipulihkan!')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Hapus Permanen">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-user-slash text-4xl mb-3 text-gray-300"></i>
                            <p>Tidak ada data santri ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($santri->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $santri->links() }}
    </div>
    @endif
</div>
@endsection
