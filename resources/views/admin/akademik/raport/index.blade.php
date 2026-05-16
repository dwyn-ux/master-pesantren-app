@extends('layouts.app')

@section('title', 'Cetak Raport')
@section('page-title', 'Cetak Raport Santri')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Pilih Santri & Tahun Ajaran</h2>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach($tahunAjaran as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                        {{ $ta->nama }} - {{ ucfirst($ta->semester) }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if(request('tahun_ajaran_id') && $santri->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Santri</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Wali</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($santri as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $s->nama }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $s->kelasSantri->firstWhere('tahun_ajaran_id', request('tahun_ajaran_id'))?->kelas->nama ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $s->wali->first()?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.akademik.raport.preview', [$s->id, request('tahun_ajaran_id')]) }}" 
                                   target="_blank"
                                   class="text-indigo-600 hover:text-indigo-800 mr-3">
                                    <i class="fa-solid fa-eye"></i> Preview
                                </a>
                                <a href="{{ route('admin.akademik.raport.download', [$s->id, request('tahun_ajaran_id')]) }}" 
                                   target="_blank"
                                   class="text-green-600 hover:text-green-800">
                                    <i class="fa-solid fa-file-pdf"></i> Download PDF
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif(request('tahun_ajaran_id'))
            <div class="text-center py-8 text-gray-500">Belum ada santri di tahun ajaran ini.</div>
        @endif
    </div>
</div>
@endsection
