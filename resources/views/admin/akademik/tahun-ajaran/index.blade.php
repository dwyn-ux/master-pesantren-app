@extends('layouts.app')

@section('title', 'Tahun Ajaran')
@section('page-title', 'Tahun Ajaran')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Daftar Tahun Ajaran</h2>
            <a href="{{ route('admin.akademik.tahun-ajaran.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fa-solid fa-plus mr-2"></i>Tambah Tahun Ajaran
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Semester</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Periode</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tahunAjaran as $ta)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $ta->nama }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($ta->semester) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $ta->tanggal_mulai->format('d M Y') }} - {{ $ta->tanggal_selesai->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($ta->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.akademik.tahun-ajaran.edit', $ta) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @if(!$ta->is_active)
                            <form action="{{ route('admin.akademik.tahun-ajaran.destroy', $ta) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus tahun ajaran ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            Belum ada tahun ajaran. <a href="{{ route('admin.akademik.tahun-ajaran.create') }}" class="text-indigo-600 hover:underline">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tahunAjaran->links() }}
        </div>
    </div>
</div>
@endsection
