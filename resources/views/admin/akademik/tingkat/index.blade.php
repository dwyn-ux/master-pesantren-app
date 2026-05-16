@extends('layouts.app')

@section('title', 'Tingkat')
@section('page-title', 'Tingkat / Marhalah')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Daftar Tingkat</h2>
                <p class="text-sm text-gray-500">Marhalah / jenjang pendidikan (mis. Ibtidaiyah, Tsanawiyah, Aliyah)</p>
            </div>
            <a href="{{ route('admin.akademik.tingkat.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fa-solid fa-plus mr-2"></i>Tambah
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Urutan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Tingkat</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tingkat as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $t->urutan }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $t->nama }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.akademik.tingkat.edit', $t) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.akademik.tingkat.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada tingkat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
