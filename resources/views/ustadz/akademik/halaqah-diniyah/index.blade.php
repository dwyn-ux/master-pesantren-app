@extends('layouts.app')

@section('title', 'Halaqah Diniyah')
@section('page-title', 'Halaqah Diniyah (Kajian Kitab)')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Daftar Halaqah Diniyah</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($halaqah as $h)
            <div class="border border-gray-200 rounded-xl p-5 hover:border-indigo-300 hover:shadow-md transition">
                <h3 class="font-bold text-gray-800 text-lg mb-2">{{ $h->nama }}</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-book text-indigo-600"></i>
                        <span>Kitab: {{ $h->kitab->nama ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-users text-indigo-600"></i>
                        <span>{{ $h->santri_count ?? 0 }} santri</span>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('ustadz.akademik.halaqah-diniyah.santri', $h) }}" class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-2 rounded-lg text-sm text-center">
                        <i class="fa-solid fa-users mr-1"></i>Santri
                    </a>
                    <a href="{{ route('ustadz.akademik.halaqah-diniyah.setoran', $h) }}" class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 px-3 py-2 rounded-lg text-sm text-center">
                        <i class="fa-solid fa-scroll mr-1"></i>Setoran
                    </a>
                    <a href="{{ route('ustadz.akademik.halaqah-diniyah.khataman', $h) }}" class="flex-1 bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-2 rounded-lg text-sm text-center">
                        <i class="fa-solid fa-certificate mr-1"></i>Khatam
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
