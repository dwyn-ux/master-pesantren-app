@extends('layouts.app')

@section('title', 'Santri Halaqah Diniyah')
@section('page-title', 'Santri - ' . $halaqah->nama)

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Daftar Santri Halaqah</h2>
            <a href="{{ route('ustadz.akademik.halaqah-diniyah.index') }}" class="text-sm text-gray-600 hover:underline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Santri</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Wali</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($santri as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $s->santri->nama }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $s->santri->wali->first()?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($s->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
