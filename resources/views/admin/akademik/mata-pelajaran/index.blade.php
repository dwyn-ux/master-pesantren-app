@extends('layouts.app')

@section('title', 'Mata Pelajaran')
@section('page-title', 'Mata Pelajaran Diniyah')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Daftar Mata Pelajaran</h2>
                <p class="text-sm text-gray-500">Mata pelajaran diniyah (Fiqih, Nahwu, Sharaf, dll)</p>
            </div>
            <a href="{{ route('admin.akademik.mata-pelajaran.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fa-solid fa-plus mr-2"></i>Tambah Mata Pelajaran
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Kitab</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($mataPelajaran as $mp)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $mp->kode }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $mp->nama }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $mp->kitab_count }} kitab</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.akademik.mata-pelajaran.edit', $mp) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.akademik.mata-pelajaran.kitab', $mp) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                                <i class="fa-solid fa-book"></i> Kitab
                            </a>
                            <form action="{{ route('admin.akademik.mata-pelajaran.destroy', $mp) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada mata pelajaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
