@extends('layouts.app')

@section('title', 'Kelas')
@section('page-title', 'Kelas / Rombel')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Daftar Kelas</h2>
                <p class="text-sm text-gray-500">Kelas / rombel untuk mengelompokkan santri</p>
            </div>
            <a href="{{ route('admin.akademik.kelas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fa-solid fa-plus mr-2"></i>Tambah Kelas
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tingkat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Wali Kelas</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Kapasitas</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kelas as $k)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $k->tingkat->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $k->nama }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $k->waliKelas->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $k->kapasitas }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.akademik.kelas.edit', $k) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.akademik.kelas.destroy', $k) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada kelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
