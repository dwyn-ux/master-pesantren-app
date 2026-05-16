@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')
@section('page-title', 'Edit Tahun Ajaran')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <form action="{{ route('admin.akademik.tahun-ajaran.update', $tahunAjaran) }}" method="POST">
            @csrf @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Tahun Ajaran</label>
                    <input type="text" name="nama" value="{{ old('nama', $tahunAjaran->nama) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                    <select name="semester" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="ganjil" {{ old('semester', $tahunAjaran->semester) == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ old('semester', $tahunAjaran->semester) == 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                    @error('semester')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $tahunAjaran->tanggal_mulai->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        @error('tanggal_mulai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $tahunAjaran->tanggal_selesai->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        @error('tanggal_selesai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tahunAjaran->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label class="ml-2 text-sm text-gray-700">Aktifkan tahun ajaran ini</label>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-4 border-t">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fa-solid fa-save mr-2"></i>Update
                </button>
                <a href="{{ route('admin.akademik.tahun-ajaran.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
