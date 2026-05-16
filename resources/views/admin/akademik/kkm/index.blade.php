@extends('layouts.app')

@section('title', 'KKM')
@section('page-title', 'KKM (Kriteria Ketuntasan Minimal)')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-2">KKM per Tingkat dan Mata Pelajaran</h2>
        <p class="text-sm text-gray-500 mb-6">Atur nilai minimum kelulusan per tingkat. Santri yang nilai akhir-nya di bawah KKM akan ditandai perlu remedial.</p>

        <form method="GET" class="mb-6">
            <div class="max-w-md">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih</option>
                    @foreach($tahunAjaran as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }} - {{ ucfirst($ta->semester) }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if(request('tahun_ajaran_id'))
            <form action="{{ route('admin.akademik.kkm.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ request('tahun_ajaran_id') }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase sticky left-0 bg-gray-50 z-10">Tingkat</th>
                                @foreach($mapel as $mp)
                                <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">{{ $mp->kode }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($tingkat as $tk)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-800 sticky left-0 bg-white whitespace-nowrap">{{ $tk->nama }}</td>
                                @foreach($mapel as $mp)
                                <td class="px-2 py-2 text-center">
                                    <input type="number" 
                                           name="kkm[{{ $tk->id }}_{{ $mp->id }}]" 
                                           value="{{ $kkmData[$tk->id . '_' . $mp->id] ?? 70 }}" 
                                           min="0" max="100" 
                                           class="w-16 px-1 py-1 border border-gray-300 rounded text-center text-sm">
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                        <i class="fa-solid fa-save mr-2"></i>Simpan KKM
                    </button>
                </div>
            </form>
        @else
            <div class="text-center py-8 text-gray-500">Pilih tahun ajaran untuk mengatur KKM.</div>
        @endif
    </div>
</div>
@endsection
