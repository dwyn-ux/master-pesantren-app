@extends('layouts.app')

@section('title', 'Kitab - ' . $mataPelajaran->nama)
@section('page-title', 'Kitab: ' . $mataPelajaran->nama)

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Form tambah kitab --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Kitab Referensi</h3>
        <form action="{{ route('admin.akademik.mata-pelajaran.kitab.store', $mataPelajaran) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kitab</label>
                <input type="text" name="nama" placeholder="Safinatun Najah" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pengarang</label>
                <input type="text" name="pengarang" placeholder="Salim bin Sumair" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Halaman</label>
                <div class="flex gap-2">
                    <input type="number" name="total_halaman" min="1" placeholder="64" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel daftar kitab --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">Daftar Kitab</h3>
            <a href="{{ route('admin.akademik.mata-pelajaran.index') }}" class="text-sm text-gray-600 hover:underline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Kitab</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pengarang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Halaman</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kitab as $k)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $k->nama }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $k->pengarang ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $k->total_halaman ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.akademik.mata-pelajaran.kitab.destroy', [$mataPelajaran, $k]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kitab ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada kitab. Tambahkan via form di atas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
