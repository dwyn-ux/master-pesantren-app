@extends('layouts.app')

@section('title', 'Komponen Nilai')
@section('page-title', 'Komponen Nilai')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Komponen Nilai</h2>
        <p class="text-sm text-gray-500 mb-6">Atur komponen penilaian per mata pelajaran (UH, Tugas, UTS, UAS) beserta bobot persentasenya. Total bobot wajib = 100%.</p>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih</option>
                    @foreach($tahunAjaran as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }} - {{ ucfirst($ta->semester) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih</option>
                    @foreach($mapel as $mp)
                    <option value="{{ $mp->id }}" {{ request('mata_pelajaran_id') == $mp->id ? 'selected' : '' }}>{{ $mp->nama }} ({{ $mp->kode }})</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if(request('tahun_ajaran_id') && request('mata_pelajaran_id'))
            <div class="mb-4 p-4 bg-{{ $totalBobot == 100 ? 'green' : 'yellow' }}-50 border border-{{ $totalBobot == 100 ? 'green' : 'yellow' }}-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-{{ $totalBobot == 100 ? 'green' : 'yellow' }}-800">Total Bobot Saat Ini: {{ $totalBobot }}%</span>
                    @if($totalBobot != 100)
                        <span class="text-sm text-{{ $totalBobot == 100 ? 'green' : 'yellow' }}-700">Target: 100% (selisih {{ 100 - $totalBobot }}%)</span>
                    @else
                        <span class="text-sm text-green-700"><i class="fa-solid fa-check-circle"></i> Bobot lengkap</span>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto mb-6">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Urutan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Komponen</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Bobot (%)</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($komponen as $k)
                        <tr class="hover:bg-gray-50">
                            <form action="{{ route('admin.akademik.komponen-nilai.update', $k) }}" method="POST" id="form-{{ $k->id }}">@csrf @method('PUT')
                                <td class="px-4 py-2"><input type="number" name="urutan" value="{{ $k->urutan }}" min="0" form="form-{{ $k->id }}" class="w-16 px-2 py-1 border border-gray-300 rounded text-sm"></td>
                                <td class="px-4 py-2"><input type="text" name="nama" value="{{ $k->nama }}" form="form-{{ $k->id }}" class="w-full px-2 py-1 border border-gray-300 rounded text-sm"></td>
                                <td class="px-4 py-2 text-center"><input type="number" name="bobot" value="{{ $k->bobot }}" min="0" max="100" form="form-{{ $k->id }}" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center"></td>
                                <td class="px-4 py-2 text-center">
                                    <button type="submit" form="form-{{ $k->id }}" class="text-green-600 hover:text-green-800 mr-2"><i class="fa-solid fa-save"></i></button>
                            </form>
                            <form action="{{ route('admin.akademik.komponen-nilai.destroy', $k) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">@csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                            </form>
                                </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada komponen. Tambahkan via form di bawah.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 rounded-xl p-4">
                <h3 class="font-semibold text-gray-800 mb-3">Tambah Komponen Baru</h3>
                <form action="{{ route('admin.akademik.komponen-nilai.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    @csrf
                    <input type="hidden" name="tahun_ajaran_id" value="{{ request('tahun_ajaran_id') }}">
                    <input type="hidden" name="mata_pelajaran_id" value="{{ request('mata_pelajaran_id') }}">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Urutan</label>
                        <input type="number" name="urutan" value="{{ $komponen->count() + 1 }}" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Nama</label>
                        <input type="text" name="nama" placeholder="UH, Tugas, UTS, UAS" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Bobot (%)</label>
                        <input type="number" name="bobot" min="0" max="100" placeholder="25" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">
                            <i class="fa-solid fa-plus mr-1"></i>Tambah
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
