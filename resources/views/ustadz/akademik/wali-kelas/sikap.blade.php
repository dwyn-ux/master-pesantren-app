@extends('layouts.app')

@section('title', 'Penilaian Sikap')
@section('page-title', 'Sikap & Catatan: ' . $santri->nama)

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <form action="{{ route('ustadz.akademik.wali-kelas.store-sikap', [$kelas, $santri]) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Penilaian Sikap & Akhlak</h2>
                    <p class="text-sm text-gray-500">{{ $santri->nama }} • {{ $kelas->tingkat->nama }} - {{ $kelas->nama }}</p>
                </div>
                <a href="{{ route('ustadz.akademik.wali-kelas.show', $kelas) }}" class="text-sm text-gray-600 hover:underline">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="space-y-4">
                @foreach($sikapDefault as $aspek)
                @php $row = $existing[$aspek] ?? null; @endphp
                <div class="border border-gray-200 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-800 mb-3">{{ $aspek }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Predikat</label>
                            <select name="sikap[{{ $aspek }}][predikat]" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                <option value="">-</option>
                                @foreach(['A','B','C','D'] as $p)
                                <option value="{{ $p }}" {{ ($row?->predikat) === $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-600 mb-1">Deskripsi (opsional)</label>
                            <input type="text" name="sikap[{{ $aspek }}][deskripsi]" value="{{ $row?->deskripsi }}" placeholder="Contoh: Sangat jujur dan dapat dipercaya" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Catatan Wali Kelas</h2>
            <textarea name="catatan" rows="6" placeholder="Catatan umum tentang perkembangan santri selama semester ini..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('catatan', $catatan?->catatan) }}</textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">
                <i class="fa-solid fa-save mr-2"></i>Simpan Penilaian
            </button>
        </div>
    </form>
</div>
@endsection
