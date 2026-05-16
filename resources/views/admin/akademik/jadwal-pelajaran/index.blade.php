@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">Jadwal Pelajaran</h2>
            <a href="{{ route('admin.akademik.jadwal-pelajaran.create', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'kelas_id' => request('kelas_id')]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fa-solid fa-plus mr-2"></i>Tambah Jadwal
            </a>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach($tahunAjaran as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }} - {{ ucfirst($ta->semester) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select name="kelas_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Kelas</option>
                    @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->tingkat->nama }} - {{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($jadwal->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Hari</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jam</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ustadz</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ruangan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($jadwal as $j)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-800 capitalize">{{ $j->hari }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $j->mataPelajaran->nama }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $j->ustadz->user->name ?? $j->ustadz->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $j->ruangan ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.akademik.jadwal-pelajaran.edit', $j) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.akademik.jadwal-pelajaran.destroy', $j) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @elseif(request('tahun_ajaran_id') && request('kelas_id'))
            <div class="text-center py-8 text-gray-500">Belum ada jadwal pelajaran untuk kelas ini.</div>
        @else
            <div class="text-center py-8 text-gray-500">Pilih tahun ajaran dan kelas untuk melihat jadwal.</div>
        @endif
    </div>
</div>
@endsection
