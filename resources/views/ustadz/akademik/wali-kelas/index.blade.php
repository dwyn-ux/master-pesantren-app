@extends('layouts.app')

@section('title', 'Wali Kelas')
@section('page-title', 'Kelas Saya (Wali Kelas)')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Kelas yang Saya Walikan</h2>

        @if($kelas->isEmpty())
            <div class="text-center py-8 text-gray-500">
                <i class="fa-solid fa-school text-4xl mb-3"></i>
                <p>Anda belum ditugaskan sebagai wali kelas. Hubungi admin.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($kelas as $k)
                <a href="{{ route('ustadz.akademik.wali-kelas.show', $k) }}" class="border border-gray-200 rounded-xl p-5 hover:border-indigo-300 hover:shadow-md transition">
                    <h3 class="font-bold text-gray-800 text-lg">{{ $k->tingkat->nama }} - {{ $k->nama }}</h3>
                    <div class="text-sm text-gray-600 mt-2">
                        <i class="fa-solid fa-users text-indigo-600 mr-1"></i>
                        {{ $k->santri->count() }} santri
                    </div>
                    <p class="text-xs text-indigo-600 mt-3"><i class="fa-solid fa-arrow-right mr-1"></i>Lihat & nilai sikap santri</p>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
