@extends('layouts.app')
@section('title', 'Data Wali')
@section('page-title', 'Data Wali')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
@if(session('credential'))
<div x-data="{ show: true }" x-show="show" class="mb-6 bg-amber-50 border-l-4 border-amber-400 p-5 rounded-r-xl shadow-sm relative">
    <button @click="show = false" class="absolute top-4 right-4 text-amber-500 hover:text-amber-700">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="flex gap-3 text-amber-800">
        <i class="fa-solid fa-key text-xl mt-0.5"></i>
        <div>
            <h4 class="font-bold mb-1">Kredensial Baru — Catat Sekarang!</h4>
            <div class="text-sm opacity-90 leading-relaxed">{!! session('credential') !!}</div>
        </div>
    </div>
</div>
@endif

<div class="glass-panel rounded-2xl shadow-sm mb-6 overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <h6 class="text-lg font-bold text-gray-800">Daftar Wali Santri</h6>
        <a href="{{ route('admin.wali.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Wali
        </a>
    </div>

    <div class="p-6 border-b border-gray-100">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 max-w-lg">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" 
                       placeholder="Cari nama / no HP...">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm">
                    Cari
                </button>
                <a href="{{ route('admin.wali.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Nama Wali</th>
                    <th class="px-6 py-4 font-medium">No HP</th>
                    <th class="px-6 py-4 font-medium">Username</th>
                    <th class="px-6 py-4 font-medium">Santri Anak</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($wali as $w)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $w->nama }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $w->no_hp }}</td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-md border border-gray-200">{{ $w->user->username }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                        @foreach($w->santri as $s)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $s->nama }}</span>
                        @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($w->user->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">Non-aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($w->user->is_active)
                                <a href="{{ route('admin.wali.edit', $w) }}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition-colors" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.wali.reset-password', $w) }}" class="inline-block" onsubmit="return confirm('Reset password untuk {{ $w->nama }}?')">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-colors" title="Reset password">
                                        <i class="fa-solid fa-key text-sm"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.wali.toggle-status', $w) }}" class="inline-block" onsubmit="return confirm('Nonaktifkan wali {{ $w->nama }}?')">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 flex items-center justify-center transition-colors" title="Nonaktifkan">
                                        <i class="fa-solid fa-user-minus text-sm"></i>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.wali.toggle-status', $w) }}" class="inline-block" onsubmit="return confirm('Aktifkan kembali wali {{ $w->nama }}?')">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 flex items-center justify-center transition-colors" title="Aktifkan">
                                        <i class="fa-solid fa-user-check text-sm"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.wali.force-destroy', $w) }}" class="inline-block" onsubmit="return confirm('Hapus PERMANEN wali {{ $w->nama }}? Akun login, relasi ke santri, dan semua pesan terkait akan ikut terhapus dan tidak bisa dipulihkan!')">
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
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-user-group text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada data wali santri.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($wali->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $wali->links() }}
    </div>
    @endif
</div>
@endsection
