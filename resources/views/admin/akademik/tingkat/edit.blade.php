@extends('layouts.app')

@section('title', 'Edit Tingkat')
@section('page-title', 'Edit Tingkat')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <form action="{{ route('admin.akademik.tingkat.update', $tingkat) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Tingkat</label>
                    <input type="text" name="nama" value="{{ old('nama', $tingkat->nama) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                    <input type="number" name="urutan" value="{{ old('urutan', $tingkat->urutan) }}" min="0" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @error('urutan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex items-center gap-3 mt-6 pt-4 border-t">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                    <i class="fa-solid fa-save mr-2"></i>Update
                </button>
                <a href="{{ route('admin.akademik.tingkat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
