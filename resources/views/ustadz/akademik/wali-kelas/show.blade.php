@extends('layouts.app')

@section('title', 'Santri Kelas ' . $kelas->nama)
@section('page-title', 'Wali Kelas: ' . $kelas->tingkat->nama . ' - ' . $kelas->nama)

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">Daftar Santri</h2>
            <a href="{{ route('ustadz.akademik.wali-kelas.index') }}" class="text-sm text-gray-600 hover:underline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if(!empty($noTahunAjaran))
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                <i class="fa-solid fa-triangle-exclamation text-yellow-600 mr-2"></i>
                <span class="text-yellow-800">Belum ada tahun ajaran aktif.</span>
            </div>
        @elseif($santri->isEmpty())
            <div class="text-center py-8 text-gray-500">Belum ada santri di kelas ini untuk tahun ajaran aktif.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Santri</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Wali</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($santri as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $s->nama }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $s->wali->first()?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('ustadz.akademik.wali-kelas.sikap', [$kelas, $s]) }}" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fa-solid fa-clipboard-list"></i> Nilai Sikap & Catatan
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
