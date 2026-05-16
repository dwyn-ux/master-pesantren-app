@extends('layouts.app')

@section('title', 'Preview Raport')
@section('page-title', 'Preview Raport - ' . $santri->nama)

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Preview Raport</h2>
                <p class="text-sm text-gray-500">{{ $santri->nama }} - {{ $tahunAjaran->nama }} ({{ ucfirst($tahunAjaran->semester) }})</p>
            </div>
            <a href="{{ route('admin.akademik.raport.download', [$santri->id, $tahunAjaran->id]) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <i class="fa-solid fa-file-pdf mr-2"></i>Download PDF
            </a>
        </div>

        <iframe src="{{ route('admin.akademik.raport.download', [$santri->id, $tahunAjaran->id]) }}" 
                class="w-full" style="height: 800px;" frameborder="0"></iframe>
    </div>
</div>
@endsection
