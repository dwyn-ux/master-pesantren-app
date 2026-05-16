@extends('layouts.app')

@section('title', 'Input Nilai')
@section('page-title', 'Input Nilai')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Input Nilai Santri</h2>

        @if(!empty($noTahunAjaran))
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                <i class="fa-solid fa-triangle-exclamation text-yellow-600 mr-2"></i>
                <span class="text-yellow-800">Belum ada tahun ajaran aktif. Hubungi admin untuk mengaktifkan tahun ajaran terlebih dahulu.</span>
            </div>
        @else
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach($komponen as $mpId => $items)
                        @php $mp = $items->first()->mataPelajaran; @endphp
                        <option value="{{ $mpId }}" {{ request('mata_pelajaran_id') == $mpId ? 'selected' : '' }}>
                            {{ $mp->nama }} ({{ $mp->kode }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if(request('mata_pelajaran_id'))
            @php
                $komponenList = $komponen[request('mata_pelajaran_id')] ?? collect();
                $totalBobot = $komponenList->sum('bobot');
            @endphp

            @if($totalBobot > 0)
                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <h3 class="font-semibold text-gray-800 mb-3">Komponen Nilai (Total Bobot: {{ $totalBobot }})</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($komponenList as $k)
                            <div class="flex items-center justify-between bg-white px-3 py-2 rounded border">
                                <span class="text-sm">{{ $k->nama }}</span>
                                <span class="text-sm font-bold text-indigo-600">{{ $k->bobot }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <form action="{{ route('ustadz.akademik.nilai.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="komponen_id" value="{{ request('mata_pelajaran_id') }}">

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Santri</th>
                                    @foreach($komponenList as $k)
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ $k->nama }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($santri as $s)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                        {{ $s->nama }}<br>
                                        <span class="text-xs text-gray-500">{{ $s->kelasSantri->first()?->kelas->nama ?? '-' }}</span>
                                    </td>
                                    @foreach($komponenList as $k)
                                    <td class="px-4 py-3 text-center">
                                        <input type="number" name="nilai[{{ $s->id }}]" min="0" max="100" step="0.01"
                                               value="{{ old('nilai.' . $s->id, 0) }}"
                                               class="w-20 px-2 py-1 text-center border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500">
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                            <i class="fa-solid fa-save mr-2"></i>Simpan Nilai
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-circle-exclamation text-4xl mb-3"></i>
                    <p>Total bobot komponen nilai adalah 0. Silakan setting komponen nilai di halaman admin.</p>
                </div>
            @endif
        @endif
        @endif
    </div>
</div>
@endsection
